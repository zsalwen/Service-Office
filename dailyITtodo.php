<?


$loginURL = "http://github.com/api/v2/json/issues/search/defunkt/github-issues/open/";

$curl = curl_init();
// Set options
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



echo $buffer;

?>