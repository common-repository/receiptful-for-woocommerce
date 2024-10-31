/**
 * jQuery Repeater 1.0.0 by Jeroen Sormani
 *
 * https://github.com/JeroenSormani/jquery.repeater.js
 */
(function ($) {

	$.fn.repeater = function (options) {

		var defaults = {
			addTrigger: '.repeater-add',
			removeTrigger: '.repeater-remove',
			template: '.repeater-template',
			elementWrap: '.repeater-row', // Used to identify what is one element within the container.
			elementsContainer: '.repeater-container', // Used to identify what is one element within the container.
			removeElement: function (el) {
				el.slideUp('fast', function () {
					$(this).remove()
				});
			},
			onAddElement: function () {
			}
		};

		return $(this).filter(':not(.repeater-active)').each(function (i, e) {
			$(this).addClass('repeater-active');

			var $self;
			var $container;

			options = $.extend({}, $.fn.repeater.defaults, options);

			var init = function () {
				$container = $self.find(options.elementsContainer);

				$self.on('click', options.addTrigger, function () {
					addElement();
				});
				$self.on('click', options.removeTrigger, function () {
					options.removeElement($(this).parents(options.elementWrap).first());
				});
			};

			var getNewElement = function () {
				return $self.find(options.template).first().clone();
			};

			var addElement = function () {
				var element = getNewElement();

				$container.append(element);
				options.onAddElement(element, $container, $self)
			};

			$self = $(this);
			init();
		});

	}

})(jQuery);


(function($) {

	// Init repeater
	$('.conversio-widgets-wrap').repeater({
		addTrigger: '#add-conversio-widget',
		removeTrigger: '.conversio-widget .delete',
		template: '.conversio-widget-template .conversio-widget',
		elementWrap: '.conversio-widget',
		elementsContainer: '.conversio-widgets-list',
		removeElement: function (el) {
			el.remove();
		},
		onAddElement: function (template, container, $self) {
			var new_id = Math.floor(Math.random() * 899999 + 100000); // Random number sequence of 9 length
			template.find('input[name], select[name]').attr('name', function (index, value) {
				return (value.replace('9999', new_id)) || value;
			});
		}
	});

	// Custom hook field
	$(document.body).on('change', '.conversio-widget-location', function(e) {
		var hookField = $(this).parents('.conversio-widget').find('.conversio-widget-custom-hook'),
			tabNameField = $(this).parents('.conversio-widget').find('.conversio-widget-tab-name'),
			priorityField = $(this).parents('.conversio-widget').find('.conversio-widget-priority'),
			valPriority = parseInt($(this).val().substr($(this).val().indexOf(':') + 1));

		// Show/hide the custom hook field according to location value
		$(this).val() == 'custom' ? hookField.show() : hookField.hide();
		$(this).val() == 'tab' ? tabNameField.show() : tabNameField.hide();

		// Set the priority field value
		priorityField.val(valPriority || 10);
	});

})(jQuery);
