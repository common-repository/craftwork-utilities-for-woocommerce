<?php
/**
 * Project : craftwork-wc-utilities
 */

class CWUT_Variation {

    private static $enable_custom_fields = null; //cwut_product_variation_enable_custom_fields

    private static $custom_field_1_label = null; //cwut_product_variation_custom_field_1_label
    private static $custom_field_1_show_label = null; //cwut_product_variation_custom_field_1_show_label
    private static $custom_field_1_type = null; //cwut_product_variation_custom_field_1_type
    private static $custom_field_1_list = null; //cwut_product_variation_custom_field_1_list
    private static $custom_field_1_default = null; //cwut_product_variation_custom_field_1_default

    private static $custom_field_2_label = null; //cwut_product_variation_custom_field_2_label
    private static $custom_field_2_show_label = null; //cwut_product_variation_custom_field_2_show_label
    private static $custom_field_2_type = null; //cwut_product_variation_custom_field_2_type
    private static $custom_field_2_list = null; //cwut_product_variation_custom_field_2_list
    private static $custom_field_2_default = null; //cwut_product_variation_custom_field_2_default

    private static $custom_field_3_label = null; //cwut_product_variation_custom_field_3_label
    private static $custom_field_3_show_label = null; //cwut_product_variation_custom_field_3_show_label
    private static $custom_field_3_type = null; //cwut_product_variation_custom_field_3_type
    private static $custom_field_3_list = null; //cwut_product_variation_custom_field_3_list
    private static $custom_field_3_default = null; //cwut_product_variation_custom_field_3_default

    /** Add All Variation Related Hooks **/
    public static function init() {

        if(self::is_custom_fields_enable()){
            add_action( 'woocommerce_variation_options_pricing', [__CLASS__, 'add_custom_field_to_variations'], 10, 3 );
            add_action( 'woocommerce_save_product_variation', [__CLASS__, 'save_custom_field_value'], 10, 2 );
            add_filter( 'woocommerce_available_variation', [__CLASS__, 'display_custom_fields'] );
            add_action( 'wp_enqueue_scripts', [__CLASS__, 'enqueue_frontend_scripts'], 30 );
            add_action( 'admin_enqueue_scripts', [__CLASS__, 'enqueue_backend_scripts'],20,1 );
        }
    }

    private static function is_custom_fields_enable(){
        if(is_null(self::$enable_custom_fields)) {
            self::$enable_custom_fields = WC_Admin_Settings::get_option( 'cwut_product_variation_enable_custom_fields', 'no' );
        }

        return self::$enable_custom_fields !== 'no' ? true : false;
    }

    private static function get_field_data (){

        if(is_null(self::$custom_field_1_label)){
            self::$custom_field_1_label = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_1_label', false );
            if(self::$custom_field_1_label !== false && ! empty(trim(self::$custom_field_1_label)) ){
                self::$custom_field_1_show_label = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_1_show_label', 'no' );
                self::$custom_field_1_type = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_1_type', 'text' );
                self::$custom_field_1_list = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_1_list', null );
                self::$custom_field_1_default = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_1_default', null );
            }
        }

        if(is_null(self::$custom_field_2_label)){
            self::$custom_field_2_label = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_2_label', false );
            if(self::$custom_field_2_label !== false && ! empty(trim(self::$custom_field_2_label)) ){
                self::$custom_field_2_show_label = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_2_show_label', 'no' );
                self::$custom_field_2_type = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_2_type', 'text' );
                self::$custom_field_2_list = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_2_list', null );
                self::$custom_field_2_default = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_2_default', null );
            }
        }

        if(is_null(self::$custom_field_3_label)){
            self::$custom_field_3_label = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_3_label', false );
            if(self::$custom_field_3_label !== false && ! empty(trim(self::$custom_field_3_label)) ){
                self::$custom_field_3_show_label = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_3_show_label', 'no' );
                self::$custom_field_3_type = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_3_type', 'text' );
                self::$custom_field_3_list = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_3_list', null );
                self::$custom_field_3_default = WC_Admin_Settings::get_option( 'cwut_product_variation_custom_field_3_default', null );
            }
        }

    }

