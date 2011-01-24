/**
 * this is a sample javascript that demonstrates how the Rad UPload Plus
 * applet can interact with a javascript.
 *
 * If you set the jsnotify configuration property, the applet will call 
 * the upload completed method when the file upload has been completed.
 *
 * You can call the getUploadStatus() method to determine whether the
 * upload was successfull. Possible values are 1, for success, 0 when the 
 * user has cancelled the upload and -1 when the upload failed (error).
 * 
 */

/* a usefull variable */
var upload=0;


/**
 * the response returned by the server will be passed as a parameter (s) to this
 * function. However in the case of Netscape the parameter will be empty. When using
 * netscape call the getResponse()method of the applet to access this information.
 *
 */
function uploadCompleted()
{
	
	upload = document.rup.getUploadStatus();
	if(upload==1)
	{
		if(confirm("the upload was successfull do you wish to see the server response?"))
		{
			alert(document.rup.getResponse());
		}
	}
	else
	{
		confirm("The upload seems to have failed");
	}
	
	return true;
}

function r_getFormData()
{
    if(document.forms[0] == undefined)
    {
        return;
    }
    for(i = 0 ; i < document.forms[0].elements.length ; i++)
    {
        document.rup.jsAddTextField(document.forms[0].elements[i].name,
                                    document.forms[0].elements[i].value);
    }
}