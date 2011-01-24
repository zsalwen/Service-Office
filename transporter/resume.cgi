#!/usr/bin/perl 

use Fcntl;
use File::Basename;

##
# Resumable file upload:
#
# When a collection of files is about to be uploaded, Rad Upload
# will send a zero byte request but the query string will have a
# parameter named 'cmd' with a value of 0.
#
# The request may or may not include a hash code. If a hash code is
# included, that means we are attempting to resume failed/paused
# transfer of multiple files. Some files may have been completely
# uploaded - some may have be partially uploaded.
#
# When a hash is not included this script will generate one and send
# it back to the client. All future requests in this transfer need to
# include this hash code in the query string.
#
# Before each file is uploaded, another empty request with a 'cmd' of
# 1 has to be sent to the script. The script must then return the
# number of bytes that have been previously written for that file. -1
# will be the response if an error has occured. 0 will be returned if
# this is the first attempt to upload the file in question.
#
# When all the files have been uploaded another zero byte request will
# be made. This time cmd parameter will be set to 2 - meaning that we
# have finished uploading the files. Now the script should send a
# nicely formatted reply and move the files to a permanent location.
#
# Last but not least when the upload was interrupted we have another
# response, in this case cmd=3
##

##
# Copyright Rad Inks (Pvt) Ltd. 2006.
# reproduction prohibited without permission.
##


##
# the top level folder for saving files.
##

$save_path="/dev/shm/uploaded/";

@qstring=split(/&/,$ENV{'QUERY_STRING'});
foreach(@qstring)
{
	@pair = split(/=/, $_);
	$pair[1] =~ tr/+/ /;
	$pair[1] =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
	$params{$pair[0]} = $pair[1];
}

$fname = $params{'fname'};
$hash = $params{'hash'};
$cmd =  $params{'cmd'};

##
# the top level folder on the client computer.
##
$userfile_parent = $params{'userfile_parent'};

if(defined($userfile_parent))
{
	#print STDERR "userfile_parent = $userfile_parent";
	$fname =~ s/$userfile_parent//;
}

##
# Finds all the files that have been uploaded, moves them to
# a permanent location, prepares server response.
##
sub find_files
{
	my ($path,$move) = @_;
	if($move==1)
	{
 		$newPath = $path;
		$newPath =~ s/$hash\///;
	}

	#print STDERR "Find Files in - $path \n";
	my @files = split("\n",`ls $path`);

	foreach(@files)
	{
		if($_ ne '.' && $_ ne '..')
		{
            #print STDERR $_;
			my $rel_path=$path . $_;
			if( -d $rel_path)
			{
				if($move == 1 && !(-d $newPath . $_))
				{
					mkdir($newPath . $_);
				}
                my $bk_newPath = $newPath;
				find_files("$rel_path/",$move);
                $newPath = $bk_newPath;           
			}
			else
			{
                print "<tr><td>$_</td><td>";
                print (-s ($rel_path));
                print "</td></tr>";

                #print STDERR "file $_ size = " . -s ($rel_path);
				if($move == 1)
				{
					rename($rel_path,$newPath . $_);
					print STDERR "Trying to move from $rel_path to $newPath$_\n";
				}
			}
		}
	}
	if($move == 1)
	{
		print STDERR "rmdir $path";
		rmdir($path);
	}
}

if(defined ($cmd))
{
	if($cmd == 0)
	{
		# trim()
        print STDERR "content length" . $ENV{'Content-length'};
        
		print "Content-type: text/plain\n\n";

		if($hash eq '')
		{

			#
			# The client wants upload a bunch of files. Since the
			# client does not have a previously generated hashcode
			# we make a new one. To be able to validate the hash we
			# prefix it with 0, and suffix it with 1.
			#
			$hash = "0";
			for($i=0 ; $i< 32 ;)
			{
				$j = chr(int(rand(127)));
				if($j =~ /[a-zA-Z0-9]/)
				{
					$hash .=$j;
					$i++;
				}
			}
			$hash .= "1";
		}
		$hash =~ s/^\s+//;
		$hash =~ s/\s+$//;

		print "$hash\n";
		#print STDERR "Sending HASH = 0($hash)1";
	}
	elsif($cmd == 1)
	{
		print "Content-type: text/plain\n\n";
		#
		# trying to find out if this particular file has been
		# uploaded before.
		#
		if( -e "$save_path$hash}/{$fname}")
		{
            print -s "{$save_path}{$hash}/{$fname}";
			#print STDERR "Sending filesize " . (-s "{$save_path}{$hash}/{$fname}");
		}
		else
		{
			print "00\n";
			#print STDERR "No resumption needed for" . "{$save_path}{$hash}/{$fname}";
		}
	}
	elsif($cmd == 2)
	{
		print "Content-type: text/html\n\n";
		#
		#  all the files in this transfer have been uploaded.
		#

		open (INFILE,"head.txt");
		while(<INFILE>) {
			print $_;
		}
		close(INFILE);

 		find_files($save_path . $hash . "/",1);

		open (INFILE,"foot.txt");
		while(<INFILE>) {
			print $_;
		}
		close(INFILE);

	}
	elsif($cmd == 3)
	{
		print "Content-type: text/html\n\n";
		#
		# Some files may not have been uploaded and/or some may have been partially uploaded.
		#

		open (INFILE,"head.txt");
		while(<INFILE>) {
			print $_;
		}
		close(INFILE);

		print '<tr><td colspan=2>File Upload Was Interrupted.</td></tr>';
		find_files($save_path . $hash . "/",0);

		open (INFILE,"foot.txt");
		while(<INFILE>) {
			print $_;
		}
		close(INFILE);

	}
}
else
{

	print "Content-type: text/plain\n\n";
    
    if(defined ($fname))
    {    
    
	    $save_path .= "$hash/";
	    $path = $save_path .  dirname($fname);
    
	    if($path =~ '/(;)|(\.\.)/')
	    {
		    print STDERR "$path rejected";
		    exit;
	    }
        else
	    {
		    if(-e $path)
		    {
			    unless(-d $path)
			    {
				    #
				    # a file by that name already exists and it's not a directory.
				    # You will need to handle this situation according to your
				    # requirements. You have a range of options including siliently
				    # ingoring this file or rejecting the upload as a whole.
				    #
			    }
		    }
		    else
		    {
			    `mkdir -p "$path"`;
		    }
    
	    }
	    $offset = $params{'offset'};
	    $fname = $save_path . $fname;
    
        #print STDERR "path = $fname   -  APPEND , Offset = $offset\n";
    
	    $size = (-s $fname);
	    if(defined($offset) && $offset > 0 && $offset <= $size)
	    {
		    sysopen(FH, $fname, O_RDWR) or die "can't open file: $!";
		    seek(FH,$offset,0);
	    }
	    else
	    {
		    sysopen(FH, $fname, O_RDWR|O_CREAT) or die "can't open file: $!";
	    }
        
        while (read (STDIN ,$LINE, 4096))
        {
            print FH $LINE;
        }
        

	    close(FH);
	    close(STDIN);
	    print '1';
     }
     else
     {
        $fname = $save_path . "trysavingafile.txt";
        #print STDERR $fname;
        sysopen(FH, $fname, O_RDWR|O_CREAT) or die print 'The folder you have chosen cannot be written to';
        print 'Please open applet.html in your browser to start an upload.';
    }
}
