<?php
$connection = ssh2_connect('hwa1.hwestauctions.com', 22);
ssh2_auth_password($connection, '', '');

$stream = ssh2_shell($connection, 'vt102', null, 80, 24, SSH2_TERM_UNIT_CHARS);
?>
