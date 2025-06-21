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
	 * Create database tables.
	 *
	 * @since    1.0.0
	 */
	private static function create_tables() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $wpdb->prefix . 'llm_url_404_logs';

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			requested_url varchar(255) NOT NULL,
			url_slug varchar(255) NOT NULL,
			referrer varchar(255) DEFAULT '',
			ip_address varchar(45) DEFAULT '',
			user_agent text,
			timestamp datetime DEFAULT CURRENT_TIMESTAMP,
			processed tinyint(1) DEFAULT 0,
			content_generated tinyint(1) DEFAULT 0,
			post_id bigint(20) DEFAULT NULL,
			confidence_score float DEFAULT NULL,
			detected_post_type varchar(50) DEFAULT NULL,
			generation_status varchar(20) DEFAULT 'pending',
			generation_message text,
			PRIMARY KEY (id),
			KEY url_slug (url_slug),
			KEY timestamp (timestamp),
			KEY processed (processed),
			KEY content_generated (content_generated)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// Update database version
		update_option( 'llm_url_solution_db_version', '1.2.0' );
	}

	/**
	 * Upgrade database tables.
	 *
	 * @since    1.1.0
	 */
	private static function upgrade_database() {
		$current_version = get_option( 'llm_url_solution_db_version', '1.0.0' );

		// Upgrade to 1.1.0
		if ( version_compare( $current_version, '1.1.0', '<' ) ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'llm_url_404_logs';

			// Add confidence_score column
			$wpdb->query( "ALTER TABLE $table_name ADD COLUMN confidence_score FLOAT DEFAULT NULL AFTER post_id" );

			// Add detected_post_type column
			$wpdb->query( "ALTER TABLE $table_name ADD COLUMN detected_post_type VARCHAR(50) DEFAULT NULL AFTER confidence_score" );

			update_option( 'llm_url_solution_db_version', '1.1.0' );
		}

		// Upgrade to 1.2.0
		if ( version_compare( $current_version, '1.2.0', '<' ) ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'llm_url_404_logs';

			// Add generation_status column
			$wpdb->query( "ALTER TABLE $table_name ADD COLUMN generation_status VARCHAR(20) DEFAULT 'pending' AFTER detected_post_type" );

			// Add generation_message column
			$wpdb->query( "ALTER TABLE $table_name ADD COLUMN generation_message TEXT AFTER generation_status" );

			update_option( 'llm_url_solution_db_version', '1.2.0' );
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
			'openai_api_key'      => '',
			'claude_api_key'      => '',
			'ai_model'            => 'gpt-4',
			'temperature'         => 0.7,
			'max_tokens'          => 1500,
			'rate_limit_hourly'   => 10,
			'rate_limit_daily'    => 50,
			'default_post_type'   => 'post',
			'default_post_status' => 'draft',
			'auto_categorize'     => true,
			'auto_tag'            => true,
			'generate_seo_meta'   => true,
			'blacklist_patterns'  => '',
			'content_min_length'  => 800,
			'content_max_length'  => 1500,
			'enable_debug_mode'   => false,
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

	/**
	 * Check if database needs to be upgraded and perform the upgrade if needed.
	 *
	 * @since    1.1.0
	 */
	public static function maybe_upgrade_database() {
		$current_version = get_option( 'llm_url_solution_db_version', '1.0.0' );

		// If the database version is different from the current version, upgrade
		if ( version_compare( $current_version, '1.2.0', '<' ) ) {
			self::upgrade_database();
		}
	}
}
