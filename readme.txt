=== LLM URL Solution ===
Contributors: yourname
Tags: 404, content generation, AI, ChatGPT, SEO, automation
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically generate SEO-optimized content for 404 URLs that originate from AI chatbot searches like ChatGPT, Claude, and others.

== Description ==

LLM URL Solution is a powerful WordPress plugin that automatically detects 404 errors originating from AI chatbot searches (ChatGPT, Claude, Perplexity, etc.) and generates relevant, SEO-optimized content to satisfy those queries.

When AI chatbots reference URLs on your site that don't exist, this plugin captures those 404 errors and uses AI to create appropriate blog posts or documentation pages, turning missed opportunities into valuable content.

= Key Features =

* **Automatic 404 Detection**: Monitors and logs 404 errors from AI chatbot referrers
* **Smart URL Analysis**: Extracts keywords and intent from requested URLs
* **AI-Powered Content Generation**: Creates relevant content using OpenAI GPT or Claude APIs
* **SEO Optimization**: Generates content with proper structure, meta descriptions, and keywords
* **Content Control**: Manual approval workflow with customizable generation settings
* **Rate Limiting**: Prevents abuse with hourly and daily generation limits
* **Blacklist Support**: Exclude specific URL patterns from content generation
* **Multi-AI Support**: Works with OpenAI GPT-4, GPT-3.5, and Claude models

= How It Works =

1. A user searches for your content on ChatGPT or another AI chatbot
2. The chatbot references a URL on your site that doesn't exist
3. When clicked, the plugin detects the 404 error and logs it
4. You can review logged URLs and generate content with one click
5. The AI creates relevant, high-quality content based on the URL intent
6. Content is saved as a draft for your review before publishing

= Use Cases =

* **Documentation Sites**: Automatically create missing documentation pages
* **Blogs**: Generate blog posts for topics users are searching for
* **E-commerce**: Create product information pages based on search intent
* **Support Sites**: Build FAQ and troubleshooting content on demand

== Installation ==

1. Upload the `llm-url-solution` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'LLM URL Solution' > 'Settings' to configure your AI API keys
4. Configure your content generation preferences
5. Monitor the dashboard for 404 errors from AI chatbots

= Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* An API key from OpenAI or Anthropic (Claude)
* SSL certificate (recommended for API security)

== Frequently Asked Questions ==

= Which AI chatbot referrers are supported? =

By default, the plugin detects referrers from:
- ChatGPT (chat.openai.com)
- Claude (claude.ai)
- Perplexity AI
- You.com
- Bing Chat
- Google Bard
- Poe.com

You can add custom referrer patterns in the Advanced settings.

= Do I need both OpenAI and Claude API keys? =

No, you only need one. The plugin works with either OpenAI (GPT models) or Anthropic (Claude models). Choose based on your preference and budget.

= How much does content generation cost? =

Costs depend on your chosen AI provider and model:
- GPT-3.5 Turbo: ~$0.002 per 1K tokens
- GPT-4: ~$0.03 per 1K tokens
- Claude 3: Check Anthropic's current pricing

Average content generation costs $0.05-$0.50 per article.

= Can I customize the generated content style? =

Yes! You can:
- Set content length (min/max words)
- Choose content tone (professional, casual, technical)
- Add custom prompt instructions
- Configure SEO settings
- Control categorization and tagging

= Is the generated content unique? =

Yes, all content is uniquely generated based on your site's context and the specific URL intent. The plugin also searches your existing content to maintain consistency with your site's style.

= What happens if I reach the rate limits? =

The plugin enforces hourly and daily rate limits to prevent abuse and control costs. When limits are reached, content generation is paused until the next period. You can adjust these limits in the Safety settings.

== Screenshots ==

1. Dashboard overview showing 404 statistics and recent logs
2. 404 logs page with filtering and bulk actions
3. Settings page with API configuration
4. Content generation in progress
5. Generated content preview before publishing

== Changelog ==

= 1.0.0 =
* Initial release
* Core 404 detection functionality
* OpenAI and Claude API integration
* URL analysis and intent extraction
* Content generation with SEO optimization
* Admin dashboard and settings
* Rate limiting and safety controls

== Upgrade Notice ==

= 1.0.0 =
Initial release of LLM URL Solution. Start converting 404 errors into valuable content!

== Privacy Policy ==

This plugin:
- Logs visitor IP addresses for 404 requests (can be anonymized)
- Sends URL data to third-party AI services (OpenAI/Anthropic) for content generation
- Stores generated content in your WordPress database
- Does not collect any personal data beyond standard WordPress functionality

For GDPR compliance, ensure your privacy policy covers the use of AI services and IP logging.

== Support ==

For support, feature requests, or bug reports, please visit our support forum or GitHub repository.

== Credits ==

* Developed by [Your Company Name]
* Uses OpenAI GPT and Anthropic Claude APIs
* Built with WordPress coding standards 