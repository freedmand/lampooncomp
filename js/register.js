var ANIMATION_DURATION = 400;
var EMAIL_REGEX = /^\S+@\S+$/;

function showError(msg)
{
	$('.error-msg').slideUp(ANIMATION_DURATION,
		function () {
			$('.error-msg').html(msg);
			$('.error-msg').slideDown(ANIMATION_DURATION);
		}
	);
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
		'year': year
	}).done(showValidation);
}

function showValidation(data)
{
	
}