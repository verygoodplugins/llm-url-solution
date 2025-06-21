<?php
/**
 * The database functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 */

/**
 * The database functionality of the plugin.
 *
 * Handles all database operations including logging 404s,
 * retrieving logs, and managing settings.
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 * @author     Very Good Plugins
 */
class LLM_URL_Database {

	/**
	 * The database table names.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $tables    Database table names.
	 */
	private $tables;

	/**
	 * Initialize the database class.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		global $wpdb;
		
		$this->tables = array(
			'404_logs' => $wpdb->prefix . 'llm_url_404_logs',
			'settings' => $wpdb->prefix . 'llm_url_settings',
		);
	}

	/**
	 * Log a 404 error.
	 *
	 * @since    1.0.0
	 * @param    array    $data    The 404 data to log.
	 * @return   int|false         The number of rows inserted, or false on error.
	 */
	public function log_404( $data ) {
		global $wpdb;

		// Sanitize data
		$insert_data = array(
			'requested_url' => esc_url_raw( $data['requested_url'] ),
			'url_slug'      => sanitize_title( $data['url_slug'] ),
			'referrer'      => ! empty( $data['referrer'] ) ? esc_url_raw( $data['referrer'] ) : '',
			'ip_address'    => ! empty( $data['ip_address'] ) ? sanitize_text_field( $data['ip_address'] ) : '',
			'user_agent'    => ! empty( $data['user_agent'] ) ? sanitize_text_field( $data['user_agent'] ) : '',
			'timestamp'     => current_time( 'mysql' ),
			'confidence_score' => isset( $data['confidence_score'] ) ? floatval( $data['confidence_score'] ) : null,
			'detected_post_type' => isset( $data['detected_post_type'] ) ? sanitize_text_field( $data['detected_post_type'] ) : null,
		);

		// Check if this URL was already logged recently (within last hour)
		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$this->tables['404_logs']} 
				WHERE url_slug = %s 
				AND timestamp > DATE_SUB(NOW(), INTERVAL 1 HOUR) 
				LIMIT 1",
				$insert_data['url_slug']
			)
		);

		if ( $existing ) {
			return false; // Don't log duplicates within an hour
		}

		return $wpdb->insert(
			$this->tables['404_logs'],
			$insert_data,
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s' )
		);
	}

	/**
	 * Get unprocessed 404 logs.
	 *
	 * @since    1.0.0
	 * @param    int      $limit    Number of logs to retrieve.
	 * @return   array              Array of log objects.
	 */
	public function get_unprocessed_404s( $limit = 10 ) {
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->tables['404_logs']} 
				WHERE processed = 0 
				ORDER BY timestamp DESC 
				LIMIT %d",
				$limit
			)
		);

		return $results ? $results : array();
	}

	/**
	 * Get all 404 logs with pagination.
	 *
	 * @since    1.0.0
	 * @param    int      $page         Current page number.
	 * @param    int      $per_page     Items per page.
	 * @param    array    $filters      Optional filters.
	 * @return   array                  Array containing logs and total count.
	 */
	public function get_404_logs( $page = 1, $per_page = 20, $filters = array() ) {
		global $wpdb;

		$offset = ( $page - 1 ) * $per_page;
		$where_clauses = array( '1=1' );

		// Apply filters
		if ( ! empty( $filters['processed'] ) ) {
			$where_clauses[] = $wpdb->prepare( 'processed = %d', $filters['processed'] );
		}

		if ( ! empty( $filters['content_generated'] ) ) {
			$where_clauses[] = $wpdb->prepare( 'content_generated = %d', $filters['content_generated'] );
		}

		if ( ! empty( $filters['search'] ) ) {
			$search = '%' . $wpdb->esc_like( $filters['search'] ) . '%';
			$where_clauses[] = $wpdb->prepare( '(requested_url LIKE %s OR url_slug LIKE %s)', $search, $search );
		}

		$where_sql = implode( ' AND ', $where_clauses );

		// Get total count
		$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$this->tables['404_logs']} WHERE $where_sql" );

		// Get logs
		$logs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->tables['404_logs']} 
				WHERE $where_sql 
				ORDER BY timestamp DESC 
				LIMIT %d OFFSET %d",
				$per_page,
				$offset
			)
		);

		return array(
			'logs'  => $logs ? $logs : array(),
			'total' => (int) $total,
		);
	}

	/**
	 * Mark a 404 log as processed.
	 *
	 * @since    1.0.0
	 * @param    int      $log_id    The log ID.
	 * @param    int      $post_id   Optional. The generated post ID.
	 * @return   int|false           The number of rows updated, or false on error.
	 */
	public function mark_as_processed( $log_id, $post_id = null ) {
		global $wpdb;

		$data = array(
			'processed' => 1,
		);

		if ( $post_id ) {
			$data['content_generated'] = 1;
			$data['post_id'] = (int) $post_id;
		}

		return $wpdb->update(
			$this->tables['404_logs'],
			$data,
			array( 'id' => (int) $log_id ),
			array( '%d', '%d', '%d' ),
			array( '%d' )
		);
	}

	/**
	 * Get a single 404 log by ID.
	 *
	 * @since    1.0.0
	 * @param    int      $log_id    The log ID.
	 * @return   object|null         The log object or null if not found.
	 */
	public function get_404_log( $log_id ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->tables['404_logs']} WHERE id = %d",
				$log_id
			)
		);
	}

	/**
	 * Delete old 404 logs.
	 *
	 * @since    1.0.0
	 * @param    int      $days    Number of days to keep logs.
	 * @return   int|false         The number of rows deleted, or false on error.
	 */
	public function cleanup_old_logs( $days = 30 ) {
		global $wpdb;

		return $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$this->tables['404_logs']} 
				WHERE timestamp < DATE_SUB(NOW(), INTERVAL %d DAY)",
				$days
			)
		);
	}

	/**
	 * Get statistics for the dashboard.
	 *
	 * @since    1.0.0
	 * @return   array    Statistics array.
	 */
	public function get_statistics() {
		global $wpdb;

		$stats = array();

		// Total 404s
		$stats['total_404s'] = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->tables['404_logs']}"
		);

		// Unprocessed 404s
		$stats['unprocessed_404s'] = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->tables['404_logs']} WHERE processed = 0"
		);

		// Content generated
		$stats['content_generated'] = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->tables['404_logs']} WHERE content_generated = 1"
		);

		// Today's 404s
		$stats['today_404s'] = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->tables['404_logs']} 
			WHERE DATE(timestamp) = CURDATE()"
		);

		// This week's 404s
		$stats['week_404s'] = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->tables['404_logs']} 
			WHERE timestamp > DATE_SUB(NOW(), INTERVAL 7 DAY)"
		);

		return $stats;
	}

	/**
	 * Check rate limits.
	 *
	 * @since    1.0.0
	 * @return   array    Array with 'allowed' boolean and 'message' string.
	 */
	public function check_rate_limits() {
		global $wpdb;

		$hourly_limit = (int) get_option( 'llm_url_solution_rate_limit_hourly', 10 );
		$daily_limit = (int) get_option( 'llm_url_solution_rate_limit_daily', 50 );

		// Check hourly limit
		$hourly_count = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->tables['404_logs']} 
			WHERE content_generated = 1 
			AND timestamp > DATE_SUB(NOW(), INTERVAL 1 HOUR)"
		);

		if ( $hourly_count >= $hourly_limit ) {
			return array(
				'allowed' => false,
				'message' => sprintf(
					/* translators: %d: hourly limit */
					__( 'Hourly rate limit reached (%d generations per hour)', 'llm-url-solution' ),
					$hourly_limit
				),
			);
		}

		// Check daily limit
		$daily_count = $wpdb->get_var(
			"SELECT COUNT(*) FROM {$this->tables['404_logs']} 
			WHERE content_generated = 1 
			AND DATE(timestamp) = CURDATE()"
		);

		if ( $daily_count >= $daily_limit ) {
			return array(
				'allowed' => false,
				'message' => sprintf(
					/* translators: %d: daily limit */
					__( 'Daily rate limit reached (%d generations per day)', 'llm-url-solution' ),
					$daily_limit
				),
			);
		}

		return array(
			'allowed' => true,
			'message' => '',
		);
	}
} 