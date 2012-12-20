define(['libs/backbone'], function() {
	
	/**
	 * A simple collection that holds a model
	 * @type Backbone.Collection
	 */
	var netforgeCollection = Backbone.Collection.extend({
		url : netforge.apipath + 'netforge.json',
		eventPrefix: 'netforge',
		autoFetch: true,
		fetchData: {},

		initialize : function(options) {
			
			// make sure only specific options can be supplanted,
			// I guess it could help to make sure you don't screw things up
			// like a private/public implementation
			this.model = options.model;
			this.url = (options.url)?options.url:this.url;
			this.autoFetch = (options.autoFetch || options.autoFetch === false)?false:this.autoFetch;
			this.fetchData = _.extend(this.fetchData, options.fetchData);

			var prefix = options.eventPrefix || this.eventPrefix;
			this.on('all', function(event) {
				netforge.dispatcher.trigger(prefix + ':' + event);
			});
			
			if(this.autoFetch) {
				this.goFetch();
			}
		},

		goFetch: function() {
			var $this = this;
			$('body').addClass(this.eventPrefix + '-loading');

			this.fetch({
				data: this.fetchData,
				success : this.onSuccess,
				error : this.onFail
			});

			netforge.dispatcher.on(this.eventPrefix + ':reset', function() {
				$('body').removeClass($this.eventPrefix + '-loading');
			});
		},

		onSuccess : function(collection) {

		},

		onFail : function(collection, response) {
			console.log('error on fetch for ', collection, ':', response);
		}


	});

	return netforgeCollection;

});