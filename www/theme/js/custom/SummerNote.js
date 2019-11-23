class SummerNote{
	init(id, options){
		$('#' + id).summernote(options || {});
	}
}

$(document).ready(function(){
	let summerNote = new SummerNote();
	summerNote.init('Dream_description', {
		minHeight: '400px',
		toolbar: [
			['style', ['style']],
			['font', ['bold', 'underline', 'clear']],
			['fontname', ['fontname']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph']],
			['table', ['table']],
			['insert',],
			['view', ['fullscreen', 'help']],
		],
		disableDragAndDrop: true,
		codeviewFilter: true,
		codeviewIframeFilter: true
	});
});

export default SummerNote;