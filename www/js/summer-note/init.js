$(document).ready(function(){
	let options = {
		minHeight: '400px',
		toolbar: [
			['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough', 'superscript', 'subscript']],
			['fontsize', []],
			['para', ['ul', 'ol', 'paragraph']],
			['table', []],
			['color', []],
			['insert',[]],
			['view', ['fullscreen']],
		],
		fontSizes: ['8','10','12','14','16','18','20','24','28','32','36'],
		disableDragAndDrop: true,
		codeviewFilter: true,
		codeviewIframeFilter: true
	};

	$(".dj-summernote").summernote(options);
});