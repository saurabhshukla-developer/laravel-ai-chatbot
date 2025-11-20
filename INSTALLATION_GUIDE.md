# Installation Guide - Where to Add Repository Configuration

## Important: This Goes in Your Laravel Application's composer.json

The repository configuration should be added to **your Laravel application's `composer.json`** file, NOT in the package's composer.json.

## Step-by-Step Instructions

### 1. Open Your Laravel Application's composer.json

Navigate to your Laravel project root and open `composer.json`:

```bash
cd /path/to/your/laravel-project
nano composer.json
# or
code composer.json
```

### 2. Find the Right Location

Your Laravel application's `composer.json` typically looks like this:

```json
{
    "name": "laravel/laravel",
    "type": "project",
    "description": "The Laravel Framework.",
    "keywords": [...],
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "guzzlehttp/guzzle": "^7.2",
        "laravel/framework": "^10.10",
        "laravel/sanctum": "^3.2",
        "laravel/tinker": "^2.8"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/pint": "^1.0",
        "laravel/sail": "^1.18",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^7.0",
        "phpunit/phpunit": "^10.1",
        "spatie/laravel-ignition": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Dummy\\": "tests/"
        }
    },
    "scripts": {...},
    "extra": {...},
    "config": {...}
}
```

### 3. Add Repository Configuration

Add the `repositories` section **BEFORE** the `require` section. Here's the exact placement:

```json
{
    "name": "laravel/laravel",
    "type": "project",
    "description": "The Laravel Framework.",
    "keywords": [...],
    "license": "MIT",
    
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/saurabhshukla-developer/laravel-ai-chatbot"
        }
    ],
    
    "require": {
        "php": "^8.1",
        "guzzlehttp/guzzle": "^7.2",
        "laravel/framework": "^10.10",
        "saurabhshukla-developer/laravel-ai-chatbot": "*",
        "laravel/sanctum": "^3.2",
        "laravel/tinker": "^2.8"
    },
    
    "require-dev": {
        ...
    },
    ...
}
```

### 4. Complete Example

Here's a complete example showing exactly where everything goes:

```json
{
    "name": "laravel/laravel",
    "type": "project",
    "description": "The Laravel Framework.",
    "keywords": ["framework", "laravel"],
    "license": "MIT",
    "require": {
        "php": "^8.1",
        "guzzlehttp/guzzle": "^7.2",
        "laravel/framework": "^10.10",
        "laravel/sanctum": "^3.2",
        "laravel/tinker": "^2.8"
    },
    "require-dev": {
        "fakerphp/faker": "^1.9.1",
        "laravel/pint": "^1.0",
        "laravel/sail": "^1.18",
        "mockery/mockery": "^1.4.4",
        "nunomaduro/collision": "^7.0",
        "phpunit/phpunit": "^10.1",
        "spatie/laravel-ignition": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Dummy\\": "tests/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    }
}
```

**After adding the repository configuration, it should look like this:**

```json
{
    "name": "laravel/laravel",
    "type": "project",
    "description": "The Laravel Framework.",
    "keywords": ["framework", "laravel"],
    "license": "MIT",
    
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/saurabhshukla-developer/laravel-ai-chatbot"
        }
    ],
    
    "require": {
        "php": "^8.1",
        "guzzlehttp/guzzle": "^7.2",
        "laravel/framework": "^10.10",
        "saurabhshukla-developer/laravel-ai-chatbot": "*",
        "laravel/sanctum": "^3.2",
        "laravel/tinker": "^2.8"
    },
    
    "require-dev": {
        ...
    },
    ...
}
```

## Visual Guide

```
composer.json
├── {
├──     "name": "laravel/laravel",
├──     "type": "project",
├──     ...
├──     "license": "MIT",
├──     
├──     "repositories": [          ← ADD HERE (after license, before require)
├──         {
├──             "type": "vcs",
├──             "url": "https://github.com/saurabhshukla-developer/laravel-ai-chatbot"
├──         }
├──     ],
├──     
├──     "require": {               ← Then add package name here
├──         ...
├──         "saurabhshukla-developer/laravel-ai-chatbot": "*",
├──         ...
├──     },
└── }
```

## After Adding Configuration

1. **Save the file**

2. **IMPORTANT: Handle Minimum Stability**

   Since the package is currently on `dev-master`, you need to either:

   **Option A: Allow dev packages (Quick Fix)**
   
   Add this to your Laravel project's `composer.json`:
   ```json
   {
       "minimum-stability": "dev",
       "prefer-stable": true,
       "repositories": [
           {
               "type": "vcs",
               "url": "https://github.com/saurabhshukla-developer/laravel-ai-chatbot"
           }
       ],
       "require": {
           "saurabhshukla-developer/laravel-ai-chatbot": "dev-master"
       }
   }
   ```

   **Option B: Use dev-master explicitly (Recommended)**
   
   ```bash
   composer require saurabhshukla-developer/laravel-ai-chatbot:dev-master
   ```

   **Option C: Create a release tag (Best for Production)**
   
   On GitHub, create a release tag:
   ```bash
   git tag -a v1.0.0 -m "Release v1.0.0"
   git push origin v1.0.0
   ```
   
   Then require a specific version:
   ```bash
   composer require saurabhshukla-developer/laravel-ai-chatbot:^1.0
   ```

3. **Run composer update:**
   ```bash
   composer update
   ```

## Alternative: Command Line Method

You can also add it via command line without editing the file manually:

```bash
# Add repository
composer config repositories.laravel-ai-chatbot vcs https://github.com/saurabhshukla-developer/laravel-ai-chatbot

# Then require the package
composer require saurabhshukla-developer/laravel-ai-chatbot
```

This will automatically add the repository configuration to your `composer.json`.

## Summary

- **Location:** Your Laravel application's `composer.json` (NOT the package's composer.json)
- **Placement:** Add `repositories` section after `license` and before `require`
- **Then:** Add package name to `require` section
- **Finally:** Run `composer update` or `composer require`

## Common Mistakes to Avoid

❌ **Don't add it to the package's composer.json** - That's wrong!
✅ **Add it to your Laravel app's composer.json** - That's correct!

❌ **Don't put repositories inside require** - That's wrong!
✅ **Put repositories as a separate top-level key** - That's correct!

