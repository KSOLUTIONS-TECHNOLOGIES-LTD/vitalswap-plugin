<?php

/**
 * Plugin Name: VitalSwap 
 * Plugin URI: https://VitalSwap.com
 * Description: VitalSwap payment gateway for WooCommerce
 * Version: 2.0
 * Author: VitalSwap
 * Author URI: https://VitalSwap.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires Plugins: woocommerce
 * Requires at least: 6.2
 * Requires PHP: 7.4
 * WC requires at least: 8.0
 * WC tested up to: 9.1
 * Text Domain: vitalswap
 * Domain Path: /languages
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;

if (!defined('ABSPATH')) {
	exit;
}

define('WC_VitalSwap_MAIN_FILE', __FILE__);
define('WC_VitalSwap_URL', untrailingslashit(plugins_url('/', __FILE__)));

define('WC_VitalSwap_VERSION', '2.0');

/**
 * Initialise VitalSwap payment gateway.
 */
function wc_VitalSwap_init()
{

	load_plugin_textdomain('vitalswap', false, plugin_basename(dirname(__FILE__)) . '/languages');

	if (!class_exists('WC_Payment_Gateway')) {
		add_action('admin_notices', 'tbz_WC_VitalSwap_wc_missing_notice');
		return;
	}

	add_action('admin_init', 'tbz_WC_VitalSwap_testmode_notice');

	require_once __DIR__ . '/includes/class-wc-gateway-vitalswap.php';

	require_once __DIR__ . '/includes/class-wc-gateway-vitalswap-subscriptions.php';

	require_once __DIR__ . '/includes/custom-gateways/class-wc-gateway-custom-vitalswap.php';

	require_once __DIR__ . '/includes/custom-gateways/gateway-one/class-wc-gateway-vitalswap-one.php';
	require_once __DIR__ . '/includes/custom-gateways/gateway-two/class-wc-gateway-vitalswap-two.php';
	require_once __DIR__ . '/includes/custom-gateways/gateway-three/class-wc-gateway-vitalswap-three.php';
	require_once __DIR__ . '/includes/custom-gateways/gateway-four/class-wc-gateway-vitalswap-four.php';
	require_once __DIR__ . '/includes/custom-gateways/gateway-five/class-wc-gateway-vitalswap-five.php';

	require_once __DIR__ . '/page/PageInterface.php';
	require_once __DIR__ . '/page/ControllerInterface.php';
	require_once __DIR__ . '/page/TemplateLoaderInterface.php';
	require_once __DIR__ . '/page/Page.php';
	require_once __DIR__ . '/page/Controller.php';
	require_once __DIR__ . '/page/TemplateLoader.php';

	$controller = new Controller(new TemplateLoader);

	add_action('init', array($controller, 'init'));

	add_filter('do_parse_request', array($controller, 'dispatch'), PHP_INT_MAX, 2);

	add_action('loop_end', function (\WP_Query $query) {
		if (isset($query->virtual_page) && !empty($query->virtual_page)) {
			$query->virtual_page = NULL;
		}
	});

	add_filter('the_permalink', function ($plink) {
		global $post, $wp_query;
		if (
			$wp_query->is_page && isset($wp_query->virtual_page)
			&& $wp_query->virtual_page instanceof Page
			&& isset($post->is_virtual) && $post->is_virtual
		) {
			$plink = home_url($wp_query->virtual_page->getUrl());
		}
		return $plink;
	});


	add_filter('woocommerce_payment_gateways', 'tbz_wc_add_VitalSwap_gateway', 99);

	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'tbz_woo_VitalSwap_plugin_action_links');
}
add_action('plugins_loaded', 'wc_VitalSwap_init', 99);

add_action('gm_virtual_pages', function ($controller) {


	//plugin_dir_path( __FILE__ )  . 'templates/checkout.php'
	// first page
	$controller->addPage(new Page('/vitalswap/checkout'))
		->setTitle('VitalSwap Checkout Page ')
		->setTemplate('checkout.php');


	// second page
	$controller->addPage(new Page('/vitalswap/thank-you'))
		->setTitle('My Second Custom Page')
		->setTemplate('templates/thank-you.php');
});

/**
 * Add Settings link to the plugin entry in the plugins menu.
 *
 * @param array $links Plugin action links.
 *
 * @return array
 **/
function tbz_woo_VitalSwap_plugin_action_links($links)
{

	$settings_link = array(
		'settings' => '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=VitalSwap') . '" title="' . __('View VitalSwap WooCommerce Settings', 'vitalswap') . '">' . __('Settings', 'vitalswap') . '</a>',
	);

	return array_merge($settings_link, $links);
}

/**
 * Add VitalSwap Gateway to WooCommerce.
 *
 * @param array $methods WooCommerce payment gateways methods.
 *
 * @return array
 */
