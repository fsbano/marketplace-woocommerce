<?php

class WooCommerceProduct {

	function PerPostcode($q) {
		session_start();
		if ( ! is_admin() ) {
			if ( $q->is_post_type_archive() || is_product_category() ) {
				$tax_query = (array) $q->get( 'tax_query' );
				$tax_query[] = array(
					'taxonomy' => 'pa_zone_name',
					'field' => 'slug',
					'terms' => array( $_SESSION["zone_name"] ),
					'operator' => 'IN'
				);
				$q->set( 'tax_query', $tax_query );				
			}
		}
	}

	function WoocommerceRelatedProducts() {
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );	
	}

	function WooCommerceShopPostCode() {
		session_start();
		if ( ! preg_match('/postcode/', $_SERVER["REQUEST_URI"]) && 
				 ! preg_match('/wp-admin/', $_SERVER["REQUEST_URI"]) && 
				 ! preg_match('/wp-login/', $_SERVER["REQUEST_URI"]) && 
				 ! isset($_SESSION["postcode"])
			 )
			 {
					if ( ! preg_match('/postcode/', $_SERVER["REQUEST_URI"]) ) {
						if ( ! is_super_admin() ) {
							wp_redirect(home_url()."/postcode/");
							exit ();
						}
					}
			}
	}

	function eraseAttributeByProductID($id, $zone_name) {
		
		$product_attributes = [];
		foreach(get_the_terms( $id, 'pa_zone_name') as $zone) {
			if ( $zone_name != $zone->name ) {
				wp_set_object_terms( $id , $zone->name, 'pa_zone_name', false );
				$product_attributes["pa_zone_name"] = $zone->name;	
			}
		}

		if (empty($product_attributes)) {
			wp_set_object_terms( $id , '', 'pa_zone_name', false );
			$product_attributes["pa_zone_name"] = '';
		}

		update_post_meta ( $id ,'_product_attributes', $product_attributes );
	}

	function addAttributeByProductID($id, $zone_name) {

		$product_attributes = [];
		foreach(get_the_terms( $id, 'pa_zone_name') as $zone) {
			wp_set_object_terms( $id , $zone->name, 'pa_zone_name', true );
			$product_attributes["pa_zone_name"] = $zone->name;
		}

		// Add
		wp_set_object_terms( $id , $zone_name, 'pa_zone_name', true );
		$product_attributes["pa_zone_name"] = $zone_name;

		update_post_meta ( $id ,'_product_attributes', $product_attributes );
	
	}
	
} 

?>
