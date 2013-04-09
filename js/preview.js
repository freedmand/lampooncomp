var comments;
var ci = 0;

var origWidth;
var origHeight;

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

function readjustComments()
{
	for (var i = 0; i < comments.length; i++)
	{
		var h = $(comments[i]);
		var coord = h.data('startCoord');
		var oldcoord = h.data('endCoord')
		
		var offset = $('.img-full').offset();
		var padding_w = ($('.img-full').outerWidth() - $('.img-full').width()) / 2;
		var padding_h = ($('.img-full').outerHeight() - $('.img-full').height()) / 2;
		
		if (coord[0] < 0)
			coord[0] = 0;
		else if (coord[0] >= origWidth)
			coord[0] = origWidth - 1;
		if (coord[1] < 0)
			coord[1] = 0;
		else if (coord[1] >= origHeight)
			coord[1] = origHeight - 1;
		if (oldcoord[0] < 0)
			oldcoord[0] = 0;
		else if (oldcoord[0] >= origWidth)
			oldcoord[0] = origWidth - 1;
		if (oldcoord[1] < 0)
			oldcoord[1] = 0;
		else if (oldcoord[1] >= origHeight)
			oldcoord[1] = origHeight - 1;
		
		h.css('left', (Math.min(oldcoord[0] / origWidth * $('.img-full').width(), coord[0] / origWidth * $('.img-full').width()) + (offset.left + padding_w)) + 'px');
		h.css('top', (Math.min(oldcoord[1] / origHeight * $('.img-full').height(), coord[1] / origHeight * $('.img-full').height()) + (offset.top + padding_h)) + 'px');
		h.css('width', (Math.abs(oldcoord[0] / origWidth * $('.img-full').width() - coord[0] / origWidth * $('.img-full').width())) + 'px');
		h.css('height', (Math.abs(oldcoord[1] / origHeight * $('.img-full').height() - coord[1] / origHeight * $('.img-full').height())) + 'px');
	}
}

