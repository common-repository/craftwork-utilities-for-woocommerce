(function($) {

    // Immediately Call Function

    let template_string = `
    <# if ( data.variation.cwut_custom_field_enable === 'yes' ) { #>
        <div class="cwut_custom_field_container">
            {{{ data.variation.cwut_custom_field_1}}}
            {{{ data.variation.cwut_custom_field_2}}}
            {{{ data.variation.cwut_custom_field_3}}}
        </div>
    <# } #>`
    ;

    $('#tmpl-variation-template').prepend(template_string);

})( jQuery );
