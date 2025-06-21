/**
 * Admin JavaScript for LLM URL Solution
 *
 * @package LLM_URL_Solution
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		
		// Generate content button click handler
		$(document).on('click', '.llm-url-generate-content', function(e) {
			e.preventDefault();
			
			var $button = $(this);
			var logId = $button.data('log-id');
			
			if (!logId) {
				return;
			}
			
			// Confirm action
			if (!confirm(llm_url_solution_ajax.confirm_generate || 'Are you sure you want to generate content for this URL?')) {
				return;
			}
			
			// Disable button and show loading state
			$button.prop('disabled', true);
			$button.html('<span class="llm-url-spinner"></span> Generating...');
			
			// Make AJAX request
			$.ajax({
				url: llm_url_solution_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'llm_url_generate_content',
					log_id: logId,
					nonce: llm_url_solution_ajax.nonce
				},
				success: function(response) {
					if (response.success) {
						// Show success message
						showNotice('success', response.data.message || 'Content generated successfully!');
						
						// Update button
						$button.html('Generated');
						$button.removeClass('button-primary').addClass('button-disabled');
						
						// If we have a post ID, add a link to view it
						if (response.data.post_id) {
							var editLink = '<a href="' + llm_url_solution_ajax.admin_url + 'post.php?post=' + response.data.post_id + '&action=edit" target="_blank">Edit Post →</a>';
							$button.after(' ' + editLink);
						}
						
						// Reload page after 2 seconds
						setTimeout(function() {
							location.reload();
						}, 2000);
					} else {
						// Show error message
						showNotice('error', response.data || 'An error occurred while generating content.');
						
						// Re-enable button
						$button.prop('disabled', false);
						$button.html('Generate Content');
					}
				},
				error: function(xhr, status, error) {
					// Show error message
					showNotice('error', 'An error occurred: ' + error);
					
					// Re-enable button
					$button.prop('disabled', false);
					$button.html('Generate Content');
				}
			});
		});
		
		// Delete log button click handler
		$(document).on('click', '.llm-url-delete-log', function(e) {
			e.preventDefault();
			
			var $button = $(this);
			var logId = $button.data('log-id');
			
			if (!logId) {
				return;
			}
			
			// Confirm action
			if (!confirm(llm_url_solution_ajax.confirm_delete || 'Are you sure you want to delete this log?')) {
				return;
			}
			
			// Disable button
			$button.prop('disabled', true);
			
			// Make AJAX request
			$.ajax({
				url: llm_url_solution_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'llm_url_delete_log',
					log_id: logId,
					nonce: llm_url_solution_ajax.nonce
				},
				success: function(response) {
					if (response.success) {
						// Show success message
						showNotice('success', response.data || 'Log deleted successfully!');
						
						// Remove the row
						$button.closest('tr').fadeOut(400, function() {
							$(this).remove();
						});
					} else {
						// Show error message
						showNotice('error', response.data || 'An error occurred while deleting the log.');
						
						// Re-enable button
						$button.prop('disabled', false);
					}
				},
				error: function(xhr, status, error) {
					// Show error message
					showNotice('error', 'An error occurred: ' + error);
					
					// Re-enable button
					$button.prop('disabled', false);
				}
			});
		});
		
		// Bulk action checkbox handling
		$('#cb-select-all-1, #cb-select-all-2').on('click', function() {
			var checked = $(this).prop('checked');
			$('input[name="log_ids[]"]').prop('checked', checked);
		});
		
		// Individual checkbox handling
		$('input[name="log_ids[]"]').on('click', function() {
			var allChecked = $('input[name="log_ids[]"]:not(:checked)').length === 0;
			$('#cb-select-all-1, #cb-select-all-2').prop('checked', allChecked);
		});
		
		// Settings page tab persistence
		if ($('.nav-tab-wrapper').length > 0) {
			// Store active tab in localStorage
			$('.nav-tab').on('click', function() {
				var tab = $(this).attr('href').split('tab=')[1];
				if (tab) {
					localStorage.setItem('llm_url_active_tab', tab);
				}
			});
		}
		
		// API key visibility toggle
		$('input[type="password"]').each(function() {
			var $input = $(this);
			var $toggle = $('<button type="button" class="button button-small llm-url-toggle-password">Show</button>');
			
			$input.after($toggle);
			
			$toggle.on('click', function() {
				if ($input.attr('type') === 'password') {
					$input.attr('type', 'text');
					$toggle.text('Hide');
				} else {
					$input.attr('type', 'password');
					$toggle.text('Show');
				}
			});
		});
		
		// Form validation
		$('form').on('submit', function() {
			var $form = $(this);
			var isValid = true;
			
			// Validate rate limits
			$form.find('input[name*="rate_limit"]').each(function() {
				var value = parseInt($(this).val());
				if (value < 1) {
					showNotice('error', 'Rate limits must be at least 1.');
					isValid = false;
				}
			});
			
			// Validate content length
			var minLength = parseInt($('#llm_url_solution_content_min_length').val());
			var maxLength = parseInt($('#llm_url_solution_content_max_length').val());
			
			if (minLength && maxLength && minLength > maxLength) {
				showNotice('error', 'Minimum content length cannot be greater than maximum content length.');
				isValid = false;
			}
			
			return isValid;
		});
		
		/**
		 * Show a notice message
		 *
		 * @param {string} type - Notice type (success, error, warning, info)
		 * @param {string} message - Message to display
		 */
		function showNotice(type, message) {
			// Remove any existing notices
			$('.llm-url-ajax-notice').remove();
			
			// Create notice HTML
			var noticeHtml = '<div class="notice notice-' + type + ' is-dismissible llm-url-ajax-notice"><p>' + message + '</p></div>';
			
			// Add notice after page title
			$('.wrap h1').after(noticeHtml);
			
			// Scroll to top
			$('html, body').animate({ scrollTop: 0 }, 'fast');
			
			// Auto-dismiss after 5 seconds
			setTimeout(function() {
				$('.llm-url-ajax-notice').fadeOut(400, function() {
					$(this).remove();
				});
			}, 5000);
			
			// Make dismissible
			$('.llm-url-ajax-notice').on('click', '.notice-dismiss', function() {
				$(this).parent().fadeOut(400, function() {
					$(this).remove();
				});
			});
		}
		
		// Add loading state to forms
		$('form').on('submit', function() {
			var $submitButton = $(this).find('input[type="submit"]');
			$submitButton.prop('disabled', true);
			$submitButton.val($submitButton.val() + '...');
		});
		
		// Dashboard stats auto-refresh
		if ($('.llm-url-dashboard-stats').length > 0) {
			// Refresh stats every 60 seconds
			setInterval(function() {
				refreshDashboardStats();
			}, 60000);
		}
		
		/**
		 * Refresh dashboard statistics via AJAX
		 */
		function refreshDashboardStats() {
			$.ajax({
				url: llm_url_solution_ajax.ajax_url,
				type: 'POST',
				data: {
					action: 'llm_url_get_stats',
					nonce: llm_url_solution_ajax.nonce
				},
				success: function(response) {
					if (response.success && response.data) {
						// Update stat numbers
						$('.llm-url-stat-box').each(function() {
							var $box = $(this);
							var statType = $box.find('h3').text().toLowerCase().replace(/[^a-z0-9_]/g, '_');
							
							if (response.data[statType]) {
								$box.find('.llm-url-stat-number').text(response.data[statType]);
							}
						});
					}
				}
			});
		}
		
	});

})(jQuery); 