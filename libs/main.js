(function() {
	// set up netforge to put the root in there
	window.netforge = {};

	netforge.verbose = true;
	netforge.log = function() {

		if(netforge.verbose) {
			for (var i = 0, j = arguments.length; i < j; i++){
				console.log(arguments[i]);
			}
		} else {
			// write it in a log somewhere
		}
	};

	if(document.URL.search('localhost') > 0) {
		netforge.root = '/kaioken';
	} else {
		netforge.root = '/dev/kaioken';
	}
	netforge.apipath = netforge.root + '/api/';

	var paths = {
			"libs" : "libs",
			"js" : "js"
		};

	var scripts = [
			"libs/jquery",
			"libs/underscore",
			"libs/backbone",
			"libs/backbone-eventbroker",

			"js/script"
		];
	var dependencies = {
			'libs/underscore' : ['libs/jquery'],
			'libs/backbone': ['libs/underscore'],
			'libs/backbone-eventbroker' : ['libs/backbone'],

			'js/script' : ['libs/underscore', 'libs/backbone', 'libs/backbone-eventbroker']
		};



requirejs.config({
    baseUrl: netforge.root,
    paths: paths,
    shim: dependencies
});

require(scripts, function(_, Backbone, EventBroker, script) {
    //This function is called when scripts/helper/util.js is loaded.
    //If util.js calls define(), then this function is not fired until
    //util's dependencies have loaded, and the util argument will hold
    //the module value for "helper/util".

    // the script is actually called after all dependencies have been loaded


});

})();