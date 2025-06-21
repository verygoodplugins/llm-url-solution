<?php
/**
 * The URL analysis functionality of the plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 */

/**
 * The URL analysis functionality of the plugin.
 *
 * Analyzes URL slugs to extract intent, keywords, and content type.
 *
 * @package    LLM_URL_Solution
 * @subpackage LLM_URL_Solution/includes
 * @author     Your Company Name
 */
class LLM_URL_Analyzer {

	/**
	 * Common stop words to filter out.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stop_words    Common stop words.
	 */
	private $stop_words = array(
		'a', 'an', 'and', 'are', 'as', 'at', 'be', 'by', 'for', 'from',
		'has', 'he', 'in', 'is', 'it', 'its', 'of', 'on', 'that', 'the',
		'to', 'was', 'will', 'with', 'the', 'this', 'but', 'they', 'have',
		'had', 'what', 'when', 'where', 'who', 'which', 'why', 'how'
	);

	/**
	 * Content type indicators.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $content_indicators    Keywords that indicate content type.
	 */
	private $content_indicators = array(
		'documentation' => array( 'docs', 'documentation', 'guide', 'manual', 'reference', 'api', 'tutorial' ),
		'blog' => array( 'blog', 'article', 'post', 'news', 'update', 'announcement' ),
		'product' => array( 'product', 'feature', 'pricing', 'plans', 'compare', 'vs' ),
		'support' => array( 'help', 'support', 'faq', 'troubleshoot', 'fix', 'solve', 'issue' ),
		'tutorial' => array( 'how-to', 'howto', 'tutorial', 'guide', 'walkthrough', 'setup' ),
	);

	/**
	 * Analyze a URL slug.
	 *
	 * @since    1.0.0
	 * @param    string    $url_slug    The URL slug to analyze.
	 * @return   array                  Analysis results.
	 */
	public function analyze_url( $url_slug ) {
		$analysis = array(
			'original_slug' => $url_slug,
			'keywords'      => array(),
			'intent'        => '',
			'content_type'  => 'blog', // Default
			'topic'         => '',
			'confidence'    => 0,
		);

		// Extract keywords
		$keywords = $this->extract_keywords( $url_slug );
		$analysis['keywords'] = $keywords;

		// Determine content type
		$content_type = $this->determine_content_type( $keywords );
		$analysis['content_type'] = $content_type;

		// Extract intent
		$intent = $this->extract_intent( $keywords, $content_type );
		$analysis['intent'] = $intent;

		// Generate topic
		$topic = $this->generate_topic( $keywords );
		$analysis['topic'] = $topic;

		// Calculate confidence
		$confidence = $this->calculate_confidence( $analysis );
		$analysis['confidence'] = $confidence;

		return apply_filters( 'llm_url_solution_url_analysis', $analysis, $url_slug );
	}

	/**
	 * Extract keywords from URL slug.
	 *
	 * @since    1.0.0
	 * @param    string    $slug    The URL slug.
	 * @return   array              Extracted keywords.
	 */
	private function extract_keywords( $slug ) {
		// Split by common separators
		$parts = preg_split( '/[-_\/\s]+/', $slug );
		
		// Filter and clean
		$keywords = array();
		foreach ( $parts as $part ) {
			$part = strtolower( trim( $part ) );
			
			// Skip empty parts and stop words
			if ( empty( $part ) || in_array( $part, $this->stop_words, true ) ) {
				continue;
			}
			
			// Skip very short words unless they're known acronyms
			if ( strlen( $part ) < 3 && ! $this->is_known_acronym( $part ) ) {
				continue;
			}
			
			$keywords[] = $part;
		}
		
		// Remove duplicates while preserving order
		$keywords = array_values( array_unique( $keywords ) );
		
		return $keywords;
	}

	/**
	 * Determine content type based on keywords.
	 *
	 * @since    1.0.0
	 * @param    array    $keywords    The extracted keywords.
	 * @return   string                The determined content type.
	 */
	private function determine_content_type( $keywords ) {
		$scores = array();
		
		foreach ( $this->content_indicators as $type => $indicators ) {
			$score = 0;
			foreach ( $keywords as $keyword ) {
				if ( in_array( $keyword, $indicators, true ) ) {
					$score += 2; // Direct match
				} else {
					// Partial match
					foreach ( $indicators as $indicator ) {
						if ( strpos( $keyword, $indicator ) !== false || strpos( $indicator, $keyword ) !== false ) {
							$score += 1;
						}
					}
				}
			}
			$scores[ $type ] = $score;
		}
		
		// Return type with highest score, default to 'blog'
		if ( empty( $scores ) || max( $scores ) === 0 ) {
			return 'blog';
		}
		
		return array_search( max( $scores ), $scores, true );
	}

