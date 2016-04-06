#!/bin/sh

# Installs Front&Back end dependencies and compiles.
# This should be replaced with a build tool as soon as we can figure out a way
# For NPM to not suck.

./composer install
bower install
node-sass --output-style compressed src/main/sass/theme/global-theme.scss web/css/global-theme.css
babel vendor/javascript/jquery/dist/jquery.js vendor/javascript/bootstrap/js/dist/util.js vendor/javascript/bootstrap/js/dist/collapse.js -o web/js/global.js --minified
