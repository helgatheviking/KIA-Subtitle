const defaultConfig                     = require( '@wordpress/scripts/config/webpack.config' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const path                              = require( 'path' );

module.exports = {
	...defaultConfig
};
