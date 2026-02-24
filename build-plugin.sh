#!/bin/bash
# Build script for Moneybag WooCommerce Plugin

PLUGIN_NAME="moneybag-woocommerce"
VERSION="1.0.0"
BUILD_DIR="./build"
DIST_DIR="./dist"

echo "Building Moneybag WooCommerce Plugin v${VERSION}..."

# Clean previous builds
rm -rf $BUILD_DIR $DIST_DIR
mkdir -p $BUILD_DIR $DIST_DIR

# Copy files
echo "Copying plugin files..."
rsync -av --exclude-from='.gitignore' \
    --exclude='.git' \
    --exclude='.gitignore' \
    --exclude='build' \
    --exclude='dist' \
    --exclude='*.sh' \
    --exclude='.env*' \
    --exclude='docker*' \
    --exclude='Dockerfile' \
    --exclude='DEVELOPMENT.md' \
    --exclude='.claude' \
    --exclude='composer.json' \
    --exclude='composer.lock' \
    --exclude='tests' \
    ./ $BUILD_DIR/$PLUGIN_NAME/

# Create zip
echo "Creating distribution package..."
cd $BUILD_DIR
zip -r ../$DIST_DIR/${PLUGIN_NAME}-${VERSION}.zip $PLUGIN_NAME/
cd ..

echo "Build complete! Package created at: $DIST_DIR/${PLUGIN_NAME}-${VERSION}.zip"
echo "File size: $(du -h $DIST_DIR/${PLUGIN_NAME}-${VERSION}.zip | cut -f1)"

# Generate checksums
echo "Generating checksums..."
cd $DIST_DIR
md5sum ${PLUGIN_NAME}-${VERSION}.zip > ${PLUGIN_NAME}-${VERSION}.md5
sha256sum ${PLUGIN_NAME}-${VERSION}.zip > ${PLUGIN_NAME}-${VERSION}.sha256

echo "Done! Distribution package is ready."
