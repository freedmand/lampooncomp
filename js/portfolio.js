function submitPiece(obj, board)
{
	if (board == 'both')
		$(obj).replaceWith('<button class="form-button orange-button" onclick="submitPiece(this, \'lit\')">Literature</button><button class="form-button purple-button" onclick="submitPiece(this, \'art\')">Art</button>');
	else if (board == 'lit')
		window.location = "editor.php";
	else if (board == 'art')
		window.location = "art.php";
}