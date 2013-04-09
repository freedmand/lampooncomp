<?php
if ($_SESSION['directors'] == '0')
{
echo '<div class="body-header">
	<div class="header-options">
		<a class="header-link" href="login.html">account</a> <span class="divider">|</span> <a class="header-link" href="signout.php">sign out</a>
	</div>
	<a class="logo-holder">
		<img src="img/logo_fut.png" id="logo_img" alt="The Harvard Lampoon" onclick="window.location=\'welcome.php\';">
	</a>
</div>
<div class="comp-header">
	<a class="comp-link" href="portfolio.php">PORTFOLIO</a>
	<span class="comp-sep">&middot;</span>
	<a class="comp-link" href="feedback.php">FEEDBACK</a>
	<span class="comp-sep">&middot;</span>
	<a class="comp-link" href="directors.php">DIRECTORS</a>
	<span class="comp-sep">&middot;</span>
	<a class="comp-link" href="account.php">ACCOUNT</a>
</div>';
}
else
{
echo '<div class="body-header">
	<div class="header-options">
		<a class="header-link" href="login.html">account</a> <span class="divider">|</span> <a class="header-link" href="signout.php">sign out</a>
	</div>
	<a class="logo-holder">
		<img src="img/logo_fut.png" id="logo_img" alt="The Harvard Lampoon" onclick="window.location=\'welcome.php\';">
	</a>
</div>
<div class="comp-header">
	<a class="comp-link" href="review.php">REVIEW</a>
	<span class="comp-sep">&middot;</span>
	<a class="comp-link" href="feedback.php">FEEDBACK</a>
	<span class="comp-sep">&middot;</span>
	<a class="comp-link" href="compers.php">COMPERS</a>
	<span class="comp-sep">&middot;</span>
	<a class="comp-link" href="account.php">ACCOUNT</a>
</div>';
}

?>