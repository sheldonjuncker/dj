import DreamQueryApp from './DreamQueryApp';

$(document).ready(function(){
	let app = new Vue({
		el: '#dream-query-app',
		data: {
			message: 'Hello, Dream Query Builder!'
		}
	});
});

export {
	DreamQueryApp
}