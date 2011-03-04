<? 

<?
if ($_FILES['affidavit']){
	if ($_FILES['affidavit']['size'] < 10145728){
		if ($_FILES['affidavit']['size'] == 0){
			$ps = id2name($_COOKIE[psdata][user_id]);
			$error = "<div>Your file size registered as zero (due to oversized files).</div>";
		}else{
			// ok first we need to go get the files
			$path = "/data/service/scans/'.date('Y').'/'.date('F').'/'.date('j').'/";
			
 


if (!file_exists('/data/service/scans/'.date('Y'))){
mkdir ('/data/service/scans/'.date('Y'),0777);
}
if (!file_exists('/data/service/scans/'.date('Y').'/'.date('F'))){
mkdir ('/data/service/scans/'.date('Y').'/'.date('F'),0777);
}
if (!file_exists('/data/service/scans/'.date('Y').'/'.date('F').'/'.date('j'))){
mkdir ('/data/service/scans/'.date('Y').'/'.date('F').'/'.date('j'),0777);
}







			$file_path = $path;
			if (!file_exists($file_path)){
				mkdir ($file_path,0777);
			}
			$ext = explode('.', $_FILES['affidavit']['name']);
			$target_path = $file_path."/".$packet.".".time().".pdf";  
			if(move_uploaded_file($_FILES['affidavit']['tmp_name'], $target_path)) {
			}
			$link1 = "http://mdwestserve.com/ps/affidavits/".date('Y')."/".date('F')."/".date('j')."/".$packet.".".time().".pdf"; 
/*
			if ($_POST[method] != 'Freeform'){
				$method=$_POST[method];
			}else{
				$method=$_POST[freeform];
			}
			$query = "INSERT into ps_affidavits (packetID, defendantID, affidavit, userID, method, uploadDate) VALUES ('$packet','$tab','$link1','$user','$method', NOW())";
			mysql_select_db('core');

			@mysql_query($query);
				timeline($packet,$_COOKIE[psdata][name]." Scanned $method");
				psActivity('docUpload');
*/
		}
		}else{
			echo "<br>".$link1;
			$error = "<div>Your file size was too large.</div>";
			$message = "<table>";
			foreach($_FILES as $key => $value){
			$message .="<tr><td>$key</td><td>$value</td></tr>";
			}
			foreach($_SERVER as $key => $value){
			$message .="<tr><td>$key</td><td>$value</td></tr>";
			}
			$message .="</table>";
			$error .= $message;
	}
	//header('Location: upload.php?packet='.$packet.');
}







 if (isset($error)){ echo $error;} ?>
	
<script>function MultiSelector( list_target, max ){this.list_target = list_target;this.count = 0;this.id = 0;if( max ){this.max = max;} else {this.max = -1;};this.addElement = function( element ){if( element.tagName == 'INPUT' && element.type == 'file' ){element.name = 'file_' + this.id++;element.multi_selector = this;element.onchange = function(){var new_element = document.createElement( 'input' );new_element.type = 'file';this.parentNode.insertBefore( new_element, this );this.multi_selector.addElement( new_element );this.multi_selector.addListRow( this );this.style.position = 'absolute';this.style.left = '-1000px';};if( this.max != -1 && this.count >= this.max ){element.disabled = true;};this.count++;this.current_element = element;} else {alert( 'Error: not a file input element' );};};this.addListRow = function( element ){var new_row = document.createElement( 'div' );var new_row_button = document.createElement( 'input' );new_row_button.type = 'button';new_row_button.value = 'Delete';new_row.element = element;new_row_button.onclick= function(){this.parentNode.element.parentNode.removeChild( this.parentNode.element );this.parentNode.parentNode.removeChild( this.parentNode );this.parentNode.element.multi_selector.count--;this.parentNode.element.multi_selector.current_element.disabled = false;return false;};new_row.innerHTML = element.value;new_row.appendChild( new_row_button );this.list_target.appendChild( new_row );};};<script>    



<form enctype="multipart/form-data" action="your_script_here.script" method = "post">
	<!-- The file element -- NOTE: it has an ID -->
	<input id="my_file_element" type="file" name="file_1" >
	<input type="submit">
</form>
Files:
<!-- This is where the output will appear -->
<div id="files_list"></div>
<script>
	<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->
	var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), 3 );
	<!-- Pass in the file element -->
	multi_selector.addElement( document.getElementById( 'my_file_element' ) );
</script>
</body>
</html>