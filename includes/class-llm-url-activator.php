<?php
/**
 * Fired during plugin activation
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 * @author     Very Good Plugins
 */
class LLM_URL_Activator {

	/**
	 * Activate the plugin.
	 *
	 * Create database tables and set default options.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::create_tables();
		self::set_default_options();
		
		// Clear the permalinks after the plugin has been activated.
		flush_rewrite_rules();
	}

	/**
	 * Create custom database tables.
	 *
	 * @since    1.0.0
	 */
	private static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		// Table for 404 logs
		$table_404_logs = $wpdb->prefix . 'llm_url_404_logs';
		$sql_404_logs = "CREATE TABLE $table_404_logs (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			requested_url varchar(255) NOT NULL,
			url_slug varchar(255) NOT NULL,
			referrer text,
			ip_address varchar(45),
			user_agent text,
			timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			processed tinyint(1) DEFAULT 0,
			content_generated tinyint(1) DEFAULT 0,
			post_id bigint(20) unsigned DEFAULT NULL,
			confidence_score float DEFAULT NULL,
			detected_post_type varchar(50) DEFAULT NULL,
			PRIMARY KEY (id),
			KEY idx_slug (url_slug),
			KEY idx_processed (processed),
			KEY idx_timestamp (timestamp),
			KEY idx_post_id (post_id)
		) $charset_collate;";

		// Table for plugin settings
		$table_settings = $wpdb->prefix . 'llm_url_settings';
		$sql_settings = "CREATE TABLE $table_settings (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			setting_key varchar(100) NOT NULL,
			setting_value longtext,
			autoload tinyint(1) DEFAULT 1,
			PRIMARY KEY (id),
			UNIQUE KEY setting_key (setting_key)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql_404_logs );
		dbDelta( $sql_settings );

		// Store the database version
		add_option( 'llm_url_solution_db_version', '1.1.0' );
		
		// Run upgrade if needed
		self::maybe_upgrade_database();
	}

	/**
	 * Check and perform database upgrades if needed.
	 *
	 * @since    1.1.0
	 */
	public static function maybe_upgrade_database() {
		$current_db_version = get_option( 'llm_url_solution_db_version', '1.0.0' );
		
		// Upgrade from 1.0.0 to 1.1.0
		if ( version_compare( $current_db_version, '1.1.0', '<' ) ) {
			global $wpdb;
			$table_404_logs = $wpdb->prefix . 'llm_url_404_logs';
			
			// Add confidence_score column if it doesn't exist
			$column_exists = $wpdb->get_results( "SHOW COLUMNS FROM $table_404_logs LIKE 'confidence_score'" );
			if ( empty( $column_exists ) ) {
				$wpdb->query( "ALTER TABLE $table_404_logs ADD COLUMN confidence_score float DEFAULT NULL AFTER post_id" );
			}
			
			// Add detected_post_type column if it doesn't exist
			$column_exists = $wpdb->get_results( "SHOW COLUMNS FROM $table_404_logs LIKE 'detected_post_type'" );
			if ( empty( $column_exists ) ) {
				$wpdb->query( "ALTER TABLE $table_404_logs ADD COLUMN detected_post_type varchar(50) DEFAULT NULL AFTER confidence_score" );
			}
			
			// Update database version
			update_option( 'llm_url_solution_db_version', '1.1.0' );
		}
	}

	/**
	 * Set default plugin options.
	 *
	 * @since    1.0.0
	 */
	private static function set_default_options() {
		// Default options array
		$default_options = array(
			'openai_api_key'        => '',
			'claude_api_key'        => '',
			'ai_model'              => 'gpt-4',
			'temperature'           => 0.7,
			'max_tokens'            => 1500,
			'rate_limit_hourly'     => 10,
			'rate_limit_daily'      => 50,
			'default_post_type'     => 'post',
			'default_post_status'   => 'draft',
			'auto_categorize'       => true,
			'auto_tag'              => true,
			'generate_seo_meta'     => true,
			'manual_approval'       => true,
			'blacklist_patterns'    => '',
			'content_min_length'    => 800,
			'content_max_length'    => 1500,
			'enable_debug_mode'     => false,
		);

		// Add default options
		foreach ( $default_options as $key => $value ) {
			add_option( 'llm_url_solution_' . $key, $value );
		}

		// Set installation timestamp
		add_option( 'llm_url_solution_installed', time() );
		
		// Set default capabilities
		$role = get_role( 'administrator' );
		if ( $role ) {
			$role->add_cap( 'manage_llm_url_solution' );
			$role->add_cap( 'approve_llm_url_content' );
		}
	}
} 