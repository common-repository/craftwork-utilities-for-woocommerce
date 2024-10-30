<?php
/**
 * Project : craftwork-wc-utilities
 */

require CWUT_DIR.'includes/class-cwut-variation.php';
require CWUT_DIR.'includes/class-cwut-product-custom-fields.php';

class CWUT_Core {

    private static $enable_replace_add_to_cart = null;
    private static $view_details_text = null;

    private static $enable_replace_single_add_to_cart_text = null;
    private static $replace_single_add_to_cart_text = null;

    private static $enable_replace_currency_symbol = null;
    private static $currency_symbol_text = null;

    private static $enable_redirect_after_login = null;
    private static $custom_redirect_url = null;

    private static $enable_redirect_checkout = null;
    private static $enable_clear_cart = null;

    private static $enable_summary_message = null;
    private static $checkout_summary_message = null;
    private static $checkout_summary_message_margin_top = null;
    private static $checkout_summary_message_margin_bottom = null;
    private static $checkout_enable_custom_shop_url = null;
    private static $checkout_custom_shop_url = null;
    //cwut_checkout_enable_custom_shop_url
    //cwut_checkout_custom_shop_url

    public static function init(){

        /**
         * Load Text Domain
         */
        add_action('init', [__CLASS__, 'load_text_domain']);

        /**
         * Load Variation Features
         */
        add_action('init', [__CLASS__, 'load_cwut_variation']);

        /**
         * Load Custom Fields Features
         */
        add_action('init', [__CLASS__, 'load_cwut_product_custom_fields']);

        /**
         * Enqueue Script
         */
        add_action( 'wp_enqueue_scripts', [__CLASS__, 'enqueue_frontend_scripts'], 30 );

        /**
         * Correct filters to insert new woocommerce settings page.
         */
        add_filter( 'woocommerce_get_settings_pages', [ __CLASS__, 'add_settings_page' ] );

        /**
         * Woocommerce Filter to replace add to cart button in product loop.
         */
        add_filter( 'woocommerce_loop_add_to_cart_link', [__CLASS__, 'replace_add_to_cart_button'], 10, 3 );

        /**
         * Woocommerce Filter to replace add to cart button in Woocommerce Product Blocks.
         */
        add_filter( 'woocommerce_blocks_product_grid_item_html', [__CLASS__, 'replace_wc_blocks_add_to_cart_button'], 10, 3 );

        /**
         * Woocommerce Filter to replace add to cart page in single product page.
         */
        add_filter('woocommerce_product_single_add_to_cart_text', [__CLASS__, 'replace_single_product_add_to_cart_text'], 10, 1);

        /**
         * Woocommerce Filter to replace currency symbol.
         */
        add_filter( 'woocommerce_currency_symbol', [__CLASS__, 'replace_currency_symbol'], 10, 1 );

        /**
         * Woocommerce Filter for after login redirect
         */
        add_filter( 'woocommerce_login_redirect', [__CLASS__, 'redirect_after_login'], 10, 2 );


        /**
         * Woocommerce Filter for redirect after add to cart
         */
        add_filter( 'woocommerce_add_to_cart_redirect', [__CLASS__, 'redirect_checkout_add_cart'], 10, 1 );

        /**
         * Woocommerce Filter for validate cart we use for clear cart.
         */
        add_filter( 'woocommerce_add_to_cart_validation', [__CLASS__, 'clear_cart'], 10, 3 );

        /**
         * Woocommerce Action in checkout page woocommerce_review_order_before_payment
         */
        add_action( 'woocommerce_review_order_before_payment', [__CLASS__, 'after_total_summary'], 10 );

        /**
         * Woocommerce Action for custom back to shop URL on cart page.
         */
        add_filter( 'woocommerce_return_to_shop_redirect', [ __CLASS__, 'custom_shop_url'], 10, 1 );
        add_filter( 'woocommerce_continue_shopping_redirect', [ __CLASS__, 'custom_shop_url'], 10, 1 );
    }

    /**
     * Load Plugin Text Domain
     */
    public static function load_text_domain(){
        load_plugin_textdomain( 'cwut', false, CWUT_LANG_DIR );
    }

    public static function load_cwut_variation() {
        CWUT_Variation::init();
    }

    public static function load_cwut_product_custom_fields(){
        CWUT_Product_Custom_Fields::init();
    }

    public static function load_option(&$variable, $option_name, $default = ''){
        if(is_null($variable)) {
            $variable = WC_Admin_Settings::get_option( $option_name, $default);
        }

        return $variable;
    }//public static function load_option

