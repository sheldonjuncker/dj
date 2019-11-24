class SummerNote {
	init(id, options) {
		$('#' + id).summernote(options || {});
	}
}

$(document).ready(function(){
	let summerNote = new SummerNote();
	summerNote.init('Dream_description', {
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
		fontSizes: [8,9,10,11,12,14,16,18,20,24,28,32,36],
		disableDragAndDrop: true,
		codeviewFilter: true,
		codeviewIframeFilter: true
	});
});

export default SummerNote;