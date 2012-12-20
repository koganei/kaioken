/* Author:

*/
define([
			'src/views/WebsiteView'
			
		], function(WebsiteView) {
	netforge.log('loading... [script.js]');


	// netforge is going to contain a dispatcher that relays all events
	netforge.dispatcher = _.clone(Backbone.EventBroker);
	netforge.dispatcher.on('all', function(event) { console.log('dispatcher triggered [' + event + ']'); });

	// so netforge is going to contain all of our collections and views
	// and a view called website, that's gonna go directly inside of netforge
	netforge.website = new WebsiteView();

	





});

