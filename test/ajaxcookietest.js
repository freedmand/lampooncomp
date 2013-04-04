function doStuff()
{
	$.cookie('testcook', 'abce', { path: '/' });
	$.post('test.php', {'email': 'email', 'password': 'password'}).done(function (data) { alert($.cookie('phpcook')); } );
}