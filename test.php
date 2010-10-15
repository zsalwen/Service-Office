<pre>
<?php
function talk($message){
	include_once '/thirdParty/xmpphp/XMPPHP/XMPP.php';
	$conn = new XMPPHP_XMPP('talk.google.com', 5222, 'talkabout.files@gmail.com', '', 'xmpphp', 'gmail.com', $printlog=true, $loglevel=XMPPHP_Log::LEVEL_INFO);
	try {
		$conn->useEncryption(true);
		$conn->connect();
		$conn->processUntil('session_start');
		$conn->message('insidenothing@gmail.com', $message);//patrick
		$conn->message('zachsalwen@gmail.com', $message);//zach
		$conn->message('jrreul55@gmail.com', $message);//rudy
		$conn->message('smskurski@gmail.com', $message);//steve
		$conn->message('alexamayhew@gmail.com', $message);//alex
		$conn->message('danny.mdwestserve@gmail.com', $message);//danny
		$conn->disconnect();
	} catch(XMPPHP_Exception $e) {
		die($e->getMessage());
	}
}
?>



<?
talk('Broadcast Test, Once all users confirm system will go online.');
?>
</pre>