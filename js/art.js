$(document).ready(function()
{
	$('#img-preview').on('dragstart', function(event) { event.preventDefault(); });
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
		if ( 4 == this.readyState ) {
			console.log(['xhr upload complete', e]);
			$('#img-loader').css('visibility', 'hidden');
			updateProgress(1.0);
			$('#content-holder').html('<h3>Upload complete.</h3>');
		}
		$('.paper').html(e.target.responseText);
	};
	xhr.open('post', 'upload.php', true);
	xhr.setRequestHeader("X-File-Name", file.name);
	xhr.send(file);
}

function updateProgress(percent)
{
	var imgLoad = $('#img-loader');
	var left = imgLoad.data('left') + percent * imgLoad.data('width');
	// var offset = imgLoad.data('width') - width;
	imgLoad.stop(true, false);
	$('#img-preview').css('visibility', 'visible');
	imgLoad.animate({'left': left + 'px'}, 100);
	$('#content-holder').html('<h3>Uploading (' + Math.floor(percent * 100) + ')</h3>');
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
				$('#img-loader').data('width', $('#img-preview').width());
				$('#img-loader').css('height', $('#img-preview').height() + 'px');
				
				$('.paper-title').css('visibility', 'visible');

				updateProgress(0.0);
			};

			reader.readAsDataURL(input.files[0]);
		}
	}
	$('#content-holder').html('<h3>Uploading...</h3>');
}