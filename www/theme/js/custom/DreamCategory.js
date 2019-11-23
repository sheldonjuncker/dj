class DreamCategory{
	init(id){
		$.ajax('/dreamcategory?type=json', {
			success: function(data){
				console.log(data);
				let categoryNames = new Bloodhound({
					datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
					queryTokenizer: Bloodhound.tokenizers.whitespace,
					local: data
				});
				categoryNames.initialize();

				let $input = $('#' + id);
				$input.tagsinput({
					typeaheadjs: {
						name: 'categoryNames',
						displayKey: 'name',
						source: categoryNames.ttAdapter()
					},
					'itemValue': 'id',
					'itemText': 'name',
					'tagClass': 'badge badge-primary',
					'freeInput': false
				});

				//Initialize
				let ids = $input.val().split(',');
				console.log(ids);
				for(let i=0; i<ids.length; i++)
				{
					let id = ids[i];
					for(let j=0; j<data.length; j++)
					{
						if(data[j].id == id)
						{
							$input.tagsinput('add', data[j]);
						}
					}

				}
			},
			error: function(){
				console.log('Failed to load dream categories.')
			}
		});
	}
}

$(document).ready(function(){
	let dc = new DreamCategory();
	dc.init('Dream_categories');
});

export default DreamCategory