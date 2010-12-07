<?
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
buildPage('user');
buildPage('mobile');
buildPage('contractor');
buildPage('client');
buildPage('debug'); 
buildPage('source'); 
buildPage('download');
buildPage('response');
buildPage('cache');
buildPage('reboot');
buildPage('upload'); 
buildPage('cancelled');
buildPage('courier');
buildPage('fail'); 
buildPage('mfg'); 
buildPage('reboot'); 
buildPage('slow.response'); 
buildPage('ssl_access'); 
buildPage('ssl_error'); 
buildPage('sso'); 
buildPage('timeline'); 
buildPage('twitter'); 
buildPage('watchdog'); 
buildPage('webservice'); 
buildPage2('/var/log/httpd/error_log'); 
buildPage('printer'); 
?>
