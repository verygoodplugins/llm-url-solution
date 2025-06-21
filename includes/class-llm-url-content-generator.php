<?php
/**
 * The content generation functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 */

/**
 * The content generation functionality of the plugin.
 *
 * Generates content using AI APIs based on URL analysis.
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 * @author     Very Good Plugins
 */
class LLM_URL_Content_Generator {

	/**
	 * The database instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      LLM_URL_Database    $db    The database instance.
	 */
	private $db;

	/**
	 * The URL analyzer instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      LLM_URL_Analyzer    $analyzer    The URL analyzer instance.
	 */
	private $analyzer;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->db       = new LLM_URL_Database();
		$this->analyzer = new LLM_URL_Analyzer();

		// Register the scheduled event handler
		add_action( 'llm_url_solution_generate_content', array( $this, 'process_scheduled_generation' ) );
	}

	/**
	 * Generate content for a 404 log entry.
	 *
	 * @since    1.0.0
	 * @param    int $log_id    The log ID.
	 * @return   array             Result array with success status and message.
	 */
	public function generate_content_for_log( $log_id ) {
		// Update status to generating
		$this->db->update_generation_status( $log_id, 'generating', 'Starting content generation' );

		// Get the log entry
		$log = $this->db->get_404_log( $log_id );
		if ( ! $log ) {
			$this->db->update_generation_status( $log_id, 'failed', 'Log entry not found' );
			return array(
				'success' => false,
				'message' => __( 'Log entry not found.', 'llm-url-solution' ),
			);
		}

		// Check if already processed
		if ( $log->processed ) {
			$this->db->update_generation_status( $log_id, 'failed', 'Already processed' );
			return array(
				'success' => false,
				'message' => __( 'This log has already been processed.', 'llm-url-solution' ),
			);
		}

		// Check rate limits
		$rate_check = $this->db->check_rate_limits();
		if ( ! $rate_check['allowed'] ) {
			$this->db->update_generation_status( $log_id, 'failed', $rate_check['message'] );
			return array(
				'success' => false,
				'message' => $rate_check['message'],
			);
		}

		// Analyze the URL
		$this->db->update_generation_status( $log_id, 'generating', 'Analyzing URL' );
		$analysis = $this->analyzer->analyze_url( $log->url_slug );

		// Check confidence threshold
		$min_confidence = (float) get_option( 'llm_url_solution_min_confidence', 0.3 );
		if ( $analysis['confidence'] < $min_confidence ) {
			$this->db->mark_as_processed( $log_id );
			$this->db->update_generation_status( $log_id, 'failed', 'Confidence too low: ' . ( $analysis['confidence'] * 100 ) . '%' );
			return array(
				'success' => false,
				'message' => __( 'URL analysis confidence too low.', 'llm-url-solution' ),
			);
		}

		// Search for related content
		$this->db->update_generation_status( $log_id, 'generating', 'Searching for related content' );
		$related_content = $this->analyzer->search_related_content( $analysis['keywords'], 5 );

		// Build context for AI
		$context = $this->build_generation_context( $analysis, $related_content );

		// Generate content
		$this->db->update_generation_status( $log_id, 'generating', 'Calling AI API' );
		$generated = $this->call_ai_api( $context );

		if ( ! $generated['success'] ) {
			$this->db->mark_as_processed( $log_id );
			$this->db->update_generation_status( $log_id, 'failed', 'AI generation failed: ' . $generated['message'] );
			return $generated;
		}

		// Create the post
		$this->db->update_generation_status( $log_id, 'generating', 'Creating post' );
		$post_id = $this->create_post( $generated['content'], $analysis, $log );

		if ( is_wp_error( $post_id ) ) {
			$this->db->mark_as_processed( $log_id );
			$this->db->update_generation_status( $log_id, 'failed', 'Post creation failed: ' . $post_id->get_error_message() );
			return array(
				'success' => false,
				'message' => $post_id->get_error_message(),
			);
		}

		// Mark as processed and link to post
		$this->db->mark_as_processed( $log_id, $post_id );
		$this->db->update_generation_status( $log_id, 'success', 'Content generated successfully' );

		// Trigger action for other plugins
		do_action( 'llm_url_solution_content_generated', $post_id, $log_id, $analysis );

		return array(
			'success' => true,
			'message' => __( 'Content generated successfully.', 'llm-url-solution' ),
			'post_id' => $post_id,
		);
	}

	/**
	 * Build context for AI generation.
	 *
	 * @since    1.0.0
	 * @param    array $analysis          URL analysis results.
	 * @param    array $related_content   Related content found.
	 * @return   array                       Context array.
	 */
	private function build_generation_context( $analysis, $related_content ) {
		$context = array(
			'url_analysis'     => $analysis,
			'related_content'  => $related_content,
			'site_name'        => get_bloginfo( 'name' ),
			'site_description' => get_bloginfo( 'description' ),
			'content_settings' => array(
				'min_length'       => (int) get_option( 'llm_url_solution_content_min_length', 800 ),
				'max_length'       => (int) get_option( 'llm_url_solution_content_max_length', 1500 ),
				'tone'             => get_option( 'llm_url_solution_content_tone', 'professional' ),
				'include_examples' => get_option( 'llm_url_solution_include_examples', true ),
				'include_code'     => get_option( 'llm_url_solution_include_code', true ),
			),
		);

		// Add custom prompt additions
		$custom_prompt = get_option( 'llm_url_solution_custom_prompt', '' );
		if ( ! empty( $custom_prompt ) ) {
			$context['custom_instructions'] = $custom_prompt;
		}

		return apply_filters( 'llm_url_solution_generation_context', $context, $analysis );
	}

	/**
	 * Call AI API to generate content.
	 *
	 * @since    1.0.0
	 * @param    array $context    Generation context.
	 * @return   array                Result array.
	 */
	private function call_ai_api( $context ) {
		$ai_model = get_option( 'llm_url_solution_ai_model', 'gpt-4' );

		// Determine which API to use
		if ( strpos( $ai_model, 'gpt' ) !== false || strpos( $ai_model, 'openai' ) !== false ) {
			return $this->call_openai_api( $context );
		} elseif ( strpos( $ai_model, 'claude' ) !== false ) {
			return $this->call_claude_api( $context );
		}

		return array(
			'success' => false,
			'message' => __( 'Invalid AI model selected.', 'llm-url-solution' ),
		);
	}

	/**
	 * Call OpenAI API.
	 *
	 * @since    1.0.0
	 * @param    array $context    Generation context.
	 * @return   array                Result array.
	 */
	private function call_openai_api( $context ) {
		$api_key = get_option( 'llm_url_solution_openai_api_key', '' );
		if ( empty( $api_key ) ) {
			return array(
				'success' => false,
				'message' => __( 'OpenAI API key not configured.', 'llm-url-solution' ),
			);
		}

		$prompt = $this->build_prompt( $context );

		$request_body = array(
			'model'       => get_option( 'llm_url_solution_ai_model', 'gpt-4' ),
			'messages'    => array(
				array(
					'role'    => 'system',
					'content' => $this->get_system_prompt(),
				),
				array(
					'role'    => 'user',
					'content' => $prompt,
				),
			),
			'temperature' => (float) get_option( 'llm_url_solution_temperature', 0.7 ),
			'max_tokens'  => (int) get_option( 'llm_url_solution_max_tokens', 1500 ),
		);

		$response = wp_remote_post(
			'https://api.openai.com/v1/chat/completions',
			array(
				'timeout' => 60,
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode( $request_body ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'message' => $response->get_error_message(),
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['error'] ) ) {
			return array(
				'success' => false,
				'message' => $data['error']['message'] ?? __( 'OpenAI API error.', 'llm-url-solution' ),
			);
		}

		if ( ! isset( $data['choices'][0]['message']['content'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid response from OpenAI API.', 'llm-url-solution' ),
			);
		}

		$content = $this->parse_ai_response( $data['choices'][0]['message']['content'] );

		return array(
			'success' => true,
			'content' => $content,
		);
	}

	/**
	 * Call Claude API.
	 *
	 * @since    1.0.0
	 * @param    array $context    Generation context.
	 * @return   array                Result array.
	 */
	private function call_claude_api( $context ) {
		$api_key = get_option( 'llm_url_solution_claude_api_key', '' );
		if ( empty( $api_key ) ) {
			return array(
				'success' => false,
				'message' => __( 'Claude API key not configured.', 'llm-url-solution' ),
			);
		}

		$prompt = $this->build_prompt( $context );

		$request_body = array(
			'model'      => 'claude-3-opus-20240229',
			'messages'   => array(
				array(
					'role'    => 'user',
					'content' => $prompt,
				),
			),
			'system'     => $this->get_system_prompt(),
			'max_tokens' => (int) get_option( 'llm_url_solution_max_tokens', 1500 ),
		);

		$response = wp_remote_post(
			'https://api.anthropic.com/v1/messages',
			array(
				'timeout' => 60,
				'headers' => array(
					'x-api-key'         => $api_key,
					'anthropic-version' => '2023-06-01',
					'Content-Type'      => 'application/json',
				),
				'body'    => wp_json_encode( $request_body ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'message' => $response->get_error_message(),
			);
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( isset( $data['error'] ) ) {
			return array(
				'success' => false,
				'message' => $data['error']['message'] ?? __( 'Claude API error.', 'llm-url-solution' ),
			);
		}

		if ( ! isset( $data['content'][0]['text'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'Invalid response from Claude API.', 'llm-url-solution' ),
			);
		}

		$content = $this->parse_ai_response( $data['content'][0]['text'] );

		return array(
			'success' => true,
			'content' => $content,
		);
	}

	/**
	 * Get system prompt for AI.
	 *
	 * @since    1.0.0
	 * @return   string    System prompt.
	 */
	private function get_system_prompt() {
		return __( 'You are a professional content writer creating SEO-optimized content for a WordPress website. Generate engaging, informative content that matches the site\'s tone and style. Include relevant examples and practical information. Format the content with proper HTML markup including headings, paragraphs, lists, and code blocks where appropriate.', 'llm-url-solution' );
	}

	/**
	 * Build the main prompt for AI.
	 *
	 * @since    1.0.0
	 * @param    array $context    Generation context.
	 * @return   string               The prompt.
	 */
	private function build_prompt( $context ) {
		$analysis = $context['url_analysis'];
		$settings = $context['content_settings'];

		$prompt = sprintf(
			__(
				'Generate a %1$s about "%2$s" based on the URL slug: %3$s

Keywords identified: %4$s
Content type: %5$s
User intent: %6$s

Requirements:
- Length: %7$d to %8$d words
- Tone: %9$s
- Include practical examples: %10$s
- Include code snippets if relevant: %11$s
- SEO-optimized with proper heading structure
- Engaging introduction and conclusion
- Format with HTML tags

Site context:
- Site name: %12$s
- Site description: %13$s',
				'llm-url-solution'
			),
			$analysis['content_type'],
			$analysis['topic'],
			$analysis['original_slug'],
			implode( ', ', $analysis['keywords'] ),
			$analysis['content_type'],
			$analysis['intent'],
			$settings['min_length'],
			$settings['max_length'],
			$settings['tone'],
			$settings['include_examples'] ? 'yes' : 'no',
			$settings['include_code'] ? 'yes' : 'no',
			$context['site_name'],
			$context['site_description']
		);

		// Add related content context
		if ( ! empty( $context['related_content'] ) ) {
			$prompt .= "\n\n" . __( 'Related existing content for context:', 'llm-url-solution' ) . "\n";
			foreach ( $context['related_content'] as $related ) {
				$prompt .= sprintf( "- %s: %s\n", $related['title'], $related['excerpt'] );
			}
		}

		// Add custom instructions
		if ( ! empty( $context['custom_instructions'] ) ) {
			$prompt .= "\n\n" . __( 'Additional instructions:', 'llm-url-solution' ) . ' ' . $context['custom_instructions'];
		}

		$prompt .= "\n\n" . __(
			'Return the content in the following JSON format:
{
  "title": "SEO-optimized title",
  "content": "Full HTML content",
  "excerpt": "Brief excerpt/meta description",
  "tags": ["tag1", "tag2", "tag3"],
  "focus_keyword": "main SEO keyword"
}',
			'llm-url-solution'
		);

		return apply_filters( 'llm_url_solution_ai_prompt', $prompt, $context );
	}

	/**
	 * Parse AI response.
	 *
	 * @since    1.0.0
	 * @param    string $response    Raw AI response.
	 * @return   array                  Parsed content array.
	 */
	private function parse_ai_response( $response ) {
		// Find JSON string, which may be wrapped in text and/or a markdown block.
		$json_string = $response;
		if ( preg_match( '/```(json)?\s*(\{[\s\S]*\})\s*```/', $response, $matches ) ) {
			$json_string = $matches[2];
		} elseif ( preg_match( '/\{[\s\S]*\}/', $response, $matches ) ) {
			$json_string = $matches[0];
		}

		// Attempt to decode the extracted string.
		$json = json_decode( $json_string, true );
		if ( json_last_error() === JSON_ERROR_NONE && isset( $json['title'] ) && isset( $json['content'] ) ) {
			return $json;
		}

		// Last resort: treat entire response as content.
		return array(
			'title'         => __( 'Generated Content', 'llm-url-solution' ),
			'content'       => $response,
			'excerpt'       => wp_trim_words( strip_tags( $response ), 55 ),
			'tags'          => array(),
			'focus_keyword' => '',
		);
	}

	/**
	 * Create a WordPress post from generated content.
	 *
	 * @since    1.0.0
	 * @param    array  $content     Generated content.
	 * @param    array  $analysis    URL analysis.
	 * @param    object $log         404 log entry.
	 * @return   int|WP_Error           Post ID or error.
	 */
	private function create_post( $content, $analysis, $log ) {
		// Detect post type based on URL structure.
		$detected_post_type = $this->detect_post_type_from_url( $log->requested_url );

		// Determine slug from the last part of the URL path.
		$path = wp_parse_url( $log->requested_url, PHP_URL_PATH );
		$slug = basename( $path );

		// Prepare post data.
		$post_data = array(
			'post_title'   => sanitize_text_field( $content['title'] ),
			'post_content' => wp_kses_post( $content['content'] ),
			'post_excerpt' => sanitize_text_field( $content['excerpt'] ?? '' ),
			'post_status'  => get_option( 'llm_url_solution_default_post_status', 'draft' ),
			'post_type'    => $detected_post_type ?: get_option( 'llm_url_solution_default_post_type', 'post' ),
			'post_author'  => get_current_user_id() ?: 1,
			'meta_input'   => array(
				'_llm_url_solution_generated'      => true,
				'_llm_url_solution_log_id'         => $log->id,
				'_llm_url_solution_original_url'   => $log->requested_url,
				'_llm_url_solution_generated_date' => current_time( 'mysql' ),
			),
		);

		// Set the slug to match the last part of the original URL.
		$post_data['post_name'] = sanitize_title( $slug );

		// Insert the post.
		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		// Add tags
		if ( ! empty( $content['tags'] ) && get_option( 'llm_url_solution_auto_tag', true ) ) {
			wp_set_post_tags( $post_id, $content['tags'] );
		}

		// Auto-categorize - pass the original URL from the log
		if ( get_option( 'llm_url_solution_auto_categorize', true ) ) {
			$analysis['original_url'] = $log->requested_url;
			$this->auto_categorize_post( $post_id, $analysis );
		}

		// Set SEO meta if Yoast is active
		if ( ! empty( $content['focus_keyword'] ) && defined( 'WPSEO_VERSION' ) ) {
			update_post_meta( $post_id, '_yoast_wpseo_focuskw', sanitize_text_field( $content['focus_keyword'] ) );
			update_post_meta( $post_id, '_yoast_wpseo_metadesc', sanitize_text_field( $content['excerpt'] ) );
		}

		return $post_id;
	}

	/**
	 * Detect post type from URL structure based on taxonomy terms.
	 *
	 * @since    1.1.0
	 * @param    string $url    The requested URL.
	 * @return   string|null       The detected post type or null.
	 */
	private function detect_post_type_from_url( $url ) {
		// Parse the URL to get the path
		$parsed = wp_parse_url( $url );
		$path   = isset( $parsed['path'] ) ? trim( $parsed['path'], '/' ) : '';

		// Split the path into segments
		$segments = explode( '/', $path );

		if ( empty( $segments ) ) {
			return null;
		}

		// Custom logic for WP Fusion site
		$site_url = get_site_url();
		if ( strpos( $site_url, 'wpfusion.com' ) !== false ) {
			// Check if URL contains /documentation/
			if ( strpos( $path, 'documentation/' ) !== false ) {
				return 'documentation';
			} else {
				// For all other URLs, use post type
				return 'post';
			}
		}

		// Original logic for other sites - check taxonomy terms
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
	 * @since    1.0.0
	 * @param    string $slug    The slug to check.
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
	 * Auto-categorize a post based on analysis.
	 *
	 * @since    1.0.0
	 * @param    int   $post_id     The post ID.
	 * @param    array $analysis    URL analysis.
	 */
	private function auto_categorize_post( $post_id, $analysis ) {
		// Get the post type
		$post_type = get_post_type( $post_id );

		// Custom logic for WP Fusion site
		$site_url = get_site_url();
		if ( strpos( $site_url, 'wpfusion.com' ) !== false ) {
			// Parse the original URL to get segments
			$parsed   = wp_parse_url( $analysis['original_url'] ?? '' );
			$path     = isset( $parsed['path'] ) ? trim( $parsed['path'], '/' ) : '';
			$segments = explode( '/', $path );

			if ( 'documentation' === $post_type ) {
				// For documentation, use the second URL segment for the category.
				if ( ! empty( $segments[1] ) ) {
					$category_name = ucfirst( $segments[1] );
					$category_slug = sanitize_title( $segments[1] );

					// Get or create the term in documentation_category taxonomy.
					$term = get_term_by( 'slug', $category_slug, 'documentation_category' );
					if ( ! $term ) {
						$term_data = wp_insert_term(
							$category_name,
							'documentation_category',
							array(
								'slug' => $category_slug,
							)
						);
						if ( ! is_wp_error( $term_data ) ) {
							wp_set_post_terms( $post_id, array( $term_data['term_id'] ), 'documentation_category' );
						}
					} else {
						wp_set_post_terms( $post_id, array( $term->term_id ), 'documentation_category' );
					}
				}
			} else {
				// For regular posts, use the first segment as category.
				if ( ! empty( $segments[0] ) ) {
					$category_name = ucfirst( $segments[0] );
					$category_slug = sanitize_title( $segments[0] );

					// Get or create the category
					$category = get_category_by_slug( $category_slug );
					if ( ! $category ) {
						$term_data = wp_insert_term(
							$category_name,
							'category',
							array(
								'slug' => $category_slug,
							)
						);
						if ( ! is_wp_error( $term_data ) ) {
							wp_set_post_categories( $post_id, array( $term_data['term_id'] ) );
						}
					} else {
						wp_set_post_categories( $post_id, array( $category->term_id ) );
					}
				}
			}
			return;
		}

		// Original categorization logic for other sites
		// Map content types to category slugs
		$category_map = array(
			'documentation' => 'documentation',
			'tutorial'      => 'tutorials',
			'blog'          => 'blog',
			'support'       => 'support',
			'product'       => 'products',
		);

		$category_slug = isset( $category_map[ $analysis['content_type'] ] ) ? $category_map[ $analysis['content_type'] ] : 'uncategorized';

		// Get or create category
		$category = get_category_by_slug( $category_slug );
		if ( ! $category ) {
			$term_data = wp_insert_term(
				ucfirst( $analysis['content_type'] ),
				'category',
				array(
					'slug'        => $category_slug,
					'description' => sprintf( __( 'Auto-generated category for %s content', 'llm-url-solution' ), $analysis['content_type'] ),
				)
			);
			if ( ! is_wp_error( $term_data ) ) {
				wp_set_post_categories( $post_id, array( $term_data['term_id'] ) );
			}
		} else {
			wp_set_post_categories( $post_id, array( $category->term_id ) );
		}
	}
}
