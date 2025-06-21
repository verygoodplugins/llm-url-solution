<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    LLM_URL_Solution
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check if user has opted to delete data on uninstall
$delete_data = get_option( 'llm_url_solution_delete_data_on_uninstall', false );

if ( $delete_data ) {
	global $wpdb;
	
	// Delete custom tables
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}llm_url_404_logs" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}llm_url_settings" );
	
	// Delete options
	$options = array(
		'llm_url_solution_db_version',
		'llm_url_solution_openai_api_key',
		'llm_url_solution_claude_api_key',
		'llm_url_solution_ai_model',
		'llm_url_solution_temperature',
		'llm_url_solution_max_tokens',
		'llm_url_solution_rate_limit_hourly',
		'llm_url_solution_rate_limit_daily',
		'llm_url_solution_default_post_type',
		'llm_url_solution_default_post_status',
		'llm_url_solution_auto_categorize',
		'llm_url_solution_auto_tag',
		'llm_url_solution_generate_seo_meta',
		'llm_url_solution_manual_approval',
		'llm_url_solution_blacklist_patterns',
		'llm_url_solution_content_min_length',
		'llm_url_solution_content_max_length',
		'llm_url_solution_enable_debug_mode',
		'llm_url_solution_installed',
		'llm_url_solution_custom_referrer_patterns',
		'llm_url_solution_custom_prompt',
		'llm_url_solution_min_confidence',
		'llm_url_solution_content_tone',
		'llm_url_solution_include_examples',
		'llm_url_solution_include_code',
		'llm_url_solution_auto_generate',
		'llm_url_solution_delete_data_on_uninstall',
	);
	
	foreach ( $options as $option ) {
		delete_option( $option );
	}
	
	// Delete user meta
	$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'llm_url_solution_%'" );
	
	// Delete posts meta
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_llm_url_solution_%'" );
	
	// Remove capabilities
	$roles = array( 'administrator', 'editor' );
	foreach ( $roles as $role_name ) {
		$role = get_role( $role_name );
		if ( $role ) {
			$role->remove_cap( 'manage_llm_url_solution' );
			$role->remove_cap( 'approve_llm_url_content' );
		}
	}
	
	// Clear scheduled events
	wp_clear_scheduled_hook( 'llm_url_solution_hourly_cleanup' );
	wp_clear_scheduled_hook( 'llm_url_solution_daily_report' );
	
	// Delete transients
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_llm_url_%'" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_llm_url_%'" );
	
	// Clear any cached data
	wp_cache_flush();
} 