module.exports = function(grunt) {

  require('load-grunt-tasks')(grunt);
	
  // Project configuration.
  grunt.initConfig({
	pkg: grunt.file.readJSON('package.json'),

    uglify: {
        options: {
            compress: {
                global_defs: {
                    "EO_SCRIPT_DEBUG": false
                },
                dead_code: true
            },
            banner: '/*! <%= pkg.name %> <%= pkg.version %> */\n'
        },
        build: {
            files: [{
                expand: true, // Enable dynamic expansion.
                src: ['js/*.js', '!js/*.min.js'], // Actual pattern(s) to match.
                ext: '.min.js', // Dest filepaths will have this extension.
            }, ]
        }
    },
    jshint: {
        options: {
            reporter: require("jshint-stylish")
        },
        all: ["js/*.js", "!js/*.min.js"]
    },

	clean: {
		//Clean up build folder
		main: ['build/<%= pkg.name %>']
	},

	copy: {
		// Copy the plugin to a versioned release directory
		main: {
			src:  [
				'**',
				'!*~',
				'!node_modules/**',
				'!build/**',
				'!.git/**','!.gitignore','!.gitmodules',
				'!tests/**',
				'!vendor/**',
				'!Gruntfile.js','!package.json','!package-lock.json',
				'!composer.lock','!composer.phar','!composer.json',
				'!CONTRIBUTING.md',
				'!gitcreds.json',
				'!.gitignore',
				'!.gitmodules',
				'!*~',
				'!*.sublime-workspace',
				'!*.sublime-project',
				'!*.transifexrc',
				'!deploy.sh',
				'!languages/.tx',
				'!languages/tx.exe',
				'!README.md',
				'!wp-assets/**',
				'!sidebar/**',
				'!package-lock.json',
				'!readme.md',
				'!.github/**',
				'!js/src/**',
				'webpack.config.js',

			],
			dest: 'build/<%= pkg.name %>/'
		},
	}, 

	// bump version numbers
	replace: {
		Version: {
			src: [
				'readme.txt',
				'readme.md',
				'<%= pkg.name %>.php'
			],
			overwrite: true,
			replacements: [
				{ 
					from: /\*\*Stable tag:\*\* .*/,
					to: "**Stable tag:** <%= pkg.version %>  "
				},
				{
					from: /Stable tag: .*/,
					to: "Stable tag: <%= pkg.version %>"
				},
				{ 
					from: /Version:.\d+(\.\d+)+/,
					to: "Version: <%= pkg.version %>"
				},
				{ 
					from: /public \$version = \'.*/,
					to: "public $version = '<%= pkg.version %>';"
				},
				{
					from: /CONST VERSION = \'.*/,
					to: "CONST VERSION = '<%= pkg.version %>';"
				}
			]
		}
	},

    // Documentation
	wp_readme_to_markdown: {
		convert:{
			files: {
				'readme.md': 'readme.txt'
			},
		},
	},

	// # Internationalization 

	// Add text domain
	addtextdomain: {
		textdomain: '<%= pkg.name %>',
		target: {
			files: {
				src: ['*.php', '**/*.php', '!node_modules/**', '!build/**']
			}
		}
	},

	// Generate .pot file
	makepot: {
		target: {
			options: {
				domainPath: '/languages', // Where to save the POT file.
				exclude: ['build'], // List of files or directories to ignore.
				mainFile: '<%= pkg.name %>.php', // Main project file.
				potFilename: '<%= pkg.name %>.pot', // Name of the POT file.
				type: 'wp-plugin' // Type of project (wp-plugin or wp-theme).
			}
		}
	}

});

grunt.registerTask( 'docs', [ 'wp_readme_to_markdown'] );
grunt.registerTask( 'build', [ 'jshint', 'uglify', 'replace', 'makepot' ] );
grunt.registerTask( 'make', [ 'build', 'clean', 'copy' ] );

};
