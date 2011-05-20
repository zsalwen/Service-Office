<?
class postage{
	var $pdf;
	var $lender;
	var $greenEnvelopes;
	var $weight;
	function weight(){
		 $file = str_replace('http://mdwestserve.com//PS_PACKETS/','/data/service/orders/',$this->pdf);
		 $file = str_replace('http://mdwestserve.com/PS_PACKETS/','/data/service/orders/',$file);
		 $file = str_replace('http://www.mdwestserve.com//PS_PACKETS/','/data/service/orders/',$file);
		 $file = str_replace('http://www.mdwestserve.com/PS_PACKETS/','/data/service/orders/',$file);
		 //where $file is the full path to your PDF document.
		 if(file_exists($file)) {
						 //open the file for reading
			 if($handle = @fopen($file, "rb")) {
				 $count = 0;
				 $i=0;
				 while (!feof($handle)) {
					 if($i > 0) {
						 $contents .= fread($handle,8152);
						 }
						 else {
							   $contents = fread($handle, 1000);
							 //In some pdf files, there is an N tag containing the number of
							 //of pages. This doesn't seem to be a result of the PDF version.
							 //Saves reading the whole file.
							 /* screwed up whites files, no time saved, only headake
							 if(preg_match("/\/N\s+([0-9]+)/", $contents, $found)) {
					  	 error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [wtf] [Count:".$count."] [Weight:".$weight."] [Weight:".$display."] \n", 3, '/logs/debug.log');
							 
								 return $found[1];
							 }
							 */
						 }
						 $i++;
					 }
					 fclose($handle);

					 //get all the trees with 'pages' and 'count'. the biggest number
					 //is the total number of pages, if we couldn't find the /N switch above.               
					 
//if(preg_match_all("/\/Type\s*\/Pages\s*.*\s*\/Count\s+([0-9]+)/", $contents, $capture, PREG_SET_ORDER)) {
				 
if(preg_match_all("/\/Count\s+([0-9]+)/", $contents, $capture, PREG_SET_ORDER)) {



						 foreach($capture as $c) {
							 if((ceil($c[1]/2)) > $count)

								 $count = ceil($c[1]/2);
						 }
						 $weight = ($count*.16)+.57+.12+.01;
						 if ($this->greenEnvelopes){
							//add weight for two envelopes
							$envelopeWeight=.18+.18;
							$weight=$weight+$envelopeWeight;
						 }elseif ($this->lender){
							//add weight for one envelope if preliminary
							$lenderWeight=.18;
							$weight=$weight+$lenderWeight;
						 }
						 $display = number_format($weight,2);
					  	 error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [a] [Count:".$count."] [C[1] $c[1]] [Weight:".$weight."] [Weight:".$display."] \n", 3, '/logs/debug.log');
						 return $display;           
					 }
				 }
			 }
			 return 0;
	}
	
	function cost(){
		if ($this->weight >  0){ $result = .88;}
		if ($this->weight >  1){ $result = 1.08;}
		if ($this->weight >  2){ $result = 1.28;}
		if ($this->weight >  3){ $result = 1.48;}
		if ($this->weight >  4){ $result = 1.68;}
		if ($this->weight >  5){ $result = 1.88;}
		if ($this->weight >  6){ $result = 2.08;}
		if ($this->weight >  7){ $result = 2.28;}
		if ($this->weight >  8){ $result = 2.48;}
		if ($this->weight >  9){ $result = 2.68;}
		if ($this->weight > 10){ $result = 2.88;}
		if ($this->weight > 11){ $result = 3.08;}
		if ($this->weight > 12){ $result = 3.28;}
		if ($this->weight > 13){ $result = 4.95;} // priority rate
		//error_log("[".date('h:iA n/j/y')."] [".$_COOKIE[psdata][name]."] [b] [".$pages."] [".$this->weight."] \n", 3, '/logs/debug.log');

	return number_format($result,2).' ('.$this->weight.')';
	}
	
	
}
?>