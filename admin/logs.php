<?php
/**
 * Logs page template
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/admin
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get current page
$current_page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$per_page = 20;

// Get filters
$filters = array();
if ( isset( $_GET['filter_processed'] ) && $_GET['filter_processed'] !== '' ) {
	$filters['processed'] = absint( $_GET['filter_processed'] );
}
if ( isset( $_GET['filter_generated'] ) && $_GET['filter_generated'] !== '' ) {
	$filters['content_generated'] = absint( $_GET['filter_generated'] );
}
if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
	$filters['search'] = sanitize_text_field( wp_unslash( $_GET['s'] ) );
}

// Get logs
$db = new LLM_URL_Database();
$results = $db->get_404_logs( $current_page, $per_page, $filters );
$logs = $results['logs'];
$total_items = $results['total'];
$total_pages = ceil( $total_items / $per_page );

// Handle bulk actions
if ( isset( $_POST['action'] ) && $_POST['action'] !== '-1' ) {
	if ( ! isset( $_POST['llm_url_logs_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['llm_url_logs_nonce'] ) ), 'llm_url_logs_bulk_action' ) ) {
		wp_die( esc_html__( 'Security check failed', 'llm-url-solution' ) );
	}
	
	if ( current_user_can( 'manage_llm_url_solution' ) && isset( $_POST['log_ids'] ) && is_array( $_POST['log_ids'] ) ) {
		$action = sanitize_text_field( wp_unslash( $_POST['action'] ) );
		$log_ids = array_map( 'absint', $_POST['log_ids'] );
		
		switch ( $action ) {
			case 'delete':
				global $wpdb;
				$table = $wpdb->prefix . 'llm_url_404_logs';
				$placeholders = implode( ',', array_fill( 0, count( $log_ids ), '%d' ) );
				$wpdb->query( $wpdb->prepare( "DELETE FROM $table WHERE id IN ($placeholders)", $log_ids ) );
				echo '<div class="notice notice-success"><p>' . esc_html__( 'Selected logs deleted successfully.', 'llm-url-solution' ) . '</p></div>';
				break;
				
			case 'mark_processed':
				foreach ( $log_ids as $log_id ) {
					$db->mark_as_processed( $log_id );
				}
				echo '<div class="notice notice-success"><p>' . esc_html__( 'Selected logs marked as processed.', 'llm-url-solution' ) . '</p></div>';
				break;
		}
		
		// Refresh logs
		$results = $db->get_404_logs( $current_page, $per_page, $filters );
		$logs = $results['logs'];
		$total_items = $results['total'];
		$total_pages = ceil( $total_items / $per_page );
	}
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	
	<!-- Filters -->
	<form method="get" action="">
		<input type="hidden" name="page" value="llm-url-solution-logs" />
		
		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="filter_processed">
					<option value=""><?php esc_html_e( 'All Logs', 'llm-url-solution' ); ?></option>
					<option value="0" <?php selected( isset( $filters['processed'] ) && $filters['processed'] === 0 ); ?>><?php esc_html_e( 'Unprocessed', 'llm-url-solution' ); ?></option>
					<option value="1" <?php selected( isset( $filters['processed'] ) && $filters['processed'] === 1 ); ?>><?php esc_html_e( 'Processed', 'llm-url-solution' ); ?></option>
				</select>
				
				<select name="filter_generated">
					<option value=""><?php esc_html_e( 'All Generation Status', 'llm-url-solution' ); ?></option>
					<option value="1" <?php selected( isset( $filters['content_generated'] ) && $filters['content_generated'] === 1 ); ?>><?php esc_html_e( 'Content Generated', 'llm-url-solution' ); ?></option>
					<option value="0" <?php selected( isset( $filters['content_generated'] ) && $filters['content_generated'] === 0 ); ?>><?php esc_html_e( 'No Content', 'llm-url-solution' ); ?></option>
				</select>
				
				<?php submit_button( __( 'Filter', 'llm-url-solution' ), 'secondary', 'filter_action', false ); ?>
			</div>
			
			<div class="alignright">
				<p class="search-box">
					<label class="screen-reader-text" for="log-search-input"><?php esc_html_e( 'Search Logs:', 'llm-url-solution' ); ?></label>
					<input type="search" id="log-search-input" name="s" value="<?php echo esc_attr( $filters['search'] ?? '' ); ?>" />
					<?php submit_button( __( 'Search Logs', 'llm-url-solution' ), 'secondary', false, false, array( 'id' => 'search-submit' ) ); ?>
				</p>
			</div>
		</div>
	</form>
	
	<!-- Logs Table -->
	<form method="post" action="">
		<?php wp_nonce_field( 'llm_url_logs_bulk_action', 'llm_url_logs_nonce' ); ?>
		
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select name="action">
					<option value="-1"><?php esc_html_e( 'Bulk Actions', 'llm-url-solution' ); ?></option>
					<option value="delete"><?php esc_html_e( 'Delete', 'llm-url-solution' ); ?></option>
					<option value="mark_processed"><?php esc_html_e( 'Mark as Processed', 'llm-url-solution' ); ?></option>
				</select>
				<?php submit_button( __( 'Apply', 'llm-url-solution' ), 'action', false, false ); ?>
			</div>
			
			<div class="tablenav-pages">
				<span class="displaying-num">
					<?php
					printf(
						/* translators: %s: Number of items */
						_n( '%s item', '%s items', $total_items, 'llm-url-solution' ),
						number_format_i18n( $total_items )
					);
					?>
				</span>
				
				<?php if ( $total_pages > 1 ) : ?>
					<span class="pagination-links">
						<?php
						$page_links = paginate_links( array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'format'    => '',
							'prev_text' => '&laquo;',
							'next_text' => '&raquo;',
							'total'     => $total_pages,
							'current'   => $current_page,
						) );
						
						if ( $page_links ) {
							echo $page_links;
						}
						?>
					</span>
				<?php endif; ?>
			</div>
		</div>
		
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<td class="manage-column column-cb check-column">
						<input type="checkbox" id="cb-select-all-1" />
					</td>
					<th scope="col" class="manage-column"><?php esc_html_e( 'URL', 'llm-url-solution' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Referrer', 'llm-url-solution' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Date', 'llm-url-solution' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Status', 'llm-url-solution' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Actions', 'llm-url-solution' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $logs ) ) : ?>
					<?php foreach ( $logs as $log ) : ?>
						<tr>
							<th scope="row" class="check-column">
								<input type="checkbox" name="log_ids[]" value="<?php echo esc_attr( $log->id ); ?>" />
							</th>
							<td>
								<strong><?php echo esc_html( $log->url_slug ); ?></strong>
								<br>
								<small><?php echo esc_url( $log->requested_url ); ?></small>
								<?php if ( $log->post_id ) : ?>
									<br>
									<a href="<?php echo esc_url( get_edit_post_link( $log->post_id ) ); ?>" target="_blank">
										<?php esc_html_e( 'View Generated Post', 'llm-url-solution' ); ?> &rarr;
									</a>
								<?php endif; ?>
							</td>
							<td>
								<?php 
								$referrer_domain = wp_parse_url( $log->referrer, PHP_URL_HOST );
								echo esc_html( $referrer_domain ?: __( 'Unknown', 'llm-url-solution' ) );
								?>
								<br>
								<small><?php echo esc_html( $log->ip_address ); ?></small>
							</td>
							<td>
								<?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $log->timestamp ) ) ); ?>
								<br>
								<small><?php echo esc_html( human_time_diff( strtotime( $log->timestamp ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'llm-url-solution' ); ?></small>
							</td>
							<td>
								<?php if ( $log->processed ) : ?>
									<span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span> <?php esc_html_e( 'Processed', 'llm-url-solution' ); ?>
								<?php else : ?>
									<span class="dashicons dashicons-marker" style="color: #ffb900;"></span> <?php esc_html_e( 'Unprocessed', 'llm-url-solution' ); ?>
								<?php endif; ?>
								
								<?php if ( $log->content_generated ) : ?>
									<br><span class="dashicons dashicons-media-document" style="color: #00a0d2;"></span> <?php esc_html_e( 'Content Generated', 'llm-url-solution' ); ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( ! $log->processed ) : ?>
									<button class="button button-primary button-small llm-url-generate-content" data-log-id="<?php echo esc_attr( $log->id ); ?>">
										<?php esc_html_e( 'Generate', 'llm-url-solution' ); ?>
									</button>
								<?php endif; ?>
								
								<button class="button button-small llm-url-delete-log" data-log-id="<?php echo esc_attr( $log->id ); ?>">
									<?php esc_html_e( 'Delete', 'llm-url-solution' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="6"><?php esc_html_e( 'No logs found.', 'llm-url-solution' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td class="manage-column column-cb check-column">
						<input type="checkbox" id="cb-select-all-2" />
					</td>
					<th scope="col" class="manage-column"><?php esc_html_e( 'URL', 'llm-url-solution' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Referrer', 'llm-url-solution' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Date', 'llm-url-solution' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Status', 'llm-url-solution' ); ?></th>
					<th scope="col" class="manage-column"><?php esc_html_e( 'Actions', 'llm-url-solution' ); ?></th>
				</tr>
			</tfoot>
		</table>
		
		<div class="tablenav bottom">
			<div class="alignleft actions bulkactions">
				<select name="action2">
					<option value="-1"><?php esc_html_e( 'Bulk Actions', 'llm-url-solution' ); ?></option>
					<option value="delete"><?php esc_html_e( 'Delete', 'llm-url-solution' ); ?></option>
					<option value="mark_processed"><?php esc_html_e( 'Mark as Processed', 'llm-url-solution' ); ?></option>
				</select>
				<?php submit_button( __( 'Apply', 'llm-url-solution' ), 'action', false, false ); ?>
			</div>
		</div>
	</form>
</div> 