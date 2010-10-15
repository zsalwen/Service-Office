<html>

<head>

<title></title>

<script type="text/javascript" language="javascript"><!--

function ChgText(myResponse,myInput)
{
    var MyElement = document.getElementById(myInput);
    MyElement.value = myResponse;
    return true;
}

//--></script>


</head>

<body>

<br><br>
<center>
<img src="my_image.gif" alt="Click Me!" onclick="ChgText('amazing','MyTextBox')" />
<img src="my_image.gif" alt="Click Me Too!" onclick="ChgText('unthinkable','MyTextBox')" />

<br><br><br>

<input type="text" size="35" id="MyTextBox" value="" />

</center>

</body>
</html>