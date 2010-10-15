<?php
require_once('lib/nusoap.php');
$client = new nusoap_client('https://mdwestserve.com/transfer.test.php');
$result = $client->call('DefendantUpload', 
	array('container' => 
		array(	'Login' => 'burson',
				'Password' => 'secure',
				'Defendants' => array( 	'0' => array(  	'FileNumber' => '10-123456P',
														'ClientIdentifier' => 'SB', 
														'DefendantNumber' => '1',
														'DefendantFullName' => 'John Doe',
														'DefendantSSN' => '111-11-1111',
														'DefendantAddress1' => '555 shared street',
														'DefendantAddress2' => 'unit 4',
														'DefendantCity' => 'Baltimore',
														'DefendantState' => 'Maryland',
														'DefendantStateID' => 'MD',
														'DefendantZip' => '21286',
														'DefendantRelationship' => 'Husband',
														'Other' => 'Living in DOT',
														'Status' => 'Active',
														'StatusDate' => '10/2/2009',
														'Addresses' => array ( 	'0' => '123 here street',
																				'1' => '345 there street'
																				 )
													),
										'1' => array( 	'FileNumber' => '10-123456P',
														'ClientIdentifier' => 'SB', 
														'DefendantNumber' => '2',
														'DefendantFullName' => 'Jane Doe',
														'DefendantSSN' => '222-22-2222',
														'DefendantAddress1' => '555 shared street',
														'DefendantAddress2' => 'unit 4',
														'DefendantCity' => 'Baltimore',
														'DefendantState' => 'Maryland',
														'DefendantStateID' => 'MD',
														'DefendantZip' => '21286',
														'DefendantRelationship' => 'Wife',
														'Other' => 'Divorced, Personally Serve',
														'Status' => 'Active',
														'StatusDate' => '10/2/2009',
														'Addresses' => array ( 	'0' => '187 somewhere lane',
																				'1' => '659 elsewhere lane',
																				'2' => '5589 random road'
																				 )
													)
									)
			  )
		  ),'https://mdwestserve.com'
						);
echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';

?>
