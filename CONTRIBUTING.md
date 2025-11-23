# Contributing to Laravel AI Chatbot

Contributions are welcome. This document outlines the process for contributing to Laravel AI Chatbot.

## Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Follow project coding standards

## Getting Started

1. **Fork the repository**
   ```bash
   git clone https://github.com/saurabhshukla-developer/laravel-ai-chatbot.git
   cd laravel-ai-chatbot
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set up database for testing**

   The package supports both SQLite (default) and MySQL for testing.

   **Option A: SQLite (Recommended - No setup required)**
   
   SQLite is the default and requires no configuration. Tests will run automatically using an in-memory database.

   **Option B: MySQL (Optional)**
   
   If you want to test with MySQL:
   
   1. Copy the example environment file:
      ```bash
      cp .env.example .env
      ```
   
   2. Update `.env` with your MySQL credentials:
      ```env
      DB_CONNECTION=mysql
      DB_HOST=127.0.0.1
      DB_PORT=3306
      DB_DATABASE=chatbot_test
      DB_USERNAME=your_username
      DB_PASSWORD=your_password
      ```
   
   3. Create the test database:
      ```bash
      mysql -u your_username -p -e "CREATE DATABASE chatbot_test;"
      ```
   
   4. Run tests with MySQL:
      ```bash
      DB_CONNECTION=mysql vendor/bin/phpunit
      ```

4. **Run tests**
   ```bash
   # Default (SQLite)
   vendor/bin/phpunit
   
   # With MySQL (if configured)
   DB_CONNECTION=mysql vendor/bin/phpunit
   ```

## Development Workflow

1. Create a new branch from `main`:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. Make your changes

3. Write or update tests

4. Ensure all tests pass:
   ```bash
   vendor/bin/phpunit
   ```

5. Commit your changes:
   ```bash
   git commit -m "Add: Description of your changes"
   ```

6. Push to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```

7. Create a Pull Request on GitHub

## Coding Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standards
- Use meaningful variable and function names
- Add PHPDoc comments for classes and methods
- Keep functions focused and single-purpose

## Testing

### Database Setup for Testing

The package supports both **SQLite** (default) and **MySQL** for testing.

#### SQLite (Default - Recommended)

SQLite is the default and requires **no setup**. Tests automatically use an in-memory database:

```bash
vendor/bin/phpunit
```

#### MySQL (Optional)

To test with MySQL:

1. **Copy environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Configure MySQL in `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=chatbot_test
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

3. **Create test database:**
   ```bash
   mysql -u your_username -p -e "CREATE DATABASE chatbot_test;"
   ```

4. **Run tests with MySQL:**
   ```bash
   DB_CONNECTION=mysql vendor/bin/phpunit
   ```

### Test Requirements

- Write tests for new features
- Ensure all existing tests pass
- Aim for good test coverage
- Test edge cases and error scenarios
- Tests should work with both SQLite and MySQL

## Commit Messages

Use clear, descriptive commit messages:

- `Add: Feature description`
- `Fix: Bug description`
- `Update: What was updated`
- `Refactor: What was refactored`
- `Docs: Documentation changes`

## Pull Request Process

1. Update the README.md if needed
2. Update documentation for any new features
3. Add tests for new functionality
4. Ensure the code follows the project's style guidelines
5. Make sure all tests pass
6. Request review from maintainers

## Reporting Bugs

Use the [GitHub issue tracker](https://github.com/saurabhshukla-developer/laravel-ai-chatbot/issues) and include:

- Description of the bug
- Steps to reproduce
- Expected behavior
- Actual behavior
- Environment details (PHP version, Laravel version, etc.)
- Screenshots if applicable

## Suggesting Features

Use the [GitHub issue tracker](https://github.com/saurabhshukla-developer/laravel-ai-chatbot/issues) and include:

- Clear description of the feature
- Use case and motivation
- Proposed implementation (if you have ideas)
- Any alternatives considered

## Questions?

Open an issue for questions or contact the maintainers.

