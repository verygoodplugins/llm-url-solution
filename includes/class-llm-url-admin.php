<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for the admin area.
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/admin
 * @author     Your Company Name
 */
class LLM_URL_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name    The name of this plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 
			$this->plugin_name, 
			LLM_URL_SOLUTION_PLUGIN_URL . 'assets/css/admin.css', 
			array(), 
			$this->version, 
			'all' 
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 
			$this->plugin_name, 
			LLM_URL_SOLUTION_PLUGIN_URL . 'assets/js/admin.js', 
			array( 'jquery' ), 
			$this->version, 
			false 
		);

		// Localize script for AJAX
		wp_localize_script(
			$this->plugin_name,
			'llm_url_solution_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'admin_url' => admin_url(),
				'nonce'    => wp_create_nonce( 'llm_url_solution_ajax' ),
				'confirm_generate' => __( 'Are you sure you want to generate content for this URL?', 'llm-url-solution' ),
				'confirm_delete' => __( 'Are you sure you want to delete this log?', 'llm-url-solution' ),
			)
		);
	}

	/**
	 * Add plugin admin menu.
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu() {
		// Main menu
		add_menu_page(
			__( 'LLM URL Solution', 'llm-url-solution' ),
			__( 'LLM URL Solution', 'llm-url-solution' ),
			'manage_llm_url_solution',
			'llm-url-solution',
			array( $this, 'display_dashboard_page' ),
			'dashicons-admin-links',
			30
		);

		// Dashboard submenu
		add_submenu_page(
			'llm-url-solution',
			__( 'Dashboard', 'llm-url-solution' ),
			__( 'Dashboard', 'llm-url-solution' ),
			'manage_llm_url_solution',
			'llm-url-solution',
			array( $this, 'display_dashboard_page' )
		);

		// 404 Logs submenu
		add_submenu_page(
			'llm-url-solution',
			__( '404 Logs', 'llm-url-solution' ),
			__( '404 Logs', 'llm-url-solution' ),
			'manage_llm_url_solution',
			'llm-url-solution-logs',
			array( $this, 'display_logs_page' )
		);

		// Settings submenu
		add_submenu_page(
			'llm-url-solution',
			__( 'Settings', 'llm-url-solution' ),
			__( 'Settings', 'llm-url-solution' ),
			'manage_llm_url_solution',
			'llm-url-solution-settings',
			array( $this, 'display_settings_page' )
		);
	}

	/**
	 * Display the dashboard page.
	 *
	 * @since    1.0.0
	 */
	public function display_dashboard_page() {
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'admin/dashboard.php';
	}

	/**
	 * Display the logs page.
	 *
	 * @since    1.0.0
	 */
	public function display_logs_page() {
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'admin/logs.php';
	}

	/**
	 * Display the settings page.
	 *
	 * @since    1.0.0
	 */
	public function display_settings_page() {
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'admin/settings.php';
	}

	/**
	 * Register plugin settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		// API Settings
		register_setting( 'llm_url_solution_api_settings', 'llm_url_solution_openai_api_key', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		
		register_setting( 'llm_url_solution_api_settings', 'llm_url_solution_claude_api_key', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		) );
		
		register_setting( 'llm_url_solution_api_settings', 'llm_url_solution_ai_model', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'gpt-4',
		) );
		
		register_setting( 'llm_url_solution_api_settings', 'llm_url_solution_temperature', array(
			'type'              => 'number',
			'sanitize_callback' => array( $this, 'sanitize_float' ),
			'default'           => 0.7,
		) );
		
		register_setting( 'llm_url_solution_api_settings', 'llm_url_solution_max_tokens', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 1500,
		) );

		// Content Settings
		register_setting( 'llm_url_solution_content_settings', 'llm_url_solution_default_post_type', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'post',
		) );
		
		register_setting( 'llm_url_solution_content_settings', 'llm_url_solution_default_post_status', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => 'draft',
		) );
		
		register_setting( 'llm_url_solution_content_settings', 'llm_url_solution_auto_categorize', array(
			'type'              => 'boolean',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'default'           => true,
		) );
		
		register_setting( 'llm_url_solution_content_settings', 'llm_url_solution_auto_tag', array(
			'type'              => 'boolean',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'default'           => true,
		) );
		
		register_setting( 'llm_url_solution_content_settings', 'llm_url_solution_content_min_length', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 800,
		) );
		
		register_setting( 'llm_url_solution_content_settings', 'llm_url_solution_content_max_length', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 1500,
		) );

		// Safety Settings
		register_setting( 'llm_url_solution_safety_settings', 'llm_url_solution_rate_limit_hourly', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 10,
		) );
		
		register_setting( 'llm_url_solution_safety_settings', 'llm_url_solution_rate_limit_daily', array(
			'type'              => 'integer',
			'sanitize_callback' => 'absint',
			'default'           => 50,
		) );
		
		register_setting( 'llm_url_solution_safety_settings', 'llm_url_solution_manual_approval', array(
			'type'              => 'boolean',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'default'           => true,
		) );
		
		register_setting( 'llm_url_solution_safety_settings', 'llm_url_solution_blacklist_patterns', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_textarea_field',
			'default'           => '',
		) );
		
		register_setting( 'llm_url_solution_safety_settings', 'llm_url_solution_min_confidence', array(
			'type'              => 'number',
			'sanitize_callback' => array( $this, 'sanitize_float' ),
			'default'           => 0.3,
		) );

		// Advanced Settings
		register_setting( 'llm_url_solution_advanced_settings', 'llm_url_solution_custom_referrer_patterns', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_textarea_field',
			'default'           => '',
		) );
		
		register_setting( 'llm_url_solution_advanced_settings', 'llm_url_solution_custom_prompt', array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_textarea_field',
			'default'           => '',
		) );
		
		register_setting( 'llm_url_solution_advanced_settings', 'llm_url_solution_enable_debug_mode', array(
			'type'              => 'boolean',
			'sanitize_callback' => 'rest_sanitize_boolean',
			'default'           => false,
		) );
	}

	/**
	 * Sanitize float value.
	 *
	 * @since    1.0.0
	 * @param    mixed    $value    The value to sanitize.
	 * @return   float               The sanitized float.
	 */
	public function sanitize_float( $value ) {
		return (float) $value;
	}

	/**
	 * Add dashboard widget.
	 *
	 * @since    1.0.0
	 */
	public function add_dashboard_widget() {
		if ( current_user_can( 'manage_llm_url_solution' ) ) {
			wp_add_dashboard_widget(
				'llm_url_solution_dashboard_widget',
				__( 'LLM URL Solution Overview', 'llm-url-solution' ),
				array( $this, 'display_dashboard_widget' )
			);
		}
	}

	/**
	 * Display dashboard widget.
	 *
	 * @since    1.0.0
	 */
	public function display_dashboard_widget() {
		$db = new LLM_URL_Database();
		$stats = $db->get_statistics();
		
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'templates/dashboard-widget.php';
	}

	/**
	 * Add plugin action links.
	 *
	 * @since    1.0.0
	 * @param    array    $links    Existing links.
	 * @return   array              Modified links.
	 */
	public function add_action_links( $links ) {
		$action_links = array(
			'<a href="' . admin_url( 'admin.php?page=llm-url-solution-settings' ) . '">' . __( 'Settings', 'llm-url-solution' ) . '</a>',
			'<a href="' . admin_url( 'admin.php?page=llm-url-solution' ) . '">' . __( 'Dashboard', 'llm-url-solution' ) . '</a>',
		);
		
		return array_merge( $action_links, $links );
	}

	/**
	 * Handle AJAX request to generate content.
	 *
	 * @since    1.0.0
	 */
	public function ajax_generate_content() {
		// Check nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'llm_url_solution_ajax' ) ) {
			wp_die( esc_html__( 'Security check failed', 'llm-url-solution' ) );
		}

		// Check permissions
		if ( ! current_user_can( 'approve_llm_url_content' ) ) {
			wp_send_json_error( __( 'Insufficient permissions', 'llm-url-solution' ) );
		}

		// Get log ID
		$log_id = isset( $_POST['log_id'] ) ? absint( $_POST['log_id'] ) : 0;
		if ( ! $log_id ) {
			wp_send_json_error( __( 'Invalid log ID', 'llm-url-solution' ) );
		}

		// Generate content
		$generator = new LLM_URL_Content_Generator();
		$result = $generator->generate_content_for_log( $log_id );

		if ( $result['success'] ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error( $result['message'] );
		}
	}

	/**
	 * Handle AJAX request to delete a log.
	 *
	 * @since    1.0.0
	 */
	public function ajax_delete_log() {
		// Check nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'llm_url_solution_ajax' ) ) {
			wp_die( esc_html__( 'Security check failed', 'llm-url-solution' ) );
		}

		// Check permissions
		if ( ! current_user_can( 'manage_llm_url_solution' ) ) {
			wp_send_json_error( __( 'Insufficient permissions', 'llm-url-solution' ) );
		}

		// Get log ID
		$log_id = isset( $_POST['log_id'] ) ? absint( $_POST['log_id'] ) : 0;
		if ( ! $log_id ) {
			wp_send_json_error( __( 'Invalid log ID', 'llm-url-solution' ) );
		}

		// Delete log
		global $wpdb;
		$table = $wpdb->prefix . 'llm_url_404_logs';
		$deleted = $wpdb->delete( $table, array( 'id' => $log_id ), array( '%d' ) );

		if ( $deleted ) {
			wp_send_json_success( __( 'Log deleted successfully', 'llm-url-solution' ) );
		} else {
			wp_send_json_error( __( 'Failed to delete log', 'llm-url-solution' ) );
		}
	}
} 