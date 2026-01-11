#!/bin/bash

# Build script for production deployment

echo "Building lottery system for production..."

# Navigate to web directory
cd web

# Install dependencies
echo "Installing dependencies..."
npm install

# Build for production
echo "Building Angular application..."
npm run build --prod

echo "Build completed!"
echo "Files ready for deployment in web/dist/web/"
echo ""
echo "Next steps:"
echo "1. Upload web/dist/web/* to public_html/"
echo "2. Upload api/* to public_html/api/"
echo "3. Upload .htaccess to public_html/"
echo "4. Update api/.env with production settings"
echo "5. Test at https://totalfreelotto.com"