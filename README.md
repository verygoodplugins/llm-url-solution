# LLM URL Solution

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-5.8%2B-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](http://makeapullrequest.com)

Automatically generate SEO-optimized content for 404 URLs that originate from AI chatbot searches like ChatGPT, Claude, and others.

## 🚀 Overview

LLM URL Solution is a powerful WordPress plugin that turns 404 errors from AI chatbot referrals into opportunities. When AI chatbots like ChatGPT or Claude reference non-existent URLs on your site, this plugin captures those 404 errors and uses AI to generate relevant, SEO-optimized content automatically.

### Key Features

- 🤖 **Automatic 404 Detection** - Monitors and logs 404 errors from AI chatbot referrers
- 🔍 **Smart URL Analysis** - Extracts keywords and intent from requested URLs
- ✨ **AI-Powered Content Generation** - Creates relevant content using OpenAI GPT or Claude APIs
- 📈 **SEO Optimization** - Generates content with proper structure, meta descriptions, and keywords
- 🎯 **Intelligent Post Type Detection** - Automatically determines the appropriate post type based on URL taxonomy
- 🔒 **Content Control** - Manual or automatic approval workflow with customizable generation settings
- ⚡ **Rate Limiting** - Prevents abuse with hourly and daily generation limits
- 🚫 **Blacklist Support** - Exclude specific URL patterns from content generation
- 🔄 **Multi-AI Support** - Works with OpenAI GPT-4, GPT-3.5, and Claude models

## 📋 Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- An API key from OpenAI or Anthropic (Claude)
- SSL certificate (recommended for API security)

## 🛠️ Installation

### From WordPress Admin

1. Download the plugin zip file
2. Navigate to Plugins > Add New in your WordPress admin
3. Click "Upload Plugin" and select the downloaded file
4. Click "Install Now" and then "Activate"

### Manual Installation

```bash
# Navigate to your WordPress plugins directory
cd /path/to/wordpress/wp-content/plugins/

# Clone the repository
git clone https://github.com/yourusername/llm-url-solution.git

# Activate the plugin through WordPress admin
```

## ⚙️ Configuration

1. **API Setup**
   - Navigate to `LLM URL Solution > Settings`
   - Enter your OpenAI or Claude API key
   - Select your preferred AI model

2. **Content Settings**
   - Configure default post type and status
   - Set content length preferences
   - Enable/disable auto-categorization and tagging

3. **Safety Settings**
   - Enable/disable automatic content generation
   - Set rate limits (hourly/daily)
   - Configure URL blacklist patterns

## 🎯 How It Works

1. **Detection**: When a user clicks a link from an AI chatbot that leads to a 404 page on your site, the plugin detects the referrer
2. **Logging**: The 404 error is logged with URL analysis and keyword extraction
3. **Generation**: Content can be generated automatically or manually through the admin interface
4. **Smart Post Type Detection**: The plugin analyzes the URL structure to determine the appropriate post type
5. **Publishing**: Generated content is created as a draft (or published, based on settings) with proper SEO optimization

### Intelligent Post Type Detection

The plugin uses a smart algorithm to detect the appropriate post type:
- Checks if the first URL segment matches any taxonomy term
- If not found, checks the second URL segment
- Automatically creates content in the post type associated with the matched taxonomy
- Falls back to the default post type if no match is found

## 📊 Supported AI Chatbot Referrers

- ChatGPT (chat.openai.com)
- Claude (claude.ai)
- Perplexity AI (perplexity.ai)
- You.com
- Bing Chat
- Google Bard
- Poe.com
- Custom patterns (configurable)

## 💻 Development

### File Structure

```
llm-url-solution/
├── admin/                  # Admin interface files
│   ├── dashboard.php      # Main dashboard
│   ├── logs.php          # 404 logs interface
│   └── settings.php      # Settings page
├── assets/                # CSS and JavaScript
│   ├── css/
│   └── js/
├── includes/              # Core plugin classes
│   ├── class-llm-url-404-detector.php
│   ├── class-llm-url-analyzer.php
│   ├── class-llm-url-content-generator.php
│   └── ...
├── languages/             # Translation files
├── templates/             # Template files
└── llm-url-solution.php   # Main plugin file
```

### Hooks and Filters

#### Actions
- `llm_url_solution_404_logged` - Fired when a 404 is logged
- `llm_url_solution_content_generated` - Fired after content generation

#### Filters
- `llm_url_solution_is_ai_referrer` - Customize AI referrer detection
- `llm_url_solution_is_blacklisted` - Customize URL blacklisting
- `llm_url_solution_generation_context` - Modify content generation context
- `llm_url_solution_ai_prompt` - Customize AI prompt

### Example Usage

```php
// Add custom AI referrer pattern
add_filter('llm_url_solution_is_ai_referrer', function($is_ai, $referrer) {
    if (strpos($referrer, 'mycustombot.com') !== false) {
        return true;
    }
    return $is_ai;
}, 10, 2);

// Modify content generation context
add_filter('llm_url_solution_generation_context', function($context, $analysis) {
    $context['custom_instructions'] .= ' Always mention our brand name.';
    return $context;
}, 10, 2);
```

## 🔒 Security

- All user inputs are properly sanitized
- Nonce verification on all forms
- Capability checks for all admin functions
- Prepared statements for database queries
- API keys are stored securely in WordPress options

## 🌐 Internationalization

The plugin is fully translatable and includes:
- POT file for translations
- Proper text domain usage
- RTL language support

## 📈 Performance

- Efficient database queries with proper indexing
- Asynchronous content generation option
- Caching of API responses where appropriate
- Minimal impact on site performance

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) for details.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 Changelog

### Version 1.1.0 (2024-01-XX)
- Added automatic content generation option
- Implemented intelligent post type detection based on URL taxonomy
- Added support for custom post types
- Improved URL analysis algorithm
- Enhanced settings interface
- Bug fixes and performance improvements

### Version 1.0.0
- Initial release
- Core 404 detection functionality
- OpenAI and Claude API integration
- URL analysis and intent extraction
- Content generation with SEO optimization
- Admin dashboard and settings
- Rate limiting and safety controls

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 🙏 Credits

- Developed by [Very Good Plugins]
- Uses OpenAI GPT and Anthropic Claude APIs
- Built with WordPress coding standards
- Special thanks to all contributors

## 💬 Support

- 📧 Email: support@yourcompany.com
- 🐛 Bug Reports: [GitHub Issues](https://github.com/yourusername/llm-url-solution/issues)
- 💡 Feature Requests: [GitHub Discussions](https://github.com/yourusername/llm-url-solution/discussions)
- 📖 Documentation: [Wiki](https://github.com/yourusername/llm-url-solution/wiki)

## ⚡ Quick Start

```bash
# 1. Install the plugin
# 2. Add your API key
wp option update llm_url_solution_openai_api_key "your-api-key"

# 3. Enable auto-generation (optional)
wp option update llm_url_solution_auto_generate 1

# 4. Monitor your dashboard for AI chatbot 404s!
```

---

Made with ❤️ for the WordPress community
