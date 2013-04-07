$(document).ready(function()
{
	$('#img-preview').on('dragstart', function(event) { event.preventDefault(); });
	$('#upload-content').click(function () { $('#file-upload').click(); return false; });
});

function uploadFile(obj)
{
	previewImg(obj);
	var file = obj.files[0];
	var xhr = new XMLHttpRequest();
	xhr.file = file;
	xhr.addEventListener('progress', function(e) {
		var done = e.position || e.loaded, total = e.totalSize || e.total;
		updateProgress(done / total);
		console.log('xhr progress: ' + (Math.floor(done/total*1000)/10) + '%');
	}, false);
	if (xhr.upload) {
		xhr.upload.onprogress = function(e) {
			var done = e.position || e.loaded, total = e.totalSize || e.total;
			updateProgress(done / total);
			console.log('xhr.upload progress: ' + done + ' / ' + total + ' = ' + (Math.floor(done/total*1000)/10) + '%');
		};
	}
	xhr.onreadystatechange = function(e) {
		if (this.readyState == 4) {
			console.log(['xhr upload complete', e]);
			
			$('#img-loader').css('visibility', 'hidden');
			$('#upload-content').html('Submit');
			$('#upload-content').removeClass('purple-button').removeClass('orange-button').addClass('red-button');
			$('#upload-content').click(submit);
			$('#upload-content').data('article-id', xhr.responseText);
		}
	};
	xhr.open('post', 'artupload.php', true);
	xhr.setRequestHeader("X-File-Name", file.name);
	xhr.send(file);
}

function submit()
{
	var articleId = parseInt($('#upload-content').data('article-id'));
	var title = $('[name="ftitle"]').val();
	if (!isNaN(articleId) && isFinite(articleId))
	{
		$.post('artsubmit.php', {
			'article_id': articleId,
			'title': title
		}).done(parseSubmit);
	}
}

function parseSubmit(data)
{
	alert(data);
}

function updateProgress(percent)
{
	if (percent < 0 || percent > 1)
		return;
	var imgLoad = $('#img-loader');
	var left = imgLoad.data('left') + percent * imgLoad.data('width');
	// var offset = imgLoad.data('width') - width;
	imgLoad.stop(true, false);
	$('#img-preview').css('visibility', 'visible');
	$('#img-loader').css('visibility', 'visible');
	imgLoad.animate({'left': left + 'px'}, 100);
	$('#upload-content').html(Math.floor(percent * 100) + '%');
}

function previewImg(input)
{
	if (window.FileReader)
	{
		if (input.files && input.files[0])
		{
			var reader = new FileReader();

			reader.onload = function (e)
			{
				$('#img-preview').css('visibility', 'hidden');
				$('#img-preview').attr('src', e.target.result);
				// $('#img-loader').data('width', $('#img-preview').width());
				var offset = $('#img-preview').offset();
				$('#img-loader').css('right', ($(window).width() - offset.left - $('#img-preview').outerWidth()) + 'px');
				$('#img-loader').css('left', offset.left + 'px');
				$('#img-loader').data('left', offset.left);
				$('#img-loader').data('width', $('#img-preview').outerWidth());
				$('#img-loader').css('height', $('#img-preview').outerHeight() + 'px');
				
				$('.paper-title').css('visibility', 'visible');
				$('#img-preview').css('visibility', 'visible');
				$('#img-loader').css('visibility', 'visible');

				updateProgress(0.0);
			};

			reader.readAsDataURL(input.files[0]);
		}
	}
	$('#title-row').css('display', 'block');
	$('#header-holder').css('display', 'none');
	$('#upload-content').unbind('click');
	$('#upload-content').html('Uploading...');
	$('#upload-content').removeClass('orange-button').addClass('purple-button');
}