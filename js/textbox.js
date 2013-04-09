var boldApplier, italicApplier, underlineApplier, commentApplier;

var ANIMATION_DURATION = 400;

var comments;

var entityMap = {
	"&": "&amp;",
	"<": "&lt;",
	">": "&gt;",
	'"': '&quot;',
	"'": '&#39;',
	"/": '&#x2F;'
};

String.prototype.escapeHTML = function() {
	var simple_escape = String(this).replace(/[&<>"'\/]/g, function (s) {
		return entityMap[s];
	});
	var result = '';
	for (var i = 0; i < simple_escape.length; i++)
	{
		var code = simple_escape.charCodeAt(i);
		if (code > 126)
			result += '&#' + code + ';';
		else
			result += simple_escape.charAt(i);
	}
	return result;
}

function showError(msg)
{
	$('#msg').slideUp(ANIMATION_DURATION,
		function () {
			$('#msg').html(msg);
			$('#msg').slideDown(ANIMATION_DURATION);
		}
	);
}

// following function credit to http://james.padolsey.com/javascript/sorting-elements-with-jquery/
$.fn.sortElements = (function(){
	var sort = [].sort;
	return function(comparator, getSortable) {
		getSortable = getSortable || function(){return this;};
		var placements = this.map(function(){
			var sortElement = getSortable.call(this),
				parentNode = sortElement.parentNode,
				nextSibling = parentNode.insertBefore(
					document.createTextNode(''),
					sortElement.nextSibling
				);
			return function() {
				if (parentNode === this) {
					throw new Error("You can't sort elements if any one is a descendant of another.");
				}
				parentNode.insertBefore(this, nextSibling);
				parentNode.removeChild(nextSibling);
			};
		});
		return sort.call(this, comparator).each(function(i){
			placements[i].call(getSortable.call(this));
		});
	};
})();

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
	var node = $("<div></div>").append(html)[0];//$(html);
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

function uploadFile(obj)
{
	var file = obj.files[0];
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
		$('.paper').html(e.target.responseText);
	};
	xhr.open('post', 'upload.php', true);
	xhr.setRequestHeader("X-File-Name", file.name);
	xhr.send(file);
}

function hideMsg()
{
	$('.notify-msg').unbind('mouseenter');
	$('.notify-msg').mouseenter(revealMsg);
	$('.notify-msg').clearQueue();
	$('.notify-msg').animate({'height': '0px', 'padding-bottom': '0px'}, 500);
}
function revealMsg()
{
	$('.notify-msg').focus();
	$('.notify-msg').unbind('mouseleave');
	$('.notify-msg').mouseleave(hideMsg);
	$('.notify-msg').clearQueue();
	$('.notify-msg').animate({'height': $('.notify-msg').data('height'), 'padding-bottom': '20px'}, 500);
}

$(document).ready(function()
{
	rangy.init();
	comments = [];
	commentApplier = rangy.createCssClassApplier("comment-inline", {normalize: false, elementTagName: 'a', elementProperties: {'name': 'new-comment'}});
	$('.notify-msg').data('height', $('.notify-msg').height());
	$('.paper-title').click(hideMsg);
	$('.paper').click(hideMsg);
	
	$(window).resize(function() {
		var fontsize = $(window).width()/84;
		$('.paper-holder').css('font-size', '' + fontsize + 'px');
		$('.panel').css('width', '' + ($('.paper-holder').outerWidth() - 59) + 'px')
		console.log(fontsize);
	});
});

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

function getRangeFromInterval(interval)
{
	var range = rangy.createRange();
	range.selectCharacters($('.paper')[0], interval[0], interval[1]);
	return range;
}

function getRangeCoords(range)
{
	var range = range.nativeRange;
	var x = 0, y = 0;
	if (window.getSelection)
	{
		if (range.getClientRects)
		{
			range.collapse(true);
			var rect = range.getClientRects()[0];
			x = rect.left;
			y = rect.top;
		}
	}
	else
	{
		range.collapse(true);
		x = range.boundingLeft;
		y = range.boundingTop;
	}
	return { x: x, y: y };
}

