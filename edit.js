function automation() {
  window.opener.location.href = window.opener.location.href;
  //window.open('write_update.php','update','width=1,height=1,toolbar=no,location=no')
  if (window.opener.progressWindow)
		
 {
    window.opener.progressWindow.close()
  }
  window.close();
}
function prompter(packetID,newDate,oldDate){
	var reply = prompt("Please enter your reason for updating the Est. Close Date", "")
	if (reply == null){
		alert("That is not a valid reason")
		window.location="http://staff.mdwestserve.com/otd/order.php?packet="+packetID;
	}
	else{
		window.location="http://staff.mdwestserve.com/otd/tlEntry.php?packet="+packetID+"&entry="+reply+"&newDate="+newDate+"&oldDate="+oldDate,"OTD Timeline Entry";
	}
}
function ChgText(myResponse,myInput)
{
    var MyElement = document.getElementById(myInput);
    MyElement.value = myResponse;
    return true;
}
function setAddress1(street,city,state,zip)
{
ChgText(street,'address');
ChgText(city,'city');
ChgText(state,'state');
ChgText(zip,'zip');
}
function setAddress2(street,city,state,zip)
{
ChgText(street,'addressa');
ChgText(city,'citya');
ChgText(state,'statea');
ChgText(zip,'zipa');
}
function setAddress3(street,city,state,zip)
{
ChgText(street,'addressb');
ChgText(city,'cityb');
ChgText(state,'stateb');
ChgText(zip,'zipb');
}
function setAddress4(street,city,state,zip)
{
ChgText(street,'addressc');
ChgText(city,'cityc');
ChgText(state,'statec');
ChgText(zip,'zipc');
}
function setAddress5(street,city,state,zip)
{
ChgText(street,'addressd');
ChgText(city,'cityd');
ChgText(state,'stated');
ChgText(zip,'zipd');
}
function setAddress6(street,city,state,zip)
{
ChgText(street,'addresse');
ChgText(city,'citye');
ChgText(state,'statee');
ChgText(zip,'zipe');
}
function confirmation(email) {
	if (email != ''){
		var answer = confirm("Are you sure that you want to cancel service per "+email+"? Emails will be sent to the client and all servers, should service be active.  Make sure that you have entered a valid client email address for reference.");
		if (answer){
			window.location = "http://staff.mdwestserve.com/otd/order.php?packet=<?=$d[packet_id]?>&cancelRef="+email+"&cancel=1";
		}
		else{
			alert("::ABORTED::");
			self.close();
		}
	}
	else{
		alert(email+"::NEED VALID EMAIL ADDRESS.  ABORTED::");
		self.close();
	}
}
function hideshow(which){
if (!document.getElementById)
return
if (which.style.display=="block")
which.style.display="none"
else
which.style.display="block"
}

function ClipBoard()
{
holdtext.innerText = copytext.innerText;
Copied = holdtext.createTextRange();
Copied.execCommand("Copy");
}
