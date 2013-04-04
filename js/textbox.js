var boldApplier, italicApplier, underlineApplier, commentApplier;

var entityMap = {
	"&": "&amp;",
	"<": "&lt;",
	">": "&gt;",
	'"': '&quot;',
	"'": '&#39;',
	"/": '&#x2F;'
};

String.prototype.escapeHTML = function() {
	return String(this).replace(/[&<>"'\/]/g, function (s) {
		return entityMap[s];
	});
}

function insertText(text)
{
	var sel = rangy.getSelection();
	var range = sel.getRangeAt(0);
	range.deleteContents();
	var node = document.createTextNode(text);
	range.insertNode(node);
	range.setStartAfter(node);
	range.collapse(true);
	sel.removeAllRanges();
	sel.addRange(range);
}

function insertHtml(html)
{
	var sel = rangy.getSelection();
	var range = sel.getRangeAt(0);
	range.deleteContents();
	var node = $("<div></div>").append($(html))[0];//$(html);
	range.insertNode(node);
	range.setStartAfter(node);
	range.collapse(true);
	sel.removeAllRanges();
	sel.addRange(range);
}

$(document).delegate('.paper', 'keydown', function(e) {
	var keyCode = e.keyCode || e.which;
	
	if (keyCode == 9)
	{
		e.preventDefault();
		insertText("\t");
	}
	
	if ((event.metaKey || event.ctrlKey) && String.fromCharCode(keyCode).toUpperCase() == 'B')
	{
		console.log("BOLD");
		bold();
	}
	else if ((event.metaKey || event.ctrlKey) && String.fromCharCode(keyCode).toUpperCase() == 'I')
		italic();
	else if ((event.metaKey || event.ctrlKey) && String.fromCharCode(keyCode).toUpperCase() == 'U')
		underline();
});

$('#file-upload').live('change', function()
{
	console.log('change');
	var file = this.files[0];
	var xhr = new XMLHttpRequest();
	xhr.file = file;
	xhr.addEventListener('progress', function(e) {
		var done = e.position || e.loaded, total = e.totalSize || e.total;
		console.log('xhr progress: ' + (Math.floor(done/total*1000)/10) + '%');
	}, false);
	if (xhr.upload) {
		xhr.upload.onprogress = function(e) {
			var done = e.position || e.loaded, total = e.totalSize || e.total;
			console.log('xhr.upload progress: ' + done + ' / ' + total + ' = ' + (Math.floor(done/total*1000)/10) + '%');
		};
	}
	xhr.onreadystatechange = function(e) {
		if ( 4 == this.readyState ) {
			console.log(['xhr upload complete', e]);
		}
		// console.log(e.target.responseText);
		$('.paper').html(e.target.responseText);
	};
	xhr.open('post', 'upload.php', true);
	xhr.setRequestHeader("X-File-Name", file.name);
	xhr.send(file);
});//, false);

window.onload = function()
{
	rangy.init();
	commentApplier = rangy.createCssClassApplier("comment-inline", {normalize: true, elementTagName: 'span', elementProperties: {'onclick': function () {alert("hell yeah!"); return false;}}});
};

function updateButtons()
{
	if (document.queryCommandState("bold"))
		$('#bold-button').removeClass('inactive-button').addClass('active-button');
	else
		$('#bold-button').removeClass('active-button').addClass('inactive-button');
	
	if (document.queryCommandState("italic"))
		$('#italic-button').removeClass('inactive-button').addClass('active-button');
	else
		$('#italic-button').removeClass('active-button').addClass('inactive-button');

	if (document.queryCommandState("underline"))
		$('#underline-button').removeClass('inactive-button').addClass('active-button');
	else
		$('#underline-button').removeClass('active-button').addClass('inactive-button');
}

function disableButtons()
{
	console.log('disable');
	$('#bold-button').removeClass('active-button').addClass('inactive-button');
	$('#italic-button').removeClass('active-button').addClass('inactive-button');
	$('#underline-button').removeClass('active-button').addClass('inactive-button');
}

