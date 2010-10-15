<?
function talk($to,$message){
	include_once '/thirdParty/xmpphp/XMPPHP/XMPP.php';
	$conn = new XMPPHP_XMPP('talk.google.com', 5222, 'talkabout.files@gmail.com', '', 'xmpphp', 'gmail.com', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);
	try {
		$conn->useEncryption(true);
		$conn->connect();
		$conn->processUntil('session_start');
		$conn->presence("Sending Log Files","available","gmail.com");
		$conn->message($to, $message);
		$conn->disconnect();
	} catch(XMPPHP_Exception $e) {
		die($e->getMessage());
	}
}
function talkLog($filename){
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
$html = addslashes($contents);
talk('insidenothing@gmail.com',$html);
fclose($handle);
}

function pushPage($log){
	$file = $log.'.ps';
	$remote_file = $log.'.ps';
	$conn_id = ftp_connect('75.94.82.44');
	$login_result = ftp_login($conn_id, 'alpha', 'beta');
	if (ftp_chdir($conn_id, "PORT1")) {
	} else { 
		mail('insidenothing@gmail.com','Daily Log Print Error','Couldn\'t change directory');
		error_log(date('r')." WARNING: Couldn't change ftp directory for $log. \n", 3, '/logs/printer.log');
	}
	if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY)) {
		$last_line = system('rm -f '.$log.'.ps', $retval);
		//error_log(date('r')." NOTICE: $log printed successfully. \n", 3, '/logs/printer.log');
	} else {
		mail('insidenothing@gmail.com','AI Break: FTP PUT','There was a problem while uploading '.$file);
		error_log(date('r')." ERROR: There was a problem while uploading $log. \n", 3, '/logs/printer.log');
	}
	ftp_close($conn_id);
}
function buildPage($log){
	//talkLog('/logs/'.$log.'.log');
	passthru('/usr/local/bin/html2ps /logs/'.$log.'.log > '.$log.'.ps');
	pushPage($log);
}
function buildPage2($log){
	//talkLog('/var/log/httpd/'.$log);
	passthru('/usr/local/bin/html2ps /var/log/httpd/'.$log.' > '.$log.'.ps');
	pushPage($log);
}
//buildPage('user');
//buildPage('mobile');
//buildPage('contractor');
//buildPage('client');
//if (file_exists('/logs/debug.log')) { buildPage('debug'); }
if (file_exists('/logs/debug.log')) { talkLog('/logs/debug.log'); }

if (file_exists('/logs/source.log')) { buildPage('source'); }
if (file_exists('/logs/source.log')) { talkLog('/logs/source.log'); }
//buildPage('download');
//buildPage('response');
//buildPage('cache');
//buildPage('reboot');
//if (file_exists('/logs/upload.log')) { buildPage('upload'); }
//if (file_exists('/logs/cancelled.log')) { buildPage('cancelled'); }
//if (file_exists('/logs/courier.log')) { buildPage('courier'); }
//if (file_exists('/logs/fail.log')) { buildPage('fail'); }
//buildPage('mfg');
//buildPage('reboot');
//buildPage('slow.response');
//if (file_exists('/logs/ssl_access.log')) { buildPage('ssl_access'); }
//if (file_exists('/logs/ssl_error.log')) { buildPage('ssl_error'); }
//if (file_exists('/logs/sso.log')) { buildPage('sso'); }
//buildPage('timeline');
//buildPage('twitter');
//buildPage('watchdog');
if (file_exists('/logs/webservice.log')) { buildPage('webservice'); }
//if (file_exists('/var/log/httpd/error_log')) { talkLog('/var/log/httpd/error_log'); }
//buildPage('printer');

?>
