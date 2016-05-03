/**
 * Show notifications in webpage title
 * @param int num
 */
function setNotificationNumber(num)
{
	if (isNaN(num)) {
		return;
	}
	
	var regx = /^\@\(\d*\+?\)-> /;
	
	if (num < 1) {
		document.title = document.title.replace(regx, '');
		return;
	}
	
	if (regx.exec(document.title)) {
		document.title = document.title.replace(regx, "@(" + num + ")-> ");
	} else {
		document.title = "@(" + num + ")-> " + document.title;
	}
}