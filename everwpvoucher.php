<?php
/*
Plugin URI: https://www.team-ever.com/produit/woocommerce-plugin-de-code-promo-sur-premiere-commande
Plugin Name: everwpvoucher
Description: Use this plugin for voucher creation on first orders
Version: 1.2.1
Author: Ever Team
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: everwpvoucher
Domain Path:  /languages
Author URI: https://www.team-ever.com/
License: GPL2
*/
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class EverWpVoucher
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Plugin Details
        $plugin = new stdClass;
        $plugin->name = 'everwpvoucher'; // Plugin Folder
        $plugin->displayName = 'everwpvoucher'; // Plugin Name
        $plugin->version = '1.2.2';
        $plugin->folder = plugin_dir_path(__FILE__);
        $plugin->url = plugin_dir_url(__FILE__);
    }
}

/**
 * Loads plugin textdomain
 */
function everwpvoucher_plugins_loaded()
{
    load_plugin_textdomain( 'everwpvoucher', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'everwpvoucher_plugins_loaded', 0 );
add_action( 'admin_menu', 'everwpvoucher_add_admin_menu' );
add_action( 'admin_init', 'everwpvoucher_settings_init' );


function everwpvoucher_add_admin_menu()
{
    add_options_page( 'Ever WP Voucher', 'Ever WP Voucher', 'manage_options', 'ever_wp_voucher', 'everwpvoucher_options_page' );
}

function everwpvoucher_settings_init()
{
    register_setting( 'everwpvoucherPage', 'everwpvoucher_settings' );

    if (class_exists('WooCommerce')) {
        add_settings_section(
            'everwpvoucher_everwpvoucherPage_section', 
            __( 'WooCommerce first vouchers settings', 'everwpvoucher' ), 
            'everwpvoucher_settings_section_callback', 
            'everwpvoucherPage'
        );

        add_settings_field( 
            'everwpvoucher_prefix', 
            __( 'Voucher prefix', 'everwpvoucher' ), 
            'everwpvoucher_prefix_render', 
            'everwpvoucherPage', 
            'everwpvoucher_everwpvoucherPage_section' 
        );

        add_settings_field( 
            'everwpvoucher_amount', 
            __( 'Voucher amount', 'everwpvoucher' ), 
            'everwpvoucher_amount_render', 
            'everwpvoucherPage', 
            'everwpvoucher_everwpvoucherPage_section' 
        );

        add_settings_field( 
            'everwpvoucher_discount', 
            __( 'Discount type', 'everwpvoucher' ), 
            'everwpvoucher_discount_type_render', 
            'everwpvoucherPage', 
            'everwpvoucher_everwpvoucherPage_section' 
        );

        add_settings_field(
            'everwpvoucher_subject', 
            __( 'Mail subject', 'everwpvoucher' ), 
            'everwpvoucher_subject_type_render', 
            'everwpvoucherPage', 
            'everwpvoucher_everwpvoucherPage_section' 
        );

        add_settings_field( 
            'everwpvoucher_content', 
            __( 'Mail content', 'everwpvoucher' ), 
            'everwpvoucher_content_type_render', 
            'everwpvoucherPage', 
            'everwpvoucher_everwpvoucherPage_section' 
        );
    }
}

function everwpvoucher_prefix_render()
{
  $options = get_option( 'everwpvoucher_settings' );
  ?>
  <input type='text' name='everwpvoucher_settings[everwpvoucher_prefix]' value='<?php echo $options['everwpvoucher_prefix']; ?>'>
  <?php
}

function everwpvoucher_amount_render()
{
  $options = get_option( 'everwpvoucher_settings' );
  ?>
  <input type='number' name='everwpvoucher_settings[everwpvoucher_amount]' value='<?php echo $options['everwpvoucher_amount']; ?>'>
  <?php
}

function everwpvoucher_discount_type_render()
{
    $options = get_option( 'everwpvoucher_settings' );
    ?>
    <select name='everwpvoucher_settings[everwpvoucher_discount_type]'>
        <option value="fixed_cart" <?php if ($options['everwpvoucher_amount'] == 'fixed_cart') { echo 'selected'; } ?>>
            <?php _e('fixed_cart'); ?>
        </option>
        <option value="percent" <?php if ($options['everwpvoucher_amount'] == 'percent') { echo 'selected'; } ?>>
            <?php _e('percent'); ?>
        </option>
    </select>
    <?php
}

function everwpvoucher_subject_render()
{
  $options = get_option( 'everwpvoucher_settings' );
  ?>
  <input type='text' name='everwpvoucher_settings[everwpvoucher_subject]' value='<?php echo $options['everwpvoucher_subject']; ?>'>
  <?php
}

function everwpvoucher_content_type_render()
{
    echo __('Use [everwpvoucher] shortcode for coupon code', 'everwpvoucher');
    $options = get_option( 'everwpvoucher_settings' );
    if (isset($options['everwpvoucher_content']) && $options['everwpvoucher_content']) {
      $default = $options['everwpvoucher_content'];
    } else {
      $default = '';
    }
    $settings = array( 
        'quicktags' => array( 'buttons' => 'strong,em,del,ul,ol,li,close' ),
        'textarea_name' => 'everwpvoucher_settings[everwpvoucher_content]'
    );
    wp_editor( $default, 'everwpvoucher_content', $settings );
}

function everwpvoucher_settings_section_callback()
{
    if (!class_exists('WooCommerce')) {
        echo __('WooCommerce settings wont work until you install this plugin', 'everwpvoucher');
    } else {
      echo __( 'Please set form settings to configure default vouchers', 'everwpvoucher');
    }
}

function everwpvoucher_options_page()
{ 
  // Useful : use var_dump($options) for seeing array of settings values
  $options = get_option( 'everwpvoucher_settings' );
  // var_dump($options);
  ?>
  <div class="jumbotron">
    <a href="https://www.team-ever.com/contact" target="_blank"><img src="https://www.team-ever.com/wp-content/uploads/2016/08/Logo-full.png" style="float:left;"></a>
    <h1><?php _e('Voucher settings'); ?></h1> 
    <p><?php _e('Please set form settings to configure default voucher'); ?></p>
    <p><?php _e('Use [everwpvoucher] on mail content to show coupon code'); ?></p>
    <p><a href="https://www.team-ever.com/contact" target="_blank"><?php _e('Feel free contact us for support or updates'); ?></a></p>
  </div>
  <style type="text/css">
    .everul li{
        list-style-type: circle;
        list-style-position: inside;
    }
  </style>
  <form action='options.php' method='post'>
    <?php
    settings_fields( 'everwpvoucherPage' );
    do_settings_sections( 'everwpvoucherPage' );
    submit_button();
    ?>
  </form>
  <!-- Bootstrap default design -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <?php
}

/**
 * Programmaticaly close order if free and create voucher if not
 * @see https://www.team-ever.com/woocommerce-un-code-promo-sur-la-premiere-commande/
 */
function ever_voucher_or_free( $order_id ) {
    $order = new WC_Order( $order_id );
    if ($order->get_total() == 0) {
        if ($options['everwpvoucher_complete_free_orders']) {
            $order->update_status('completed');
        }
    } else {
        if (!get_user_meta($order->get_customer_id(), 'first_voucher', true)
        ) {
            $options = get_option( 'everwpvoucher_settings' );
            $code = uniqid();
            $coupon_code = $options['everwpvoucher_prefix'].$code;
                      
            $coupon = array(
              'post_title' => $coupon_code,
              'post_content' => '',
              'post_status' => 'publish',
              'post_author' => 1,
              'post_type'   => 'shop_coupon'
            );
                      
            $new_coupon_id = wp_insert_post($coupon);
            update_post_meta( $new_coupon_id, 'discount_type', $options['everwpvoucher_discount_type']);
            update_post_meta( $new_coupon_id, 'coupon_amount', $options['everwpvoucher_amount']);
            update_post_meta( $new_coupon_id, 'individual_use', 'yes');
            update_post_meta( $new_coupon_id, 'usage_limit_per_user', '1');
            update_post_meta( $new_coupon_id, 'product_ids', '' );
            update_post_meta( $new_coupon_id, 'exclude_product_ids', '');
            update_post_meta( $new_coupon_id, 'usage_limit', '1');
            update_post_meta( $new_coupon_id, 'expiry_date', '');
            update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes');
            update_post_meta( $new_coupon_id, 'free_shipping', 'no');
            update_post_meta( $new_coupon_id, 'customer_email', $order->get_billing_email());
            update_user_meta($order->get_customer_id(), 'first_voucher', true);
            $voucher_msg = '<div class="ever_voucher">';
            $voucher_msg .= '<p><strong>';
            $voucher_msg .= 'Merci pour votre première commande !';
            $voucher_msg .= '</strong></p>';
            $voucher_msg .= '<p><strong>';
            $voucher_msg .= 'Votre code promo : '.$coupon_code.$amount;
            $voucher_msg .= ' d\'une valeur de '.$amount.'%';
            $voucher_msg .= '</strong></p>';
            $voucher_msg .= '</div>';
            // Prepare email
            if (isset($options['everwpvoucher_content'])
                && $options['everwpvoucher_content']
            ) {
                $content = $options['everwpvoucher_content'];
            } else {
                $content = '';
            }
            $content = str_replace('[everwpvoucher]', $coupon_code, $content);
            if (isset($options['everwpvoucher_subject']) && $options['everwpvoucher_subject']) {
              $subject = $options['everwpvoucher_subject'];
            } else {
              $subject = 'Votre code promo sur '.$blog_title;
            }
            $to = $order->get_billing_email();
            $headers = array('Content-Type: text/html; charset=UTF-8');
            // Send email
            wp_mail( $to, $subject, $content, $headers );
            // Show coupon
            echo $voucher_msg;
        }
    }
}
add_action( 'woocommerce_thankyou', 'ever_voucher_or_free', 10, 1 );