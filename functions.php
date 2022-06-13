<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */
// add_action('woocommerce_after_main_content','woocamp_close_div',50);
// function woocamp_close_div(){
// 	if (is_product()&&has_term(18,'product_cat')){
// 		return;
// 	}
// 	echo '</div>';
// }

//remove breadcrumbs
/**
 * Remove WooCommerce breadcrumbs 
 */
/**
 * Remove the breadcrumbs 
 */
/**
 * Remove breadcrumbs for Storefront theme
 */
// add_action( 'init', 'bc_remove_storefront_breadcrumbs');

// function bc_remove_storefront_breadcrumbs() {
//   remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
// }

// add_action('woocommerce_after_template_part','woocommerce_breadcrumb', 20)??
//add sale text to prodcut loop page
add_action('woocommerce_before_shop_loop','woocamp_add_sale_text',10);
function woocamp_add_sale_text(){
	echo '<div class="sale-text">';
	echo '<h2>Summer sales! all hoodies and t shirt 20%off!</h2>';
	echo '<p>hurry! sale end Agust 31</p>';
	echo '</div>';
}

//add content after loop item**//
add_action('woocommerce_after_shop_loop_item','woocamp_add_content_after_loop_item',10);
function woocamp_add_content_after_loop_item(){
	global $product;
	//add only to sale products
	if ($product->is_on_sale()){
		echo '<p class="after-loop-item-text">On sale now!</p>';
	}else{
        echo '<p class="after-loop-item-text">Not on sale now!</p>';

	}
}
//single product title
add_filter('the_title','woocamp_single_product_page_title',10,1);
function woocamp_single_product_page_title($title){
	if((is_product()&& in_the_loop()&& has_term(array('hoodies','tshirts'),'product_cat'))){
		$title='<span class="filtered_title">WooCamp Special</span>' . ' - ' .$title;
		return $title;
	
	}
	return $title;
}


//update all title on shops (archive) page

remove_action('woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10);

add_action('woocommerce_shop_loop_item_title','woocamp_shop_loop_title',10,1);
function woocamp_shop_loop_title($title){
	if(has_term(18,'product_cat')){
		$additional_text='New!';
		echo '<h2 class="woocommerce-loop-product__title">','<span class="new">' .$additional_text .'</span>'.get_the_title().'</h2>';

	}else{
		echo '<h2 class="woocommerce-loop-product__title">' . get_the_title() .'</h2>';
	}
}