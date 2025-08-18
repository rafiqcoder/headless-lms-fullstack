#!/bin/bash

echo "🧹 Cleaning up WordPress installation for headless LMS..."

# Navigate to WordPress root
cd /c/xampp/htdocs/lms

# Remove unnecessary admin files
echo "Removing unnecessary admin files..."
rm -f wp-admin/about.php
rm -f wp-admin/credits.php
rm -f wp-admin/freedoms.php
rm -f wp-admin/contribute.php
rm -f wp-admin/press-this.php

# Remove unnecessary admin pages we don't need in headless
rm -f wp-admin/customize.php
rm -f wp-admin/theme-*.php
rm -f wp-admin/edit-comments.php
rm -f wp-admin/comment.php
rm -f wp-admin/moderation.php

# Clean up uploads directory (remove default files)
echo "Cleaning uploads directory..."
if [ -d "wp-content/uploads" ]; then
    find wp-content/uploads -name "*.log" -delete 2>/dev/null || true
    find wp-content/uploads -name "debug.log" -delete 2>/dev/null || true
fi

# Remove language files if not needed (optional)
if [ -d "wp-content/languages" ]; then
    echo "Found language files. Keeping only English..."
    # Keep only English language files
    find wp-content/languages -name "*.po" ! -name "*en_US*" -delete 2>/dev/null || true
    find wp-content/languages -name "*.mo" ! -name "*en_US*" -delete 2>/dev/null || true
fi

# Remove unnecessary includes files
echo "Cleaning wp-includes..."
rm -f wp-includes/wp-db-backup.php 2>/dev/null || true

# Set proper permissions
echo "Setting proper file permissions..."
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 600 wp-config.php

echo "✅ WordPress cleanup completed!"
echo ""
echo "📁 Removed files:"
echo "   - Unnecessary admin pages"
echo "   - Default theme files (kept minimal theme)"
echo "   - Language files (non-English)"
echo "   - Log files"
echo ""
echo "🔧 Next steps:"
echo "   1. Activate the 'Headless LMS' theme"
echo "   2. Activate the performance optimizer plugins"
echo "   3. Test REST API endpoints"
echo "   4. Run frontend build process"
