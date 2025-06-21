<?php
/**
 * Plugin Name:       LLM URL Solution
 * Plugin URI:        https://example.com/llm-url-solution
 * Description:       Automatically generates content for 404 URLs that originate from AI chatbot searches, creating SEO-optimized blog posts or documentation pages.
 * Version:           1.1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Very Good Plugins
 * Author URI:        https://verygoodplugins.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       llm-url-solution
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/verygoodplugins/llm-url-solution
 *
 * @package LLM_URL_Solution
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'LLM_URL_SOLUTION_VERSION', '1.1.0' );

/**
 * Plugin constants
 */
define( 'LLM_URL_SOLUTION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LLM_URL_SOLUTION_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LLM_URL_SOLUTION_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-llm-url-activator.php
 */
function llm_url_solution_activate() {
	require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-activator.php';
	LLM_URL_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-llm-url-deactivator.php
 */
function llm_url_solution_deactivate() {
	require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-deactivator.php';
	LLM_URL_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'llm_url_solution_activate' );
register_deactivation_hook( __FILE__, 'llm_url_solution_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-core.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function llm_url_solution_run() {
	$plugin = new LLM_URL_Core();
	$plugin->run();
}

llm_url_solution_run();