function tbz_wc_add_VitalSwap_gateway($methods)
{

	if (class_exists('WC_Subscriptions_Order') && class_exists('WC_Payment_Gateway_CC')) {
		$methods[] = 'WC_Gateway_VitalSwap_Subscriptions';
	} else {
		$methods[] = 'WC_Gateway_VitalSwap';
	}

	if ('NGN' === get_woocommerce_currency()) {

		$settings        = get_option('woocommerce_VitalSwap_settings', '');
		$custom_gateways = isset($settings['custom_gateways']) ? $settings['custom_gateways'] : '';

		switch ($custom_gateways) {
			case '5':
				$methods[] = 'WC_Gateway_VitalSwap_One';
				$methods[] = 'WC_Gateway_VitalSwap_Two';
				$methods[] = 'WC_Gateway_VitalSwap_Three';
				$methods[] = 'WC_Gateway_VitalSwap_Four';
				$methods[] = 'WC_Gateway_VitalSwap_Five';
				break;

			case '4':
				$methods[] = 'WC_Gateway_VitalSwap_One';
				$methods[] = 'WC_Gateway_VitalSwap_Two';
				$methods[] = 'WC_Gateway_VitalSwap_Three';
				$methods[] = 'WC_Gateway_VitalSwap_Four';
				break;

			case '3':
				$methods[] = 'WC_Gateway_VitalSwap_One';
				$methods[] = 'WC_Gateway_VitalSwap_Two';
				$methods[] = 'WC_Gateway_VitalSwap_Three';
				break;

			case '2':
				$methods[] = 'WC_Gateway_VitalSwap_One';
				$methods[] = 'WC_Gateway_VitalSwap_Two';
				break;

			case '1':
				$methods[] = 'WC_Gateway_VitalSwap_One';
				break;

			default:
				break;
		}
	}

	return $methods;
}

/**
 * Display a notice if WooCommerce is not installed
 */
function tbz_WC_VitalSwap_wc_missing_notice()
{
	/* translators: WooCommerce installation link  */
	echo esc_html('<div class="error"><p><strong>' . sprintf(__('VitalSwap requires WooCommerce to be installed and active. Click %s to install WooCommerce.', 'vitalswap'), '<a href="' . admin_url('plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true&width=772&height=539') . '" class="thickbox open-plugin-details-modal">here</a>') . '</strong></p></div>');
}

/**
 * Display the test mode notice.
 **/
function tbz_WC_VitalSwap_testmode_notice()
{

	if (!class_exists(Notes::class)) {
		return;
	}

	if (!class_exists(WC_Data_Store::class)) {
		return;
	}

	if (!method_exists(Notes::class, 'get_note_by_name')) {
		return;
	}

	$test_mode_note = Notes::get_note_by_name('vitalswap-test-mode');

	if (false !== $test_mode_note) {
		return;
	}

	$VitalSwap_settings = get_option('woocommerce_VitalSwap_settings');
	$test_mode         = $VitalSwap_settings['testmode'] ?? '';

	if ('yes' !== $test_mode) {
		Notes::delete_notes_with_name('vitalswap-test-mode');

		return;
	}

	$note = new Note();
	$note->set_title(__('VitalSwap test mode enabled', 'vitalswap'));
	$note->set_content(__('VitalSwap test mode is currently enabled. Remember to disable it when you want to start accepting live payment on your site.', 'vitalswap'));
	$note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
	$note->set_layout('plain');
	$note->set_is_snoozable(false);
	$note->set_name('vitalswap-test-mode');
	$note->set_source('vitalswap');
	$note->add_action('disable-vitalswap-test-mode', __('Disable VitalSwap test mode', 'vitalswap'), admin_url('admin.php?page=wc-settings&tab=checkout&section=VitalSwap'));
	$note->save();
}

add_action(
	'before_woocommerce_init',
	function () {
		if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
		}
	}
);

/**
 * Registers WooCommerce Blocks integration.
 */
function tbz_wc_gateway_VitalSwap_woocommerce_block_support()
{
	if (class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
		require_once __DIR__ . '/includes/class-wc-gateway-vitalswap-blocks-support.php';
		require_once __DIR__ . '/includes/custom-gateways/class-wc-gateway-custom-vitalswap-blocks-support.php';
		require_once __DIR__ . '/includes/custom-gateways/gateway-one/class-wc-gateway-vitalswap-one-blocks-support.php';
		require_once __DIR__ . '/includes/custom-gateways/gateway-two/class-wc-gateway-vitalswap-two-blocks-support.php';
		require_once __DIR__ . '/includes/custom-gateways/gateway-three/class-wc-gateway-vitalswap-three-blocks-support.php';
		require_once __DIR__ . '/includes/custom-gateways/gateway-four/class-wc-gateway-vitalswap-four-blocks-support.php';
		require_once __DIR__ . '/includes/custom-gateways/gateway-five/class-wc-gateway-vitalswap-five-blocks-support.php';
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			static function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
				$payment_method_registry->register(new WC_Gateway_VitalSwap_Blocks_Support());
				$payment_method_registry->register(new WC_Gateway_VitalSwap_One_Blocks_Support());
				$payment_method_registry->register(new WC_Gateway_VitalSwap_Two_Blocks_Support());
				$payment_method_registry->register(new WC_Gateway_VitalSwap_Three_Blocks_Support());
				$payment_method_registry->register(new WC_Gateway_VitalSwap_Four_Blocks_Support());
				$payment_method_registry->register(new WC_Gateway_VitalSwap_Five_Blocks_Support());
			}
		);
	}
}
add_action('woocommerce_blocks_loaded', 'tbz_wc_gateway_VitalSwap_woocommerce_block_support');


function vitalswap_template_array()
{

	$temps['checkout.php'] = "VitalSwap Checkout";
	$temps['thank_you.php'] = "VitalSwap Thank you";

	return $temps;
}

function vitalswap_template_register($page_templates, $theme, $post)
{
	$templates = vitalswap_template_array();

	foreach ($templates as $key => $value) {
		$page_templates[$key] = $page_templates[$value];
	}

	return $page_templates;
}
add_filter('theme_page_templates', 'vitalswap_template_register', 10, 3);
