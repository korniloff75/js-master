'use strict';

// export var Styles= {
export default {
	clickBtn: function(e) {
		var t= e.target;
	}
}

(function () {
	var styles= document.querySelectorAll('link[href$="css"]'),
		lastStyle= styles[styles.length-1],
		newStyle= document.createElement('link');

	newStyle.href= 'dark.css';

	insertAfter(newStyle, lastStyle);


	// *Helpers
	function insertAfter (node, refNode) {
		if(refNode.nextSibling)
			refNode.parentNode.insertBefore(node, refNode.nextSibling);
		else
			refNode.parentNode.appendChild(node);

		return refNode;
	}
})()