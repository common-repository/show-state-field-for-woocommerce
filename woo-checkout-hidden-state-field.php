<?php

/**
 * Plugin Name:		  Show State Field for WooCommerce
 * Plugin URI:		  https://plugins.hirewebxperts.com/show-state-field-for-woocommerce/
 * Description:		  Show State Field for WooCommerce provides you to flexibility to show hidden state field for some countries which are hidden by WooCommerce.
 * Version: 		  1.2
 * Requires at least: 6.3.2 or higher
 * Requires PHP:      7.4 or higher
 * Author: 			  Coder426
 * Text Domain: 	  woo-show-state-field
 * Author URI:		  https://www.hirewebxperts.com
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*
**define plugin paths
*/
define('WCHSF_VAR', rand());
define('WCHSF_NAME', 'woo-show-state-field');
define('WCHSF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WCHSF_PLUGIN_DIR', dirname(__FILE__));
define('WCHSF_ASSETS', WCHSF_PLUGIN_URL . 'assets/');
define('WCHSF_IMG', WCHSF_PLUGIN_URL . 'assets/img/');
define('WCHSF_INC', WCHSF_PLUGIN_DIR . '/include/');
define('WCHSF_INC_URL', WCHSF_PLUGIN_URL . '/include/');

if (!defined('ABSPATH')) {
	exit; // exit if accessed directly    
}

//Setting link to pluign
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wchsf_add_plugin_page_settings_link');
function wchsf_add_plugin_page_settings_link($links)
{
	$links[] = '<a href="' . admin_url('admin.php?page=woo-show-state-field') . '">' . __('Settings') . '</a>';
	return $links;
}

/**
 **Include css js files
 */

if (isset($_REQUEST['page'])) {
	if (!function_exists('wchsf_add_admin_scripts')  && ($_REQUEST['page'] == 'woo-show-state-field')) {
		function wchsf_add_admin_scripts()
		{
			/** 
			 ** Admin Dashboard Style
			 **/
			wp_enqueue_style(WCHSF_NAME . '_fontawesome_min', WCHSF_ASSETS . 'libs/fontawesome/all.css', array(), WCHSF_VAR);
			wp_enqueue_style(WCHSF_NAME . '_bootstrap_min', WCHSF_ASSETS . 'libs/bootstrap/css/bootstrap.min.css', array(), WCHSF_VAR);
			wp_enqueue_style(WCHSF_NAME . '-owl-carousel-css', WCHSF_ASSETS . 'libs/owl-carousel/css/owl.carousel.min.css', array(), WCHSF_VAR);
			wp_enqueue_style(WCHSF_NAME . '-owl-carousel-theme', WCHSF_ASSETS . 'libs/owl-carousel/css/owl.theme.default.min.css', array(), WCHSF_VAR);
			wp_enqueue_style(WCHSF_NAME . '_admin', WCHSF_ASSETS . 'css/wchsf-admin.css', array(), WCHSF_VAR);

			/** 
			 ** Admin Dashboard Script
			 **/
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script(WCHSF_NAME . '_popper', WCHSF_ASSETS . 'libs/popper/popper.min.js', array('jquery'), WCHSF_VAR, true);
			wp_enqueue_script(WCHSF_NAME . '_bootstrap_min', WCHSF_ASSETS . 'libs/bootstrap/js/bootstrap.min.js', array('jquery'), WCHSF_VAR, true);
			wp_enqueue_script(WCHSF_NAME . '-owl-carousel-js', WCHSF_ASSETS . 'libs/owl-carousel/js/owl.carousel.min.js', array(), rand(), true);
			wp_enqueue_script(WCHSF_NAME . '_admin', WCHSF_ASSETS . 'js/wchsf-admin.js', array('jquery'), WCHSF_VAR, true);
			$admin_url = strtok(admin_url('admin-ajax.php', (is_ssl() ? 'https' : 'http')), '?');
			wp_localize_script(WCHSF_NAME . '_admin', 'MyAjax', array(
				'ajaxurl' => $admin_url,
				'no_export_data' => 'There are no exporting data in your selection fields',
				'ajax_public_nonce' => wp_create_nonce('ajax_public_nonce'),
			));
		}
		add_action('admin_enqueue_scripts', 'wchsf_add_admin_scripts');
	}
}

/**
 **Create wchsf menu
 */
add_action('admin_menu', 'wchsf_admin_menu');
if (!function_exists('wchsf_admin_menu')) {
	function wchsf_admin_menu()
	{
		$menu_slug = 'woo-show-state-field';
		add_submenu_page(
			'woocommerce',
			__('Show State Field for WooCommerce', 'woo-show-state-field'),
			__('WCHSF', 'woo-show-state-field'),
			'manage_woocommerce',
			'woo-show-state-field',
			'wchsf_admin_menu_output_html'
		);
	}
}

