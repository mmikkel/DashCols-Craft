module.exports = function ( grunt, options ) {
	return {
		js : {
			expand : true,
			cwd : options.devJsDir,
			src : '**/*',
			dest : options.buildJsDir
		},
		vendor : {
			expand : true,
			cwd : options.devVendorDir,
			src : '**/*',
			dest : options.buildVendorDir
		}
	};
};
