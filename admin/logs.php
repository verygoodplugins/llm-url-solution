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

// phpcs:disable WordPress.Security.NonceVerification.Recommended
// phpcs:disable WordPress.Security.NonceVerification.Missing
// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$db = new LLM_URL_Database();

// Handle flush all logs action.
if ( isset( $_POST['llm_url_solution_flush_logs_nonce'] ) ) {
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['llm_url_solution_flush_logs_nonce'] ) ), 'llm_url_solution_flush_logs' ) ) {
		wp_die( esc_html__( 'Security check failed', 'llm-url-solution' ) );
	}

	if ( current_user_can( 'manage_options' ) ) {
		$db->flush_logs();
		wp_safe_redirect( admin_url( 'admin.php?page=llm-url-solution-logs&flushed=true' ) );
		exit;
	}
}

// Handle bulk actions.
if ( isset( $_POST['action'] ) && '-1' !== $_POST['action'] ) {
	if ( ! isset( $_POST['llm_url_logs_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['llm_url_logs_nonce'] ) ), 'llm_url_logs_bulk_action' ) ) {
		wp_die( esc_html__( 'Security check failed', 'llm-url-solution' ) );
	}

	if ( current_user_can( 'manage_llm_url_solution' ) || current_user_can( 'manage_options' ) ) {
		if ( isset( $_POST['log_ids'] ) && is_array( $_POST['log_ids'] ) ) {
			$action  = sanitize_text_field( wp_unslash( $_POST['action'] ) );
			$log_ids = array_map( 'absint', $_POST['log_ids'] );

			switch ( $action ) {
				case 'delete':
					$db->delete_logs( $log_ids );
					wp_safe_redirect( admin_url( 'admin.php?page=llm-url-solution-logs&bulk_deleted=true' ) );
					exit;

				case 'mark_processed':
					foreach ( $log_ids as $log_id ) {
						$db->mark_as_processed( $log_id );
					}
					wp_safe_redirect( admin_url( 'admin.php?page=llm-url-solution-logs&bulk_processed=true' ) );
					exit;
			}
		}
	}
}


// Get current page.
$current_page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
$per_page     = 20;

// Get filters.
$filters = array();
if ( isset( $_GET['filter_processed'] ) && '' !== $_GET['filter_processed'] ) {
	$filters['processed'] = absint( $_GET['filter_processed'] );
}
if ( isset( $_GET['filter_generated'] ) && '' !== $_GET['filter_generated'] ) {
	$filters['content_generated'] = absint( $_GET['filter_generated'] );
}
if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
	$filters['search'] = sanitize_text_field( wp_unslash( $_GET['s'] ) );
}

// Get logs.
$results     = $db->get_404_logs( $current_page, $per_page, $filters );
$logs        = $results['logs'];
$total       = $results['total'];
$total_pages = ceil( $total / $per_page );

?>

<div class="wrap">
	<h1><?php esc_html_e( '404 Logs', 'llm-url-solution' ); ?></h1>

	<?php if ( isset( $_GET['flushed'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'All logs have been flushed successfully.', 'llm-url-solution' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['bulk_deleted'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Selected logs deleted successfully.', 'llm-url-solution' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['bulk_processed'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Selected logs marked as processed.', 'llm-url-solution' ); ?></p></div>
	<?php endif; ?>
	
	<form method="post" style="margin-bottom: 20px;">
		<?php wp_nonce_field( 'llm_url_solution_flush_logs', 'llm_url_solution_flush_logs_nonce' ); ?>
		<input type="submit" name="flush_logs" class="button button-secondary" value="<?php esc_attr_e( 'Flush All Logs', 'llm-url-solution' ); ?>" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete all logs?', 'llm-url-solution' ); ?>');" />
	</form>
	
	<div class="tablenav top">
		<div class="alignleft actions bulkactions">
			<?php wp_nonce_field( 'llm_url_logs_bulk_action', 'llm_url_logs_nonce' ); ?>
			<label for="bulk-action-selector-top" class="screen-reader-text"><?php esc_html_e( 'Select bulk action', 'llm-url-solution' ); ?></label>
			<select name="action" id="bulk-action-selector-top">
				<option value="-1"><?php esc_html_e( 'Bulk Actions', 'llm-url-solution' ); ?></option>
				<option value="delete"><?php esc_html_e( 'Delete', 'llm-url-solution' ); ?></option>
				<option value="mark_processed"><?php esc_html_e( 'Mark as Processed', 'llm-url-solution' ); ?></option>
			</select>
			<input type="submit" id="doaction" class="button action" value="<?php esc_attr_e( 'Apply', 'llm-url-solution' ); ?>">
		</div>
		<div class="tablenav-pages">
			<span class="displaying-num">
				<?php
				printf(
					/* translators: %s: number of items */
					esc_html( _n( '%s item', '%s items', $total, 'llm-url-solution' ) ),
					number_format_i18n( $total )
				);
				?>
			</span>
			<?php
			$page_links = paginate_links(
				array(
					'base'      => add_query_arg( 'paged', '%#%' ),
					'format'    => '',
					'prev_text' => __( '&laquo;', 'llm-url-solution' ),
					'next_text' => __( '&raquo;', 'llm-url-solution' ),
					'total'     => $total_pages,
					'current'   => $current_page,
				)
			);

			if ( $page_links ) {
				echo '<span class="pagination-links">' . $page_links . '</span>';
			}
			?>
		</div>
	</div>
	
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column">
					<label class="screen-reader-text" for="cb-select-all-1"><?php esc_html_e( 'Select All', 'llm-url-solution' ); ?></label>
					<input id="cb-select-all-1" type="checkbox">
				</td>
				<th><?php esc_html_e( 'URL', 'llm-url-solution' ); ?></th>
				<th><?php esc_html_e( 'Referrer', 'llm-url-solution' ); ?></th>
				<th><?php esc_html_e( 'Analysis', 'llm-url-solution' ); ?></th>
				<th><?php esc_html_e( 'Status', 'llm-url-solution' ); ?></th>
				<th><?php esc_html_e( 'Timestamp', 'llm-url-solution' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'llm-url-solution' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $logs ) ) : ?>
				<tr>
					<td colspan="7"><?php esc_html_e( 'No 404 logs found.', 'llm-url-solution' ); ?></td>
				</tr>
			<?php else : ?>
				<?php foreach ( $logs as $log ) : ?>
					<tr>
						<th scope="row" class="check-column">
							<input id="cb-select-<?php echo esc_attr( $log->id ); ?>" type="checkbox" name="log_ids[]" value="<?php echo esc_attr( $log->id ); ?>">
						</th>
						<td>
							<strong><?php echo esc_html( $log->requested_url ); ?></strong><br>
							<small><?php echo esc_html( $log->url_slug ); ?></small>
						</td>
						<td>
							<?php
							if ( ! empty( $log->referrer ) ) {
								$parsed = wp_parse_url( $log->referrer );
								echo esc_html( isset( $parsed['host'] ) ? $parsed['host'] : $log->referrer );
							} else {
								echo '-';
							}
							?>
						</td>
						<td>
							<?php if ( ! empty( $log->confidence_score ) ) : ?>
								<span class="confidence-score" style="color: <?php echo $log->confidence_score >= 0.5 ? '#46b450' : '#dc3232'; ?>">
									<?php echo esc_html( number_format( $log->confidence_score * 100, 1 ) . '%' ); ?>
								</span>
							<?php endif; ?>
							<?php if ( ! empty( $log->detected_post_type ) ) : ?>
								<br><small><?php echo esc_html( 'Type: ' . $log->detected_post_type ); ?></small>
							<?php else : ?>
								<br><small><?php esc_html_e( 'Type: Not detected', 'llm-url-solution' ); ?></small>
							<?php endif; ?>
						</td>
						<td>
							<?php
							$status_colors = array(
								'pending'    => '#f0ad4e',
								'generating' => '#5bc0de',
								'success'    => '#5cb85c',
								'failed'     => '#d9534f',
							);
							$status        = $log->generation_status ?? 'pending';
							$color         = $status_colors[ $status ] ?? '#999';
							?>
							<span style="color: <?php echo esc_attr( $color ); ?>; font-weight: bold;">
								<?php echo esc_html( ucfirst( $status ) ); ?>
							</span>
							<?php if ( ! empty( $log->generation_message ) ) : ?>
								<br><small><?php echo esc_html( $log->generation_message ); ?></small>
							<?php endif; ?>
							<?php if ( $log->content_generated && $log->post_id ) : ?>
								<br><a href="<?php echo esc_url( get_edit_post_link( $log->post_id ) ); ?>" target="_blank">
									<?php esc_html_e( 'Edit Post', 'llm-url-solution' ); ?>
								</a>
							<?php endif; ?>
						</td>
						<td><?php echo esc_html( $log->timestamp ); ?></td>
						<td>
							<?php if ( ! $log->processed ) : ?>
								<button class="button button-primary generate-content" data-log-id="<?php echo esc_attr( $log->id ); ?>">
									<?php esc_html_e( 'Generate Content', 'llm-url-solution' ); ?>
								</button>
							<?php elseif ( $log->content_generated && $log->post_id ) : ?>
								<a href="<?php echo esc_url( get_permalink( $log->post_id ) ); ?>" class="button" target="_blank">
									<?php esc_html_e( 'View Post', 'llm-url-solution' ); ?>
								</a>
							<?php else : ?>
								<span class="dashicons dashicons-yes" style="color: #46b450;"></span>
								<?php esc_html_e( 'Processed', 'llm-url-solution' ); ?>
							<?php endif; ?>
							<button class="button button-link-delete delete-log" data-log-id="<?php echo esc_attr( $log->id ); ?>" style="color: #a00;">
								<?php esc_html_e( 'Delete', 'llm-url-solution' ); ?>
							</button>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div> 