function sortComments()
{
	$('.comment-inactive').sortElements(function (a,b) { return $(a).data('interval')[0] > $(b).data('interval')[0] ? 1 : -1; });
}

function comment()
{
	var bounds = rangy.createRange();
	bounds.selectNodeContents($('.paper')[0]);
	var paper_bounds = bounds.toCharacterRange($('.paper')[0]);
	
	var r = rangy.getSelection().getRangeAt(0);
	var offsets = r.toCharacterRange($('.paper')[0]);
	if (offsets.start < paper_bounds.start || offsets.end > paper_bounds.end)
	{
		console.log('out of document contents');
		// show error message
		return false;
	}
	
	for (var i = 0; i < comments.length; i++)
	{
		var interval = comments[i];
		if ((interval[0] >= offsets.start && interval[0] < offsets.end) || (interval[1] > offsets.start && interval[1] <= offsets.end) ||
			(offsets.start >= interval[0] && offsets.start < interval[1]) || (offsets.end > interval[0] && offsets.end <= interval[1]))
		{
			console.log('intersection');
			// show intersection message
			return;
		}
	}
	
	if ($('.comment-pane').css('display') == 'none')
	{
		$('.comment-pane').fadeIn(200);
		$('.comment-placeholder').css('display', 'inline-block');
	}
	commentApplier.applyToSelection();
	var comment = $('[name="new-comment"]');
	comment.removeAttr('name');
	
	var r_int = Array(offsets.start, offsets.end, comment);
	var commentIndex = comments.push(r_int) - 1;
	
	var text = rangy.getSelection().toString();
	var input = $('<textarea rows="1" class="feedback-text"></textarea>');
	var div = $('<div></div>');
	div.append(input);
	div.addClass("comment-inactive");
	div.addClass("comment-focus");
	
	div.data('interval', r_int);
	div.data('comment', comment);
	comment.data('div', div);
	
	for (var i = 0; i < comment.length; i++)
	{
		$(comment[i]).data('comment', comment);
	}
	
	var commentDivs = $('.comment-inactive');
	var inserted = false;
	for (var i = 0; i < commentDivs.length; i++)
	{
		if ($(commentDivs[i]).data('interval')[0] > r_int[0])
		{
			$(commentDivs[i]).before(div);
			inserted = true;
			break;
		}
	}
	if (!inserted)
		$('.comment-content').append(div);
	
	var checkButton = $('<button class="comment-button">&#x2713;</button>');
	var closeButton = $('<button class="comment-button">&#215;</button>');
	div.append(checkButton);
	div.append(closeButton);
	input.focus();
	input.keyup(function(e) {
		while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
			$(this).height($(this).height()+1);
		};
	});
	
	function removeComment()
	{
		var r = rangy.createRange();
		r.selectCharacters($('.paper')[0], offsets.start, offsets.end);
		comment.css('background-color', "");
		comment.contents().unwrap();
		
		comments.splice(commentIndex, 1);
		div.remove();
	}
	
	function addComment()
	{
		div.removeClass('comment-focus');
		var text = input.val();
		input.remove();
		div.append($('<span><span class=\'comment-author\'>' + name + ': </span>' + text + '</span>'));
		checkButton.remove();
		closeButton.addClass('comment-button-small');
		div.click(function () {
			var commentDivs = $(this).data('comment');
			var y = $('.paper-holder').scrollTop()+$(commentDivs[0]).offset().top-$('.paper-holder').offset().top-100;
			$('.paper-holder').animate({'scrollTop': '' + y + 'px'}, 500, function () {
				commentDivs.animate({'backgroundColor': '#fdfdfd'}, 200, function () { commentDivs.animate({'backgroundColor': '#ffd7a3'}, 200); });
			});
		});
		div.mouseenter(function () {
			var commentDivs = $(this).data('comment');
			commentDivs.css('backgroundColor', '#ffaa3c');
		});
		div.mouseleave(function () {
			var commentDivs = $(this).data('comment');
			commentDivs.css('backgroundColor', '');
		});
		
		comment.click(function () {
			var y = $('.comment-content').scrollTop()+div.offset().top-$('.comment-content').offset().top-42;
			$('.comment-content').animate({'scrollTop': '' + y + 'px'}, 200, function () {
				div.animate({'backgroundColor': '#ffad34'}, 500, function () { div.animate({'backgroundColor': '#dfddec'}, 500, function () {
					div.css('backgroundColor', "");
				}); });
			});
		});
		comment.mouseenter(function () {
			comment.data('comment').css('backgroundColor', '#ffaa3c');
			div.addClass('comment-inactive-hover');
		});
		comment.mouseleave(function () {
			comment.data('comment').css('backgroundColor', '');
			div.removeClass('comment-inactive-hover');
		});
	}
	
	div.data('rm', removeComment);
	
	input.blur(function () { if (input.val().length == 0) removeComment(div); });
	input.keypress(function(e) {
		if(e.which == 13) {
			if (input.val().length > 0)
				addComment();
			else
				removeComment();
		}
	});
	checkButton.click(addComment);
	closeButton.click(removeComment);
	// sortComments();
}

