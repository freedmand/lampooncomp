var ANIMATION_DURATION = 400;
var EMAIL_REGEX = /^\S+@\S+$/;

var state = 0;
var states = ['New password', 'Name', 'Room', 'Year', 'Grant privileges'];

function showError(msg)
{
	$('#msg').slideUp(ANIMATION_DURATION,
		function () {
			$('#msg').html(msg);
			$('#msg').slideDown(ANIMATION_DURATION);
		}
	);
}

function next()
{
	state = (state + 1) % states.length;
	$('.sub-form-label, [name="finput"], [name="fconfirm"]').fadeOut(400, function () {
		$('#finput-label').html(states[state] + ':');
		if (state == 0)
			$('[name="finput"], [name="fconfirm"]').prop('type', 'password');
		else
			$('[name="finput"], [name="fconfirm"]').prop('type', 'text');
		$('[name="finput"]').val('');
		$('[name="fconfirm"]').val('');
		$('.sub-form-label, [name="finput"], [name="fconfirm"]').fadeIn(400);
	});
}

function apply()
{
	var pass = $('[name="fpass"]').val();
	var val = $('[name="finput"]').val();
	var conf = $('[name="fconfirm"]').val();
	if (pass.length == 0)
	{
		showError('Please enter a password.');
		return;
	}
	if (pass.length < 5)
	{
		showError('Passwords should be at least 5 characters.');
		return;
	}
	if (val != conf)
	{
		showError('Confirmation does not match input.');
		return;
	}
	if (state == 0 && val.length < 5)
	{
		showError('Passwords should be at least 5 characters.');
		return;
	}
	if (state >=1 && state <= 3 && val.length == 0)
	{
		showError('Please enter a valid ' + states[0].toLowerCase() + '.');
		return;
	}
	if (state == 4 && (val.length == 0 || !val.match(EMAIL_REGEX)))
	{
		showError('Please submit a valid email address.');
		return;
	}
	
	$.post('changeaccount.php', {
		'state': states[state],
		'pass': pass,
		'value': val
	}).done(changeAccount);
}

function changeAccount(data)
{
	if (data == 'false')
	{
		showError('Incorrect password or input.');
		return;
	}
	if (data == 'error')
	{
		showError('An error occurred. Please try again.');
		return;
	}
	if (data == 'true')
		window.location = 'welcome.php';
}