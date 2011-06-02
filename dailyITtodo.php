<?
function processOpen($repo){
$loginURL = "http://github.com/api/v2/json/issues/list/MDWestServe-Inc/$repo/open";
$curl = curl_init();
curl_setopt ($curl, CURLOPT_URL, $loginURL);
curl_setopt ($curl, CURLOPT_TIMEOUT, '5');
curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
curl_setopt( $curl, CURLOPT_COOKIEJAR, $cookie );
    curl_setopt( $curl, CURLOPT_ENCODING, "" );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl, CURLOPT_AUTOREFERER, true );
    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
$buffer = curl_exec ($curl);
//if all goes well
echo "<h1>$repo</h1>";
$buffer = json_decode($buffer, true);

foreach ($buffer as $key => $value){
echo "<li>$key :: $value</li>";
}


}

processOpen('Service-Office');
processOpen('Service-Interface');
processOpen('Service-Web-Service');
processOpen('Service-Client-Access');
processOpen('Mobile-Service');
processOpen('Service-Accounting');
processOpen('Verify');

?>