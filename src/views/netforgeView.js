define(['libs/backbone', 'libs/mustache'], function() {
	
	var NetforgeView = Backbone.View.extend({
		template : false,
		limit: false,
		templateReceived: true,
		currentItem: 0,
		initialized: false,
		
		initialize: function() {
			// we fetch the template and cache it
			// we can either fetch the template through ajax
			// or get it from require js using the text! plugin
			// we're ajaxing it

			//console.log('coll:', this.collection, this.collection.models, MustacheObject);
			var $this = this;
			var trigger = this.prefix;
			if(this.handles) { trigger = this.handles; }

			if(this.options.prefix) { this.prefix = this.options.prefix; }
			if(this.options.templateURL) { this.templateURL = this.options.templateURL; }

			if(window.location.href.indexOf("admin/") !== -1) { this.templateURL = "../"+this.templateURL; }
			this.templateReceived = $.ajax({url: this.templateURL})
				.done(function(data) {
						$this.template = data;
						netforge.dispatcher.on(trigger + ':reset', $this.render, $this);
						netforge.dispatcher.on(trigger + ':change', $this.render, $this);
						
						if(_.isFunction($this.options.onInit)) {
							$this.options.onInit.apply(this);
						} else if(_.isFunction($this.onInit)) {
							$this.onInit.apply(this);
						}

						$this.render();


				});


		},

		events: {
			"click .next" : "goToNext",
			"click .previous" : "goToPrevious"
		},

		goToNext: function() {
			this.currentItem++;
			if(this.currentItem > this.collection.models.length) { this.currentItem = 0; }
			this.render();
		},

		goToPrevious: function() {
			this.currentItem--;
			if(this.currentItem == -1) { this.currentItem = 0; }
			this.render();
		},

		curate : function(models) {
			// you can override this function in order to trim the models shown
			return models;
		},

		render : function() {
			var $this = this;
			var items = this.curate(this.collection.models);
			if(this.initialized) {
				// then we first need to remove the ones we have already
				$($this.el).addClass('remove');
			}

			if(items.length > 0 && items[0].attributes) {
				
				// var containsData = false;

				// for (var i = items[0].attributes.length - 1; i >= 0; i--) {
				// 	if(items[0].attributes[i] !== "" &&
				// 		items[0].attributes[i] != null &&
				// 		items[0].attributes[i] !== undefined) {
						
				// 			containsData = true;
				// 	}
				// }

				// if(containsData) {

					if(this.limit > 0) { items = _.first(_.rest(items, this.currentItem), this.limit); }

					var MustacheObject = { items: items };
					
					
					$.when(this.templateReceived).done(function() {
						var html = Mustache.to_html($this.template, MustacheObject);
						
						var timeout = 0;
						if($this.initialized) { timeout = 500; }

						setTimeout(function() {
							$($this.el).removeClass('remove');
							$($this.el).html(html);
							if(_.isFunction($this.options.onRender)) {
								$this.options.onRender.apply(this);
							} else if(_.isFunction($this.onRender)) {
								$this.onRender.apply(this);
							}
						}, timeout);
						
					});
				// }
			}


			this.initialized = true;

		}

	});

	return NetforgeView;

});