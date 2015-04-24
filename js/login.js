function checkButton(value){
	var buttonText = "Log In";
	if(value.length > 0){
		buttonText = "Register";
	}
	document.getElementById("button").value = buttonText;
}