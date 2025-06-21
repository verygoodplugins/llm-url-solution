<?php
/**
 * Settings page template
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

// Get current tab
$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'api';
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	
	<h2 class="nav-tab-wrapper">
		<a href="?page=llm-url-solution-settings&tab=api" class="nav-tab <?php echo $active_tab === 'api' ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'API Settings', 'llm-url-solution' ); ?>
		</a>
		<a href="?page=llm-url-solution-settings&tab=content" class="nav-tab <?php echo $active_tab === 'content' ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Content Settings', 'llm-url-solution' ); ?>
		</a>
		<a href="?page=llm-url-solution-settings&tab=safety" class="nav-tab <?php echo $active_tab === 'safety' ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Safety Settings', 'llm-url-solution' ); ?>
		</a>
		<a href="?page=llm-url-solution-settings&tab=advanced" class="nav-tab <?php echo $active_tab === 'advanced' ? 'nav-tab-active' : ''; ?>">
			<?php esc_html_e( 'Advanced', 'llm-url-solution' ); ?>
		</a>
	</h2>
	
	<form method="post" action="options.php">
		<?php
		switch ( $active_tab ) {
			case 'api':
				settings_fields( 'llm_url_solution_api_settings' );
				?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="llm_url_solution_openai_api_key"><?php esc_html_e( 'OpenAI API Key', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<input type="password" id="llm_url_solution_openai_api_key" name="llm_url_solution_openai_api_key" value="<?php echo esc_attr( get_option( 'llm_url_solution_openai_api_key' ) ); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e( 'Enter your OpenAI API key for GPT models.', 'llm-url-solution' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_claude_api_key"><?php esc_html_e( 'Claude API Key', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<input type="password" id="llm_url_solution_claude_api_key" name="llm_url_solution_claude_api_key" value="<?php echo esc_attr( get_option( 'llm_url_solution_claude_api_key' ) ); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e( 'Enter your Anthropic API key for Claude models.', 'llm-url-solution' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_ai_model"><?php esc_html_e( 'AI Model', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<select id="llm_url_solution_ai_model" name="llm_url_solution_ai_model">
								<option value="gpt-4" <?php selected( get_option( 'llm_url_solution_ai_model' ), 'gpt-4' ); ?>>GPT-4</option>
								<option value="gpt-4-turbo-preview" <?php selected( get_option( 'llm_url_solution_ai_model' ), 'gpt-4-turbo-preview' ); ?>>GPT-4 Turbo</option>
								<option value="gpt-3.5-turbo" <?php selected( get_option( 'llm_url_solution_ai_model' ), 'gpt-3.5-turbo' ); ?>>GPT-3.5 Turbo</option>
								<option value="claude-3-opus-20240229" <?php selected( get_option( 'llm_url_solution_ai_model' ), 'claude-3-opus-20240229' ); ?>>Claude 3 Opus</option>
							</select>
							<p class="description"><?php esc_html_e( 'Select the AI model to use for content generation.', 'llm-url-solution' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_temperature"><?php esc_html_e( 'Temperature', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<input type="number" id="llm_url_solution_temperature" name="llm_url_solution_temperature" value="<?php echo esc_attr( get_option( 'llm_url_solution_temperature', 0.7 ) ); ?>" min="0" max="2" step="0.1" class="small-text" />
							<p class="description"><?php esc_html_e( 'Controls randomness: 0 = focused, 2 = very random. Default: 0.7', 'llm-url-solution' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_max_tokens"><?php esc_html_e( 'Max Tokens', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<input type="number" id="llm_url_solution_max_tokens" name="llm_url_solution_max_tokens" value="<?php echo esc_attr( get_option( 'llm_url_solution_max_tokens', 1500 ) ); ?>" min="100" max="4000" class="small-text" />
							<p class="description"><?php esc_html_e( 'Maximum number of tokens to generate. Default: 1500', 'llm-url-solution' ); ?></p>
						</td>
					</tr>
				</table>
				<?php
				break;

			case 'content':
				settings_fields( 'llm_url_solution_content_settings' );
				?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="llm_url_solution_default_post_type"><?php esc_html_e( 'Default Post Type', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<select id="llm_url_solution_default_post_type" name="llm_url_solution_default_post_type">
								<?php
								$post_types = get_post_types( array( 'public' => true ), 'objects' );
								foreach ( $post_types as $post_type ) :
									if ( $post_type->name === 'attachment' ) {
										continue;
									}
									?>
									<option value="<?php echo esc_attr( $post_type->name ); ?>" <?php selected( get_option( 'llm_url_solution_default_post_type' ), $post_type->name ); ?>>
										<?php echo esc_html( $post_type->labels->singular_name ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_default_post_status"><?php esc_html_e( 'Default Post Status', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<select id="llm_url_solution_default_post_status" name="llm_url_solution_default_post_status">
								<option value="draft" <?php selected( get_option( 'llm_url_solution_default_post_status' ), 'draft' ); ?>><?php esc_html_e( 'Draft', 'llm-url-solution' ); ?></option>
								<option value="pending" <?php selected( get_option( 'llm_url_solution_default_post_status' ), 'pending' ); ?>><?php esc_html_e( 'Pending Review', 'llm-url-solution' ); ?></option>
								<option value="publish" <?php selected( get_option( 'llm_url_solution_default_post_status' ), 'publish' ); ?>><?php esc_html_e( 'Published', 'llm-url-solution' ); ?></option>
							</select>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Auto-categorize', 'llm-url-solution' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="llm_url_solution_auto_categorize" value="1" <?php checked( get_option( 'llm_url_solution_auto_categorize', true ) ); ?> />
								<?php esc_html_e( 'Automatically assign categories based on content type', 'llm-url-solution' ); ?>
							</label>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Auto-tag', 'llm-url-solution' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="llm_url_solution_auto_tag" value="1" <?php checked( get_option( 'llm_url_solution_auto_tag', true ) ); ?> />
								<?php esc_html_e( 'Automatically generate and assign tags', 'llm-url-solution' ); ?>
							</label>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_content_min_length"><?php esc_html_e( 'Minimum Content Length', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<input type="number" id="llm_url_solution_content_min_length" name="llm_url_solution_content_min_length" value="<?php echo esc_attr( get_option( 'llm_url_solution_content_min_length', 800 ) ); ?>" min="100" max="5000" class="small-text" />
							<?php esc_html_e( 'words', 'llm-url-solution' ); ?>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_content_max_length"><?php esc_html_e( 'Maximum Content Length', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<input type="number" id="llm_url_solution_content_max_length" name="llm_url_solution_content_max_length" value="<?php echo esc_attr( get_option( 'llm_url_solution_content_max_length', 1500 ) ); ?>" min="100" max="5000" class="small-text" />
							<?php esc_html_e( 'words', 'llm-url-solution' ); ?>
						</td>
					</tr>
				</table>
				<?php
				break;

			case 'safety':
				settings_fields( 'llm_url_solution_safety_settings' );
				?>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Auto-Generate Content', 'llm-url-solution' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="llm_url_solution_auto_generate" value="1" <?php checked( get_option( 'llm_url_solution_auto_generate', false ) ); ?> />
								<?php esc_html_e( 'Automatically generate content when 404 errors are detected from AI chatbots', 'llm-url-solution' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'When enabled, content will be generated automatically without manual approval. Use with caution.', 'llm-url-solution' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_rate_limit_hourly"><?php esc_html_e( 'Hourly Rate Limit', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<input type="number" id="llm_url_solution_rate_limit_hourly" name="llm_url_solution_rate_limit_hourly" value="<?php echo esc_attr( get_option( 'llm_url_solution_rate_limit_hourly', 10 ) ); ?>" min="1" max="100" class="small-text" />
							<?php esc_html_e( 'generations per hour', 'llm-url-solution' ); ?>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_rate_limit_daily"><?php esc_html_e( 'Daily Rate Limit', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<input type="number" id="llm_url_solution_rate_limit_daily" name="llm_url_solution_rate_limit_daily" value="<?php echo esc_attr( get_option( 'llm_url_solution_rate_limit_daily', 50 ) ); ?>" min="1" max="1000" class="small-text" />
							<?php esc_html_e( 'generations per day', 'llm-url-solution' ); ?>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_blacklist_patterns"><?php esc_html_e( 'URL Blacklist Patterns', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<textarea id="llm_url_solution_blacklist_patterns" name="llm_url_solution_blacklist_patterns" rows="5" cols="50" class="large-text"><?php echo esc_textarea( get_option( 'llm_url_solution_blacklist_patterns' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Enter URL patterns to ignore, one per line. These URLs will not trigger content generation.', 'llm-url-solution' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_min_confidence"><?php esc_html_e( 'Minimum Confidence Score', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<input type="number" id="llm_url_solution_min_confidence" name="llm_url_solution_min_confidence" value="<?php echo esc_attr( get_option( 'llm_url_solution_min_confidence', 0.3 ) ); ?>" min="0" max="1" step="0.1" class="small-text" />
							<p class="description"><?php esc_html_e( 'Minimum confidence score (0-1) required for content generation. Default: 0.3', 'llm-url-solution' ); ?></p>
						</td>
					</tr>
				</table>
				<?php
				break;

			case 'advanced':
				settings_fields( 'llm_url_solution_advanced_settings' );
				?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="llm_url_solution_custom_referrer_patterns"><?php esc_html_e( 'Custom AI Referrer Patterns', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<textarea id="llm_url_solution_custom_referrer_patterns" name="llm_url_solution_custom_referrer_patterns" rows="5" cols="50" class="large-text"><?php echo esc_textarea( get_option( 'llm_url_solution_custom_referrer_patterns' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Add custom referrer domains to detect, one per line. Default patterns include: chat.openai.com, claude.ai, etc.', 'llm-url-solution' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="llm_url_solution_custom_prompt"><?php esc_html_e( 'Custom Prompt Instructions', 'llm-url-solution' ); ?></label>
						</th>
						<td>
							<textarea id="llm_url_solution_custom_prompt" name="llm_url_solution_custom_prompt" rows="5" cols="50" class="large-text"><?php echo esc_textarea( get_option( 'llm_url_solution_custom_prompt' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Add custom instructions to be included in the AI prompt for content generation.', 'llm-url-solution' ); ?></p>
						</td>
					</tr>
					
					<tr>
						<th scope="row"><?php esc_html_e( 'Debug Mode', 'llm-url-solution' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="llm_url_solution_enable_debug_mode" value="1" <?php checked( get_option( 'llm_url_solution_enable_debug_mode', false ) ); ?> />
								<?php esc_html_e( 'Enable debug mode (logs additional information)', 'llm-url-solution' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<?php
				break;
		}

		submit_button();
		?>
	</form>
</div> 