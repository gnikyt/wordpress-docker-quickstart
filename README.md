# Wordpress Docker Quickstart

Provides a quick Docker, theme, and Grunt setup.

## Installation

1. Replace `your_theme/` with your theme name
2. Open `Gruntfile.js` and replace `{YOUR_THEME}` with your theme name
3. Open `docker-compose.yml` and replace `{YOUR_THEME}` with your theme name
4. Run `docker-compose up`
5. Setup Wordpress
6. Run `npm install`
7. Run `grunt watch`

Before deploying to production, run `grunt release` which will prep files for production (minify, autoprefix, transpile, etc).

## Reference

This repo is for a post I wrote, for more information, see [this link](http://typebrew.com/2017/06/15/wordpress-under-docker).
