<?php

function sendEmail($from, $from_name, $to, $subject, $html, $text)
{
	$from = "$from_name <$from>";
	$mime_boundary = 'Multipart_Boundary_x'.md5(time()).'x';

	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\r\n";
	$headers .= "Content-Transfer-Encoding: 7bit\r\n";
	$replyto .= "reply-to: $from";

	$body = "This is a multi-part message in mime format.\n\n";

	# Add in plain text version
	$body.= "--$mime_boundary\n";
	$body.= "Content-Type: text/plain; charset=\"charset=us-ascii\"\n";
	$body.= "Content-Transfer-Encoding: 7bit\n\n";
	$body.= $text;
	$body.= "\n\n";

	# Add in HTML version
	$body.= "--$mime_boundary\n";
	$body.= "Content-Type: text/html; charset=\"UTF-8\"\n";
	$body.= "Content-Transfer-Encoding: 7bit\n\n";
	$body.= $html;
	$body.= "\n\n";
	
	# End email
	$body.= "--$mime_boundary--\n";

	# Finish off headers
	$headers .= "From: $from\r\n";
	$headers .= "X-Sender-IP: $_SERVER[SERVER_ADDR]\r\n";
	$headers .= 'Date: '.date('n/d/Y g:i A')."\r\n";
	$replyto .= "reply-to: $from";
	# Mail it out
	return mail($to, $subject, $body, $headers);
}

?>