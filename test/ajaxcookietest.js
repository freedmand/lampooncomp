function doStuff()
{
	$.post('test.php', {'email': 'email', 'password': 'password'}).done(function (data) {
		if (data == 'login again')
			window.location = "../login.html";
		alert(data);
	});
}