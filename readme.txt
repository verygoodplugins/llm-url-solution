=== LLM URL Solution ===
Contributors: verygoodplugins
Tags: 404, content generation, AI, ChatGPT, SEO, automation, SEOPress, Rank Math, Yoast, All in One SEO

Requires at least: 5.8
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.3.0
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
* **Intelligent Post Type Detection**: Automatically determines the appropriate post type based on URL taxonomy
* **SEO Optimization**: Generates content with proper structure, meta descriptions, and keywords. Now supports Yoast SEO, Rank Math, SEOPress, and All in One SEO.
* **Content Control**: Manual or automatic approval workflow with customizable generation settings
* **Rate Limiting**: Prevents abuse with hourly and daily generation limits
* **Blacklist Support**: Exclude specific URL patterns from content generation
* **Multi-AI Support**: Works with OpenAI GPT-4, GPT-3.5, and Claude models

= How It Works =

1. A user searches for your content on ChatGPT or another AI chatbot
2. The chatbot references a URL on your site that doesn't exist
3. When clicked, the plugin detects the 404 error and logs it
4. Content can be generated automatically or manually with one click
5. The plugin intelligently detects the appropriate post type based on URL structure
6. The AI creates relevant, high-quality content based on the URL intent
7. Content is saved as a draft (or published) for your review

= Intelligent Post Type Detection =

The plugin uses a smart algorithm to detect the appropriate post type:
* Checks if the first URL segment matches any taxonomy term
* If not found, checks the second URL segment
* Automatically creates content in the post type associated with the matched taxonomy
* Falls back to the default post type if no match is found

This means the plugin adapts to your site's structure without any hardcoded rules!

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

= Automatic Updates =

To enable automatic updates, install the [GitHub Updater](https://github.com/a8cteam51/github-updater) plugin. Once installed and activated, LLM URL Solution will receive updates automatically from the official GitHub repository.

= Requirements =

* WordPress 5.8 or higher
* PHP 7.4 or higher
* An API key from OpenAI or Anthropic (Claude)
* SSL certificate (recommended for API security)

== Settings ==

The plugin provides comprehensive settings to customize content generation:

= API Settings =
* **OpenAI API Key** - Your OpenAI API key for GPT models
* **Claude API Key** - Your Anthropic API key for Claude models
* **AI Model** - Choose between GPT-4, GPT-3.5 Turbo, or Claude 3 Opus
* **Temperature** (0.0-1.0) - Controls creativity vs consistency (default: 0.7)
* **Max Tokens** - Maximum length of generated content (default: 1500)

= Content Settings =
* **Default Post Type** - Choose post, page, or any custom post type
* **Default Post Status** - Draft, Published, Pending, or Private
* **Auto-Categorize** - Automatically assign categories based on URL structure
* **Auto-Tag** - Generate and assign relevant tags
* **Content Length** - Set minimum (default: 800) and maximum (default: 1500) word counts
* **Content Tone** - Professional, casual, or technical writing style
* **Include Examples** - Add practical examples to generated content
* **Include Code** - Include code snippets when relevant

= Safety Settings =
* **Auto-Generate Content** - Enable automatic content generation on 404 detection
* **Rate Limits** - Set hourly (default: 10) and daily (default: 50) generation limits
* **URL Blacklist Patterns** - Exclude specific URL patterns from generation
* **Minimum Confidence Score** - Only generate content above this threshold (default: 0.3)

= Advanced Settings =
* **Custom Referrer Patterns** - Add additional AI chatbot domains to monitor
* **Custom AI Prompt** - Add custom instructions for content generation
* **Enable Debug Mode** - Log detailed information for troubleshooting

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

= 1.3.0 =
* Added - Support for setting meta descriptions and focus keywords in Yoast SEO, Rank Math, SEOPress, and All in One SEO.
* Tweak - Updated plugin Readme and documentation with new marketing copy and screenshot.

= 1.2.0 =
* Fixed - Redundant H1 tag was being added to the top of generated content.
* Fixed - Improved AI response parsing to handle JSON wrapped in markdown code blocks.
* Tweak - Updated plugin Readme and documentation.

= 1.1.0 =
* Added automatic content generation option - content can now be generated automatically when 404s are detected
* Implemented intelligent post type detection based on URL taxonomy structure
* Added support for custom post types without hardcoding
* Improved URL analysis algorithm for better content matching
* Enhanced settings interface with new auto-generation toggle
* Added smart taxonomy-based routing for generated content
* Improved error handling and user feedback
* Bug fixes and performance improvements

= 1.0.0 =
* Initial release
* Core 404 detection functionality
* OpenAI and Claude API integration
* URL analysis and intent extraction
* Content generation with SEO optimization
* Admin dashboard and settings
* Rate limiting and safety controls

== Upgrade Notice ==

= 1.3.0 =
This version adds support for Yoast SEO, Rank Math, SEOPress, and All in One SEO. The readme has also been updated with additional details about the plugin.

= 1.2.0 =
This version fixes a bug that caused a redundant H1 tag to be added to generated content, and improves AI response parsing.

= 1.1.0 =
New automatic content generation feature and intelligent post type detection! The plugin now adapts to your site's taxonomy structure automatically.

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

* Developed by [Very Good Plugins]
* Uses OpenAI GPT and Anthropic Claude APIs
* Built with WordPress coding standards 