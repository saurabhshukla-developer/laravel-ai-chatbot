#!/bin/bash

# Quick test script for the Laravel AI Chatbot package
# This script helps you test the package in a fresh Laravel installation

echo "=== Laravel AI Chatbot Package Test Script ==="
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the package directory
if [ ! -f "composer.json" ] || [ ! -d "src" ]; then
    echo -e "${RED}Error: This script must be run from the package root directory${NC}"
    exit 1
fi

PACKAGE_DIR=$(pwd)
TEST_APP_DIR="../test-chatbot-app"

echo "Package directory: $PACKAGE_DIR"
echo "Test app directory: $TEST_APP_DIR"
echo ""

# Step 1: Install dependencies
echo -e "${YELLOW}Step 1: Installing package dependencies...${NC}"
composer install --quiet
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Dependencies installed${NC}"
else
    echo -e "${RED}✗ Failed to install dependencies${NC}"
    exit 1
fi

# Step 2: Run package tests
echo -e "${YELLOW}Step 2: Running package unit tests...${NC}"
if [ -f "vendor/bin/phpunit" ]; then
    vendor/bin/phpunit
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ All tests passed${NC}"
    else
        echo -e "${RED}✗ Some tests failed${NC}"
    fi
else
    echo -e "${YELLOW}⚠ PHPUnit not found, skipping tests${NC}"
fi

# Step 3: Check if test app exists
echo -e "${YELLOW}Step 3: Checking test Laravel application...${NC}"
if [ ! -d "$TEST_APP_DIR" ]; then
    echo -e "${YELLOW}Test app not found. Would you like to create one? (y/n)${NC}"
    read -r response
    if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
        echo "Creating new Laravel application..."
        cd ..
        composer create-project laravel/laravel test-chatbot-app --quiet
        cd "$PACKAGE_DIR"
        TEST_APP_DIR="../test-chatbot-app"
    else
        echo -e "${YELLOW}Skipping Laravel app test${NC}"
        exit 0
    fi
fi

if [ -d "$TEST_APP_DIR" ]; then
    echo -e "${GREEN}✓ Test app found${NC}"
    
    # Step 4: Install package in test app
    echo -e "${YELLOW}Step 4: Installing package in test app...${NC}"
    cd "$TEST_APP_DIR"
    
    # Add repository to composer.json
    if ! grep -q "laravel-ai/chatbot" composer.json; then
        # This is a simplified approach - in real scenario, you'd use composer config
        echo -e "${YELLOW}Please add the package repository manually to composer.json:${NC}"
        echo ""
        echo "  \"repositories\": ["
        echo "      {"
        echo "          \"type\": \"path\","
        echo "          \"url\": \"$PACKAGE_DIR\""
        echo "      }"
        echo "  ],"
        echo ""
        echo "Then run: composer require laravel-ai/chatbot"
        echo ""
    else
        composer require laravel-ai/chatbot --quiet
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Package installed in test app${NC}"
        else
            echo -e "${RED}✗ Failed to install package${NC}"
            cd "$PACKAGE_DIR"
            exit 1
        fi
    fi
    
    # Step 5: Publish and migrate
    echo -e "${YELLOW}Step 5: Publishing package files...${NC}"
    php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-config" --force --quiet
    php artisan vendor:publish --provider="LaravelAI\Chatbot\ChatbotServiceProvider" --tag="chatbot-migrations" --force --quiet
    
    if [ ! -f ".env" ]; then
        cp .env.example .env
        php artisan key:generate --quiet
    fi
    
    php artisan migrate --force --quiet
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ Migrations completed${NC}"
    else
        echo -e "${RED}✗ Migration failed${NC}"
    fi
    
    echo ""
    echo -e "${GREEN}=== Setup Complete ===${NC}"
    echo ""
    echo "Test app is ready at: $TEST_APP_DIR"
    echo ""
    echo "To test the package:"
    echo "  1. cd $TEST_APP_DIR"
    echo "  2. php artisan serve"
    echo "  3. Visit http://localhost:8000/chatbot/api-keys"
    echo ""
    
    cd "$PACKAGE_DIR"
fi

echo ""
echo -e "${GREEN}=== All done! ===${NC}"

