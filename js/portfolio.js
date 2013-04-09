function submitPiece(obj, board)
{
	if (board == 'both')
		$(obj).replaceWith('<div style="margin-bottom:20px;"><button class="compact-button orange-button" onclick="submitPiece(this, \'lit\')">Literature</button><button class="compact-button purple-button" onclick="submitPiece(this, \'art\')">Art</button></div>');
	else if (board == 'lit')
		window.location = "editor.php";
	else if (board == 'art')
		window.location = "art.php";
}