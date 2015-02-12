module.exports = function ( grunt, options ) {
	return {
		options : {
			force : true
		},
		fonts : options.buildFontsDir,
        images : options.buildImgDir,
        js : options.buildJsDir,
        css : options.buildCssDir,
        build : options.buildDir
	};
};
