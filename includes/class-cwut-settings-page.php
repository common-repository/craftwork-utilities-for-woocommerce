<?php
/**
 * Project : craftwork-woocommerce-utility
 */

class CWUT_Settings_Page extends WC_Settings_Page {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->id    = 'cwut_settings';
        $this->label = __( 'Utilities', 'cwut' );

        parent::__construct();
    }

    /**
     * Get sections.
     *
     * @return array
     */
    public function get_sections() {
        $sections = array(
            ''             => __( 'Welcome', 'cwut' ),
            'general'    => __( 'General', 'cwut' ),
            'product'    => __( 'Product', 'cwut' ),
            'account'    => __( 'Account', 'cwut' ),
            'checkout' => __( 'Checkout', 'cwut' )
        );

        return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
    }

    /**
     * Output the settings.
     */
    public function output() {
        global $current_section,
               $hide_save_button; //this global variable use by /woocommerce/includes/admin/views/html-admin-settings.php

        if(strlen($current_section) === 0){
            echo $this->render_welcome_page();
            $hide_save_button = true;
        }
        else{
            $settings = $this->get_settings( $current_section );

            WC_Admin_Settings::output_fields( $settings );
        }
    }

    private function render_welcome_page(){
        return __("
            <h2>Welcome to Craftwork Utilities for Woocommerce</h2>
            <div>We provide the following function to make woocommerce more engage with customer.</div>
            <ol>
              <li>Replace 'add to cart' with 'view details' buttons.</li>
              <li>Replace 'add to cart' text in single product page.</li>
              <li>Replace Currency Symbol.</li>
              <li>Redirect to checkout page after click add to cart in single product page.</li>
              <li>Clear previous cart when click add to cart</li>
              <li>Redirect to specific url after woocommerce login</li>
              <li>Add Variable Product Custom Fields</li>
              <li>And more!</li>
            </ol>
        ", 'cwut');
    }

    /**
     * Save settings.
     */
    public function save() {
        global $current_section;

        $settings = $this->get_settings( $current_section );
        WC_Admin_Settings::save_fields( $settings );

        if ( $current_section ) {
            do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
        }
    }

    /**
     * Get settings array.
     *
     * @param string $current_section Current section name.
     * @return array
     */
    public function get_settings( $current_section = '' ) {
        if ( 'general' === $current_section ) {
            $settings = apply_filters('cwut_general_settings',array(
                array(
                    'title' => __( 'Enhanced general options', 'cwut' ),
                    'type'  => 'title',
                    'desc'  => __('Enhance your general options in woocommerce','cwut'),
                    'id'    => 'cwut_general_options',
                ),
                array(
                    'title'         => __( "Replace currency symbol", 'cwut' ),
                    'desc'          => __( "This option will replace current currency symbol with provided text", 'cwut' ),
                    'id'            => 'cwut_general_enable_replace_currency_symbol',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),
                array(
                    'title'    => __( "Text for currency symbol", 'cwut' ),
                    'desc'     => __( "This option will use as text for currency symbol", 'cwut' ),
                    'id'       => 'cwut_general_currency_symbol_text',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Currency', 'cwut'),
                    'css'      => 'min-width: 300px',
                    'desc_tip' => true,
                ),
                array(
                    'type' => 'sectionend',
                    'id'   => 'cwut_general_options',
                ),
            ));
        }
        else if ('product' === $current_section) {
            $settings = apply_filters('cwut_product_settings',array(
                array(
                    'title' => __( 'Enhance product options', 'cwut' ),
                    'type'  => 'title',
                    'desc'  => __('Enhance your product display.', 'cwut'),
                    'id'    => 'cwut_product_options',
                ),
                array(
                    'title'         => __( "Replace 'add to cart' button", 'cwut' ),
                    'desc'          => __( "This option will replace 'add to cart' button with 'view details' button.This option will apply on archive/shop pages and woocommerce product items loop.", 'cwut' ),
                    'id'            => 'cwut_product_enable_replace_add_to_cart',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),
                array(
                    'title'    => __( "Text for 'view details' button", 'cwut' ),
                    'desc'     => __( "This option will use as text for 'view details' button otherwise it use 'view details'", 'cwut' ),
                    'id'       => 'cwut_product_view_details_text',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('View Details', 'cwut'),
                    'css'      => 'min-width: 300px',
                    'desc_tip' => true,
                ),

                array(
                    'title'         => __( "Replace 'add to cart' text on single product", 'cwut' ),
                    'desc'          => __( "This option will replace 'add to cart' text on single product page.", 'cwut' ),
                    'id'            => 'cwut_product_enable_replace_single_add_to_cart_text',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'    => __( "Text for replace 'add to cart' on single product", 'cwut' ),
                    'desc'     => __( "This option will use as text for 'add to cart' button otherwise it will use 'Order Now'", 'cwut' ),
                    'id'       => 'cwut_product_single_add_to_cart_text',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Order Now', 'cwut'),
                    'css'      => 'min-width: 300px',
                    'desc_tip' => true,
                ),

                array(
                    'title'         => __( "Add single product pricing suffix", 'cwut' ),
                    'desc'          => __( "This option will add suffix after woocommerce single product price.", 'cwut' ),
                    'id'            => 'cwut_product_enable_price_suffix_single',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'         => __( "Insert newline before single product price suffix", 'cwut' ),
                    'desc'          => __( "This option will insert newline before single product price suffix.", 'cwut' ),
                    'id'            => 'cwut_product_enable_price_suffix_single_newline',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'         => __( "Add pricing suffix on archive", 'cwut' ),
                    'desc'          => __( "This option will add suffix after woocommerce product price in archive.", 'cwut' ),
                    'id'            => 'cwut_product_enable_price_suffix_archive',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'         => __( "Insert newline price suffix on archive", 'cwut' ),
                    'desc'          => __( "This option will insert newline before price suffix on archive page.", 'cwut' ),
                    'id'            => 'cwut_product_enable_price_suffix_archive_newline',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'cwut_product_options',
                ),

                array(
                    'title' => __( 'Enhance product variations', 'cwut' ),
                    'type'  => 'title',
                    'desc'  => __('Enhance product variations.', 'cwut'),
                    'id'    => 'cwut_product_variation_options',
                ),

                array(
                    'title'         => __( "Enable variation custom fields", 'cwut' ),
                    'desc'          => __( "This option will enabled custom fields in product variation setup box.", 'cwut' ),
                    'id'            => 'cwut_product_variation_enable_custom_fields',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'cwut_product_variation_options',
                ),

                array(
                    'title' => __( 'Custom Field 1', 'cwut' ),
                    'type'  => 'title',
                    'id'    => 'cwut_product_variation_custom_field_1_options',
                ),

                array(
                    'title'    => __( "Custom Field 1 Label", 'cwut' ),
                    'desc'     => __( "This is the label of custom field 1", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_1_label',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Custom Field 1 Label', 'cwut'),
                    'css'      => 'min-width: 300px;',
                    'desc_tip' => true,
                ),

                array(
                    'title'         => __( "Show custom field 1 Label", 'cwut' ),
                    'desc'          => __( "Show custom field label in single product page.", 'cwut' ),
                    'id'            => 'cwut_product_variation_custom_field_1_show_label',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'    => __( "Custom Field 1 Type", 'cwut' ),
                    'desc'     => __( "This is the type of custom field 1", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_1_type',
                    'type'     => 'select',
                    'class'    => 'wc-enhanced-select',
                    'css'      => 'min-width:300px;',
                    'default'  => 'text',
                    'options'  => array(
                        'text'  => __( 'Text', 'woocommerce' ),
                        'textarea'   => __( 'Textarea', 'woocommerce' ),
                        'radio' => __( 'Radio', 'woocommerce' ),
                        'select' => __( 'Select', 'woocommerce' ),
                    ),

                    'desc_tip' => true,
                ),

                array(
                    'title'    => __( "Custom Field 1 List Values", 'cwut' ),
                    'desc'     => __( "This is value for input type radio and select. separate value with comma (,)", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_1_list',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Use comma (,) to separate value', 'cwut'),
                    'css'      => 'min-width: 300px;',
                    'desc_tip' => true,
                ),

                array(
                    'title'    => __( "Custom Field 1 Default Value", 'cwut' ),
                    'desc'     => __( "Default value will use if no value presented. If no default value set, woocommerce will not display this custom field.", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_1_default',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Please specify default value', 'cwut'),
                    'css'      => 'min-width: 300px;margin-bottom: 50px;',
                    'desc_tip' => true,
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'cwut_product_variation_custom_field_1_options',
                ),

                array(
                    'title' => __( 'Custom Field 2', 'cwut' ),
                    'type'  => 'title',
                    'id'    => 'cwut_product_variation_custom_field_2_options',
                ),

                array(
                    'title'    => __( "Custom Field 2 Label", 'cwut' ),
                    'desc'     => __( "This is the label of custom field 2", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_2_label',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Custom Field 2 Label', 'cwut'),
                    'css'      => 'min-width: 300px;',
                    'desc_tip' => true,
                ),

                array(
                    'title'         => __( "Show custom field 2 Label", 'cwut' ),
                    'desc'          => __( "Show custom field label in single product page.", 'cwut' ),
                    'id'            => 'cwut_product_variation_custom_field_2_show_label',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'    => __( "Custom Field 2 Type", 'cwut' ),
                    'desc'     => __( "This is the type of custom field 2", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_2_type',
                    'type'     => 'select',
                    'class'    => 'wc-enhanced-select',
                    'css'      => 'min-width:300px;',
                    'default'  => 'text',
                    'options'  => array(
                        'text'  => __( 'Text', 'woocommerce' ),
                        'textarea'   => __( 'Textarea', 'woocommerce' ),
                        'radio' => __( 'Radio', 'woocommerce' ),
                        'select' => __( 'Select', 'woocommerce' ),
                    ),

                    'desc_tip' => true,
                ),

                array(
                    'title'    => __( "Custom Field 2 List Values", 'cwut' ),
                    'desc'     => __( "This is value for input type radio and select. separate value with comma (,)", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_2_list',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Use comma (,) to separate value', 'cwut'),
                    'css'      => 'min-width: 300px;',
                    'desc_tip' => true,
                ),

                array(
                    'title'    => __( "Custom Field 2 Default Value", 'cwut' ),
                    'desc'     => __( "Default value will use if no value presented. If no default value set, woocommerce will not display this custom field.", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_2_default',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Please specify default value', 'cwut'),
                    'css'      => 'min-width: 300px;margin-bottom: 50px;',
                    'desc_tip' => true,
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'cwut_product_variation_custom_field_2_options',
                ),

                array(
                    'title' => __( 'Custom Field 3', 'cwut' ),
                    'type'  => 'title',
                    'id'    => 'cwut_product_variation_custom_field_3_options',
                ),

                array(
                    'title'    => __( "Custom Field 3 Label", 'cwut' ),
                    'desc'     => __( "This is the label of custom field 3", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_3_label',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Custom Field 3 Label', 'cwut'),
                    'css'      => 'min-width: 300px;',
                    'desc_tip' => true,
                ),

                array(
                    'title'         => __( "Show custom field 3 Label", 'cwut' ),
                    'desc'          => __( "Show custom field label in single product page.", 'cwut' ),
                    'id'            => 'cwut_product_variation_custom_field_3_show_label',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'    => __( "Custom Field 3 Type", 'cwut' ),
                    'desc'     => __( "This is the type of custom field 3", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_3_type',
                    'type'     => 'select',
                    'class'    => 'wc-enhanced-select',
                    'css'      => 'min-width:300px;',
                    'default'  => 'text',
                    'options'  => array(
                        'text'  => __( 'Text', 'woocommerce' ),
                        'textarea'   => __( 'Textarea', 'woocommerce' ),
                        'radio' => __( 'Radio', 'woocommerce' ),
                        'select' => __( 'Select', 'woocommerce' ),
                    ),

                    'desc_tip' => true,
                ),

                array(
                    'title'    => __( "Custom Field 3 List Values", 'cwut' ),
                    'desc'     => __( "This is value for input type radio and select. separate value with comma (,)", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_3_list',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Use comma (,) to separate value', 'cwut'),
                    'css'      => 'min-width: 300px;',
                    'desc_tip' => true,
                ),

                array(
                    'title'    => __( "Custom Field 3 Default Value", 'cwut' ),
                    'desc'     => __( "Default value will use if no value presented. If no default value set, woocommerce will not display this custom field.", 'cwut' ),
                    'id'       => 'cwut_product_variation_custom_field_3_default',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Please specify default value', 'cwut'),
                    'css'      => 'min-width: 300px;',
                    'desc_tip' => true,
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'cwut_product_variation_custom_field_3_options',
                ),
            ));
        }
        else if ('account' === $current_section) {
            $settings = apply_filters('cwut_account_settings',array(
                array(
                    'title' => __( 'Enhanced Account Flow', 'cwut' ),
                    'type'  => 'title',
                    'desc'  => 'Enhance Your Woocommerce Shop Account Flow.',
                    'id'    => 'cwut_account_options',
                ),

                array(
                    'title'         => __( "Enable redirect after LOGIN", 'cwut' ),
                    'desc'          => __( "This option will redirect user to specific url after login", 'cwut' ),
                    'id'            => 'cwut_account_enable_redirect',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'    => __( "Custom URL for redirect button", 'cwut' ),
                    'desc'     => __( "This option will use as url for redirect user after login", 'cwut' ),
                    'id'       => 'cwut_account_custom_url',
                    'type'     => 'url',
                    'default'  => '',
                    'placeholder' => __('URL', 'cwut'),
                    'desc_tip' => true,
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'cwut_account_options',
                ),
            ));
        }
        else if ('checkout' === $current_section) {
            $settings = apply_filters('cwut_checkout_settings',array(
                array(
                    'title' => __( 'Enhance Checkout Flow', 'cwut' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'cwut_checkout_options',
                ),

                array(
                    'title'         => __( "Enable redirect to checkout", 'cwut' ),
                    'desc'          => __( "This option will redirect user to checkout page after click add to cart.", 'cwut' ),
                    'id'            => 'cwut_checkout_enable_redirect',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'         => __( "Clear cart when click add to cart", 'cwut' ),
                    'desc'          => __( "This option will clear old cart items when click add to cart.Useful with redirect to checkout option.", 'cwut' ),
                    'id'            => 'cwut_checkout_enable_clear_cart',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'         => __( "Enable checkout total summary message", 'cwut' ),
                    'desc'          => __( "This option will enable message on checkout page total summary area.", 'cwut' ),
                    'id'            => 'cwut_checkout_enable_summary_message',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'    => __( "Checkout Total Summary Message", 'cwut' ),
                    'desc'     => __( "This is the message for checkout page total summary area", 'cwut' ),
                    'id'       => 'cwut_checkout_summary_message',
                    'type'     => 'textarea',
                    'default'  => '',
                    'placeholder' => __('Your Message', 'cwut'),
                    'css'      => 'min-width: 500px;',
                    'custom_attributes' => array( 'rows' => 5 ),
                    'desc_tip' => true,
                ),

                array(
                    'title'    => __( "Checkout Total Summary Message Margin Top", 'cwut' ),
                    'desc'     => __( "Margin top in pixel for summary message area.", 'cwut' ),
                    'id'       => 'cwut_checkout_summary_message_margin_top',
                    'type'     => 'text',
                    'default'  => '0',
                    'placeholder' => __('Please put value in numeric', 'cwut'),
                    'css'      => 'max-width: 100px;',
                    'desc_tip' => true,
                ),

                array(
                    'title'    => __( "Checkout Total Summary Message Margin Bottom", 'cwut' ),
                    'desc'     => __( "Margin bottom in pixel for summary message area.", 'cwut' ),
                    'id'       => 'cwut_checkout_summary_message_margin_bottom',
                    'type'     => 'text',
                    'default'  => '0',
                    'placeholder' => __('Please put value in numeric', 'cwut'),
                    'css'      => 'max-width: 100px;',
                    'desc_tip' => true,
                ),

                array(
                    'title'         => __( "Enable custom back to shop URL", 'cwut' ),
                    'desc'          => __( "This option will enable custom back to shop URL on cart page (after add to cart).", 'cwut' ),
                    'id'            => 'cwut_checkout_enable_custom_shop_url',
                    'default'       => 'no',
                    'type'          => 'checkbox'
                ),

                array(
                    'title'    => __( "Custom back to shop URL", 'cwut' ),
                    'desc'     => __( "Custom back to shop URL when user click back to shop on add to cart page.", 'cwut' ),
                    'id'       => 'cwut_checkout_custom_shop_url',
                    'type'     => 'text',
                    'default'  => '',
                    'placeholder' => __('Please put valid url', 'cwut'),
                    'css'      => 'max-width: 300px;',
                    'desc_tip' => true,
                ),

                array(
                    'type' => 'sectionend',
                    'id'   => 'cwut_checkout_options',
                ),
            ));
        }
        else{
            $settings = apply_filters('cwut_welcome_settings',array(
                array(
                    'title' => __( 'Welcome', 'cwut' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'cwut_welcome_options',
                ),
                array(
                    'type' => 'sectionend',
                    'id'   => 'cwut_welcome_options',
                ),
            ));
        }

        return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
    }

}

return new CWUT_Settings_Page();