function createComment(start, end, author, data)
{
	var bounds = rangy.createRange();
	bounds.selectNodeContents($('.paper')[0]);
	var paper_bounds = bounds.toCharacterRange($('.paper')[0]);
	
	var offsets = {'start': start, 'end': end};
	if (offsets.start < paper_bounds.start || offsets.end > paper_bounds.end)
	{
		console.log('out of document contents');
		return false;
	}
	
	for (var i = 0; i < comments.length; i++)
	{
		var interval = comments[i];
		if ((interval[0] >= offsets.start && interval[0] < offsets.end) || (interval[1] > offsets.start && interval[1] <= offsets.end) ||
			(offsets.start >= interval[0] && offsets.start < interval[1]) || (offsets.end > interval[0] && offsets.end <= interval[1]))
		{
			console.log('intersection');
			return;
		}
	}
	
	if ($('.comment-pane').css('display') == 'none')
	{
		$('.comment-pane').fadeIn(200);
		$('.comment-placeholder').css('display', 'inline-block');
	}
	var ca_range = rangy.createRange();
	ca_range.selectCharacters($('.paper')[0], offsets.start, offsets.end);
	commentApplier.applyToRange(ca_range);
	var comment = $('[name="new-comment"]');
	comment.removeAttr('name');
	
	var r_int = Array(offsets.start, offsets.end, comment);
	var commentIndex = comments.push(r_int) - 1;
	
	var input = $('<textarea rows="1" class="feedback-text"></textarea>');
	var div = $('<div></div>');
	div.append(input);
	div.addClass("comment-inactive");
	div.addClass("comment-focus");
	
	div.data('interval', r_int);
	div.data('comment', comment);
	comment.data('div', div);
	
	for (var i = 0; i < comment.length; i++)
	{
		$(comment[i]).data('comment', comment);
	}
	
	var commentDivs = $('.comment-inactive');
	var inserted = false;
	for (var i = 0; i < commentDivs.length; i++)
	{
		if ($(commentDivs[i]).data('interval')[0] > r_int[0])
		{
			$(commentDivs[i]).before(div);
			inserted = true;
			break;
		}
	}
	if (!inserted)
		$('.comment-content').append(div);
	
	var checkButton = $('<button class="comment-button">&#x2713;</button>');
	var closeButton = $('<button class="comment-button">&#215;</button>');
	div.append(checkButton);
	div.append(closeButton);
	input.focus();
	input.keyup(function(e) {
		while($(this).outerHeight() < this.scrollHeight + parseFloat($(this).css("borderTopWidth")) + parseFloat($(this).css("borderBottomWidth"))) {
			$(this).height($(this).height()+1);
		};
	});
	
	function removeComment()
	{
		var r = rangy.createRange();
		r.selectCharacters($('.paper')[0], offsets.start, offsets.end);
		comment.css('background-color', "");
		comment.contents().unwrap();
		
		comments.splice(commentIndex, 1);
		div.remove();
	}
	
	function addComment()
	{
		div.removeClass('comment-focus');
		var text = input.val();
		input.remove();
		div.append($('<span><span class=\'comment-author\'>' + author + ': </span>' + data + '</span>'));
		checkButton.remove();
		closeButton.addClass('comment-button-small');
		div.click(function () {
			var commentDivs = $(this).data('comment');
			var y = $('.paper-holder').scrollTop()+$(commentDivs[0]).offset().top-$('.paper-holder').offset().top-100;
			$('.paper-holder').animate({'scrollTop': '' + y + 'px'}, 500, function () {
				commentDivs.animate({'backgroundColor': '#fdfdfd'}, 200, function () { commentDivs.animate({'backgroundColor': '#ffd7a3'}, 200); });
			});
		});
		div.mouseenter(function () {
			var commentDivs = $(this).data('comment');
			commentDivs.css('backgroundColor', '#ffaa3c');
		});
		div.mouseleave(function () {
			var commentDivs = $(this).data('comment');
			commentDivs.css('backgroundColor', '');
		});
		
		comment.click(function () {
			var y = $('.comment-content').scrollTop()+div.offset().top-$('.comment-content').offset().top-42;
			$('.comment-content').animate({'scrollTop': '' + y + 'px'}, 200, function () {
				div.animate({'backgroundColor': '#ffad34'}, 500, function () { div.animate({'backgroundColor': '#dfddec'}, 500, function () {
					div.css('backgroundColor', "");
				}); });
			});
		});
		comment.mouseenter(function () {
			comment.data('comment').css('backgroundColor', '#ffaa3c');
			div.addClass('comment-inactive-hover');
		});
		comment.mouseleave(function () {
			comment.data('comment').css('backgroundColor', '');
			div.removeClass('comment-inactive-hover');
		});
	}
	
	div.data('rm', removeComment);
	
	input.blur(function () { if (input.val().length == 0) removeComment(div); });
	input.keypress(function(e) {
		if(e.which == 13) {
			if (input.val().length > 0)
				addComment();
			else
				removeComment();
		}
	});
	addComment();
	closeButton.click(removeComment);
}

