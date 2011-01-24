<?php

/**
 * Resumable file upload:
 *
 * When a collection of files is about to be uploaded, Rad Upload
 * will send a zero byte request but the query string will have
 * a parameter named 'cmd' with a value of 0.
 *
 * The request may or may not include a hash code. If a hash code is
 * included, that means we are attempting to resume failed/paused
 * transfer of multiple files. Some files may have been completely
 * uploaded - some may have be partially uploaded.
 *
 * When a hash is not included this script will generate one and send
 * it back to the client. All future requests in this transfer need to
 * include this hash code in the query string.
 *
 * Before each file is uploaded, another empty request with a 'cmd' of
 * 1 has to be sent to the script. The script must then return the
 * number of bytes that have been previously written for that file. -1
 * will be the response if an error has occured. 0 will be returned if
 * this is the first attempt to upload the file in question.
 *
 * When all the files have been uploaded another zero byte request will
 * be made. This time cmd parameter will be set to 2 - meaning that we
 * have finished uploading the files. Now the script should send a
 * nicely formatted reply and move the files to a permanent location.
 *
 * Last but not least when the upload was interrupted we have another
 * response, in this case cmd=3
 */

/**
 * Copyright Rad Inks (Pvt) Ltd. 2008
 **/


/*
 * the top level folder for saving files. It should be a folder
 * that the web server can write to. The path should end with a
 * '/'.
 * examples:
 *   d:/uploaded/
 *   /home/radupload/uploaded/
 */
$save_path="/dev/shm/uploaded/";

$log_file="";


if($_SERVER['REQUEST_METHOD'] == 'PUT')
{
		/*
		* determine the filename by stripping out the script name.
		*/
        //log_error("Script Name = {$_SERVER["SCRIPT_NAME"]}  and Self = {$_SERVER["PHP_SELF"]}");
        
        $fname = str_replace($_SERVER["SCRIPT_NAME"],"",$_SERVER["PHP_SELF"]);
        $fname = str_replace("+"," ",$fname);
}
else
{
        /*
         * With post the filename is in the query string.
         */
        $fname = (isset($_REQUEST['fname'])) ? $_REQUEST['fname'] : "";
}

        
/*
 * Retrieve the hash if it has been sent.
 */
$hash = (isset($_REQUEST['hash'])) ? $_REQUEST['hash'] : "";
                
/*
 * if an absolute path is sent, it may contain double '//' because the
 * filename is appended to the scriptname with a '/' and not a '?'
 */

$pos = strpos($fname,'//');
if($pos !== false && $pos == 0)
{
        $fname = substr($fname,1);
}
        
if(isset($_REQUEST['userfile_parent']))
{
        /*
        * the top level folder on te client computer.
        */
        $userfile_parent = preg_quote($_REQUEST['userfile_parent'],"|");        
        $fname = preg_replace("|{$userfile_parent}|","",$fname);
        //error_log("{$userfile_parent} , $fname");
}

$windbag = isset($_ENV['OS']) ? stristr($_ENV['OS'],'windows') : 0 ;

/*
log_error("fname = $fname");
log_error("SELF ="  .$_SERVER["PHP_SELF"] );
log_error("SCRIPT_NAME ="  .$_SERVER["SCRIPT_NAME"] );
log_error("URI ="  .$_SERVER["REQUEST_URI"] );
log_error("\nuserfile_parent = " . $userfile_parent);
log_error("HASH = $hash");
*/

function log_error($err)
{
    global $log_file;
    if($log_file != '')
    {
        error_log("\n$err", 3, $log_file);
    }
    else
    {
        error_log($err);
    }   
}

/**
 * Finds all the files that have been uploaded, moves them to
 * a permanent location, prepares server response.
 */
function find_files($path,$move=1)
{
	global $hash;

	if($move==1)
	{
		$newPath = str_replace("{$hash}/","",$path);
	}
        
	//log_error("Find Files in - $path");
	if (file_exists($path) && ($handle = opendir($path)))
    {
		while (false !== ($file = readdir($handle)))
        {
        	if ($file != "." && $file != "..")
            {
            	$rel_path=$path . $file;
                               
				if(is_dir($rel_path))
				{
						if($move == 1 && !is_dir($newPath . $file))
					{
						mkdir($newPath . $file);
					}
					find_files("$rel_path/");
				}
				else
				{
					if($move == 1)
					{
						rename($rel_path,$newPath . $file);
						//log_error("Trying to move from {$rel_path} to {$newPath}{$file}");
					}
					echo "<tr><td>$file</td><td>". filesize($newPath . $file) . "</td></tr>";
				}
			}
		}
		closedir($handle);
		if($move == 1)
		{
			rmdir($path);
		}
	}
}

