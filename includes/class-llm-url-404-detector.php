<?php
/**
 * The 404 detection functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 */

/**
 * The 404 detection functionality of the plugin.
 *
 * Detects 404 errors that originate from AI chatbot searches
 * and logs them for processing.
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 * @author     Very Good Plugins
 */
class LLM_URL_404_Detector {

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
	 * The database instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      LLM_URL_Database    $db    The database instance.
	 */
	private $db;

	/**
	 * AI chatbot referrer patterns.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $ai_referrer_patterns    Patterns to match AI chatbot referrers.
	 */
	private $ai_referrer_patterns = array(
		'chat.openai.com',
		'chatgpt.com',
		'claude.ai',
		'bard.google.com',
		'perplexity.ai',
		'you.com',
		'bing.com/chat',
		'poe.com',
	);

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name    The name of the plugin.
	 * @param    string    $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->db = new LLM_URL_Database();
	}

	/**
	 * Detect 404 errors with AI chatbot referrers.
	 *
	 * @since    1.0.0
	 */
	public function detect_404_with_ai_referrer() {
		if ( ! is_404() ) {
			return;
		}

		// Get referrer
		$referrer = isset( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
		
		// Check if referrer is from an AI chatbot
		if ( ! $this->is_ai_chatbot_referrer( $referrer ) ) {
			return;
		}

		// Get the requested URL
		$requested_url = $this->get_requested_url();
		$url_slug = $this->extract_url_slug( $requested_url );

		// Check blacklist
		if ( $this->is_blacklisted( $url_slug ) ) {
			return;
		}

		// Analyze URL for confidence and post type
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-analyzer.php';
		require_once LLM_URL_SOLUTION_PLUGIN_DIR . 'includes/class-llm-url-content-generator.php';
		
		$analyzer = new LLM_URL_Analyzer();
		$analysis = $analyzer->analyze_url( $url_slug );
		
		// Detect post type
		$generator = new LLM_URL_Content_Generator();
		$detected_post_type = $this->detect_post_type_from_url( $requested_url );

		// Prepare data for logging
		$log_data = array(
			'requested_url' => $requested_url,
			'url_slug'      => $url_slug,
			'referrer'      => $referrer,
			'ip_address'    => $this->get_client_ip(),
			'user_agent'    => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '',
			'confidence_score' => $analysis['confidence'],
			'detected_post_type' => $detected_post_type,
		);

		// Log the 404
		$logged = $this->db->log_404( $log_data );

		if ( $logged ) {
			// Trigger action for other plugins/themes to hook into
			do_action( 'llm_url_solution_404_logged', $log_data );
			
			// Maybe trigger immediate content generation
			if ( get_option( 'llm_url_solution_auto_generate', false ) ) {
				$this->maybe_auto_generate_content( $logged );
			}
		}
	}

	/**
	 * Check if the referrer is from an AI chatbot.
	 *
	 * @since    1.0.0
	 * @param    string    $referrer    The referrer URL.
	 * @return   bool                   True if from AI chatbot, false otherwise.
	 */
	private function is_ai_chatbot_referrer( $referrer ) {
		if ( empty( $referrer ) ) {
			return false;
		}

		// Check against known AI chatbot patterns
		foreach ( $this->ai_referrer_patterns as $pattern ) {
			if ( strpos( $referrer, $pattern ) !== false ) {
				return true;
			}
		}

		// Check custom patterns from settings
		$custom_patterns = get_option( 'llm_url_solution_custom_referrer_patterns', '' );
		if ( ! empty( $custom_patterns ) ) {
			$patterns = array_map( 'trim', explode( "\n", $custom_patterns ) );
			foreach ( $patterns as $pattern ) {
				if ( ! empty( $pattern ) && strpos( $referrer, $pattern ) !== false ) {
					return true;
				}
			}
		}

		return apply_filters( 'llm_url_solution_is_ai_referrer', false, $referrer );
	}

	/**
	 * Get the full requested URL.
	 *
	 * @since    1.0.0
	 * @return   string    The requested URL.
	 */
	private function get_requested_url() {
		$protocol = is_ssl() ? 'https://' : 'http://';
		$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		$uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		
		return $protocol . $host . $uri;
	}

	/**
	 * Extract the URL slug from the full URL.
	 *
	 * @since    1.0.0
	 * @param    string    $url    The full URL.
	 * @return   string            The URL slug.
	 */
	private function extract_url_slug( $url ) {
		$parsed = wp_parse_url( $url );
		$path = isset( $parsed['path'] ) ? $parsed['path'] : '';
		
		// Remove leading/trailing slashes
		$path = trim( $path, '/' );
		
		// Remove common file extensions
		$path = preg_replace( '/\.(html?|php|aspx?|jsp)$/i', '', $path );
		
		// Convert to slug format
		$slug = sanitize_title( $path );
		
		return $slug;
	}

	/**
	 * Check if a URL slug is blacklisted.
	 *
	 * @since    1.0.0
	 * @param    string    $slug    The URL slug.
	 * @return   bool               True if blacklisted, false otherwise.
	 */
	private function is_blacklisted( $slug ) {
		// Common patterns to ignore
		$default_blacklist = array(
			'wp-admin',
			'wp-login',
			'wp-content',
			'wp-includes',
			'feed',
			'rss',
			'sitemap',
			'robots.txt',
			'.git',
			'.env',
			'xmlrpc.php',
		);

		// Check default blacklist
		foreach ( $default_blacklist as $pattern ) {
			if ( strpos( $slug, $pattern ) !== false ) {
				return true;
			}
		}

		// Check custom blacklist from settings
		$custom_blacklist = get_option( 'llm_url_solution_blacklist_patterns', '' );
		if ( ! empty( $custom_blacklist ) ) {
			$patterns = array_map( 'trim', explode( "\n", $custom_blacklist ) );
			foreach ( $patterns as $pattern ) {
				if ( ! empty( $pattern ) && strpos( $slug, $pattern ) !== false ) {
					return true;
				}
			}
		}

		return apply_filters( 'llm_url_solution_is_blacklisted', false, $slug );
	}

	/**
	 * Get the client IP address.
	 *
	 * @since    1.0.0
	 * @return   string    The client IP address.
	 */
	private function get_client_ip() {
		$ip_keys = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
		
		foreach ( $ip_keys as $key ) {
			if ( array_key_exists( $key, $_SERVER ) === true ) {
				foreach ( explode( ',', sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ) ) as $ip ) {
					$ip = trim( $ip );
					
					if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
						return $ip;
					}
				}
			}
		}
		
		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
	}

	/**
	 * Maybe auto-generate content for a 404 log.
	 *
	 * @since    1.0.0
	 * @param    int    $log_id    The log ID.
	 */
	private function maybe_auto_generate_content( $log_id ) {
		// Check rate limits
		$rate_check = $this->db->check_rate_limits();
		if ( ! $rate_check['allowed'] ) {
			return;
		}

		// Schedule content generation
		wp_schedule_single_event( time() + 10, 'llm_url_solution_generate_content', array( $log_id ) );
	}

	/**
	 * Detect post type from URL structure based on taxonomy terms.
	 *
	 * @since    1.1.0
	 * @param    string    $url    The requested URL.
	 * @return   string|null       The detected post type or null.
	 */
	private function detect_post_type_from_url( $url ) {
		// Parse the URL to get the path
		$parsed = wp_parse_url( $url );
		$path = isset( $parsed['path'] ) ? trim( $parsed['path'], '/' ) : '';
		
		// Split the path into segments
		$segments = explode( '/', $path );
		
		if ( empty( $segments ) ) {
			return null;
		}
		
		// Check first segment
		if ( isset( $segments[0] ) && ! empty( $segments[0] ) ) {
			$post_type = $this->check_taxonomy_term_for_post_type( $segments[0] );
			if ( $post_type ) {
				return $post_type;
			}
		}
		
		// Check second segment if first didn't match
		if ( isset( $segments[1] ) && ! empty( $segments[1] ) ) {
			$post_type = $this->check_taxonomy_term_for_post_type( $segments[1] );
			if ( $post_type ) {
				return $post_type;
			}
		}
		
		return null;
	}

	/**
	 * Check if a slug is a taxonomy term and return associated post type.
	 *
	 * @since    1.1.0
	 * @param    string    $slug    The slug to check.
	 * @return   string|null        The associated post type or null.
	 */
	private function check_taxonomy_term_for_post_type( $slug ) {
		// Get all public taxonomies
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		
		foreach ( $taxonomies as $taxonomy ) {
			// Check if term exists in this taxonomy
			$term = get_term_by( 'slug', $slug, $taxonomy->name );
			
			if ( $term ) {
				// Get post types associated with this taxonomy
				$post_types = $taxonomy->object_type;
				
				// Return the first non-attachment post type
				foreach ( $post_types as $post_type ) {
					if ( $post_type !== 'attachment' ) {
						return $post_type;
					}
				}
			}
		}
		
		return null;
	}

	/**
	 * Maybe generate content on template redirect.
	 *
	 * @since    1.0.0
	 */
	public function maybe_generate_content() {
		// This method can be used for real-time content generation
		// if needed in future versions
	}
} 