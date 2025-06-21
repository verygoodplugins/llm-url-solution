<?php
/**
 * Dashboard widget template
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/templates
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Stats should be passed from the calling function
if ( ! isset( $stats ) ) {
	return;
}
?>

<div class="llm-url-dashboard-widget">
	<div class="llm-url-widget-stats">
		<div class="llm-url-widget-stat">
			<span class="llm-url-stat-value"><?php echo esc_html( number_format_i18n( $stats['unprocessed_404s'] ) ); ?></span>
			<span class="llm-url-stat-label"><?php esc_html_e( 'Unprocessed 404s', 'llm-url-solution' ); ?></span>
		</div>
		
		<div class="llm-url-widget-stat">
			<span class="llm-url-stat-value"><?php echo esc_html( number_format_i18n( $stats['content_generated'] ) ); ?></span>
			<span class="llm-url-stat-label"><?php esc_html_e( 'Content Generated', 'llm-url-solution' ); ?></span>
		</div>
		
		<div class="llm-url-widget-stat">
			<span class="llm-url-stat-value"><?php echo esc_html( number_format_i18n( $stats['today_404s'] ) ); ?></span>
			<span class="llm-url-stat-label"><?php esc_html_e( 'Today\'s 404s', 'llm-url-solution' ); ?></span>
		</div>
	</div>
	
	<p class="llm-url-widget-actions">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=llm-url-solution' ) ); ?>" class="button button-primary">
			<?php esc_html_e( 'View Dashboard', 'llm-url-solution' ); ?>
		</a>
		
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=llm-url-solution-logs' ) ); ?>" class="button">
			<?php esc_html_e( 'View All Logs', 'llm-url-solution' ); ?>
		</a>
	</p>
</div>

<style>
.llm-url-widget-stats {
	display: flex;
	justify-content: space-between;
	margin-bottom: 15px;
}

.llm-url-widget-stat {
	text-align: center;
	flex: 1;
}

.llm-url-stat-value {
	display: block;
	font-size: 24px;
	font-weight: bold;
	color: #23282d;
}

.llm-url-stat-label {
	display: block;
	font-size: 12px;
	color: #666;
	margin-top: 5px;
}

.llm-url-widget-actions {
	text-align: center;
	margin: 0;
	padding-top: 10px;
	border-top: 1px solid #eee;
}
</style> 