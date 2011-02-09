<?
// this is the dual layer merge system
mysql_connect();
mysql_select_db('service');

// Pull the template


// merge the data
$contents = str_replace('[DEPOSIT]', $bid_deposit, $contents);
$contents = str_replace('[FS/GR]', $gr_desc, $contents);
$contents = str_replace('[GRPARA]', $gr_para, $contents);
$contents = str_replace('[GRPARAW]', $gr_paraw, $contents);

// Put the final affidavit



?>