    public static function enqueue_frontend_scripts() {
        wp_enqueue_style('craftwork-wc-front-css',CWUT_DIR_URL.'assets/css/craftwork-wc-front.css',array(),CWUT_VERSION);
    }

    /**
     *
     * use this method to insert new settings page for woocommerce
     * since woocommerce will load necessary php includes for settings page.
     *
     * @param $settings
     *
     * @return array
     */
    public static function add_settings_page( $settings ) {
        $settings[] = include_once CWUT_DIR.'includes/class-cwut-settings-page.php';
        return $settings;
    }

    /**
     * Replace 'add to cart' button with view details button
     *
     * @param string $output previous filter output.
     * @param WC_Product $product
     * @param array $args
     * @return string
     */
    public static function replace_add_to_cart_button($output, $product, $args = array()){

        if(is_null(self::$enable_replace_add_to_cart)) {
            self::$enable_replace_add_to_cart = WC_Admin_Settings::get_option( 'cwut_product_enable_replace_add_to_cart', 'no' );
            if(self::$enable_replace_add_to_cart === 'yes'){
                self::$view_details_text = WC_Admin_Settings::get_option( 'cwut_product_view_details_text', '' );
                if(empty(self::$view_details_text)){
                    self::$view_details_text = __('View Details', 'cwut'); //fix get_option default value wont work with expression
                }
            }
        }//load only once on the first loop.

        if(self::$enable_replace_add_to_cart === 'yes'){

            $args['class'] = str_replace('ajax_add_to_cart', '', $args['class']);

            return apply_filters(
                'cwut_woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
                sprintf(
                    '<a href="%s" class="%s" %s>%s</a>',
                    esc_url( $product->get_permalink() ),
                    esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
                    isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
                    esc_html( self::$view_details_text )
                ),
                $product,
                $args
            );
        }
        else {
            return $output;
        }
    }

    public static function replace_wc_blocks_add_to_cart_button($str, $data, $product){
        if(is_null(self::$enable_replace_add_to_cart)) {
            self::$enable_replace_add_to_cart = WC_Admin_Settings::get_option( 'cwut_product_enable_replace_add_to_cart', 'no' );
            if(self::$enable_replace_add_to_cart === 'yes'){
                self::$view_details_text = WC_Admin_Settings::get_option( 'cwut_product_view_details_text', '' );
                if(empty(self::$view_details_text)){
                    self::$view_details_text = __('View Details', 'cwut'); //fix get_option default value wont work with expression
                }
            }
        }//load only once on the first loop.

        if(self::$enable_replace_add_to_cart === 'yes'){

            $new_button_link = self::replace_wc_blocks_button_data($data->button, $product);

            return apply_filters(
                'craftwork_woocommerce_blocks_product_grid_item_html',
                "<li class=\"wc-block-grid__product\">
				<a href=\"{$data->permalink}\" class=\"wc-block-grid__product-link\">
					{$data->image}
					{$data->title}
				</a>
				{$data->badge}
				{$data->price}
				{$data->rating}
				{$new_button_link}
			</li>",
                $data,
                $product
            );
        }
        else {
            return $str;
        }
    }

    private static function replace_wc_blocks_button_data($link, $product){

        $product_link = $product->get_permalink();
        $new_href = "href=\"$product_link\"";
        $new_button_text = self::$view_details_text;

        //link it position at 2 in replacement pattern
        //string on button at 5 in replacement pattern
        return str_replace('ajax_add_to_cart','',preg_replace('/(<a.+)(href="\S+")(.+)(>)(.+)(<\/a>)/', '$1'.$new_href.'$3$4'.$new_button_text.'$6', $link));
    }

    public static function replace_currency_symbol($output){
        if(is_null(self::$enable_replace_currency_symbol)) {
            self::$enable_replace_currency_symbol = WC_Admin_Settings::get_option( 'cwut_general_enable_replace_currency_symbol', 'no' );
            if(self::$enable_replace_currency_symbol === 'yes'){
                self::$currency_symbol_text = WC_Admin_Settings::get_option( 'cwut_general_currency_symbol_text', '' );
            }
        }//load only once on the first loop.

        if(self::$enable_replace_currency_symbol === 'yes' && ! empty(self::$currency_symbol_text)){
            return self::$currency_symbol_text;
        }
        else{
            return $output;
        }
    }