/**
 **Create wchsf sub-menu
 */
if (!function_exists('wchsf_admin_menu_output_html')) {
	function wchsf_admin_menu_output_html()
	{

		// Setting Save
		if (isset($_POST["wchsf-nonce"]) && wp_verify_nonce($_POST["wchsf-nonce"], basename(__FILE__))) {
			$s_save = false;
			if (isset($_GET) && !empty($_GET['page']) && $_GET['page'] == 'woo-show-state-field') {
				$nonce = $_POST['wchsf-nonce'];
				$final_settings = array();
				if (isset($_POST['wchsf_setting']) && !empty($_POST['wchsf_setting']) && is_array($_POST['wchsf_setting']['wchsf_countries'])) {

					// sanitize text field
					if (!empty($_POST['wchsf_setting']['wchsf_countries'])) {
						$final_settings['wchsf_countries'] = array_map("sanitize_text_field", $_POST['wchsf_setting']['wchsf_countries']);
					}

					$finaldata['wchsf_setting'] = $final_settings;
					update_option('_wchsf_settings', $finaldata);
				} else {
					update_option('_wchsf_settings', '');
				}

				$s_save = true;
			} // end if isset($_GET)       
		}

		$settings = get_option('_wchsf_settings');
		if (isset($settings['wchsf_setting']) && !empty($settings['wchsf_setting']) && is_array($settings['wchsf_setting'])) {
			$settings = $settings['wchsf_setting'];
		}

		$countries = array('AF', 'AX', 'AT', 'BH', 'BE', 'BJ', 'BG', 'BI', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'GF', 'GP', 'HU', 'HR', 'IS', 'IM', 'IL', 'IE', 'IT', 'KW', 'KR', 'LB', 'LT', 'LK', 'LU', 'LV', 'MT', 'MQ', 'YT', 'NL', 'NO', 'PL', 'PT', 'RO', 'RE', 'SG', 'SE', 'SI', 'SK', 'VN');
		sort($countries);
		$WC_Countries = new WC_Countries();
		// 
?>
		<form class="wchsf_support_page" method="POST" id="" action="<?php echo admin_url() . 'admin.php?page=woo-show-state-field'; ?>">
			<div class="container-fluid">
				<?php
				if (isset($s_save) && !empty($s_save) && $s_save == 'true') {
				?>
					<div class="row">
						<div class="col-12 p-0">
							<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible mx-0 my-2">
								<p><strong><?php echo __('Settings saved', 'horizontal-slider-with-scroll'); ?>.</strong></p>
							</div>
						</div>
					</div> <?php
						}
							?>
				<div class="row">
					<div class="col-xl-6 ">
						<div class="card text-dark bg-light p-0 mw-100">
							<h5 class="card-header">Show State Field For Specific Countries</h5>
							<div class="card-body">
								<div class="row">
									<div class="col-12">
										<div class="country_drop">
											<!-- <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-globe-europe"></i></button> -->
											<ul class="p-0">
												<?php
												foreach ($countries as $countrie) {
													$WC_Countries->country_exists($countrie);
													if (isset($settings['wchsf_countries'][$countrie]) && !empty($settings['wchsf_countries'][$countrie]) && is_array($settings['wchsf_countries']) && $settings['wchsf_countries'][$countrie] == 'on') {
														$checked = 'checked="checked"';
													} else {
														$checked = '';
													}
													echo '<li class="px-3"><a href="#" class="" data-bs-value="' . esc_html($countrie) . '" tabIndex="-1"><input ' . esc_html($checked) . ' id="' . esc_html($countrie) . '" type="checkbox" name="wchsf_setting[wchsf_countries][' . esc_html($countrie) . ']" />&nbsp;' . esc_html($WC_Countries->countries[$countrie]) . '</a></li>';
												}
												?>
											</ul>
										</div>
									</div>
									<div class="col-12 mt-3">
										<?php wp_nonce_field(basename(__FILE__), "wchsf-nonce"); ?>
										<button type="submit" class="btn btn-primary">Save</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-6 ">
						<div class="colbox p-3 mb-3">

							<div class="row">
								<div class="col-xl-6 col-md-6">
									<h6 class="px-0 mb-3 sec_heading"><?php echo __('How to use Show State Field for WooCommerce?', 'woo-show-state-field'); ?></h6>
									<div class="colbox">

										<div class="side_review">
											<iframe src="https://www.youtube.com/embed/dgojRZLE_pY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
											<p class="mb-0 mt-1 p-3 vido"><a href="https://wordpress.org/support/plugin/show-state-field-for-woocommerce/reviews/" target="_blank"><?php echo __('Please Review', 'woo-show-state-field'); ?> <span class="dashicons dashicons-thumbs-up"></span></a></p>
											<p class="mb-0 mt-1 p-3 vido text-end"><a href="https://www.youtube.com/channel/UClog8CJFaMUqll0X5zknEEQ" class="sub_btn" target="_blank"><?php echo __('SUBSCRIBE', 'woo-show-state-field'); ?></a>
											</p>
											<div class="clear"></div>
										</div>
									</div>
								</div>
								<div class="col-xl-6 col-md-6">
									<h6 class="px-0 mb-3 sec_heading"><?php echo __('Explore Our Services', 'woo-show-state-field'); ?></h6>
									<div class="colbox">
										<div class="side_review optigif">
											<a href="https://1.bp.blogspot.com/-Gh_wRgDCnTc/YNxa8JzXTaI/AAAAAAAABlY/Rrbh-3PVYtYh7XWYVeeyJXHIa_wZfRUegCLcBGAsYHQ/s0/optimize-new-min.gif" target="_blank"><img src="<?php echo WCHSF_IMG . 'hirewebxperts.jpg' ?>" /></a>
											<p class="mb-0 p-3"><a href="https://plugins.hirewebxperts.com/support/" target="_blank"><?php echo __('For WordPress Design & Development | Custom Plugin Services', 'woo-show-state-field'); ?></a>
											</p>
										</div>
									</div>
								</div>
							</div>
							<div class="row mid-row">
								<div class="col-12 p-0">
									<h6 class="px-0 mb-3 sec_heading"><?php echo __('Try Our Other WordPress Plugins', 'woo-show-state-field'); ?></h6>

									<div class="owl-carousel owl-theme " id="banners">
										<div class="item">
											<div class="side_review colbox">
												<a href="https://wordpress.org/plugins/woo-custom-cart-button/" target="_blank"><img src="<?php echo WCHSF_IMG . 'custom-add-to-cart.jpg' ?>" /></a>
												<p class="mb-0 p-3 vido55"><a href="https://wordpress.org/plugins/woo-custom-cart-button/" target="_blank"><?php echo __('Custom Add to Cart Button', 'woo-show-state-field'); ?></a>
												</p>
												<p class="mb-0 p-3 vido45 text-end"><a href="https://plugins.hirewebxperts.com/custom-add-to-cart-button-and-link-pro/#ctbtnprice" class="sub_btn" target="_blank"><?php echo __('Get Pro', 'woo-show-state-field'); ?></a>
												<div class="clear"></div>
											</div>
										</div>
										<div class="item">
											<div class="side_review colbox">
												<a href="https://wordpress.org/plugins/awesome-checkout-templates/" target="_blank"><img src="<?php echo WCHSF_IMG . 'awesome-checkout.jpg' ?>" /></a>
												<p class="mb-0 p-3"><a href="https://wordpress.org/plugins/awesome-checkout-templates/" target="_blank"><?php echo __('Awesome Checkout Templates', 'woo-show-state-field'); ?></a>
												</p>
											</div>
										</div>
										<div class="item">
											<div class="side_review colbox">
												<a href="https://wordpress.org/plugins/passwords-manager/" target="_blank"><img src="<?php echo WCHSF_IMG . 'pasword-manager.jpg' ?>" /></a>
												<p class="mb-0 p-3"><a href="https://wordpress.org/plugins/passwords-manager/" target="_blank"><?php echo __('Passwords Manager', 'woo-show-state-field'); ?></a></p>
											</div>
										</div>
										<div class="item">
											<div class="side_review colbox">
												<a href="https://wordpress.org/plugins/gforms-addon-for-country-and-state-selection" target="_blank"><img src="<?php echo WCHSF_IMG . 'country-state-selection.jpg' ?>" /></a>
												<p class="mb-0 p-3"><a href="https://wordpress.org/plugins/gforms-addon-for-country-and-state-selection" target="_blank"><?php echo __('Country and State Selection Addon for Gravity Forms', 'woo-show-state-field'); ?></a>
												</p>
											</div>
										</div>
										<div class="item">
											<div class="side_review colbox">
												<a href="https://wordpress.org/plugins/digital-warranty-card-generator/" target="_blank"><img src="<?php echo WCHSF_IMG . 'digital-warranty-card.jpg' ?>" /></a>
												<p class="mb-0 p-3"><a href="https://wordpress.org/plugins/digital-warranty-card-generator/" target="_blank"><?php echo __('Digital Warranty Card Generator', 'woo-show-state-field'); ?></a>
												</p>
											</div>
										</div>
										<div class="item">
											<div class="side_review colbox">
												<a href="https://wordpress.org/plugins/horizontal-slider-with-scroll/" target="_blank"><img src="<?php echo WCHSF_IMG . 'horizontal-slider.jpg' ?>" /></a>
												<p class="mb-0 p-3"><a href="https://wordpress.org/plugins/horizontal-slider-with-scroll/" target="_blank"><?php echo __('Horizontal Slider with Scroll', 'woo-show-state-field'); ?></a>
												</p>
											</div>
										</div>
										<div class="item">
											<div class="side_review colbox">
												<a href="https://wordpress.org/plugins/text-case-converter/" target="_blank"><img src="<?php echo WCHSF_IMG . 'text-case-converter.jpg' ?>" /></a>
												<p class="mb-0 p-3"><a href="https://wordpress.org/plugins/text-case-converter/" target="_blank"><?php echo __('Text Case Converter', 'woo-show-state-field'); ?></a>
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row mid-row">
								<div class="col-12 p-0">
									<h6 class="px-0 mb-3 sec_heading"><?php echo __('Try World Class Hosting Services', 'woo-show-state-field'); ?></h6>

									<div class="owl-carousel owl-theme " id="kinsta_banners">
										<div class="item">
											<a href="https://kinsta.com/?kaid=NSFASHTZZXQG" target="_blank">
												<img src="<?php echo WCHSF_IMG . 'kinsta1.png' ?>" />
											</a>
										</div>
										<div class="item">
											<a href="https://kinsta.com/?kaid=NSFASHTZZXQG" target="_blank">
												<img src="<?php echo WCHSF_IMG . 'kinsta2.jpg' ?>" />
											</a>
										</div>
										<div class="item">
											<a href="https://kinsta.com/?kaid=NSFASHTZZXQG" target="_blank">
												<img src="<?php echo WCHSF_IMG . 'kinsta3.png' ?>" />
											</a>
										</div>
										<div class="item">
											<a href="https://kinsta.com/?kaid=NSFASHTZZXQG" target="_blank">
												<img src="<?php echo WCHSF_IMG . 'kinsta4.png' ?>" />
											</a>
										</div>
										<div class="item">
											<a href="https://kinsta.com/?kaid=NSFASHTZZXQG" target="_blank">
												<img src="<?php echo WCHSF_IMG . 'kinsta5.png' ?>" />
											</a>
										</div>
										<div class="item">
											<a href="https://kinsta.com/?kaid=NSFASHTZZXQG" target="_blank">
												<img src="<?php echo WCHSF_IMG . 'kinsta6.jpg' ?>" />
											</a>
										</div>
										<div class="item">
											<a href="https://kinsta.com/?kaid=NSFASHTZZXQG" target="_blank">
												<img src="<?php echo WCHSF_IMG . 'kinsta7.jpg' ?>" />
											</a>
										</div>
										<div class="item">
											<a href="https://kinsta.com/?kaid=NSFASHTZZXQG" target="_blank">
												<img src="<?php echo WCHSF_IMG . 'kinsta8.png' ?>" />
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</form>
<?php
	}
}

