$(document).ready(function(){
	$.ajax('/dreamcategory?type=json', {
		success: function(data){
			var categoryNames = new Bloodhound({
				datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
				queryTokenizer: Bloodhound.tokenizers.whitespace,
				local: $.map(data, function (category) {
					return {
						name: category
					};
				})
			});
			categoryNames.initialize();

			$('#Dream_categories').tagsinput({
				typeaheadjs: {
					name: 'categoryNames',
					displayKey: 'name',
					valueKey: 'name',
					source: categoryNames.ttAdapter()
				},
				'tagClass': 'badge badge-primary',
				'freeInput': false
			});
		},
		error: function(){
			console.log('Failed to load dream categories.')
		}
	});
});