	/**
	 * Extract intent from keywords and content type.
	 *
	 * @since    1.0.0
	 * @param    array     $keywords       The extracted keywords.
	 * @param    string    $content_type   The content type.
	 * @return   string                    The extracted intent.
	 */
	private function extract_intent( $keywords, $content_type ) {
		$intent_patterns = array(
			'learn'        => array( 'learn', 'understand', 'what', 'introduction', 'basics' ),
			'implement'    => array( 'how', 'howto', 'implement', 'setup', 'install', 'configure' ),
			'troubleshoot' => array( 'fix', 'solve', 'error', 'issue', 'problem', 'troubleshoot' ),
			'compare'      => array( 'vs', 'versus', 'compare', 'difference', 'between' ),
			'reference'    => array( 'api', 'reference', 'docs', 'documentation', 'manual' ),
		);
		
		foreach ( $intent_patterns as $intent => $patterns ) {
			foreach ( $keywords as $keyword ) {
				if ( in_array( $keyword, $patterns, true ) ) {
					return $intent;
				}
			}
		}
		
		// Default intent based on content type
		$default_intents = array(
			'documentation' => 'reference',
			'blog'          => 'learn',
			'tutorial'      => 'implement',
			'support'       => 'troubleshoot',
			'product'       => 'learn',
		);
		
		return isset( $default_intents[ $content_type ] ) ? $default_intents[ $content_type ] : 'learn';
	}

	/**
	 * Generate a human-readable topic from keywords.
	 *
	 * @since    1.0.0
	 * @param    array    $keywords    The extracted keywords.
	 * @return   string                The generated topic.
	 */
	private function generate_topic( $keywords ) {
		if ( empty( $keywords ) ) {
			return __( 'General Information', 'llm-url-solution' );
		}
		
		// Capitalize first letter of each keyword
		$formatted_keywords = array_map( 'ucfirst', $keywords );
		
		// Join with spaces
		$topic = implode( ' ', $formatted_keywords );
		
		return $topic;
	}

	/**
	 * Calculate confidence score for the analysis.
	 *
	 * @since    1.0.0
	 * @param    array    $analysis    The analysis results.
	 * @return   float                 Confidence score (0-1).
	 */
	private function calculate_confidence( $analysis ) {
		$confidence = 0;
		
		// More keywords = higher confidence
		$keyword_count = count( $analysis['keywords'] );
		if ( $keyword_count >= 3 ) {
			$confidence += 0.3;
		} elseif ( $keyword_count >= 2 ) {
			$confidence += 0.2;
		} elseif ( $keyword_count >= 1 ) {
			$confidence += 0.1;
		}
		
		// Clear content type = higher confidence
		if ( $analysis['content_type'] !== 'blog' ) {
			$confidence += 0.2;
		}
		
		// Clear intent = higher confidence
		if ( ! empty( $analysis['intent'] ) && $analysis['intent'] !== 'learn' ) {
			$confidence += 0.2;
		}
		
		// Meaningful topic = higher confidence
		if ( strlen( $analysis['topic'] ) > 10 ) {
			$confidence += 0.1;
		}
		
		// URL structure bonus
		if ( strpos( $analysis['original_slug'], '/' ) !== false ) {
			$confidence += 0.1; // Hierarchical URL
		}
		
		// Cap at 1.0
		return min( $confidence, 1.0 );
	}

	/**
	 * Check if a word is a known acronym.
	 *
	 * @since    1.0.0
	 * @param    string    $word    The word to check.
	 * @return   bool               True if known acronym, false otherwise.
	 */
	private function is_known_acronym( $word ) {
		$acronyms = array(
			'ai', 'api', 'ui', 'ux', 'id', 'ip', 'it', 'qa', 'hr',
			'crm', 'cms', 'erp', 'seo', 'roi', 'kpi', 'b2b', 'b2c',
			'css', 'html', 'js', 'php', 'sql', 'xml', 'json', 'rss',
			'cdn', 'dns', 'ftp', 'http', 'ssl', 'url', 'vpn', 'lan',
			'cpu', 'gpu', 'ram', 'ssd', 'hdd', 'os', 'vm', 'ci', 'cd'
		);
		
		return in_array( strtolower( $word ), $acronyms, true );
	}

	/**
	 * Search for related content in existing posts.
	 *
	 * @since    1.0.0
	 * @param    array    $keywords    The keywords to search for.
	 * @param    int      $limit       Maximum number of results.
	 * @return   array                 Array of related posts.
	 */
	public function search_related_content( $keywords, $limit = 10 ) {
		if ( empty( $keywords ) ) {
			return array();
		}

		// Build search query
		$args = array(
			'post_type'      => array( 'post', 'page' ),
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			's'              => implode( ' ', $keywords ),
			'orderby'        => 'relevance',
			'order'          => 'DESC',
		);

		// Add meta query for better relevance
		$meta_queries = array( 'relation' => 'OR' );
		foreach ( $keywords as $keyword ) {
			$meta_queries[] = array(
				'key'     => '_yoast_wpseo_focuskw',
				'value'   => $keyword,
				'compare' => 'LIKE',
			);
		}
		
		if ( count( $meta_queries ) > 1 ) {
			$args['meta_query'] = $meta_queries;
		}

		$query = new WP_Query( $args );
		$related_posts = array();

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$related_posts[] = array(
					'ID'           => get_the_ID(),
					'title'        => get_the_title(),
					'excerpt'      => get_the_excerpt(),
					'url'          => get_permalink(),
					'content_type' => get_post_type(),
				);
			}
			wp_reset_postdata();
		}

		return $related_posts;
	}
} 