function pasteHandle(e)
{
	if ($('.paper').attr('contenteditable') == 'true')
	{
		if (e && e.clipboardData && e.clipboardData.getData)
		{
			if (/text\/html/.test(e.clipboardData.types))
				insertHtml(parseHtml(e.clipboardData.getData('text/html')));
			else if (/text\/plain/.test(e.clipboardData.types))
				insertText(e.clipboardData.getData('text/plain'))
		}
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

function feedbackSubmit()
{
	var error = false;
	var intersect = false;
	var endpoint = comments.length;
	for (var i = 0; i < comments.length; i++)
	{
		var data = $($(comments[i][2]).data('div').children()[1]).contents()[1].data;
		var start = '' + comments[i][0];
		var end = '' + comments[i][1];
		$.post('feedbackupload.php', {
			'articleid': articleid,
			'data': data,
			'start': start,
			'end': end
		}).done(function (data) {
			if (data == 'error')
				error = true;
			if (data == 'false')
				intersect = true;
			if (i == endpoint)
			{
				if (error)
				{
					showError('An error occurred. You may have to try again.');
					return;
				}
				else
				{
					window.location = 'portfolio.php';
				}
			}
		});
	}
}

function submit()
{
	var r = rangy.createRange();
	r.selectNode($('.paper')[0]);
	commentApplier.undoToRange(r);
	var title = $('.paper-title').val().escapeHTML();
	if (title.length == 0)
	{
		showError('Please enter a title.');
		return;
	}
	var data = $('.paper').html();
	if (data.length == 0)
	{
		showError('Please enter text in the body of the document.');
		return;
	}
	$.post('submit.php', {
		'title': title,
		'istext': '1',
		'data': data
	}).done(function (data) {
		if (data != 'true')
		{
			showError('An error occurred. Please try again.');
			return;
		}
		window.location = 'portfolio.php';
	});
}