    public static function add_custom_field_to_variations($loop, $variation_data, $variation) {

        self::get_field_data();

        if(self::$custom_field_1_label !== false && ! empty(trim(self::$custom_field_1_label)) ){
            self::render_custom_field($variation, $loop, 1);
        }

        if(self::$custom_field_2_label !== false && ! empty(trim(self::$custom_field_2_label)) ){
            self::render_custom_field($variation, $loop, 2);
        }

        if(self::$custom_field_3_label !== false && ! empty(trim(self::$custom_field_3_label)) ){
            self::render_custom_field($variation, $loop, 3);
        }
    }

    public static function render_custom_field($variation, $loop, $field_number) {

        $field = 'cwut_custom_field_'.$field_number;
        $field_loop = $field.'[' . $loop . ']';
        $label = self::${ 'custom_field_'.$field_number.'_label' };
        $type = self::${ 'custom_field_'.$field_number.'_type' };
        $list = self::${ 'custom_field_'.$field_number.'_list' };
        $default = self::${ 'custom_field_'.$field_number.'_default' };
        $list_arr = array();
        $list_options = array();

        if(! is_null ($list) && !empty(trim($list)) ){
            $list_arr = explode(',', $list);
            for($i = 0; $i < count($list_arr); $i++) {
                $list_arr[$i] = trim($list_arr[$i]);
                $list_options[$list_arr[$i]] = $list_arr[$i];
            }
        }

        switch($type){
            case 'text':
                woocommerce_wp_text_input( array(
                        'id' => $field_loop,
                        'name' => $field_loop,
                        'wrapper_class' => 'form-row form-row-full',
                        'label' => $label,
                        'value' => get_post_meta( $variation->ID, $field, true )
                    )
                );
                break;

            case 'textarea':
                woocommerce_wp_textarea_input(
                    array(
                        'id'            => $field_loop,
                        'name'          => $field_loop,
                        'value'         => get_post_meta( $variation->ID, $field, true ),
                        'label'         => $label,
                        'wrapper_class' => 'form-row form-row-full',
                    )
                );
                break;

            case 'radio':

                if(empty ($list_options)){
                    break;
                }

                $value = get_post_meta( $variation->ID, $field, true );
                if(is_null($value) || empty($value)){
                    $value = $default;
                }
                woocommerce_wp_radio( array(
                    'id'            => $field_loop,
                    'name'          => $field_loop,
                    'label'         => $label,
                    'options'       => $list_options,
                    'value'         => $value,
                    'wrapper_class' => 'form-row form-row-full craftwork-form-radio',
                ));
                break;

            case 'select':

                if(empty ($list_options)){
                    break;
                }

                $value = get_post_meta( $variation->ID, $field, true );
                if(is_null($value) || empty($value)){
                    $value = $default;
                }
                woocommerce_wp_select(
                    array(
                        'id'            => $field_loop,
                        'name'          => $field_loop,
                        'value'         => $value,
                        'label'         => $label,
                        'options'       => $list_options,
                        'wrapper_class' => 'form-row form-row-full',
                    )
                );
                break;
        }
    }

