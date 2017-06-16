'use strict';

module.exports = function(grunt) {
  require('load-grunt-tasks')(grunt);

  grunt.initConfig({
    browserify: {
      scripts: {
        src: [
          'wordpress/wp-content/themes/{YOUR_THEME}/assets/javascripts/src/scripts.js',
        ],
        dest: 'wordpress/wp-content/themes/{YOUR_THEME}/assets/javascripts/scripts.js',
        options: {
          transform: [['babelify', { 'presets': ['es2015'] }]]
        },
      }
    },

    concat: {
      vendorScripts: {
        src: [
          'wordpress/wp-content/themes/{YOUR_THEME}/assets/javascripts/src/vendor/*.js'
        ],
        dest: 'wordpress/wp-content/themes/{YOUR_THEME}/assets/javascripts/scripts-vendors.js',
      }
    },

    watch: {
      scripts: {
        files: [
          'wordpress/wp-content/themes/{YOUR_THEME}/assets/javascripts/src/**/*.js',
        ],
        tasks: ['browserify:scripts'],
        options: {
          spawn: false,
        },
      },

      vendorScripts: {
        files: [
          'wordpress/wp-content/themes/{YOUR_THEME}/assets/javascripts/src/vendor/**/*.js',
        ],
        tasks: ['concat:vendorScripts'],
        options: {
          spawn: false,
        },
      },

      styles: {
        files: 'wordpress/wp-content/themes/{YOUR_THEME}/assets/stylesheets/src/**/*',
        tasks: 'sass',
        options: {
          spawn: false,
        },
      }
    },

    sass: {
      default: {
        options : {
          sourcemap: 'none',
          lineNumbers: true,
          cacheLocation: 'wordpress/wp-content/themes/{YOUR_THEME}/assets/stylesheets/.sass-cache/',
        },
        files: {
          'wordpress/wp-content/themes/{YOUR_THEME}/assets/stylesheets/styles.css': 'wordpress/wp-content/themes/{YOUR_THEME}/assets/stylesheets/src/styles.scss'
        }
      }
    },

    postcss: {
      options: {
        map: false,
        processors: [
          require('autoprefixer')({ browsers: 'last 2 versions' }),
        ],
      },
      styles: {
        'wordpress/wp-content/themes/{YOUR_THEME}/assets/stylesheets/styles.css': 'wordpress/wp-content/themes/{YOUR_THEME}/assets/stylesheets/styles.css'
      }
    },

    uglify: {
      options: {
        screwIE8: true,
        mangle: false,
        preserveComments: /^!|@preserve|@license|@cc_on|\/\*\!/i,
      },

      default: {
        files: {
          'wordpress/wp-content/themes/{YOUR_THEME}/assets/javascripts/scripts.js': 'wordpress/wp-content/themes/{YOUR_THEME}/assets/javascripts/scripts.js',
          'wordpress/wp-content/themes/{YOUR_THEME}/assets/javascripts/scripts-vendors.js': 'wordpress/wp-content/themes/{YOUR_THEME}/assets/javascripts/scripts-vendors.js',
        },
      },
    },

    cssmin: {
      options: {
        advanced: false,
        aggressiveMerging: false,
        roundingPrecision: -1,
        shorthandCompacting: false,
      },
      default: {
        files: {
          'wordpress/wp-content/themes/{YOUR_THEME}/assets/stylesheets/styles.css': 'wordpress/wp-content/themes/{YOUR_THEME}/assets/stylesheets/styles.css',
        },
      },
    },
  });

  grunt.registerTask('default', ['watch']);
  grunt.registerTask('release', ['browserify:scripts', 'concat:vendorScripts', 'uglify', 'sass', 'postcss', 'cssmin']);
};
