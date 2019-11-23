class SummerNote{
	init(id){
		$('#' + id).summernote();
	}
}

$(document).ready(function(){
	let summerNote = new SummerNote();
	summerNote.init('Dream_description');
});

export default SummerNote;