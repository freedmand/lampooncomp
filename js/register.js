var ANIMATION_DURATION = 400;
var EMAIL_REGEX = /^\S+@\S+$/;

function showError(msg)
{
	$('#msg').slideUp(ANIMATION_DURATION,
		function () {
			$('#msg').html(msg);
			$('#msg').slideDown(ANIMATION_DURATION);
		}
	);
}

function getURLParameter(name) {
	return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]);
}

function login()
{
	$("#reset_msg").slideUp(ANIMATION_DURATION);
	var email = $('[name="femail"]').val();
	if (email.length == 0 || !email.match(EMAIL_REGEX))
	{
		showError('Please submit a valid email address.');
		return;
	}
	var password = $('[name="fpassword"]').val();
	if (password.length < 5)
	{
		showError('Passwords should be at least 5 characters.');
		return;
	}
	$.post('login.php', {'email': email, 'password': password}).done(loginValidate);
}

function loginValidate(data)
{
	if (data == 'error')
	{
		showError('An error occurred. Please try again.');
		return;
	}
	else if (data == 'false')
	{
		showError('Invalid email or password. <a href="#" onclick="resetPassword(); return false;">Reset password?</a>');
		return;
	}
	else if (data == 'true')
	{
		var redirect = getURLParameter("redirect");
		if (redirect != "null")
			window.location = redirect;
		else
			window.location = "welcome.php";
	}
}

function resetPassword()
{
	var email = $('[name="femail"]').val();
	if (email.length == 0 || !email.match(EMAIL_REGEX))
	{
		showError('Please submit a valid email address to <a href="#" onclick="resetPassword(); return false;">reset password</a>.');
		return;
	}
	
	$.post('validate.php', {
		'email': $('[name="femail"]').val(),
		'type': 'reset'
	}).done(showReset);
}

function showReset(data)
{
	if (data == 'false')
	{
		showError('No account exists with that email.<br>Would you like to <a href="register.html">register?</a>');
		return;
	}
	else if (data == 'error')
	{
		showError('An error occurred. <a href="#" onclick="resetPassword(); return false;">Please try again.</a>');
		return;
	}
	
	$('#msg').slideUp(ANIMATION_DURATION);
	$("#reset_msg").slideDown(ANIMATION_DURATION);
}

function submitCheck()
{
	var name = $('[name="fname"]').val();
	if (name.length == 0)
	{
		showError('Please submit a valid name.');
		return;
	}
	var email = $('[name="femail"]').val();
	if (email.length == 0 || !email.match(EMAIL_REGEX))
	{
		showError('Please submit a valid email address.');
		return;
	}
	$.post('datacheck.php', {'email': email}).done(emailValidation);
}

function emailValidation(data)
{
	if (data == 'true')
	{
		showError('We already have an account with that email.<br>Would you like to <a href="login.html">login</a> instead?');
		return;
	}
	else if (data == 'error')
	{
		showError('An error occurred. Please try again.');
		return;
	}
	
	var room = $('[name="froom"]').val();
	if (room.length == 0)
	{
		showError('Please submit a valid room number.');
		return;
	}
	
	var year = parseInt($('[name="fyear"]').val());
	if (isNaN(year))
	{
		showError('Please submit a numeric class year.');
		return;
	}
	
	$.post('validate.php', {
		'name': $('[name="fname"]').val(),
		'email': $('[name="femail"]').val(),
		'room': room,
		'year': year,
		'type': 'register'
	}).done(showValidation);
}

function showValidation(data)
{
	if (data == 'error')
	{
		showError('An error occurred. Please try again.');
		return;
	}
	
	// $.removeCookie('reg_email', { path: '/' });
	// $.cookie('reg_email', $('[name="femail"]').val(), { path: '/' });
	
	window.location = "validate.html";
}

function resendEmail()
{
	// var email = $.cookie('reg_email');
	if (email === undefined)
	{
		showError('Please enable cookies and try again.');
		return;
	}
	
	$.post('validate.php', {
		// 'email': email,
		'type': 'resend'
	}).done(function () { location.reload(); });
}

function validate()
{
	var validation = $('[name="fvalidation"]').val().toLowerCase();
	if (validation.length < 32 || !(/^[0-9a-f]*$/i.test(validation)))
	{
		showError('The format you have entered is not correct.<br>Try again, or follow the link from your email.');
		return;
	}
	
	// var email = $.cookie('reg_email');
	// if (email === undefined)
	// {
	// 	showError('Please enable cookies and try again.');
	// 	return;
	// }
	
	$.post('validate.php', {
		// 'email': email,
		'validation': validation,
		'type': 'validate'
	}).done(validateFinal);
}

function validateFinal(data)
{
	if (data == 'false')
	{
		showError('Incorrect validation code.');
		return;
	}
	if (data == 'false')
	{
		showError('An error occurred. Please try again.');
		return;
	}
	
	// $.removeCookie('reg_val', { path: '/' });
	// $.cookie('reg_val', $('[name="fvalidation"]').val().toLowerCase(), { path: '/' });
	
	window.location = "createaccount.html";
}

function clickBoard(board)
{
	var buttons = $('.board-div').children('button');
	$.map(buttons, function (element) { $(element).removeClass('red-button').addClass('orange-button'); });
	
	$('[name=' + board + ']').removeClass('orange-button').addClass('red-button');
}

function finish()
{
	var password = $('[name=fpassword]').val();
	
	if (password.length < 5)
	{
		showError('Password should be at least 5 characters.');
		return;
	}
	
	var confirm = $('[name=fconfirm]').val();
	if (password != confirm)
	{
		showError('Password and confirmation do not match.');
		return;
	}
	
	var selectedButton = $('.red-button');
	if (selectedButton.length != 1)
	{
		showError('Please select a board position.');
		return;
	}
	var board = selectedButton[0].name;
	
	// var validation = $.cookie('reg_val');
	// var email = $.cookie('reg_email');
	// if (validation === undefined || email === undefined)
	// {
	// 	showError('Please enable cookies and try again.');
	// 	return;
	// }
	
	$.post('validate.php', {
		'board': board,
		// 'email': email,
		// 'validation': validation,
		'password': password,
		'type': 'finish'
	}).done(function (data) { alert(data); });
}