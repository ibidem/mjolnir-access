/**
 * @version 1.0
 */
(function ($) {

	'use strict';

	$.fn.autosuggest = function (options) {
		
		// settings
		var settings = $.extend({
			'name'        : 'unnamed',
			'minLength'   : 2,
			'cache'       : true,
			'contentType' : null,
			'type'        : 'get',
			'format'      : null,
			'li_format' : function (value, data) {
					return value;
				},
			'url'         : null,
			'query_key'   : 'term',
			'limit'       : 10,
			'timeout'     : 1,
			'context'     : null,
			'emptyMsg'    : 'No matches.',
			'status'      : {
					'404' : function () {
							// empty
						},
					'500' : function () {
							// empty
						},
					'403' : function () {
							// empty
						}
				},
			'success'     : function (data, textStatus, jqXHR) { 
					// empty
					return true;
				},
			'select'      : function (object) {
					return;
				}
		}, options);
		
		// clean up all events, in case of another instance
		$(this).off('keyup.autosuggest');
		$(this).off('blur.autosuggest');

		var ajax_handler = null;
		
		var self = $(this).filter('input');
		self.attr('autocomplete', 'off');
		self.wrap('<div class="js-autosuggest" />');
		self = self.parent('div');
		
		if (settings.url === null)
		{
			// abort initialization
			return;
		}

		var suggested = [],
			suggestions = {},
			complete = [],
			dataset = {};

		function formatted_answer(value, term) {
			var split = value.toLowerCase().indexOf(term.toLowerCase());
			return value.substr(0, split) + '<b>' + value.substr(split, term.length) + '</b>' + value.substr(split + term.length);
		}

		function hide_suggestions() {
			setTimeout(function () {
				self.find('ul').remove();
			}, 500);
		}

		function show_suggestions(self, data, term, settings) {
			var info = '', i;
			self.find('ul').remove();

			// got suggestions?
			if (data.length > 0) {
				info = '<ul>';
				for (i = 0; i < data.length; ++i) {
					info += '<li class="js-found" data-value="'+data[i].value+'" data-object="'+i+'">' + 
						settings.li_format(formatted_answer(data[i].value, term), data[i]) + 
						'</li>';
				}
				info += '</ul>';
			} else { // no suggestions
				if (settings.emptyMsg != null) {
					info = '<ul><li class="js-nothing">'+settings.emptyMsg+'</li></ul>'
				}	
			}
			// generate suggestion list
			self.append(info);

			self.find('li').click(function () {
				self.find('input').val($(this).attr('data-value'));
				// convert index to integer value
				var index = parseInt($(this).attr('data-object'));
				$.proxy(settings.select, self.find('input'))(data[index]);
				self.find('ul').remove();
			});
		}

		function default_postformat(value, query_key) {
			// this is the default handling for post; get will simply 
			// generate a query
			var json = {};
			json[query_key] = value;
			return JSON.stringify(json);
		}

		function default_getformat(value, query_key) {
			return query_key + '=' + value;
		}

		function get_subterm(value) {
			var i;
			value = value.toLowerCase();
			for (i = 0; i < complete.length; ++i) {
				if (value.indexOf(complete[i]) != -1) {
					return complete[i];
				}
			}

			return null;
		}

		function filtered(data, term) {
			var i, result = [];
			term = term.toLowerCase();
			for (i = 0; i < data.length; ++i) {
				if (data[i].value.toLowerCase().indexOf(term) != -1) {
					result.push(data[i]);
				}
			}

			return result;
		}

		this.on('blur.autosuggest', function (event) {
			hide_suggestions();
		});
		
		// arrow key handling
		this.on('keydown.autosuggest', function (event) {
			var container, selected, next, prev, first, last,
			    code = (event.keyCode ? event.keyCode : event.which);
			
			if (code == 40) {
				// down arrow
				container = self.closest('.js-autosuggest');
				selected = container.find('li.selected');
				if (selected.length) {
					next = selected.next('li.js-found');
					if (next.length) {
						next.addClass('selected');
						selected.removeClass('selected');
						$(this).val(next.text());
					} else { // no next
						selected.removeClass('selected');
					}
				} else { // no element selected
					// select first
					first = container.find('li.js-found:first');
					if (first.length) {
						first.addClass('selected');
						$(this).val(first.text());
					}
				}
			} else if (code == 38) {
				// up arrow
				container = self.closest('.js-autosuggest');
				selected = container.find('li.selected');
				if (selected.length) {
					prev = selected.prev('li.js-found');
					if (prev.length) {
						prev.addClass('selected');
						selected.removeClass('selected');
						$(this).val(prev.text());
					} else { // no next
						selected.removeClass('selected');
					}
				} else { // no element selected
					// select first
					last = container.find('li.js-found:last');
					if (last.length) {
						last.addClass('selected');
						$(this).val(last.text());
					}
				}
			} else if (code == 13) {
				hide_suggestions();
			}
		});

		this.on('keyup.autosuggest', function (event)	{
			
			var code = (event.keyCode ? event.keyCode : event.which);
			if (code == 40 || code == 38 || code == 13) {
				return;
			}
			
			// retrieve the value from the field
			var value = $(this).val();
			
			// do we have at enough characters?
			if (value.length < settings.minLength) {
				// clear any previous results
				self.find('ul').remove();
				// do nothing else
				return;
			}
			
			// cancel any previous request
			if (ajax_handler !== null) {
				ajax_handler.abort();
				ajax_handler = null;
			}
			
			// was this request already satisfied?
			if ($.inArray(value, suggested) != -1) {
				show_suggestions(self, suggestions[value], value, settings)
				return;
			}
			
			var subterm = get_subterm(value);
			if (subterm != null) {
				show_suggestions(self, filtered(dataset[subterm], value), value, settings)
				return;
			}
			
			// the function must be in this scope to access value
			function success_handler(data, textStatus, jqXHR) {
				try {
					if (settings.success(data, textStatus, jqXHR)) {
						// store resultset
						suggested.push(value);
						suggestions[value] = data;

						if (data.length < settings.limit) {
							complete.push(value);
							dataset[value] = data;
						}

						show_suggestions(self, data, value, settings);
					}
				} catch (error) {
					console.log(error);
				}
			}
			
			function fail_handler(jqXHR, textStatus) {
				console.log("jQuery ajax failed: " + textStatus);
			}
			
			if (settings.type == 'get') {
				if (settings.format == null) {
					settings.format = default_getformat;
				}
				if (settings.contentType == null) {
					settings.contentType = 'application/x-www-form-urlencoded';
				}
				ajax_handler = $.ajax({
					cache      : settings.cache,
					type       : 'get',
					url        : settings.url,
					data       : settings.format(value, settings.query_key),
					dataType   : 'json',
					statusCode : settings.status,
					success    : success_handler
				}).fail(fail_handler);
			} else { // method post
				if (settings.format == null) {
					settings.format = default_postformat;
				}
				if (settings.contentType == null) {
					settings.contentType = 'application/json';
				}
				ajax_handler = $.ajax({
					cache       : settings.cache,
					type        : 'post',
					url         : settings.url,
					data        : settings.format(value, settings.query_key),
					contentType : settings.contentType,
					dataType    : 'json',
					statusCode  : settings.status,
					success     : success_handler
				}).fail(fail_handler);
			}
		});

	};

} (jQuery));
