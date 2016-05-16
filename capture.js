var args = require('minimist')(process.argv.slice(2));
var webshot = require('webshot');
var fs = require('fs');

if(args._.length !== 2) {
	console.error('Usage: capture <url of webpage> <output file> [--screenWidth=<width> --screenHeight=<height>]');
	process.exit(1);
}

var options = {};

if(args.screenHeight || args.screenWidth) {
	options.screenSize = {
		width: args.screenWidth,
		height: args.screenHeight
	} 
}

webshot(args._[0], args._[1], options, function(err) {
  	console.log('done');
	process.exit(0);
});