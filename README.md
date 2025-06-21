# LLM URL Solution

[![WordPress Plugin Version](https://img.shields.io/badge/WordPress-5.8%2B-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](http://makeapullrequest.com)

Automatically generate SEO-optimized content for 404 URLs that originate from AI chatbot searches like ChatGPT, Claude, and others.

## 🚀 Overview

LLM URL Solution is a powerful WordPress plugin that turns 404 errors from AI chatbot referrals into opportunities. When AI chatbots like ChatGPT or Claude reference non-existent URLs on your site, this plugin captures those 404 errors and uses AI to generate relevant, SEO-optimized content automatically.

## The Problem: AI-Generated 404 "Hallucinations"

AI chatbots like ChatGPT are a rapidly growing source of referral traffic. But what happens when they "hallucinate" and link to pages on your site that don't exist? You get a stream of 404 errors from highly-qualified visitors who were looking for specific information.

Each of these 404s represents a missed opportunity to engage a user and provide value. LLM URL Solution was built to solve this exact problem by turning these AI-generated 404s into SEO-optimized content automatically based on existing content on your site and blog.

### Key Features

- 🤖 **Automatic 404 Detection** - Monitors and logs 404 errors from AI chatbot referrers
- 🔍 **Smart URL Analysis** - Extracts keywords and intent from requested URLs
- 🥞 **Extracts relevant content** - Searches database for relevant content based on the post body and SEO meta data
- ✨ **AI-Powered Content Generation** - Uses URL and matching content to generate a new post
- 🔍 **Selects the best AI model** - Select from the best OpenAI GPTs or Claude APIs based on the URL and content
- 📈 **SEO Optimization** - Generates content with proper structure, meta descriptions, and keywords
- 🎯 **Intelligent Post Type Detection** - Automatically determines the appropriate post type based on URL taxonomy
- 🔒 **Content Control** - Manual or automatic approval workflow with customizable generation settings
- ⚡ **Rate Limiting** - Prevents abuse with hourly and daily generation limits
- 🚫 **Blacklist Support** - Exclude specific URL patterns from content generation
- 🔄 **Multi-AI Support** - Works with OpenAI GPT-4, GPT-3.5, and Claude models

## 📈 Supported SEO Plugins
The plugin now automatically detects and sets meta descriptions and focus keywords for the following SEO plugins:
- Yoast SEO
- Rank Math
- SEOPress
- All in One SEO

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

## 📋 Available Settings

### API Settings
- **OpenAI API Key** - Your OpenAI API key for GPT models
- **Claude API Key** - Your Anthropic API key for Claude models
- **AI Model** - Choose from:
  - GPT-4 (Most capable, higher cost)
  - GPT-3.5 Turbo (Fast and cost-effective)
  - Claude 3 Opus (Anthropic's most capable model)
- **Temperature** (0.0-1.0) - Controls creativity vs consistency (default: 0.7)
- **Max Tokens** - Maximum length of generated content (default: 1500)

<img width="724" alt="image" src="https://github.com/user-attachments/assets/c6ef5369-600c-49ac-8e72-275b8de68a20" />


### Content Settings
- **Default Post Type** - Post type for generated content (post, page, or custom)
- **Default Post Status** - Status for new content:
  - Draft (recommended for review)
  - Published
  - Pending
  - Private
- **Auto-Categorize** - Automatically assign categories based on URL structure
- **Auto-Tag** - Generate and assign relevant tags
- **Content Min Length** - Minimum word count (default: 800)
- **Content Max Length** - Maximum word count (default: 1500)
- **Content Tone** - Writing style (professional, casual, technical)
- **Include Examples** - Add practical examples to content
- **Include Code** - Include code snippets when relevant

<img width="683" alt="image" src="https://github.com/user-attachments/assets/cfba8548-dbea-40de-ba6f-b17c97655445" />


### Safety Settings
- **Auto-Generate Content** - Enable automatic content generation on 404 detection
- **Hourly Rate Limit** - Max generations per hour (default: 10)
- **Daily Rate Limit** - Max generations per day (default: 50)
- **URL Blacklist Patterns** - Patterns to exclude (one per line):
  - Admin pages (wp-admin, wp-login)
  - System files (.env, .git, config)
  - Media files (.jpg, .png, .pdf)
- **Minimum Confidence Score** (0.0-1.0) - Only generate content above this threshold (default: 0.3)

<img width="920" alt="image" src="https://github.com/user-attachments/assets/48e911cc-311f-4a3d-9958-b0b41027fc45" />


### Advanced Settings
- **Custom Referrer Patterns** - Additional AI chatbot domains to monitor
- **Custom AI Prompt** - Additional instructions for content generation
- **Enable Debug Mode** - Log detailed information for troubleshooting

<img width="1001" alt="image" src="https://github.com/user-attachments/assets/40ec1b7e-ccb6-4a23-ba97-bf54b4a6cae7" />


## ⚡ Automatic Updates
To enable automatic updates, you can use the [GitHub Updater](https://github.com/a8cteam51/github-updater) plugin. Once installed, it will automatically manage updates for LLM URL Solution directly from the official GitHub repository, ensuring you always have the latest features and security fixes.

## 🎯 How It Works

1. **Detection**: When a user clicks a link from an AI chatbot that leads to a 404 page on your site, the plugin detects the referrer
2. **Logging**: The 404 error is logged with URL analysis and keyword extraction
3. **Generation**: Content can be generated automatically or manually through the admin interface
4. **Smart Post Type Detection**: The plugin analyzes the URL structure to determine the appropriate post type
5. **Publishing**: Generated content is created as a draft (or published, based on settings) with proper SEO optimization

<img width="1348" alt="image" src="https://github.com/user-attachments/assets/6461ec0a-45da-422d-9846-a32d037df51f" />


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

### Version 1.3.0 (2024-07-XX)
- **Added**: Support for Yoast SEO, Rank Math, SEOPress, and All in One SEO.
- **Tweak**: Added marketing copy and new screenshot to readme.

### Version 1.2.0 (2024-07-XX)
- **Fixed**: Removed redundant `<h1>` tag that was being added to generated content.
- **Fixed**: Improved AI response parsing to correctly handle JSON wrapped in markdown blocks.
- **Tweak**: Updated documentation and plugin version.

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
