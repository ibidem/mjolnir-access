/**
 * @version 1.0
 */
var DCIActor;
(function ($) {

	'use strict';

	// this function expects at least 2 parameters
	// 1 actor-object and at least 1 role object
	DCIActor = function () {
		var i, the_actor;
		the_actor = {};
		for (i = 0; i < arguments.length; i += 1) {
			the_actor = $.extend(the_actor, arguments[i]);
		}

		return the_actor;
	};

}(jQuery));