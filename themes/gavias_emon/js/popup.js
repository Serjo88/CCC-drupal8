jQuery(document).ready(function ()
{
	// Check for cookie
    /*
	if (!jQuery.cookie('PopUp')){
		// Create cookie
		jQuery.cookie('PopUp', '1', { expires: 7, path: '/' });
  		//Fade in delay for the popup (control timing here)
		jQuery("#delayedPopup").delay(5000).slideDown(400);
	} */
	
    jQuery("#delayedPopup").delay(5000).slideDown(400);
    
	//Hide dialogue when the user clicks the close button
	jQuery("#btnClose").click(function (e)
	{
		HideDialog();
		e.preventDefault();
	});
});
//Controls how the modal popup is closed with the close button
function HideDialog()
{
	jQuery("#delayedPopup").slideUp(300);
}
