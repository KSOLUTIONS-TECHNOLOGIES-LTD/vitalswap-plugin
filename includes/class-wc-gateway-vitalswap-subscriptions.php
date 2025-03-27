<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Gateway_VitalSwap_Subscriptions
 */
class WC_Gateway_VitalSwap_Subscriptions extends WC_Gateway_VitalSwap {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();

		if ( class_exists( 'WC_Subscriptions_Order' ) ) {

			add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );

		}
	}

	/**
	 * Process a trial subscription order with 0 total.
	 *
	 * @param int $order_id WC Order ID.
	 *
	 * @return array|void
	 */
	public function process_payment( $order_id ) {

		$order = wc_get_order( $order_id );

		// Check for trial subscription order with 0 total.
		if ( $this->order_contains_subscription( $order ) && $order->get_total() == 0 ) {

			$order->payment_complete();

			$order->add_order_note( __( 'This subscription has a free trial, reason for the 0 amount', 'woo-VitalSwap' ) );

			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);

		} else {

			return parent::process_payment( $order_id );

		}

	}

	/**
	 * Process a subscription renewal.
	 *
	 * @param float    $amount_to_charge Subscription payment amount.
	 * @param WC_Order $renewal_order Renewal Order.
	 */
	public function scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {

		$response = $this->process_subscription_payment( $renewal_order, $amount_to_charge );

		if ( is_wp_error( $response ) ) {

			$renewal_order->update_status( 'failed', sprintf( __( 'VitalSwap Transaction Failed (%s)', 'woo-VitalSwap' ), $response->get_error_message() ) );

		}

	}

	/**
	 * Process a subscription renewal payment.
	 *
	 * @param WC_Order $order  Subscription renewal order.
	 * @param float    $amount Subscription payment amount.
	 *
	 * @return bool|WP_Error
	 */
	public function process_subscription_payment( $order, $amount ) {

		$order_id = $order->get_id();

		$VitalSwap_token = $order->get_meta( '_VitalSwap_token' );

		if ( ! empty( $VitalSwap_token ) ) {

			$order_amount = $amount * 100;
			$txnref       = $order_id . '_' . time();

			$order->update_meta_data( '_VitalSwap_txn_ref', $txnref );
			$order->save();

			$VitalSwap_url = 'https://api.VitalSwap.co/transaction/charge_authorization';

			$headers = array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $this->secret_key,
			);

			$metadata['custom_fields'] = $this->get_custom_fields( $order_id );

			if ( strpos( $VitalSwap_token, '###' ) !== false ) {
				$payment_token  = explode( '###', $VitalSwap_token );
				$auth_code      = $payment_token[0];
				$customer_email = $payment_token[1];
			} else {
				$auth_code      = $VitalSwap_token;
				$customer_email = $order->get_billing_email();
			}

			$body = array(
				'email'              => $customer_email,
				'amount'             => absint( $order_amount ),
				'metadata'           => $metadata,
				'authorization_code' => $auth_code,
				'reference'          => $txnref,
				'currency'           => $order->get_currency(),
			);

			$args = array(
				'body'    => json_encode( $body ),
				'headers' => $headers,
				'timeout' => 60,
			);

			$request = wp_remote_post( $VitalSwap_url, $args );

			if ( ! is_wp_error( $request ) && 200 === wp_remote_retrieve_response_code( $request ) ) {

				$VitalSwap_response = json_decode( wp_remote_retrieve_body( $request ) );

				if ( 'success' == $VitalSwap_response->data->status ) {

					$VitalSwap_ref = $VitalSwap_response->data->reference;

					$order->payment_complete( $VitalSwap_ref );

					$message = sprintf( __( 'Payment via VitalSwap successful (Transaction Reference: %s)', 'woo-VitalSwap' ), $VitalSwap_ref );

					$order->add_order_note( $message );

					if ( parent::is_autocomplete_order_enabled( $order ) ) {
						$order->update_status( 'completed' );
					}

					return true;

				} else {

					$gateway_response = __( 'VitalSwap payment failed.', 'woo-VitalSwap' );

					if ( isset( $VitalSwap_response->data->gateway_response ) && ! empty( $VitalSwap_response->data->gateway_response ) ) {
						$gateway_response = sprintf( __( 'VitalSwap payment failed. Reason: %s', 'woo-VitalSwap' ), $VitalSwap_response->data->gateway_response );
					}

					return new WP_Error( 'VitalSwap_error', $gateway_response );

				}
			}
		}

		return new WP_Error( 'VitalSwap_error', __( 'This subscription can&#39;t be renewed automatically. The customer will have to login to their account to renew their subscription', 'woo-VitalSwap' ) );

	}

}
