<?php

/**
 * Plugin Name: marketplace
 * Plugin URI: https://fsbano.io/marketplace/
 * Description: marketplace
 * Version: 1.0.0
 * Author: Fabio Sbano
 * Author URI: https://fsbano.io
 * Text Domain: marketplace
 * Domain Path: /i18n/languages/
 * Requires at least: 5.6
 * Requires PHP: 7.0
 *
 * @package marketplace
 */

defined( 'ABSPATH' ) || exit;

final class Plugin {

  public static $_instance;

  public function __construct() {
		self::include();
	}

  /**
	 * Includes files
  */
	public function include() {
		include_once __DIR__ . '/includes/admin/class-wc-distributor.php';
		include_once __DIR__ . '/includes/admin/class-wc-postcode.php';
		include_once __DIR__ . '/includes/class-wc-product.php';
	}

  public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

}

Plugin::instance();

function input_mask()
{
	?>
	<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
	<script>
		$(document).ready(function(){
        $('#postcode').mask('00000-000');
        $('#calc_shipping_postcode').mask('00000-000');
        $('#billing_postcode').mask('00000-000');
        $('#billing_phone').mask('(99) 99999-9999');
    });
    </script>
  <?php
}

// Template
add_filter( 'template_include', 'TemplatePostCode::postcode_page', 99 );

// Input Field Mask
add_action( 'wp_head', 'input_mask' );

// Woolentor, Woolementor, Crocoblock::JetWooBuilder - Product Per PostCode 
add_action( 'pre_get_posts', 'WooCommerceProduct::PerPostcode' );
// WooCommerce - Product Per PostCode 
add_action( 'woocommerce_product_query', 'WooCommerceProduct::PerPostcode' );

// WooCommerceShopPostCode
session_start();
if ( empty($_SESSION["postcode"]) ) {
		add_action( 'init', 'WooCommerceProduct::WooCommerceShopPostCode');
} 

// Remove Related Products Output 
add_action( 'init', 'WooCommerceProduct::WoocommerceRelatedProducts' );

// Activation Hook
register_activation_hook(__FILE__, 'TemplatePostCode::create');

// Deactivation Hook
register_deactivation_hook( __FILE__, 'TemplatePostCode::delete');

add_action('admin_menu', 'marketplace_menu');

function marketplace_menu(){
  add_menu_page('All Products', 'All Products', 'manage_options', 'my-menu', '', '', 33);
  add_submenu_page('my-menu', 'All Products', 'All Products', 'manage_options', 'my-menu', 'init' );
}

function init() {

	if (isset($_POST['action'])) {
		if ($_POST['action'] == 'Adicionar Produto(s)') {
			foreach($_POST['Products'] as $value) {
				WooCommerceProduct::addAttributeByProductID($value, $_POST["zone_name"]);
			}
			echo '<center>Produto(s) adicionado(s) com sucesso</center>';
		}
		if ($_POST['action'] == 'Retirar Produto(s)') {
			foreach($_POST['Products'] as $value) {
				WooCommerceProduct::eraseAttributeByProductID($value, $_POST["zone_name"]);
			}
			echo '<center>Produto(s) removido(s) com sucesso</center>';
		}
	}

	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		);
	$all_products = new WP_Query( $args );

	?>
	<style> 
		input[type=button], input[type=submit], input[type=reset] {
			background-color: #4CAF50;
			border: none;
			color: white;
			padding: 16px 32px;
			text-decoration: none;
			margin: 4px 2px;
			cursor: pointer;
		}
	</style>

	<table border="0">
	<form method="post" action="#">
		<tr>
	<?php
		print "<td>";
		wc_distributor::getShippingZone($_POST["zone_name"]);
		print "<input name=\"action\" type=\"submit\" value=\"Selecionar Distribuidor\">";
		print "</td>";
	?>
		</tr>
		<p>
		
		<tr>
		<?php

		if ($_POST) {
			print "<td><input name=\"action\" type=\"submit\" value=\"Retirar Produto(s)\">";
			print "<input name=\"action\" type=\"submit\" value=\"Adicionar Produto(s)\"></td>";		
			
			foreach ( $all_products->posts as $value ) {
				$isChecked = 0;
				foreach(get_the_terms($value->ID, 'pa_zone_name') as $zone) {
					if ($_POST["zone_name"] == $zone->name ) {
						$isChecked = 1;
					}
				}
				print "<tr>";
				if ( $isChecked ) {
					print "<td><input type=\"checkbox\" name=\"Products[]\" value=$value->ID checked>";
				} else {
					print "<td><input type=\"checkbox\" name=\"Products[]\" value=$value->ID>";
				}
				print $value->post_title."</td>";
				print "</tr>";
			}
			?>
			</tr>
		</form>
	</table>
	<?php
		}
}