    public static function redirect_after_login($url, $user){
        if(is_null(self::$enable_redirect_after_login)) {
            self::$enable_redirect_after_login = WC_Admin_Settings::get_option( 'cwut_account_enable_redirect', 'no' );
            if(self::$enable_redirect_after_login === 'yes'){
                self::$custom_redirect_url = WC_Admin_Settings::get_option( 'cwut_account_custom_url', '' );
            }
        }//load only once on the first loop.

        if(self::$enable_redirect_after_login === 'yes' && ! empty(self::$custom_redirect_url)){
            return self::$custom_redirect_url;
        }
        else{
            return $url;
        }
    }

    public static function redirect_checkout_add_cart($url) {

        if(is_null(self::$enable_redirect_checkout)) {
            self::$enable_redirect_checkout = WC_Admin_Settings::get_option( 'cwut_checkout_enable_redirect', 'no' );
        }//load only once on the first loop.

        if(self::$enable_redirect_checkout === 'yes'){
            $url = get_permalink( get_option( 'woocommerce_checkout_page_id' ) );
        }

        return $url;
    }

    public static function clear_cart( $valid, $product_id, $quantity ) {

        if(is_null(self::$enable_clear_cart)) {
            self::$enable_clear_cart = WC_Admin_Settings::get_option( 'cwut_checkout_enable_clear_cart', 'no' );
        }//load only once on the first loop.

        if(self::$enable_clear_cart === 'yes'){
            if(!empty(WC()->cart->get_cart()) && $valid){
                foreach (WC()->cart->get_cart() as $cart_item_key => $values) {
                    unset(WC()->cart->cart_contents[$cart_item_key]);
                }
            }
        }

		return $valid;
    }

    public static function replace_single_product_add_to_cart_text($output){
        if(is_null(self::$enable_replace_single_add_to_cart_text)) {
            self::$enable_replace_single_add_to_cart_text = WC_Admin_Settings::get_option( 'cwut_product_enable_replace_single_add_to_cart_text', 'no' );
            if(self::$enable_replace_single_add_to_cart_text === 'yes'){
                self::$replace_single_add_to_cart_text = WC_Admin_Settings::get_option( 'cwut_product_single_add_to_cart_text', '');
                if(empty(self::$replace_single_add_to_cart_text)){
                    self::$replace_single_add_to_cart_text = __('Order Now','cwut'); //fix get_option default value wont work with expression
                }
            }
        }//load only once on the first loop.

        if(self::$enable_replace_single_add_to_cart_text === 'yes'){
            return self::$replace_single_add_to_cart_text;
        }
        else{
            return $output;
        }
    }

    public static function after_total_summary() {

        if(is_null(self::$enable_summary_message)) {
            self::$enable_summary_message = WC_Admin_Settings::get_option( 'cwut_checkout_enable_summary_message', 'no' );
            if(self::$enable_summary_message === 'yes'){
                self::$checkout_summary_message = WC_Admin_Settings::get_option( 'cwut_checkout_summary_message', '' );
                self::$checkout_summary_message_margin_top = WC_Admin_Settings::get_option( 'cwut_checkout_summary_message_margin_top', '0' );
                self::$checkout_summary_message_margin_bottom = WC_Admin_Settings::get_option( 'cwut_checkout_summary_message_margin_bottom', '0' );
            }
        }//load only once on the first loop.

        if(self::$enable_summary_message === 'yes' && ! empty(self::$checkout_summary_message) ){

            $message = self::$checkout_summary_message;
            $margin_top = intval(self::$checkout_summary_message_margin_top).'px';
            $margin_bottom = intval(self::$checkout_summary_message_margin_bottom).'px';

            $str = "
            <div class='cwut-after-total-summary' style='margin-top:$margin_top;margin-bottom:$margin_bottom;'>
              <div class='cwut-after-total-summary-text'>{$message}</div>
            </div>
        ";

            echo $str;
        }
    }

    public static function custom_shop_url($original) {
        if(is_null(self::$checkout_enable_custom_shop_url)) {
            self::$checkout_enable_custom_shop_url = WC_Admin_Settings::get_option( 'cwut_checkout_enable_custom_shop_url', 'no' );
            if(self::$checkout_enable_custom_shop_url === 'yes'){
                self::$checkout_custom_shop_url = WC_Admin_Settings::get_option( 'cwut_checkout_custom_shop_url', '' );
            }
        }//load only once on the first loop.

        if(self::$checkout_enable_custom_shop_url === 'yes'){
            return self::$checkout_custom_shop_url;
        }
        else {
            return $original;
        }

    }

    /**
     * Hide Woocommerce single product tab
     */
    public static function hideProductTab(){

    }

}