function wchsf_filter_woocommerce_states($states)
{
	$settings = get_option('_wchsf_settings');
	if (isset($settings['wchsf_setting']) && !empty($settings['wchsf_setting']) && is_array($settings['wchsf_setting'])) {
		$settings = $settings['wchsf_setting'];
	}

	if (isset($settings['wchsf_countries']) && !empty($settings['wchsf_countries']) && is_array($settings['wchsf_countries'])) {
		foreach ($settings['wchsf_countries'] as $code => $codeval) {
			unset($states[esc_html($code)]);
		}
	}
	return $states;
};
add_filter('woocommerce_states', 'wchsf_filter_woocommerce_states', 10, 1);

function wchsf_filter_woocommerce_get_country_locale($locale)
{

	$settings = get_option('_wchsf_settings');
	if (isset($settings['wchsf_setting']) && !empty($settings['wchsf_setting']) && is_array($settings['wchsf_setting'])) {
		$settings = $settings['wchsf_setting'];
	}

	if (isset($settings['wchsf_countries']) && !empty($settings['wchsf_countries']) && is_array($settings['wchsf_countries'])) {
		foreach ($settings['wchsf_countries'] as $code => $codeval) {

			$locale[esc_html($code)]['state']['required'] = true;
		}
	}
	return $locale;
};
add_filter('woocommerce_get_country_locale', 'wchsf_filter_woocommerce_get_country_locale', 10, 1);


?>