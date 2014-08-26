<?php
/**
 * class-wrp-categories-admin.php
 *
 * Copyright (c) "eggemplo" Antonio Blanco www.eggemplo.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 * 
 * @author Antonio Blanco
 * @package woo-role-pricing
 * @since 2.1
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Category product admin handlers. 
*/
class WRP_Categories_Admin {

	/**
	 * Sets up the init action.
	 */
	public static function init() {

		// when adding a new category
		add_action( "product_cat_add_form_fields", array( __CLASS__, 'taxonomy_add_form_fields' ) );
		
		// when editing a category
		add_action( "product_cat_edit_form", array( __CLASS__, 'taxonomy_edit_form' ), 10, 2 );
		
		// Save for a new category
		add_action( "created_product_cat", array( __CLASS__, 'created_taxonomy' ), 10, 2 );
		
		// Save for a category
		add_action( "edited_product_cat", array( __CLASS__, 'edited_taxonomy' ), 10, 2 );
		
	}

	/**
	 * WRP fields before the "Add New Category" button.
	 * 
	 * @param string $taxonomy
	 */
	public static function taxonomy_add_form_fields( $taxonomy ) {
		self::panel( $taxonomy );
		
	}

	/**
	 * Hook in wp-admin/edit-tag-form.php - add fields
	 * 
	 * @param string $tag
	 * @param string $taxonomy
	 */
	public static function taxonomy_edit_form( $tag, $taxonomy ) {
		self::panel( $tag, $taxonomy );
		
	}

	/**
	 * Renders our  panel.
	 */
	public static function panel( $tag = null ) {

		global $post, $wpdb;
		global $wp_roles;
		
		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}
		
		$output        = '';
		$term_id       = isset( $tag->term_id ) ? $tag->term_id : null;

		$output .= '<div class="form-field">';

		$pricing_options = array();
		foreach ( $wp_roles->role_objects as $role ) {
			$pricing_options['value_' . $role->name] = get_woocommerce_term_meta($term_id, 'role_pricing_value_' . $role->name, true);
		}
		
		$output .= '<div class="options_role"  style="border: 1px solid #ccc; padding:10px;">';
		$output .=  '<h4>' . __( 'Woocommerce Role Pricing', WOO_ROLE_PRICING_DOMAIN ) . '</h4>';
		$output .= '<p class="description">';
		$output .= __( 'Leave empty if no custom role discount should be applied to this category.', WOO_ROLE_PRICING_DOMAIN );
		$output .= '</p>';
		
		foreach ( $wp_roles->role_objects as $role ) {
			$output .= '<p>';
			$output .= '<label style="width:120px;float:left;">' . ucwords($role->name) . '</label>';
			$output .= '<input type="text" style="width:auto;" size="10" name="role_pricing_value_' . $role->name . '" value="' . @$pricing_options['value_' . $role->name] . '" />';
			$output .= '</p>';
		}

		$output .= '</div>';
		
		$output .= '</div>'; // .form-field
		echo $output;

	}

	/**
	 * Save WRP values for a new category
	 * @param int $term_id
	 * @param int $tt_id
	 */
	public static function created_taxonomy( $term_id, $tt_id ) {
		self::edited_taxonomy( $term_id, $tt_id );
	}

	/**
	 * Save category WRP values
	 * @param int $term_id term ID
	 * @param int $tt_id taxonomy ID
	 */
	public static function edited_taxonomy( $term_id, $tt_id ) {
		global $wp_roles;
		
		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}
		foreach ( $wp_roles->role_objects as $role ) {
			update_woocommerce_term_meta( $term_id, 'role_pricing_value_' . $role->name, ( isset($_POST['role_pricing_value_' . $role->name]) && ( $_POST['role_pricing_value_' . $role->name] !== "" ) ) ? trim($_POST['role_pricing_value_' . $role->name]) : '' );
		}
	}


}
WRP_Categories_Admin::init();
