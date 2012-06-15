/**
 * @version 1.0
 */
(function ($) {

	'use strict';

	$.fn.enableSwipe = function (options) {
		// deep copy of default options, merged with provided options
		options = $.extend(true, {}, $.fn.enableSwipe.options, options);
		
		if (options.overlap_margin > 45) {
			options.overlap_margin = 45;
		}

		var State = {
			'idle': 1, 
			'start': 2,
			'moved': 3,
			'end': 4
		}

		return this.each(function () {
			
			var state = State.idle,
			    start_point = null,
				end_point = null;
				
			var reset = function () {
				state = State.idle;
				start_point = null;
				end_point = null;
			}
			
			var getPoint = function (event) {
				var touch = event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
				return {'x': touch.pageX, 'y': touch.pageY};
			}
			
			var getAngle = function (a, b) {
				var x = b.x - a.x,
				    y = b.y - a.y;
					
				var theta = Math.atan2(-y, x);
				
				return theta * (180.0 / Math.PI);
			}
			
			$(this).live('touchstart', function (event) {
				// make sure state chain is intact
				if (state != State.idle) {
					reset();
				}
				
				state = State.start;
				start_point = getPoint(event);
			});
			
			$(this).live('touchmove', function (event) {
				if (state == State.start) {
					state = State.moved;
				} else if (state != State.moved) {
					reset();
				}
				
				// m is shorhand for the options.overalp_margin; used 
				// extensively in calculations
				var move_point, angle, m;
				
				if (options.lock_x) {
					move_point = getPoint(event);
					angle = getAngle(start_point, move_point);
					m = options.overlap_margin;
					
					// swipe is right or left under specified overlap margin?
					if ((angle > -45+m && angle < 45-m) || (angle > 135+m && angle < 180) || (angle > -180 && angle < -135-m)) {
						event.preventDefault();
					}
				}
				
				if (options.lock_y) {
					move_point = getPoint(event);
					angle = getAngle(start_point, move_point);
					m = options.overlap_margin;
					
					// swipe is top or bottom under specified overlap margin?
					if ((angle >= 45+m && angle < 135-m) || (angle > -135+m && angle < -45-m)) {
						event.preventDefault();
					}
				}
			});
			
			$(this).live('touchend', function (event) {
				if (state == State.moved) {
					state = State.idle;
					$(this).trigger('swipe');
					end_point = getPoint(event);
					// angle is between 180 and -180
					var angle = getAngle(start_point, end_point);
					var m = options.overlap_margin;
					if (angle > 0+m && angle < 90-m) {
						$(this).trigger('swipetopright');
					}
					if (angle >= 45+m && angle < 135-m) {
						$(this).trigger('swipetop');
					}
					if (angle > 90+m && angle < 180-m) {
						$(this).trigger('swipetopleft');
					}
					if ((angle > 135+m && angle < 180) || (angle > -180 && angle < -135-m)) {
						$(this).trigger('swipeleft');
					}
					if (angle > -180+m && angle < -90-m) {
						$(this).trigger('swipebottomleft');
					}
					if (angle > -135+m && angle < -45-m) {
						$(this).trigger('swipebottom');
					}
					if (angle > -90+m && angle < 0-m) {
						$(this).trigger('swipebottomright');
					}
					if (angle > -45+m && angle < 45-m) {
						$(this).trigger('swiperight');
					}
				}
				reset();
			});
			
			$(this).live('touchcancel', function (event) {
				reset();
			});
			
			$(this).live('gesturestart', function (event) {
				reset();
			});
			
			$(this).live('gesturechange', function (event) {
				reset();
			});
			
			$(this).live('gestureend', function (event) {
				reset();
			});
			
		});
	}

	$.fn.enableSwipe.options = {
		// the event cone is limited on each side by this margin
		// this value should NOT be greater then 45
		'overlap_margin': 0,
		'lock_x': false,
		'lock_y': false
	};	

}(jQuery));