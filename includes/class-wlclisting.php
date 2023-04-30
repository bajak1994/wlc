<?php
namespace App\WLCFORM;

/**
 * Represents a listing of form entries.
 *
 * @category Class
 * @package WLCForm
 */
class WLCListing {

	/**
	 * Register shortcode and ajax hooks.
	 */
	public function __construct() {
		add_shortcode( 'wlclisting', array( $this, 'render_listing' ) );
		add_action( 'wp_ajax_get_entry_details', array( $this, 'ajax_get_entry_details' ) );
		add_action( 'wp_ajax_nopriv_get_entry_details', array( $this, 'ajax_nopriv_get_entry_details' ) );
		add_action( 'wp_ajax_get_entries', array( $this, 'ajax_get_entries' ) );
		add_action( 'wp_ajax_nopriv_get_entries', array( $this, 'ajax_nopriv_get_entries' ) );
	}

	/**
	 * Render the listing.
	 * Needs to be public, otherwise add_shortcode() won't work.
	 *
	 * @return string HTML markup for the listing.
	 */
	public function render_listing() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '<p>You are not authorized to view the content of this page.</p>';
		}

		global $wpdb;
		$table_name    = $wpdb->prefix . WLCFORM_LEADS_TABLE;
		$first_page    = $wpdb->get_results( $wpdb->prepare( 'SELECT id, first_name, last_name, email, subject FROM %1s ORDER BY id DESC LIMIT 10', $table_name ) );
		$total_entries = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %1s', $table_name ) );

		$listing_data = array(
			'first_page'    => $first_page,
			'total_entries' => $total_entries,
		);

		$form = include_template( 'listing', $listing_data );

		return $form;
	}

	/**
	 * Get a page of entries.
	 *
	 * @param int $page The page number.
	 * @return string HTML markup for the page of entries.
	 */
	protected function get_entries_page( $page ) {
		$offset = ( $page - 1 ) * 10;

		global $wpdb;
		$table_name = $wpdb->prefix . WLCFORM_LEADS_TABLE;
		$entries    = $wpdb->get_results( $wpdb->prepare( 'SELECT id, first_name, last_name, email, subject FROM %1s ORDER BY id DESC LIMIT 10 OFFSET %1s ', $table_name, $offset ) );
		$page       = '';

		foreach ( $entries as $entry ) {
			$page .= include_template( 'listing-entry', array( 'entry' => $entry ) );
		}

		return $page;
	}

	/**
	 * Ajax callback for getting details of a single entry.
	 */
	public function ajax_get_entry_details() {
		check_ajax_referer( 'wlclisting', 'security' );

		$entry_id = isset( $_POST['entry_id'] ) ? absint( $_POST['entry_id'] ) : 0;

		global $wpdb;
		$table_name = $wpdb->prefix . WLCFORM_LEADS_TABLE;
		$entry      = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %1s WHERE id = %1s', $table_name, $entry_id ) );

		if ( $entry ) {
			$content = include_template( 'listing-entry-details', array( 'entry' => $entry ) );
			wp_send_json_success( $content );
		} else {
			wp_send_json_error( 'Invalid entry ID' );
		}
	}

	/**
	 * Get a page of entries via ajax.
	 */
	public function ajax_get_entries() {
		check_ajax_referer( 'wlclisting', 'security' );

		if ( ! isset( $_POST['page'] ) || empty( $_POST['page'] ) ) {
			wp_send_json_error( 'Something went wrong. Please, try again later.' );
		}

		$entries_page = $this->get_entries_page( sanitize_text_field( wp_unslash( $_POST['page'] ) ) );

		if ( $entries_page ) {
			wp_send_json_success( $entries_page );
		} else {
			wp_send_json_error( 'Something went wrong. Please, try again later.' );
		}
	}

	/**
	 * AJAX handler for retrieving the details of a single listing entry for unauthorized users.
	 */
	public function ajax_nopriv_get_entry_details() {
		wp_send_json_error( 'You are not authorized to view the content of this page.' );
	}

	/**
	 * AJAX handler for retrieving a list of all listing entries for unauthorized users.
	 */
	public function ajax_nopriv_get_entries() {
		wp_send_json_error( 'You are not authorized to view the content of this page.' );
	}

}
