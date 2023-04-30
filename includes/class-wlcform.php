<?php

namespace App\WLCFORM;

/**
 * Class for rendering and manipulating the form.
 *
 * @category Class
 * @package WLCForm
 */
class WLCForm {

	/**
	 * Register shortcode and ajax hooks.
	 */
	public function __construct() {
		add_shortcode( 'wlcform', array( $this, 'render_form' ) );
		add_action( 'wp_ajax_wlcform_submit', array( $this, 'form_submit' ) );
		add_action( 'wp_ajax_nopriv_wlcform_submit', array( $this, 'form_submit' ) );
	}

	/**
	 * Render the form.
	 * Needs to be public, otherwise add_shortcode() won't work.
	 *
	 * @return string HTML markup for the feedback form.
	 */
	public function render_form() {
		$current_user = wp_get_current_user();
		$nonce        = wp_create_nonce( 'wlcform' );
		$user_data    = array(
			'first_name' => $current_user->user_firstname,
			'last_name'  => $current_user->user_lastname,
			'email'      => $current_user->user_email,
			'nonce'      => $nonce,
		);

		$form = include_template( 'form', $user_data );

		return $form;
	}

	/**
	 * Process the feedback form.
	 * Needs to be public, otherwise AJAX won't work.
	 */
	public function form_submit() {
		if ( ! wp_doing_ajax() ) {
			wp_die( esc_html( __( 'This method can only be called via AJAX.', 'wlcform' ) ) );
		}

		try {
			$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
			$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
			$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
			$subject    = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
			$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

			if ( ! isset( $_POST['nonce'] ) || empty( $_POST['nonce'] ) ) {
				wp_send_json_error( esc_html( __( 'Something went wrong. Please, try again later.', 'wlcform' ) ) );
			}

			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wlcform' ) ) {
				wp_send_json_error( esc_html( __( 'Something went wrong. Please, try again later.', 'wlcform' ) ) );
			}

			if ( empty( $first_name ) || empty( $last_name ) || empty( $email ) || empty( $subject ) || empty( $message ) ) {
				wp_send_json_error( esc_html( __( 'All fields are required.', 'wlcform' ) ) );
			}

			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				wp_send_json_error( esc_html( __( 'Invalid email address.', 'wlcform' ) ) );
			}

			$feedback_data = array(
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'email'      => $email,
				'subject'    => $subject,
				'message'    => $message,
			);

			global $wpdb;
			$result = $wpdb->query(
				$wpdb->prepare(
					'INSERT INTO %1s (first_name, last_name, email, subject, message) VALUES (%s, %s, %s, %s, %s)',
					$wpdb->prefix . WLCFORM_LEADS_TABLE,
					$first_name,
					$last_name,
					$email,
					$subject,
					$message
				)
			);

			if ( false === $result ) {
				wp_send_json_error( esc_html( __( 'Something went wrong. Please, try again later.', 'wlcform' ) ) );
			} else {
				wp_send_json_success( esc_html( __( 'Thank you for sending us your feedback.', 'wlcform' ) ) );
			}

			wp_die();

		} catch ( Exception $e ) {
			wp_send_json_error( esc_html( __( 'Something went wrong. Please, try again later.', 'wlcform' ) ) );
			wp_die();
		}
	}
}
