function hideshow(which){
	if (!document.getElementById)
		return
	if (which.style.display=="block")
		which.style.display="none"
	else
		which.style.display="block"
}

function hide(which){
	if (!document.getElementById)
		return
		which.style.display="none"
}

function show(which){
	if (!document.getElementById)
		return
		which.style.display="block"
}

function workspace(workspaceWidth,workspaceHeight){
	var workspaceWidth;
	var workspaceHeight;
	self.resizeTo(workspaceWidth,workspaceHeight); 
	var lr = (screen.width/2)-(workspaceWidth/2);
	var tb = (screen.height/2-10)-(workspaceHeight/2+10);
	self.moveTo(lr,tb);
}

function dateString(){
	var currentTime = new Date()
	var month = currentTime.getMonth() + 1
	var day = currentTime.getDate()
	var year = currentTime.getFullYear()
	var hours = currentTime.getHours()
	var minutes = currentTime.getMinutes()
	if (minutes < 10)
	minutes = "0" + minutes
	document.write("0" + month + "/" + day + "/" + year + " 0" + hours + ":" + minutes + " ")
	if(hours > 11){
	document.write("PM")
	} else {
	document.write("AM")
	}
}