    public static function save_custom_field_value($variation_id, $i){
        $field_1 = 'cwut_custom_field_1';
        $field_2 = 'cwut_custom_field_2';
        $field_3 = 'cwut_custom_field_3';

        $allowed_html = array(
            'a' => array(
                'href' => array(),
                'title' => array()
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
        );

        self::get_field_data();

        if(self::$custom_field_1_label !== false && ! empty(trim(self::$custom_field_1_label)) ){
            $custom_field_1 = $_POST[$field_1][$i];
            if ( isset( $custom_field_1 ) ) {
                update_post_meta( $variation_id, $field_1, wp_kses( $custom_field_1, $allowed_html ) );
            }
        }

        if(self::$custom_field_2_label !== false && ! empty(trim(self::$custom_field_2_label)) ){
            $custom_field_2 = $_POST[$field_2][$i];
            if ( isset( $custom_field_2 ) ) {
                update_post_meta( $variation_id, $field_2, wp_kses( $custom_field_2, $allowed_html ) );
            }
        }

        if(self::$custom_field_3_label !== false && ! empty(trim(self::$custom_field_3_label)) ){
            $custom_field_3 = $_POST[$field_3][$i];
            if ( isset( $custom_field_3 ) ) {
                update_post_meta( $variation_id, $field_3, wp_kses( $custom_field_3, $allowed_html ) );
            }
        }
    }

    public static function display_custom_fields($variations){

        $field_1 = 'cwut_custom_field_1';
        $field_2 = 'cwut_custom_field_2';
        $field_3 = 'cwut_custom_field_3';

        self::get_field_data();

        $variations['cwut_custom_field_enable'] = self::$enable_custom_fields;

        if(self::$custom_field_1_label !== false && ! empty(trim(self::$custom_field_1_label)) ){

            $custom_field_1_value = trim(get_post_meta( $variations[ 'variation_id' ], $field_1, true ));
            if(empty($custom_field_1_value) && ! is_null(self::$custom_field_1_default)){
                $custom_field_1_value = self::$custom_field_1_default;
            }

            if(!empty($custom_field_1_value)){
                $variations[$field_1.'_enable'] = 'yes';
                $variations[$field_1] = '<span class="cwut_custom_field_data cwut_custom_field_1_data">'.$custom_field_1_value.'</span>';
                if(self::$custom_field_1_show_label === 'yes'){
                    $variations[$field_1] = '<span class="cwut_custom_field_label cwut_custom_field_1_label">'.self::$custom_field_1_label.' : </span>'.$variations[$field_1];
                }

                $variations[$field_1] = '<div class="cwut_custom_field cwut_custom_field_1">'.$variations[$field_1].'</div>';
            }
        }

        if(self::$custom_field_2_label !== false && ! empty(trim(self::$custom_field_2_label)) ){

            $custom_field_2_value = trim(get_post_meta( $variations[ 'variation_id' ], $field_2, true ));
            if(empty($custom_field_2_value) && ! is_null(self::$custom_field_2_default)){
                $custom_field_2_value = self::$custom_field_2_default;
            }

            if(!empty($custom_field_2_value)){
                $variations[$field_2.'_enable'] = 'yes';
                $variations[$field_2] = '<span class="cwut_custom_field_data cwut_custom_field_2_data">'.$custom_field_2_value.'</span>';
                if(self::$custom_field_2_show_label === 'yes'){
                    $variations[$field_2] = '<span class="cwut_custom_field_label cwut_custom_field_2_label">'.self::$custom_field_2_label.' : </span>'.$variations[$field_2];
                }

                $variations[$field_2] = '<div class="cwut_custom_field cwut_custom_field_1">'.$variations[$field_2].'</div>';
            }
        }

        if(self::$custom_field_3_label !== false && ! empty(trim(self::$custom_field_3_label)) ){

            $custom_field_3_value = trim(get_post_meta( $variations[ 'variation_id' ], $field_3, true ));
            if(empty($custom_field_3_value) && ! is_null(self::$custom_field_3_default)){
                $custom_field_3_value = self::$custom_field_3_default;
            }

            if(!empty($custom_field_3_value)){
                $variations[$field_3.'_enable'] = 'yes';
                $variations[$field_3] = '<span class="cwut_custom_field_data cwut_custom_field_3_data">'.$custom_field_3_value.'</span>';
                if(self::$custom_field_3_show_label === 'yes'){
                    $variations[$field_3] = '<span class="cwut_custom_field_label cwut_custom_field_3_label">'.self::$custom_field_3_label.' : </span>'.$variations[$field_3];
                }

                $variations[$field_3] = '<div class="cwut_custom_field cwut_custom_field_1">'.$variations[$field_3].'</div>';
            }
        }

        return $variations;
    }

    public static function enqueue_frontend_scripts() {
        if(is_product()){
            wp_enqueue_style('craftwork-wc-variation-front-css',CWUT_DIR_URL.'assets/css/craftwork-wc-variation-front.css',array(),CWUT_VERSION);
            wp_enqueue_script( 'craftwork-wc-variation-js', CWUT_DIR_URL . 'assets/js/craftwork-wc-variation.js', array('jquery'), CWUT_VERSION, true );
        }

    }

    public static function enqueue_backend_scripts($hook){
        if($hook === 'post.php'){
            wp_enqueue_style('craftwork-wc-variation-front-css',CWUT_DIR_URL.'assets/css/craftwork-wc-variation-back.css',array(),CWUT_VERSION);
        }
    }
}
