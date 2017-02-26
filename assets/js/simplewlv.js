(function( $ ) {
	'use strict';

	$(function() {

		var hidden_input_value = $('.simplewlv_hidden').val();

		function toggle_select( e, attributes_linked, select_hidden_element ) {
			var $this = $( e.currentTarget ),
				id = $this.attr('id');

			if ( -1 !== attributes_linked.indexOf( id ) ) { // Clicked correct attribute
				var re = new RegExp( id+':', 'g' ),
					attr_values = attributes_linked.replace(re, ''),
					array_attribute_values = attr_values.split('|');

				if ( -1 !== $.inArray( $this.val(), array_attribute_values ) ) {
					select_hidden_element.parents('tr').removeClass('hidden');
				} else {
					select_hidden_element.find('option').each(function(){
						var $option = $(this);
						if ( '' !== $option.val() ) {
							$option.parents('select').val($option.val()).trigger('change');
						}
					});

					if ( ! select_hidden_element.parents('tr').hasClass('hidden') ) {
						select_hidden_element.parents('tr').addClass('hidden');
					}
				}
			}
		}

		if ( hidden_input_value.length ) {
			var linked_json = JSON.parse( hidden_input_value ),
				linked_attributes = $.map(Object.keys( linked_json.linked_attributes ), function(elem){
    				return elem;
				}).join('|'), // pa_frame-dimensions:30x50-with-frame|pa_frame-dimensions:40x70-with-frame
				$hidden_select = $( '#'+linked_json.selected_attribute );

			$hidden_select.parents('tr').addClass('hidden hidden_select');

			// if selected values is clicked, toggle visibility of the selected attribute
			$(document).on( 'change', 'select[name*="attribute_pa"]', function(e) { toggle_select( e, linked_attributes, $hidden_select ); } );

		}
	});

})( jQuery );