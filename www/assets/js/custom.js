/*!
  * DreamJournal Custom JS
  * Copyright 2019-2019 Sheldon Juncker
  * All custom JS gets compiled into this file.
  */
(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
	typeof define === 'function' && define.amd ? define(['exports'], factory) :
	(global = global || self, factory(global.custom = {}));
}(this, function (exports) { 'use strict';

	class Test{
		test(){
			console.log("I'm a test class!");
		}
	}

	class SummerNote{
		init(id){
			$('#' + id).summernote();
		}
	}

	$(document).ready(function(){
		let summerNote = new SummerNote();
		summerNote.init('Dream_description');
	});

	exports.SummerNote = SummerNote;
	exports.Test = Test;

	Object.defineProperty(exports, '__esModule', { value: true });

}));
//# sourceMappingURL=custom.js.map
