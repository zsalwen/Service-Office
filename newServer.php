<?
$step1 = @mysql_connect ();
$step2 = mysql_select_db ('core');
include '/sandbox/portal/common/functions.php';
if (isset($_POST['name']) && ($_POST['name'] != '') && isset($_POST['email']) && ($_POST['email'] != '') && isset($_POST['password']) && ($_POST['password'] != '')){
// check and prevent duplicate email addresses
$q2="SELECT email FROM ps_users where email = '".$_POST['email']."'";
$r2=@mysql_query($q2) or die("Query: $q2<br>".mysql_error());
$d2=mysql_fetch_array($r2, MYSQL_ASSOC);
	if ($d2['email']){
	echo "<div>This email address has already been registered in the system.</div>";
	//echo "<script>window.location.href = '../ps_new_user.php?orange=no';< /script>";
		//header('Location: ps_new_user.php?orange=no');
	} else {

// -------------------------------------------
$link = md5(rand());
$add = $_SERVER['REMOTE_ADDR'];
$name = $_POST['name']." ".$_POST['name2'];
//$makeLnL = $_POST['address'].", ".$_POST['city'].", ".$_POST['state']." ".$_POST['zip'];
//$lnl = getLnL($makeLnL);
$q1 = "INSERT INTO ps_users ( 
							company,
							password,
							name,
							email,
							address,
							city,
							state,
							zip,
							phone,
							email_status,
							signup,
							ip_address,
							verify,
							contract
							) VALUES (
							'".$_POST['company']."',
							'".$_POST['password']."',
							'$name',
							'".$_POST['email']."',
							'".addslashes($_POST['address'])."',
							'".$_POST['city']."',
							'".$_POST['state']."',
							'".$_POST['zip']."',
							'".addslashes($_POST['phone'])."',
							'VERIFIED',
							NOW(),
							'$add',
							'YES',
							'YES'
							)";
@mysql_query($q1) or die("Query: $q1<br>".mysql_error());
		
		$headers  = "MIME-Version: 1.0 \n";
		$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
		$headers .= "From: Service <service@hwestauctions.com> \n";
		$headers .= "Cc: System Operations <sysop@hwestauctions.com> \n";

$to  = $_POST['email'];
$subject = "New Process Server: ".$_POST['name'];
$msg = "Welcome ".$_POST['name'].", <br>
We have received your registration! At this point you should have placed your bids per file in your service area. Our service manager will be contacting you at ".$_POST['phone']." to review the details of your application. From time to time there will be broadcast messages sent to our members as a whole or by county, so please keep your email address up to date. To ensure delivery add service@hwestauctions.com to your white-list or address book.<br>
Please click here to validate your email address http://mdwestserve.com/box/?green=$link
<br>
To review your information:<br>
E-Mail : ".$_POST['email']." <br>
Address : ".$_POST['address']." <br>
City : ".$_POST['city']." <br>
State : ".$_POST['state']." <br>
Zip : ".$_POST['zip']." <br>
Phone : ".$_POST['phone']." <br>";
//mail($to,$subject,$msg,$headers);
//logAction($_COOKIE[psdata][user_id], $_SERVER['PHP_SELF'], 'Account Created');
//echo "<script>window.location.href = 'login.php?message=Account Created, Please Log In.';< /script>";
echo "<div>$msg</div>";
	}
}else{
?>
<form method="post">
<table border="1" width="100%" height="98%" style="border-collapse:collapse; background-color:#CCFFFF; font-variant:small-caps; color:#0000CC; padding:0px;" cellpadding="4">
                <? if (isset($_GET['orange'])){ ?>
	<tr>
		<td bgcolor="#FF0000" colspan="2" align="center"><strong>An existing account has already been registered with that email address.<br>Please contact technical support.</strong></td>
	</tr>
    <? } ?>
                
				<tr>
                	<td>First Name</td>
                    <td><input name="name" size="50" /></td>
                </tr>
				<tr>
                	<td>Last Name</td>
                    <td><input name="name2" size="50" /></td>
                </tr>
				<tr>
                	<td>Company</td>
                    <td><input name="company" size="50" /></td>
                </tr>
				<tr>
                	<td>Phone</td>
                    <td><input name="phone" size="50" /></td>
                </tr>
                <tr>
                	<td>Address</td>
                	<td><input name="address" size="50" /></td>
				</tr>
                <tr>
                	<td>City</td>
                	<td><input name="city" size="20" /></td>
				</tr>
                <tr>
                	<td>State</td>
                	<td><input name="state" size="2" maxlength="2"></td>
				</tr>
                <tr>
                	<td>ZIP Code</td>
                	<td><input name="zip" size="10" /></td>
				</tr>
                <tr>
                	<td>Email Address</td>
                    <td><input name="email" size="50" /></td>
                </tr>
                <tr>
                	<td>Password</td>
                    <td><input type="password" name="password" size="50" /></td>
                </tr>
      
        <tr>
        
                	<td colspan="2" align="right" style="font-size:24px"  bgcolor="#FFFF99"><input  style="font-size:24px" type="submit" name="submit" value="Register" /></td>
                </tr>
</form>
</table>
<? } ?>