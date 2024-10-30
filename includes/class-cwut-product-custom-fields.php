<?php

/**
 * Project : craftwork-wc-utilities
 */
class CWUT_Product_Custom_Fields {

    private static $enable_price_suffix_single = null;
    private static $enable_price_suffix_single_newline = null;

    private static $enable_price_suffix_archive = null;
    private static $enable_price_suffix_archive_newline = null;

    public static function init() {
        // The code for displaying WooCommerce Product Custom Fields
        add_action( 'woocommerce_product_options_general_product_data', [ __CLASS__, 'product_price_suffix_fields'] );
        // Following code Saves  WooCommerce Product Custom Fields
        add_action( 'woocommerce_process_product_meta', [ __CLASS__, 'product_price_suffix_fields_save'], 10, 1 );

        // display price html
        add_filter( 'woocommerce_get_price_html', [ __CLASS__, 'product_price_suffix_fields_display' ], 10, 2 );

    }

    public static function product_price_suffix_fields() {

        self::$enable_price_suffix_single = CWUT_Core::load_option(self::$enable_price_suffix_single, 'cwut_product_enable_price_suffix_single', 'no');
        self::$enable_price_suffix_archive = CWUT_Core::load_option(self::$enable_price_suffix_archive, 'cwut_product_enable_price_suffix_archive', 'no');
        if(self::$enable_price_suffix_single === 'no' && self::$enable_price_suffix_archive === 'no'){
            return;
        }

        echo '<div class="cwut-product-price-suffix-field">';

        woocommerce_wp_text_input(
            array(
                'id' => '_cwut_price_suffix',
                'placeholder' => 'Suffix after price',
                'label' => __('Price Suffix', 'cwut'),
                'desc_tip' => 'true'
            )
        );

        echo '</div>';
    }

    public static function product_price_suffix_fields_save($post_id) {
        $cwut_price_suffix_field = $_POST['_cwut_price_suffix'];
        if (!empty($cwut_price_suffix_field)) {
            update_post_meta($post_id, '_cwut_price_suffix', esc_html($cwut_price_suffix_field));
        }
        else {
            $meta = get_post_meta($post_id, '_cwut_price_suffix', true);
            if( ! empty($meta) ) {
                delete_post_meta( $post_id, '_cwut_price_suffix' );
            }
        }
    }

    public static function product_price_suffix_fields_display($price, $product) {

        self::$enable_price_suffix_single = CWUT_Core::load_option(self::$enable_price_suffix_single, 'cwut_product_enable_price_suffix_single', 'no');
        self::$enable_price_suffix_archive = CWUT_Core::load_option(self::$enable_price_suffix_archive, 'cwut_product_enable_price_suffix_archive', 'no');

        if(self::$enable_price_suffix_single === 'no' && self::$enable_price_suffix_archive === 'no'){
            return $price;
        }

        self::$enable_price_suffix_single_newline = CWUT_Core::load_option(self::$enable_price_suffix_single_newline, 'cwut_product_enable_price_suffix_single_newline', 'no');
        self::$enable_price_suffix_archive_newline = CWUT_Core::load_option(self::$enable_price_suffix_archive_newline, 'cwut_product_enable_price_suffix_archive_newline', 'no');

        //archive mode
        if(self::$enable_price_suffix_archive === 'yes' && ! is_product()) {

            $field = esc_html($product->get_meta('_cwut_price_suffix'));

            if(empty($field)){
                return $price;
            }

            $suffix = '';
            if(self::$enable_price_suffix_archive_newline === 'yes'){
                $suffix .= '<br/>';
            }

            $suffix .= '<span class="cwut-product-price-suffix-loop">'.$field.'</span>';
            return $price.$suffix;
        }
        else if(self::$enable_price_suffix_single === 'yes' && is_product()) {

            $field = esc_html($product->get_meta('_cwut_price_suffix'));

            if(empty($field)){
                return $price;
            }

            $suffix = '';
            if(self::$enable_price_suffix_single_newline === 'yes'){
                $suffix .= '<br/>';
            }

            $suffix .= '<span class="cwut-product-price-suffix-single">'.$field.'</span>';
            return $price.$suffix;
        }
        else {
            return $price;
        }
    }
}