/**
 * mimic `mkdir -p` for windows systems.
 */
function mkdir_p($path, $mode = 0700) {
    $dirs = split("/",$path);
	$path = $dirs[0];
	for($i = 1;$i < count($dirs);$i++) {
		$path .= "/".$dirs[$i];
		if(!is_dir($path))
		{
			mkdir($path,$mode);
		}
	}
}			

if(isset($_REQUEST['cmd']))
{
    $cmd = $_REQUEST['cmd'];
    //log_error("\n Init = {$_REQUEST['cmd']}");
	switch ($cmd)
	{
		case 0:
			/*
			 * The client wants upload a bunch of files. Since the
			 * client does not have a previously generated hashcode
			 * we make a new one. To be able to validate the has we
			 * prefix it with 0, and suffix it with 1.
			 */
			if(!isset($hash) || trim($hash) == '')
			{
					$hash ="0". uniqid(rand() . rand(), false) ."1";
					//log_error("Sending HASH = {$hash}");
			}
			echo "$hash\n";

			break;

		case 1:
			/*
			* trying to find out if this particular file has been
			* uploaded before.
			*/
			if(file_exists("{$save_path}{$hash}/{$fname}"))
			{
				//log_error("Sending filesize " . filesize("{$save_path}{$hash}/{$fname}") ."\n");
				echo filesize("{$save_path}{$hash}/{$fname}");
			}
			else
			{
				echo "00\n";
				//log_error("No resumption needed for" . "{$save_path}{$hash}/{$fname}");
			}

			break;

		case 2:
			/*
			 * all the files in this transfer have been uploaded.
			 */
			// log_error('Make Perm: ');
			require_once('head.txt');
			find_files("{$save_path}{$hash}/");
			require_once('foot.txt');
			break;

		case 3:
			/*
			 * Some files may not have been uploaded and/or some may have been partially uploaded.
			 */
                        
			require_once('head.txt');
			echo '<tr><td colspan=2>File Upload Was Interrupted.</td></tr>';
			//   log_error('find files 1');
			find_files("{$save_path}{$hash}/",0);
			require_once('foot.txt');
			break;
	}
}
else
{
	if($fname != '')
	{
        //log_error('The data comes in, the file name is ' . $fname);
        
		/* PUT data comes in on the stdin stream */
		$putdata = fopen("php://input","r");
		$save_path .= "{$hash}/";
		$path ="$save_path" .  dirname($fname);
        
        //log_error('save file in = ' . $save_path);
                
		if(preg_match('/(;)|(\.\.)/',$path))
		{
			echo "$path rejected";
			exit;
		}
		else
		{
			if(file_exists($path))
			{
				if(!is_dir($path))
				{
					/*
					* a file by that name already exists and it's not a directory.
					* You will need to handle this situation according to your
					* requirements. You have a range of options including siliently
					* ingoring this file or rejecting the upload as a whole.
					*/
					//log_error("file exists $path");
				}
			}
			else
			{
				//   log_error('making path ' . $path);
				if($windbag)
				{
					mkdir_p($path,777);
				}
				else
				{
					`mkdir -p "$path"`;
				}
			}
		}
	
		$offset = $_REQUEST['offset'];
		
		$fname = $save_path . $fname;
		if(isset($offset) && $offset > 0 && $offset <= filesize($fname))
		{
			//log_error("APPEND , Offset = $offset");
			$fp = fopen($fname,"a+");
			fseek($fp,$offset);
		}
		else
		{
			$fp = fopen($fname,"wb");
		}
		/* Read the data 1kb at a time
		and write to the file */
		while (!feof($putdata))
		{
			$data = fread($putdata,1024);
			fwrite($fp,$data);
		}
			
		/* Close the streams */
		/*******************************************************************
		* ADD CODE TO CUSTOMIZE THE SCRIPT BEHAVIOUR HERE
		*******************************************************************/
		fclose($fp);
		fclose($putdata);
		echo '1';
	}
	else
	{

		$fp = @fopen("{$save_path}trysavingafile.txt","a+");
		//log_error($fp);
		if($fp)
		{
			echo 'Please open applet.html in your browser to start an upload.';
		}
		else
		{
			echo 'The folder you have chosen cannot be written to';
		}
	}
}

?>
