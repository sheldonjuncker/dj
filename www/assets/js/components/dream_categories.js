$(document).ready(function(){
	$.ajax('/dreamcategory?type=json', {
		success: function(data){
			console.log(data);
			var categoryNames = new Bloodhound({
				datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
				queryTokenizer: Bloodhound.tokenizers.whitespace,
				local: data
			});
			categoryNames.initialize();

			var $input = $('#Dream_categories');
			$('#Dream_categories').tagsinput({
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
			var ids = $input.val().split(',');
			for(var i=0; i<ids.length; i++)
			{
				var id = ids[i];
				$('#Dream_categories').tagsinput('add', data[id]);
			}
		},
		error: function(){
			console.log('Failed to load dream categories.')
		}
	});
});