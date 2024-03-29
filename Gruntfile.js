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
                src: ['assets/js/*.js', '!assets/js/*.min.js'], // Actual pattern(s) to match.
                ext: '.min.js', // Dest filepaths will have this extension.
            }, ]
        }
    },
    jshint: {
        options: {
            reporter: require("jshint-stylish")
        },
        all: ["assets/js/*.js", "!assets/js/*.min.js"]
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
				'!assets/js/src/**',
				'!webpack.config.js',
				'!.nvmrc',

				'!demo-content/**'
			],
			dest: 'build/'
		},
	}, 

	// Make a zipfile.
	compress: {
		main: {
			options: {
				mode: 'zip',
				archive: 'deploy/<%= pkg.name %>-<%= pkg.version %>.zip',
			},
			expand: true,
			cwd: 'build/',
			dest: '<%= pkg.name %>',
			src: [ '**/*' ]
		},
	},

	// bump version numbers
	replace: {
		Version: {
			src: [
				'readme.txt',
				'<%= pkg.name %>.php'
			],
			overwrite: true,
			replacements: [
				{
					from: /Stable tag:.*$/m,
					to: "Stable tag: <%= pkg.version %>"
				},
				{
					from: /Version:.*$/m,
					to: "Version: <%= pkg.version %>"
				},
				{
					from: /public \$version = \'.*.'/m,
					to: "public $version = '<%= pkg.version %>'"
				},
				{
					from: /public \$version      = \'.*.'/m,
					to: "public $version      = '<%= pkg.version %>'"
				},
				{
					from: /CONST VERSION = \'.*.'/m,
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
	}

});
grunt.registerTask( 'docs', [ 'wp_readme_to_markdown'] );
grunt.registerTask( 'build', [ 'replace', 'jshint', 'uglify','clean', 'copy' ] );
grunt.registerTask( 'deploy', [ 'build', 'compress' ] );
grunt.registerTask( 'release', [ 'deploy', 'clean' ] );
grunt.registerTask( 'zip', [ 'clean', 'copy', 'compress' ] );
};
