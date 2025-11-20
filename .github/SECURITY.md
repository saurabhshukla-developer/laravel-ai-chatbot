# Security Policy

## Supported Versions

We actively support the following versions of Laravel AI Chatbot:

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability, please **do not** open a public issue. Instead, please report it via one of the following methods:

### Email
Send an email to: **saurabhshukla.developer@gmail.com**

### GitHub Security Advisory
Use GitHub's [Security Advisory](https://github.com/saurabhshukla-developer/laravel-ai-chatbot/security/advisories/new) feature.

### What to Include

When reporting a vulnerability, please include:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

## Security Best Practices

### For Users

1. **Keep API Keys Secure**
   - Never commit API keys to version control
   - Use environment variables or database storage
   - Rotate API keys regularly

2. **Use HTTPS**
   - Always use HTTPS in production
   - Ensure your Laravel application uses secure connections

3. **Protect Routes**
   - Add authentication middleware to protect package routes
   - Limit access to API key management interfaces

4. **Keep Updated**
   - Update the package regularly
   - Keep Laravel and dependencies up to date

### For Developers

1. **Encryption**
   - API keys are encrypted using Laravel's encryption
   - Ensure `APP_KEY` is set and secure

2. **Input Validation**
   - All user inputs are validated
   - SQL injection protection via Eloquent ORM

3. **Dependencies**
   - Regularly update dependencies
   - Monitor security advisories

## Security Updates

Security updates will be released as:
- **Patch releases** (e.g., 1.0.1) for critical security fixes
- **Minor releases** (e.g., 1.1.0) for security improvements

## Disclosure Policy

- We will acknowledge receipt of your vulnerability report within 48 hours
- We will provide an initial assessment within 7 days
- We will keep you informed of our progress
- We will notify you when the vulnerability has been fixed
- We will credit you in the security advisory (unless you prefer to remain anonymous)

## Known Security Considerations

### API Key Storage
- API keys are encrypted in the database using Laravel's encryption
- The encryption key (`APP_KEY`) must be kept secure
- If `APP_KEY` is compromised, re-encrypt all API keys

### Provider API Keys
- Provider API keys are sent to external services (OpenAI, Anthropic, Google)
- Ensure you trust these providers and review their security practices
- Monitor API usage for unauthorized access

### Rate Limiting
- Consider implementing rate limiting for chat endpoints
- Monitor API usage to prevent abuse

## Thank You

Thank you for helping keep Laravel AI Chatbot secure! 🙏

