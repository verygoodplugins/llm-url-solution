<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 * @author     Your Company Name
 */
class LLM_URL_Deactivator {

	/**
	 * Deactivate the plugin.
	 *
	 * Clean up scheduled events but preserve data.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Clear any scheduled events
		wp_clear_scheduled_hook( 'llm_url_solution_hourly_cleanup' );
		wp_clear_scheduled_hook( 'llm_url_solution_daily_report' );
		
		// Clear rewrite rules
		flush_rewrite_rules();
		
		// Note: We do NOT delete database tables or options on deactivation
		// This preserves user data if they temporarily deactivate the plugin
	}
} 