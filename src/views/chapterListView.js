define(['src/views/netforgeView'], function(NetforgeView) {

	var chapterListView = NetforgeView.extend({
		templateURL : 'templates/chapterlist.html',
		prefix: 'chapterlist',
		handles: 'chapterlist',

		events: {
			"change select":"openPage"
		},

		curate: function(models) {
			if(models.length > 0 &&
				models[0].attributes.title &&
				models[0].attributes.title !== "") {

				if(models[0].attributes.chapters) {
					for (var i = models[0].attributes.chapters.length - 1; i >= 0; i--) {
						var chapter = models[0].attributes.chapters[i];
						chapter['number'] = chapter[0];
						chapter['name'] = chapter[2];
						chapter['id'] = chapter[3];
					}

				}

				models[0].attributes.getDate = function() {
					return function(text, render) {
						var d = new Date(this.attributes.last_chapter_date);
						console.log('mustaching', d, this);
						return d.toLocaleDateString();
					};
				};

				return models;
			} else { return false; }
		},

		onRender: function() {
			if(netforge.collections.chapterlist.models &&
				netforge.collections.chapterlist.models.length > 0 &&
				netforge.collections.chapterlist.models[0].attributes.chapters &&
				netforge.collections.chapterlist.models[0].attributes.chapters.length > 0) {
				netforge.collections.pagelist.setChapterId(
				netforge.collections.chapterlist.models[0].attributes.chapters[0].id);
				netforge.collections.pagelist.goFetch();
			}
		},

		refresh: function(event) {
			netforge.views.chapterlist.render();
		},

		openPage: function(event) {
			var target = event.currentTarget;
			var chapterId = $(target).val();

			netforge.collections.pagelist.setChapterId(chapterId);
			netforge.collections.pagelist.goFetch();
		}

	});

	return chapterListView;

});