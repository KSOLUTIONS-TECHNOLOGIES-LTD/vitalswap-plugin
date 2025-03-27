<?php

/**
 * Class Tbz_WC_VitalSwap_Custom_Gateway.
 */
class WC_Gateway_Custom_VitalSwap extends WC_Gateway_VitalSwap_Subscriptions {

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
 
		$this->form_fields = array(
			'enabled'                          => array(
				'title'       => __( 'Enable/Disable', 'woo-vitalswap' ),
				/* translators: payment method title */
				'label'       => sprintf( __( 'Enable VitalSwap - %s', 'woo-vitalswap' ), $this->title ),
				'type'        => 'checkbox',
				'description' => __( 'Enable this gateway as a payment option on the checkout page.', 'woo-vitalswap' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'title'                            => array(
				'title'       => __( 'Title', 'woo-vitalswap' ),
				'type'        => 'text',
				'description' => __( 'This controls the payment method title which the user sees during checkout.', 'woo-vitalswap' ),
				'desc_tip'    => true,
				'default'     => __( 'VitalSwap', 'woo-vitalswap' ),
			),
			'description'                      => array(
				'title'       => __( 'Description', 'woo-vitalswap' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the payment method description which the user sees during checkout.', 'woo-vitalswap' ),
				'desc_tip'    => true,
				'default'     => '',
			),
			'payment_page'                     => array(
				'title'       => __( 'Payment Option', 'woo-vitalswap' ),
				'type'        => 'select',
				'description' => __( 'Popup shows the payment popup on the page while Redirect will redirect the customer to VitalSwap to make payment.', 'woo-vitalswap' ),
				'default'     => '',
				'desc_tip'    => false,
				'options'     => array(
					''         => __( 'Select One', 'woo-vitalswap' ),
					'inline'   => __( 'Popup', 'woo-vitalswap' ),
					'redirect' => __( 'Redirect', 'woo-vitalswap' ),
				),
			),
			'autocomplete_order'               => array(
				'title'       => __( 'Autocomplete Order After Payment', 'woo-vitalswap' ),
				'label'       => __( 'Autocomplete Order', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'class'       => 'wc-vitalswap-autocomplete-order',
				'description' => __( 'If enabled, the order will be marked as complete after successful payment', 'woo-vitalswap' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'remove_cancel_order_button'       => array(
				'title'       => __( 'Remove Cancel Order & Restore Cart Button', 'woo-vitalswap' ),
				'label'       => __( 'Remove the cancel order & restore cart button on the pay for order page', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'split_payment'                    => array(
				'title'       => __( 'Split Payment', 'woo-vitalswap' ),
				'label'       => __( 'Enable Split Payment', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'description' => '',
				'class'       => 'woocommerce_vitalswap_split_payment',
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'subaccount_code'                  => array(
				'title'       => __( 'Subaccount Code', 'woo-vitalswap' ),
				'type'        => 'text',
				'description' => __( 'Enter the subaccount code here.', 'woo-vitalswap' ),
				'class'       => __( 'woocommerce_vitalswap_subaccount_code', 'woo-vitalswap' ),
				'default'     => '',
			),
			'split_payment_transaction_charge' => array(
				'title'             => __( 'Split Payment Transaction Charge', 'woo-vitalswap' ),
				'type'              => 'number',
				'description'       => __( 'A flat fee to charge the subaccount for this transaction, in Naira (&#8358;). This overrides the split percentage set when the subaccount was created. Ideally, you will need to use this if you are splitting in flat rates (since subaccount creation only allows for percentage split). e.g. 100 for a &#8358;100 flat fee.', 'woo-vitalswap' ),
				'class'             => 'woocommerce_vitalswap_split_payment_transaction_charge',
				'default'           => '',
				'custom_attributes' => array(
					'min'  => 1,
					'step' => 0.1,
				),
				'desc_tip'          => false,
			),
			'split_payment_charge_account'     => array(
				'title'       => __( 'VitalSwap Charges Bearer', 'woo-vitalswap' ),
				'type'        => 'select',
				'description' => __( 'Who bears VitalSwap charges?', 'woo-vitalswap' ),
				'class'       => 'woocommerce_vitalswap_split_payment_charge_account',
				'default'     => '',
				'desc_tip'    => false,
				'options'     => array(
					''           => __( 'Select One', 'woo-vitalswap' ),
					'account'    => __( 'Account', 'woo-vitalswap' ),
					'subaccount' => __( 'Subaccount', 'woo-vitalswap' ),
				),
			),
			'payment_channels'                 => array(
				'title'             => __( 'Payment Channels', 'woo-vitalswap' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select wc-vitalswap-payment-channels',
				'description'       => __( 'The payment channels enabled for this gateway', 'woo-vitalswap' ),
				'default'           => '',
				'desc_tip'          => true,
				'select_buttons'    => true,
				'options'           => $this->channels(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select payment channels', 'woo-vitalswap' ),
				),
			),
			'cards_allowed'                    => array(
				'title'             => __( 'Allowed Card Brands', 'woo-vitalswap' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select wc-vitalswap-cards-allowed',
				'description'       => __( 'The card brands allowed for this gateway. This filter only works with the card payment channel.', 'woo-vitalswap' ),
				'default'           => '',
				'desc_tip'          => true,
				'select_buttons'    => true,
				'options'           => $this->card_types(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select card brands', 'woo-vitalswap' ),
				),
			),
			'banks_allowed'                    => array(
				'title'             => __( 'Allowed Banks Card', 'woo-vitalswap' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select wc-vitalswap-banks-allowed',
				'description'       => __( 'The banks whose card should be allowed for this gateway. This filter only works with the card payment channel.', 'woo-vitalswap' ),
				'default'           => '',
				'desc_tip'          => true,
				'select_buttons'    => true,
				'options'           => $this->banks(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select banks', 'woo-vitalswap' ),
				),
			),
			'payment_icons'                    => array(
				'title'             => __( 'Payment Icons', 'woo-vitalswap' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select wc-vitalswap-payment-icons',
				'description'       => __( 'The payment icons to be displayed on the checkout page.', 'woo-vitalswap' ),
				'default'           => '',
				'desc_tip'          => true,
				'select_buttons'    => true,
				'options'           => $this->payment_icons(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select payment icons', 'woo-vitalswap' ),
				),
			),
			'custom_metadata'                  => array(
				'title'       => __( 'Custom Metadata', 'woo-vitalswap' ),
				'label'       => __( 'Enable Custom Metadata', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'class'       => 'wc-vitalswap-metadata',
				'description' => __( 'If enabled, you will be able to send more information about the order to VitalSwap.', 'woo-vitalswap' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_order_id'                    => array(
				'title'       => __( 'Order ID', 'woo-vitalswap' ),
				'label'       => __( 'Send Order ID', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'class'       => 'wc-vitalswap-meta-order-id',
				'description' => __( 'If checked, the Order ID will be sent to VitalSwap', 'woo-vitalswap' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_name'                        => array(
				'title'       => __( 'Customer Name', 'woo-vitalswap' ),
				'label'       => __( 'Send Customer Name', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'class'       => 'wc-vitalswap-meta-name',
				'description' => __( 'If checked, the customer full name will be sent to VitalSwap', 'woo-vitalswap' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_email'                       => array(
				'title'       => __( 'Customer Email', 'woo-vitalswap' ),
				'label'       => __( 'Send Customer Email', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'class'       => 'wc-vitalswap-meta-email',
				'description' => __( 'If checked, the customer email address will be sent to VitalSwap', 'woo-vitalswap' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_phone'                       => array(
				'title'       => __( 'Customer Phone', 'woo-vitalswap' ),
				'label'       => __( 'Send Customer Phone', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'class'       => 'wc-vitalswap-meta-phone',
				'description' => __( 'If checked, the customer phone will be sent to VitalSwap', 'woo-vitalswap' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_billing_address'             => array(
				'title'       => __( 'Order Billing Address', 'woo-vitalswap' ),
				'label'       => __( 'Send Order Billing Address', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'class'       => 'wc-vitalswap-meta-billing-address',
				'description' => __( 'If checked, the order billing address will be sent to VitalSwap', 'woo-vitalswap' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_shipping_address'            => array(
				'title'       => __( 'Order Shipping Address', 'woo-vitalswap' ),
				'label'       => __( 'Send Order Shipping Address', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'class'       => 'wc-vitalswap-meta-shipping-address',
				'description' => __( 'If checked, the order shipping address will be sent to VitalSwap', 'woo-vitalswap' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_products'                    => array(
				'title'       => __( 'Product(s) Purchased', 'woo-vitalswap' ),
				'label'       => __( 'Send Product(s) Purchased', 'woo-vitalswap' ),
				'type'        => 'checkbox',
				'class'       => 'wc-vitalswap-meta-products',
				'description' => __( 'If checked, the product(s) purchased will be sent to VitalSwap', 'woo-vitalswap' ),
				'default'     => 'no',
				'desc_tip'    => true,
			),
		);

	}

	/**
	 * Admin Panel Options.
	 */
	public function admin_options() {

		$vitalswap_settings_url = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=vitalswap' );
		$checkout_settings_url = admin_url( 'admin.php?page=wc-settings&tab=checkout' );
		?>

		<h2>
			<?php
			/* translators: payment method title */
			printf( __( 'VitalSwap - %s', 'woo-vitalswap' ), esc_attr( $this->title ) );
			?>
			<?php
			if ( function_exists( 'wc_back_link' ) ) {
				wc_back_link( __( 'Return to payments', 'woo-vitalswap' ), $checkout_settings_url );
			}
			?>
		</h2>

		<h4>
			<?php
			/* translators: link to VitalSwap developers settings page */
			printf( __( 'Important: To avoid situations where bad network makes it impossible to verify transactions, set your webhook URL <a href="%s" target="_blank" rel="noopener noreferrer">here</a> to the URL below', 'woo-vitalswap' ), 'https://dashboard.vitalswap.co/#/settings/developer' );
			?>
		</h4>

		<p style="color: red">
			<code><?php echo esc_url( WC()->api_request_url( 'Tbz_WC_VitalSwap_Webhook' ) ); ?></code>
		</p>

		<p>
			<?php
			/* translators: link to VitalSwap general settings page */
			printf( __( 'To configure your VitalSwap API keys and enable/disable test mode, do that <a href="%s">here</a>', 'woo-vitalswap' ), esc_url( $vitalswap_settings_url ) );
			?>
		</p>

		<?php

		if ( $this->is_valid_for_use() ) {

			echo '<table class="form-table">';
			$this->generate_settings_html();
			echo '</table>';

		} else {

			/* translators: disabled message */
			echo '<div class="inline error"><p><strong>' . sprintf( __( 'VitalSwap Payment Gateway Disabled: %s', 'woo-vitalswap' ), esc_attr( $this->msg ) ) . '</strong></p></div>';

		}

	}

	/**
	 * Payment Channels.
	 */
	public function channels() {

		return array(
			'card'          => __( 'Cards', 'woo-vitalswap' ),
			'bank'          => __( 'Pay with Bank', 'woo-vitalswap' ),
			'ussd'          => __( 'USSD', 'woo-vitalswap' ),
			'qr'            => __( 'QR', 'woo-vitalswap' ),
			'bank_transfer' => __( 'Bank Transfer', 'woo-vitalswap' ),
		);

	}

	/**
	 * Card Types.
	 */
	public function card_types() {

		return array(
			'visa'       => __( 'Visa', 'woo-vitalswap' ),
			'verve'      => __( 'Verve', 'woo-vitalswap' ),
			'mastercard' => __( 'Mastercard', 'woo-vitalswap' ),
		);

	}

	/**
	 * Banks.
	 */
	public function banks() {

		return array(
			'044'  => __( 'Access Bank', 'woo-vitalswap' ),
			'035A' => __( 'ALAT by WEMA', 'woo-vitalswap' ),
			'401'  => __( 'ASO Savings and Loans', 'woo-vitalswap' ),
			'023'  => __( 'Citibank Nigeria', 'woo-vitalswap' ),
			'063'  => __( 'Access Bank (Diamond)', 'woo-vitalswap' ),
			'050'  => __( 'Ecobank Nigeria', 'woo-vitalswap' ),
			'562'  => __( 'Ekondo Microfinance Bank', 'woo-vitalswap' ),
			'084'  => __( 'Enterprise Bank', 'woo-vitalswap' ),
			'070'  => __( 'Fidelity Bank', 'woo-vitalswap' ),
			'011'  => __( 'First Bank of Nigeria', 'woo-vitalswap' ),
			'214'  => __( 'First City Monument Bank', 'woo-vitalswap' ),
			'058'  => __( 'Guaranty Trust Bank', 'woo-vitalswap' ),
			'030'  => __( 'Heritage Bank', 'woo-vitalswap' ),
			'301'  => __( 'Jaiz Bank', 'woo-vitalswap' ),
			'082'  => __( 'Keystone Bank', 'woo-vitalswap' ),
			'014'  => __( 'MainStreet Bank', 'woo-vitalswap' ),
			'526'  => __( 'Parallex Bank', 'woo-vitalswap' ),
			'076'  => __( 'Polaris Bank Limited', 'woo-vitalswap' ),
			'101'  => __( 'Providus Bank', 'woo-vitalswap' ),
			'221'  => __( 'Stanbic IBTC Bank', 'woo-vitalswap' ),
			'068'  => __( 'Standard Chartered Bank', 'woo-vitalswap' ),
			'232'  => __( 'Sterling Bank', 'woo-vitalswap' ),
			'100'  => __( 'Suntrust Bank', 'woo-vitalswap' ),
			'032'  => __( 'Union Bank of Nigeria', 'woo-vitalswap' ),
			'033'  => __( 'United Bank For Africa', 'woo-vitalswap' ),
			'215'  => __( 'Unity Bank', 'woo-vitalswap' ),
			'035'  => __( 'Wema Bank', 'woo-vitalswap' ),
			'057'  => __( 'Zenith Bank', 'woo-vitalswap' ),
		);

	}

	/**
	 * Payment Icons.
	 */
	public function payment_icons() {

		return array(
			'verve'         => __( 'Verve', 'woo-vitalswap' ),
			'visa'          => __( 'Visa', 'woo-vitalswap' ),
			'mastercard'    => __( 'Mastercard', 'woo-vitalswap' ),
			'vitalswapwhite' => __( 'Secured by VitalSwap White', 'woo-vitalswap' ),
			'vitalswapblue'  => __( 'Secured by VitalSwap Blue', 'woo-vitalswap' ),
			'vitalswap-wc'   => __( 'VitalSwap Nigeria', 'woo-vitalswap' ),
			'vitalswap-gh'   => __( 'VitalSwap Ghana', 'woo-vitalswap' ),
			'access'        => __( 'Access Bank', 'woo-vitalswap' ),
			'alat'          => __( 'ALAT by WEMA', 'woo-vitalswap' ),
			'aso'           => __( 'ASO Savings and Loans', 'woo-vitalswap' ),
			'citibank'      => __( 'Citibank Nigeria', 'woo-vitalswap' ),
			'diamond'       => __( 'Access Bank (Diamond)', 'woo-vitalswap' ),
			'ecobank'       => __( 'Ecobank Nigeria', 'woo-vitalswap' ),
			'ekondo'        => __( 'Ekondo Microfinance Bank', 'woo-vitalswap' ),
			'enterprise'    => __( 'Enterprise Bank', 'woo-vitalswap' ),
			'fidelity'      => __( 'Fidelity Bank', 'woo-vitalswap' ),
			'firstbank'     => __( 'First Bank of Nigeria', 'woo-vitalswap' ),
			'fcmb'          => __( 'First City Monument Bank', 'woo-vitalswap' ),
			'gtbank'        => __( 'Guaranty Trust Bank', 'woo-vitalswap' ),
			'heritage'      => __( 'Heritage Bank', 'woo-vitalswap' ),
			'jaiz'          => __( 'Jaiz Bank', 'woo-vitalswap' ),
			'keystone'      => __( 'Keystone Bank', 'woo-vitalswap' ),
			'mainstreet'    => __( 'MainStreet Bank', 'woo-vitalswap' ),
			'parallex'      => __( 'Parallex Bank', 'woo-vitalswap' ),
			'polaris'       => __( 'Polaris Bank Limited', 'woo-vitalswap' ),
			'providus'      => __( 'Providus Bank', 'woo-vitalswap' ),
			'stanbic'       => __( 'Stanbic IBTC Bank', 'woo-vitalswap' ),
			'standard'      => __( 'Standard Chartered Bank', 'woo-vitalswap' ),
			'sterling'      => __( 'Sterling Bank', 'woo-vitalswap' ),
			'suntrust'      => __( 'Suntrust Bank', 'woo-vitalswap' ),
			'union'         => __( 'Union Bank of Nigeria', 'woo-vitalswap' ),
			'uba'           => __( 'United Bank For Africa', 'woo-vitalswap' ),
			'unity'         => __( 'Unity Bank', 'woo-vitalswap' ),
			'wema'          => __( 'Wema Bank', 'woo-vitalswap' ),
			'zenith'        => __( 'Zenith Bank', 'woo-vitalswap' ),
		);

	}

	/**
	 * Display the selected payment icon.
	 */
	public function get_icon() {
		$icon_html = '<img src="' . WC_HTTPS::force_https_url( WC_PAYSTACK_URL . '/assets/images/vitalswap.png' ) . '" alt="vitalswap" style="height: 40px; margin-right: 0.4em;margin-bottom: 0.6em;" />';
		$icon      = $this->payment_icons;

		if ( is_array( $icon ) ) {

			$additional_icon = '';

			foreach ( $icon as $i ) {
				$additional_icon .= '<img src="' . WC_HTTPS::force_https_url( WC_PAYSTACK_URL . '/assets/images/' . $i . '.png' ) . '" alt="' . $i . '" style="height: 40px; margin-right: 0.4em;margin-bottom: 0.6em;" />';
			}

			$icon_html .= $additional_icon;
		}

		return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
	}

	/**
	 * Outputs scripts used for vitalswap payment.
	 */
	public function payment_scripts() {

		if ( isset( $_GET['pay_for_order'] ) || ! is_checkout_pay_page() ) {
			return;
		}

		if ( $this->enabled === 'no' ) {
			return;
		}

		$order_key = urldecode( $_GET['key'] );
		$order_id  = absint( get_query_var( 'order-pay' ) );

		$order = wc_get_order( $order_id );

		if ( $this->id !== $order->get_payment_method() ) {
			return;
		}

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( 'vitalswap', 'https://js.vitalswap.co/v2/inline.js', array( 'jquery' ), WC_PAYSTACK_VERSION, false );

		wp_enqueue_script( 'wc_vitalswap', plugins_url( 'assets/js/vitalswap' . $suffix . '.js', WC_PAYSTACK_MAIN_FILE ), array( 'jquery', 'vitalswap' ), WC_PAYSTACK_VERSION, false );

		$vitalswap_params = array(
			'key' => $this->public_key,
		);

		if ( is_checkout_pay_page() && get_query_var( 'order-pay' ) ) {

			$email = $order->get_billing_email();

			$amount = $order->get_total() * 100;

			$txnref = $order_id . '_' . time();

			$the_order_id  = $order->get_id();
			$the_order_key = $order->get_order_key();
			$currency      = $order->get_currency();

			if ( $the_order_id == $order_id && $the_order_key == $order_key ) {

				$vitalswap_params['email']    = $email;
				$vitalswap_params['amount']   = absint( $amount );
				$vitalswap_params['txnref']   = $txnref;
				$vitalswap_params['currency'] = $currency;

			}

			if ( $this->split_payment ) {

				$vitalswap_params['subaccount_code']     = $this->subaccount_code;
				$vitalswap_params['charges_account']     = $this->charges_account;
				$vitalswap_params['transaction_charges'] = $this->transaction_charges * 100;

			}

			/** This filter is documented in includes/class-wc-gateway-vitalswap.php */
			$payment_channels = apply_filters( 'wc_vitalswap_payment_channels', $this->payment_channels, $this->id, $order );

			if ( in_array( 'bank', $payment_channels, true ) ) {
				$vitalswap_params['bank_channel'] = 'true';
			}

			if ( in_array( 'card', $payment_channels, true ) ) {
				$vitalswap_params['card_channel'] = 'true';
			}

			if ( in_array( 'ussd', $payment_channels, true ) ) {
				$vitalswap_params['ussd_channel'] = 'true';
			}

			if ( in_array( 'qr', $payment_channels, true ) ) {
				$vitalswap_params['qr_channel'] = 'true';
			}

			if ( in_array( 'bank_transfer', $payment_channels, true ) ) {
				$vitalswap_params['bank_transfer_channel'] = 'true';
			}

			if ( $this->banks ) {

				$vitalswap_params['banks_allowed'] = $this->banks;

			}

			if ( $this->cards ) {

				$vitalswap_params['cards_allowed'] = $this->cards;

			}

			if ( $this->custom_metadata ) {

				if ( $this->meta_order_id ) {

					$vitalswap_params['meta_order_id'] = $order_id;

				}

				if ( $this->meta_name ) {

					$vitalswap_params['meta_name'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

				}

				if ( $this->meta_email ) {

					$vitalswap_params['meta_email'] = $email;

				}

				if ( $this->meta_phone ) {

					$vitalswap_params['meta_phone'] = $order->get_billing_phone();

				}

				if ( $this->meta_products ) {

					$line_items = $order->get_items();

					$products = '';

					foreach ( $line_items as $item_id => $item ) {
						$name      = $item['name'];
						$quantity  = $item['qty'];
						$products .= $name . ' (Qty: ' . $quantity . ')';
						$products .= ' | ';
					}

					$products = rtrim( $products, ' | ' );

					$vitalswap_params['meta_products'] = $products;

				}

				if ( $this->meta_billing_address ) {

					$billing_address = $order->get_formatted_billing_address();
					$billing_address = esc_html( preg_replace( '#<br\s*/?>#i', ', ', $billing_address ) );

					$vitalswap_params['meta_billing_address'] = $billing_address;

				}

				if ( $this->meta_shipping_address ) {

					$shipping_address = $order->get_formatted_shipping_address();
					$shipping_address = esc_html( preg_replace( '#<br\s*/?>#i', ', ', $shipping_address ) );

					if ( empty( $shipping_address ) ) {

						$billing_address = $order->get_formatted_billing_address();
						$billing_address = esc_html( preg_replace( '#<br\s*/?>#i', ', ', $billing_address ) );

						$shipping_address = $billing_address;

					}

					$vitalswap_params['meta_shipping_address'] = $shipping_address;

				}
			}

			$order->update_meta_data( '_vitalswap_txn_ref', $txnref );
			$order->save();
		}

		wp_localize_script( 'wc_vitalswap', 'wc_vitalswap_params', $vitalswap_params );

	}

	/**
	 * Add custom gateways to the checkout page.
	 *
	 * @param $available_gateways
	 *
	 * @return mixed
	 */
	public function add_gateway_to_checkout( $available_gateways ) {

		if ( $this->enabled == 'no' ) {
			unset( $available_gateways[ $this->id ] );
		}

		return $available_gateways;

	}

	/**
	 * Check if the custom VitalSwap gateway is enabled.
	 *
	 * @return bool
	 */
	public function is_available() {

		if ( 'yes' == $this->enabled ) {

			if ( ! ( $this->public_key && $this->secret_key ) ) {

				return false;

			}

			return true;

		}

		return false;
	}
}