function bold()
{
	console.log('yea');
	document.execCommand('bold');
	updateButtons();
}
function italic()
{
	document.execCommand("italic");
	updateButtons();
}
function underline()
{
	document.execCommand("underline");
	updateButtons();
}
function comment()
{
	if ($('.comment-pane').css('display') == 'none')
		$('.comment-pane').fadeIn(200);
		// $('.comment-pane').show("slide", { direction: "left" }, 1000);
	commentApplier.toggleSelection();
	var text = rangy.getSelection().toString();
	var div = document.createElement('div');
	div.className = "comment-inactive";
	div = $(div);
	div.html(text);
	$('.comment-pane').append(div);
}

function pasteHandle(e)
{
	if (e && e.clipboardData && e.clipboardData.getData)
	{
		if (/text\/html/.test(e.clipboardData.types))
			insertHtml(parseHtml(e.clipboardData.getData('text/html')));
		else if (/text\/plain/.test(e.clipboardData.types))
			insertText(e.clipboardData.getData('text/plain'))
	}
}

function elementStyle(elem)
{
	if (!elem || !elem.tagName)
		return { "bold" : false, "italic" : false, "underline" : false };
	var tagName = elem.tagName.toUpperCase();
	var boldStyle = ('' + $(elem).css('font-weight')).toLowerCase();
	var bold = (tagName == 'B' || tagName == 'STRONG') || (boldStyle == 'bold' || boldStyle == 'bolder' || boldStyle == '500' || boldStyle == '600' || boldStyle == '700' || boldStyle == '800' || boldStyle == '800' || boldStyle == '900');
	var italicStyle = $(elem).css('font-style').toLowerCase();
	var italic = (tagName == 'I' || tagName == 'EM') || (italicStyle == 'italic' || italicStyle == 'oblique');
	var underlineStyle = $(elem).css('text-decoration').toLowerCase();
	var underline = (tagName == 'U') || (underlineStyle == 'underline');
	return { "bold" : bold, "italic" : italic, "underline" : underline };
}

function mergeStyles(s1, s2)
{
	return { "bold" : s1.bold || s2.bold, "italic" : s1.italic || s2.italic, "underline" : s1.underline || s2.underline };
}

function parseHtml(html)
{
	html = '<div>' + html.replace('<body', '<body><div id="lampoon_body"').replace('</body>','</div></body>') + '</div>';
	var parsed = $('#lampoon_body',$(html));
	if (parsed.length == 0)
		parsed = $(html);
	
	var output_html = "";
	
	for (var i = 0; i < parsed.length; i++)
	{
		var style = elementStyle(parsed[i]);
		recurse(parsed[i], style);
	}
	
	function recurse(elem, style) {
		if (elem.nodeType == 8)
			return;
		if (elem.tagName)
		{
			var tagName = elem.tagName.toUpperCase();
			if (tagName == 'BR')
				output_html += "<br>";
			if (tagName == 'META' || tagName == 'STYLE' || tagName == 'LINK' || tagName == 'IFRAME')
				return;
		}
		else
		{
			var text = elem.data;
			output_html += text.replace(/\n/g, ' ').replace(/ +/g, ' ').escapeHTML();
			var parentTag = elem.parentNode.tagName.toUpperCase();
			if (parentTag == 'DIV' || parentTag == 'P')
				output_html += '<br>'
		}
		
		for (var i = 0; i < $(elem).contents().length; i++)
		{
			var child = $(elem).contents()[i];
			var newStyle = mergeStyles(style, elementStyle(child));
			output_html += (newStyle.bold ? "<b>" : "") + (newStyle.italic ? "<i>" : "") + (newStyle.underline ? "<u>" : "");
			recurse(child, newStyle);
			output_html += (newStyle.bold ? "</b>" : "") + (newStyle.italic ? "</i>" : "") + (newStyle.underline ? "</u>" : "");
		}
	}
	
	return output_html;
}