function createComment(author, start_x, start_y, end_x, end_y, data)
{
	var h = $('<div></div>');
	h.addClass('art-comment');
	h.data('startCoord', [start_x, start_y]);
	h.data('endCoord', [end_x, end_y]);
	$('#content-div').append(h);
	var comment = h;
	var commentIndex = comments.push(comment) - 1;
	
	if ($('.comment-pane').css('display') == 'none')
	{
		$('.comment-pane').fadeIn(200, function () { readjustComments(); });
		$('.comment-placeholder').css('display', 'inline-block');
	}
	var input = $('<textarea rows="1" class="feedback-text"></textarea>');
	var div = $('<div></div>');
	div.append(input);
	div.addClass("comment-inactive");
	div.addClass("comment-focus");

	div.data('comment', comment);
	comment.data('div', div);

	var commentDivs = $('.comment-inactive');
	var inserted = false;
	for (var i = 0; i < commentDivs.length; i++)
	{
		var startCoord = $(commentDivs[i]).data('comment').data('startCoord');
		var endCoord = $(commentDivs[i]).data('comment').data('endCoord');
		var oldStartCoord = div.data('comment').data('startCoord');
		var oldEndCoord = div.data('comment').data('endCoord');
		
		if (Math.min(startCoord[1] * origWidth + startCoord[0], endCoord[1] * origWidth + endCoord[0])  > Math.min(oldStartCoord[1] * origWidth + oldStartCoord[0], oldEndCoord[1] * origWidth + oldEndCoord[0]))
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
		comment.remove();
		comments.splice(commentIndex, 1);
		div.remove();
	}

	function addComment()
	{
		div.removeClass('comment-focus');
		var text = data;
		input.remove();
		div.append($('<span><span class=\'comment-author\'>' + name + ': </span>' + text + '</span>'));
		checkButton.remove();
		closeButton.addClass('comment-button-small');
		div.click(function () {
			var commentDiv = $(this).data('comment');
			commentDiv.animate({'backgroundColor': '#fdfdfd'}, 200, function () { commentDiv.animate({'backgroundColor': 'rgba(255,255,0,0.2)'}, 200, function () { commentDiv.css('backgroundColor', '')}); });
		});
		div.mouseenter(function () {
			var commentDiv = $(this).data('comment');
			commentDiv.css('backgroundColor', 'rgba(251, 134, 0, 0.5)');
		});
		div.mouseleave(function () {
			var commentDiv = $(this).data('comment');
			commentDiv.css('backgroundColor', '');
		});

		comment.click(function () {
			var y = $('.comment-content').scrollTop()+div.offset().top-$('.comment-content').offset().top-42;
			$('.comment-content').animate({'scrollTop': '' + y + 'px'}, 200, function () {
				div.animate({'backgroundColor': '#ffad34'}, 500, function () { div.animate({'backgroundColor': '#dfddec'}, 500, function () {
					div.css('backgroundColor', "");
				}); });
			});
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
	readjustComments();
}

$(document).ready(function()
{
	comments = [];
	$('.notify-msg').data('height', $('.notify-msg').height());
	$('.img-full').click(hideMsg);
	
	var img = new Image();
	
	$(window).resize(function() {
		readjustComments();
	});
	
	$(img).load(function () {
		origWidth = this.width;
		origHeight = this.height;
		readjustComments();
		$(".img-full").on('mousedown', function (e)
		{
			var offset = $(this).offset();
			var padding_w = ($(this).outerWidth() - $(this).width()) / 2;
			var padding_h = ($(this).outerHeight() - $(this).height()) / 2;
			var x = e.pageX - offset.left - padding_w;
			var y = e.pageY - offset.top - padding_h;
			
			var coord = [parseInt(x * origWidth / $(this).width()), parseInt(y * origHeight / $(this).height())];
			if (coord[0] < 0 || coord[0] >= origWidth || coord[1] < 0 || coord[1] >= origHeight)
				return;

			var h = $('<div></div>');
			h.addClass('art-comment');
			h.css('width','0px');
			h.css('height','0px');
			h.css('left','' + (coord[0] / origWidth * $(this).width() + offset.left + padding_w)  + 'px');
			h.css('top','' + (coord[1] / origHeight * $(this).height() + offset.top + padding_h) + 'px');
			$('#content-div').append(h);
			$(this).data('highlight', h);
			$(h).data('coord', coord);
			$(h).data('origWidth', origWidth);
			$(h).data('origHeight', origHeight);

			console.log('down: ' + coord);
		});
		
		$(document).on('mouseup', function (e)
		{
			var h = $('.img-full').data('highlight');
			if (!h)
				return;
			var oldcoord = $(h).data('coord');
			if (oldcoord)
			{
				h.removeData('coord');
				
				var offset = $('.img-full').offset();
				var padding_w = ($('.img-full').outerWidth() - $('.img-full').width()) / 2;
				var padding_h = ($('.img-full').outerHeight() - $('.img-full').height()) / 2;
				var x = e.pageX - offset.left - padding_w;
				var y = e.pageY - offset.top - padding_h;
				
				var coord = [parseInt(x * origWidth / $('.img-full').width()), parseInt(y * origHeight / $('.img-full').height())];
				if (coord[0] < 0 || coord[0] >= origWidth || coord[1] < 0 || coord[1] >= origHeight)
				{
					$('.img-full').removeData('highlight');
					h.remove();
				}
				var comment = h;
				if (comment)
				{
					comment.data('startCoord', coord);
					comment.data('endCoord', oldcoord);
					var commentIndex = comments.push(comment) - 1;
					
					if ($('.comment-pane').css('display') == 'none')
					{
						$('.comment-pane').fadeIn(200, function () { readjustComments(); });
						$('.comment-placeholder').css('display', 'inline-block');
					}
					var input = $('<textarea rows="1" class="feedback-text"></textarea>');
					var div = $('<div></div>');
					div.append(input);
					div.addClass("comment-inactive");
					div.addClass("comment-focus");

					div.data('comment', comment);
					comment.data('div', div);

					var commentDivs = $('.comment-inactive');
					var inserted = false;
					for (var i = 0; i < commentDivs.length; i++)
					{
						var startCoord = $(commentDivs[i]).data('comment').data('startCoord');
						var endCoord = $(commentDivs[i]).data('comment').data('endCoord');
						var oldStartCoord = div.data('comment').data('startCoord');
						var oldEndCoord = div.data('comment').data('endCoord');
						
						if (Math.min(startCoord[1] * origWidth + startCoord[0], endCoord[1] * origWidth + endCoord[0])  > Math.min(oldStartCoord[1] * origWidth + oldStartCoord[0], oldEndCoord[1] * origWidth + oldEndCoord[0]))
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
						comment.remove();
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
							var commentDiv = $(this).data('comment');
							commentDiv.animate({'backgroundColor': '#fdfdfd'}, 200, function () { commentDiv.animate({'backgroundColor': 'rgba(255,255,0,0.2)'}, 200, function () { commentDiv.css('backgroundColor', '')}); });
						});
						div.mouseenter(function () {
							var commentDiv = $(this).data('comment');
							commentDiv.css('backgroundColor', 'rgba(251, 134, 0, 0.5)');
						});
						div.mouseleave(function () {
							var commentDiv = $(this).data('comment');
							commentDiv.css('backgroundColor', '');
						});

						comment.click(function () {
							var y = $('.comment-content').scrollTop()+div.offset().top-$('.comment-content').offset().top-42;
							$('.comment-content').animate({'scrollTop': '' + y + 'px'}, 200, function () {
								div.animate({'backgroundColor': '#ffad34'}, 500, function () { div.animate({'backgroundColor': '#dfddec'}, 500, function () {
									div.css('backgroundColor', "");
								}); });
							});
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
				}
			}
		});
		$(document).on('mousemove', function (e)
		{
			var h = $('.img-full').data('highlight');
			if (!h)
				return;
			var oldcoord = $(h).data('coord');
			if (oldcoord)
			{
				var offset = $('.img-full').offset();
				var padding_w = ($('.img-full').outerWidth() - $('.img-full').width()) / 2;
				var padding_h = ($('.img-full').outerHeight() - $('.img-full').height()) / 2;
				var x = e.pageX - offset.left - padding_w;
				var y = e.pageY - offset.top - padding_h;
				
				var coord = [parseInt(x * origWidth / $('.img-full').width()), parseInt(y * origHeight / $('.img-full').height())];
				if (coord[0] < 0)
					coord[0] = 0;
				else if (coord[0] >= origWidth)
					coord[0] = origWidth - 1;
				if (coord[1] < 0)
					coord[1] = 0;
				else if (coord[1] >= origHeight)
					coord[1] = origHeight - 1;
				if (oldcoord[0] < 0)
					oldcoord[0] = 0;
				else if (oldcoord[0] >= origWidth)
					oldcoord[0] = origWidth - 1;
				if (oldcoord[1] < 0)
					oldcoord[1] = 0;
				else if (oldcoord[1] >= origHeight)
					oldcoord[1] = origHeight - 1;
				
				h.css('left', (Math.min(oldcoord[0] / origWidth * $('.img-full').width(), coord[0] / origWidth * $('.img-full').width()) + (offset.left + padding_w)) + 'px');
				h.css('top', (Math.min(oldcoord[1] / origHeight * $('.img-full').height(), coord[1] / origHeight * $('.img-full').height()) + (offset.top + padding_h)) + 'px');
				h.css('width', (Math.abs(oldcoord[0] / origWidth * $('.img-full').width() - coord[0] / origWidth * $('.img-full').width())) + 'px');
				h.css('height', (Math.abs(oldcoord[1] / origHeight * $('.img-full').height() - coord[1] / origHeight * $('.img-full').height())) + 'px');
				console.log('drag');
			}
		});
		$(".img-full").on('dragstart', function (e)
		{
			e.preventDefault();
		});
	});
	img.src = $('.img-full').attr('src');
});

function sortComments()
{
	$('.comment-inactive').sortElements(function (a,b) { return $(a).data('interval')[0] > $(b).data('interval')[0] ? 1 : -1; });
}

function feedbackSubmit()
{
	for (var i = 0; i < comments.length; i++)
	{
		var data = $($(comments[i]).data('div').children()[1]).contents()[1].data;
		
		var start = $(comments[i]).data('startCoord');
		var start_x = '' + start[0];
		var start_y = '' + start[1];
		
		var end = $(comments[i]).data('endCoord');
		var end_x = '' + end[0];
		var end_y = '' + end[1];
		
		$.post('feedbackupload.php', {
			'articleid': articleid,
			'data': data,
			'start_x': start_x,
			'start_y': start_y,
			'end_x': end_x,
			'end_y': end_y
		}).done(function (data) { alert(data); });
	}
}