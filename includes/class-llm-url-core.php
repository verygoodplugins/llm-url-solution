<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 * @author     Your Company Name
 */
class LLM_URL_Core {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      LLM_URL_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->version     = LLM_URL_SOLUTION_VERSION;
		$this->plugin_name = 'llm-url-solution';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-i18n.php';

		/**
		 * The class responsible for database operations.
		 */
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-database.php';

		/**
		 * The class responsible for detecting 404 errors.
		 */
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-404-detector.php';

		/**
		 * The class responsible for analyzing URLs.
		 */
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-analyzer.php';

		/**
		 * The class responsible for generating content.
		 */
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-content-generator.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-admin.php';

		$this->loader = new LLM_URL_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new LLM_URL_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new LLM_URL_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'add_dashboard_widget' );
		$this->loader->add_filter( 'plugin_action_links_' . LLM_URL_SOLUTION_PLUGIN_BASENAME, $plugin_admin, 'add_action_links' );
		
		// AJAX handlers
		$this->loader->add_action( 'wp_ajax_llm_url_generate_content', $plugin_admin, 'ajax_generate_content' );
		$this->loader->add_action( 'wp_ajax_llm_url_delete_log', $plugin_admin, 'ajax_delete_log' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_detector = new LLM_URL_404_Detector( $this->get_plugin_name(), $this->get_version() );
		
		// Hook into WordPress 404 handling
		$this->loader->add_action( 'wp', $plugin_detector, 'detect_404_with_ai_referrer' );
		$this->loader->add_action( 'template_redirect', $plugin_detector, 'maybe_generate_content' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    LLM_URL_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
} 