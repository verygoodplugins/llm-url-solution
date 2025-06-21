<?php
/**
 * Dashboard page template
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

// Get statistics
$db          = new LLM_URL_Database();
$stats       = $db->get_statistics();
$recent_404s = $db->get_unprocessed_404s( 5 );
$rate_limits = $db->check_rate_limits();
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	
	<?php if ( ! get_option( 'llm_url_solution_openai_api_key' ) && ! get_option( 'llm_url_solution_claude_api_key' ) ) : ?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: %s: Settings page URL */
					esc_html__( 'Please configure your AI API keys in the %s to start generating content.', 'llm-url-solution' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=llm-url-solution-settings' ) ) . '">' . esc_html__( 'settings page', 'llm-url-solution' ) . '</a>'
				);
				?>
			</p>
		</div>
	<?php endif; ?>
	
	<!-- Statistics Overview -->
	<div class="llm-url-dashboard-stats">
		<h2><?php esc_html_e( 'Statistics Overview', 'llm-url-solution' ); ?></h2>
		
		<div class="llm-url-stat-boxes">
			<div class="llm-url-stat-box">
				<h3><?php esc_html_e( 'Total 404s', 'llm-url-solution' ); ?></h3>
				<p class="llm-url-stat-number"><?php echo esc_html( number_format_i18n( $stats['total_404s'] ) ); ?></p>
			</div>
			
			<div class="llm-url-stat-box">
				<h3><?php esc_html_e( 'Unprocessed', 'llm-url-solution' ); ?></h3>
				<p class="llm-url-stat-number"><?php echo esc_html( number_format_i18n( $stats['unprocessed_404s'] ) ); ?></p>
			</div>
			
			<div class="llm-url-stat-box">
				<h3><?php esc_html_e( 'Content Generated', 'llm-url-solution' ); ?></h3>
				<p class="llm-url-stat-number"><?php echo esc_html( number_format_i18n( $stats['content_generated'] ) ); ?></p>
			</div>
			
			<div class="llm-url-stat-box">
				<h3><?php esc_html_e( 'Today\'s 404s', 'llm-url-solution' ); ?></h3>
				<p class="llm-url-stat-number"><?php echo esc_html( number_format_i18n( $stats['today_404s'] ) ); ?></p>
			</div>
		</div>
	</div>
	
	<!-- Rate Limits Status -->
	<div class="llm-url-rate-limits">
		<h2><?php esc_html_e( 'Rate Limits', 'llm-url-solution' ); ?></h2>
		
		<?php if ( $rate_limits['allowed'] ) : ?>
			<div class="notice notice-success inline">
				<p><?php esc_html_e( 'Content generation is available. Rate limits have not been reached.', 'llm-url-solution' ); ?></p>
			</div>
		<?php else : ?>
			<div class="notice notice-error inline">
				<p><?php echo esc_html( $rate_limits['message'] ); ?></p>
			</div>
		<?php endif; ?>
		
		<p>
			<?php
			printf(
				/* translators: 1: Hourly limit, 2: Daily limit */
				esc_html__( 'Current limits: %1$s per hour, %2$s per day', 'llm-url-solution' ),
				'<strong>' . esc_html( number_format_i18n( get_option( 'llm_url_solution_rate_limit_hourly', 10 ) ) ) . '</strong>',
				'<strong>' . esc_html( number_format_i18n( get_option( 'llm_url_solution_rate_limit_daily', 50 ) ) ) . '</strong>'
			);
			?>
		</p>
	</div>
	
	<!-- Recent Unprocessed 404s -->
	<div class="llm-url-recent-404s">
		<h2><?php esc_html_e( 'Recent Unprocessed 404s', 'llm-url-solution' ); ?></h2>
		
		<?php if ( ! empty( $recent_404s ) ) : ?>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'URL', 'llm-url-solution' ); ?></th>
						<th><?php esc_html_e( 'Referrer', 'llm-url-solution' ); ?></th>
						<th><?php esc_html_e( 'Date', 'llm-url-solution' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'llm-url-solution' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $recent_404s as $log ) : ?>
						<tr>
							<td>
								<strong><?php echo esc_html( $log->url_slug ); ?></strong><br>
								<small><?php echo esc_url( $log->requested_url ); ?></small>
							</td>
							<td>
								<?php
								$referrer_domain = wp_parse_url( $log->referrer, PHP_URL_HOST );
								echo esc_html( $referrer_domain ?: __( 'Unknown', 'llm-url-solution' ) );
								?>
							</td>
							<td><?php echo esc_html( human_time_diff( strtotime( $log->timestamp ), current_time( 'timestamp' ) ) ); ?> <?php esc_html_e( 'ago', 'llm-url-solution' ); ?></td>
							<td>
								<button class="button button-primary llm-url-generate-content" data-log-id="<?php echo esc_attr( $log->id ); ?>">
									<?php esc_html_e( 'Generate Content', 'llm-url-solution' ); ?>
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=llm-url-solution-logs' ) ); ?>" class="button">
					<?php esc_html_e( 'View All Logs', 'llm-url-solution' ); ?>
				</a>
			</p>
		<?php else : ?>
			<p><?php esc_html_e( 'No unprocessed 404 errors found.', 'llm-url-solution' ); ?></p>
		<?php endif; ?>
	</div>
	
	<!-- Quick Actions -->
	<div class="llm-url-quick-actions">
		<h2><?php esc_html_e( 'Quick Actions', 'llm-url-solution' ); ?></h2>
		
		<p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=llm-url-solution-settings' ) ); ?>" class="button">
				<?php esc_html_e( 'Configure Settings', 'llm-url-solution' ); ?>
			</a>
			
			<a href="<?php echo esc_url( admin_url( 'edit.php?meta_key=_llm_url_solution_generated&meta_value=1' ) ); ?>" class="button">
				<?php esc_html_e( 'View Generated Content', 'llm-url-solution' ); ?>
			</a>
		</p>
	</div>
</div> 