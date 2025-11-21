# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-11-21

### Added
- **File-based tool system** - Create tools by simply adding PHP files to `app/Tools/`
- **Tool management** - Complete CRUD interface for managing tools
- **Automatic tool discovery** - Tools are automatically discovered from files
- **Tool execution** - Automatic tool execution when AI requests to use them
- **Artisan commands** for tool management:
  - `php artisan chatbot:make-tool` - Create new tool files
  - `php artisan chatbot:test-tool` - Test tools directly
  - `php artisan chatbot:list-tools` - List all available tools
- **Tool tracking** - `executed_tools` array in responses shows which tools were used
- **Tool logging** - Optional logging of tool execution for debugging
- **Database tools** - Support for storing tools in database (legacy method)
- **Tool assignment** - Assign multiple tools to agents via UI or code
- **Provider integration** - Tools work with OpenAI, Anthropic, and Google AI
- **Comprehensive documentation** - Multiple guides for tool creation and usage

### Enhanced
- **Response tracking** - Responses now include `executed_tools` and `tool_calls` data
- **Better error handling** - Improved error messages for tool execution failures
- **Tool validation** - Automatic validation of tool parameters

### Documentation
- `TOOLS_README.md` - Complete tool creation guide
- `EASY_TOOL_CREATION.md` - Quick start with artisan commands
- `FILE_BASED_TOOLS.md` - File-based tools overview
- `SETUP_FILE_TOOLS.md` - Setup guide for tools
- `HOW_TO_TRACK_TOOL_USAGE.md` - Tracking and monitoring tools
- `TROUBLESHOOTING_TOOLS.md` - Common issues and solutions
- `ABOUT_TOOL_CALLS.md` - Understanding tool call responses

### Technical Details
- New `BaseTool` abstract class for file-based tools
- `ToolLoader` service for automatic tool discovery
- Updated `BaseProvider` to handle tool execution loops
- Tool execution tracking in `ChatbotManager`
- Enhanced provider implementations for tool support

## [1.0.0] - 2025-11-21

### Added
- Initial release of Laravel AI Chatbot package
- Support for multiple AI providers:
  - OpenAI (GPT-4, GPT-3.5, etc.)
  - Anthropic (Claude 3 Opus, Sonnet, Haiku)
  - Google AI (Gemini Pro)
- Secure API key management with database encryption
- AI agent builder with custom system prompts
- Built-in web UI for managing API keys and agents
- Chat interface for testing agents
- Programmatic API for using agents in code
- Streaming support for AI responses
- Comprehensive documentation
- Unit and feature tests
- GitHub Actions CI/CD workflow

### Features
- Encrypted API key storage
- Multiple AI provider support
- Built-in chat interface
- Modern UI with Tailwind CSS
- Secure by default
- Easy Laravel integration
- Full test coverage
- Comprehensive documentation

### Requirements
- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x
- Guzzle HTTP Client

### Documentation
- Complete README with installation instructions
- Setup and hosting guide
- Code examples
- Troubleshooting guide
- Contributing guidelines
- Testing guide

