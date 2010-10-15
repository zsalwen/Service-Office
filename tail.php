<html>
<head>
<script>
function getLog(timer) {
var url = "http://<?=$_GET['source'];?>";
request.open("GET", url, true);
request.onreadystatechange = updatePage;
request.send(null);
startTail(timer);
}

function startTail(timer) {
if (timer == "stop") {
stopTail();
} else {
t= setTimeout("getLog()",<?=$_GET['seconds'];?>000);
}
}

function stopTail() {
clearTimeout(t);
var pause = "The log viewer has been paused. To begin viewing logs again, click the Start Viewer button.";
logDiv = document.getElementById("log");
var newNode=document.createTextNode(pause);
logDiv.replaceChild(newNode,logDiv.childNodes[0]);
}

function updatePage() {
if (request.readyState == 4) {
if (request.status == 200) {
var currentLogValue = request.responseText.split("\n");
eval(currentLogValue);
logDiv = document.getElementById("log");
var logLine = ' ';
for (i=0; i < currentLogValue.length - 1; i++) {
logLine += currentLogValue[i] + '<br/>';
}
logDiv.innerHTML=logLine;
} else
alert("Error! Request status is " + request.status);
}
}

/* ajax.js */
var request = null;
try {
request = new XMLHttpRequest();
} catch (trymicrosoft) {
try {
request = new ActiveXObject("Msxml2.XMLHTTP");
} catch (othermicrosoft) {
try {
request = new ActiveXObject("Microsoft.XMLHTTP");
} catch (failed) {
request = null;
}
}
}
if (request == null)
alert("Error creating request object!");

var request = createRequest();

</script>



</head>
<body onLoad="getLog('start');">
<div id="log" style="border:solid 1px #dddddd; font-size:12px; padding:0px; width:100%; margin-top:0px; margin-bottom:0px; text-align:left;"></div>
<small>loading http://<?=$_GET['source'];?> every <?=$_GET['seconds'];?> seconds.</small>
</body>
</html>
