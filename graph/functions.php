<?
function monthConvert($month){
	if ($month == '01'){ return 'Jan'; }
	if ($month == '02'){ return 'Feb'; }
	if ($month == '03'){ return 'Mar'; }
	if ($month == '04'){ return 'Apr'; }
	if ($month == '05'){ return 'May'; }
	if ($month == '06'){ return 'Jun'; }
	if ($month == '07'){ return 'Jul'; }
	if ($month == '08'){ return 'Aug'; }
	if ($month == '09'){ return 'Sep'; }
	if ($month == '10'){ return 'Oct'; }
	if ($month == '11'){ return 'Nov'; }
	if ($month == '12'){ return 'Dec'; }
}
function leading_zeros($value, $places){
    if(is_numeric($value)){
        for($x = 1; $x <= $places; $x++){
            $ceiling = pow(10, $x);
            if($value < $ceiling){
                $zeros = $places - $x;
                for($y = 1; $y <= $zeros; $y++){
                    $leading .= "0";
                }
            $x = $places + 1;
            }
        }
        $output = $leading . $value;
    }
    else{
        $output = $value;
    }
    return $output;
}
function getPage($url, $referer, $timeout, $header){
	if(!isset($timeout))
        $timeout=30;
    $curl = curl_init();
    if(strstr($referer,"://")){
        curl_setopt ($curl, CURLOPT_REFERER, $referer);
    }
    curl_setopt ($curl, CURLOPT_URL, $url);
    curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($curl, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
    curl_setopt ($curl, CURLOPT_HEADER, (int)$header);
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
    $html = curl_exec ($curl);
    curl_close ($curl);
    return $html;
}
?>