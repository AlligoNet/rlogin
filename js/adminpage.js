var users = [];

var filters = {
	username: "",
	useremail: "",
	groupname: ""
}

function reFilter(){
	var filterForm = document.getElementById("filters").elements;
	filters.username = filterForm.namedItem("username").value;
	filters.useremail = filterForm.namedItem("useremail").value;
	filters.groupname = filterForm.namedItem("groupname").value;
	finishRefresh();
}

function filtered(user){
	if(user.username.toLowerCase().search(filters.username.toLowerCase()) == -1){
		return true;
	}
	if(user.useremail.toLowerCase().search(filters.useremail.toLowerCase()) == -1){
		return true;
	}
	if(user.groupname && user.groupname.toLowerCase().search(filters.groupname.toLowerCase()) == -1){
		return true;
	}
	return false;
}

function assignGroup(group, id){
	window.location.assign("action.php?id=" + username + "&group=" + group);
}

function finishRefresh(){
	var contentsString = "<tr><th>user</th><th>email</th><th>status</th><th>action</th></tr>";
	for(user in users){
		if(!filtered(users[user])){
			console.log(users[user]);
			contentsString = contentsString + "<tr><td>" + users[user].username + "</td><td>" + users[user].useremail + "</td><td>" + users[user].groupname + "</td><td>" + "<select onchange=\"takeAction(this.value, &quot;" + users[user].username + "&quot;)\" type=\"text\"><option value=\"none\">none</option><option value=\"ban\">ban</option><option value=\"setadmin\">set admin</option><option value=\"delete\">delete</option><option value=\"reset\">reset</option></select>" + "</td></tr>";
		}
	}
	document.getElementById("users").innerHTML = contentsString;
}


function continueRefresh(response){
	var jsonResponse = JSON.parse(response);
	users = jsonResponse;
	finishRefresh();
}

function requestUsers(){
	var request = new XMLHttpRequest();
	var url = "ajax/users.json.php";
	
	request.onreadystatechange=function() {
		if (request.readyState == 4 && request.status == 200){
			continueRefresh(request.responseText);
		}
	}
	request.open("GET" , url, true);
	request.send();
}

window.onload = function() {
	requestUsers();
}