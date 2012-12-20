define(['src/views/netforgeView'], function(NetforgeView) {

	var mangaListView = NetforgeView.extend({
		templateURL : 'templates/mangalist.html',
		prefix: 'mangalist',
		handles: 'mangalist',

		events: {
			"click .refresh":"refresh",
			"click .manga":"openChapter"
		},

		curate: function(models) {
			
				return models;
			//return _.first(models, 25);
		},

		refresh: function(event) {
			// we could make this pull it from the manga eden API
			netforge.views.mangalist.render();
		},

		openChapter: function(event) {
			var target = event.currentTarget;
			var mangaId = $(target).data('i');

			netforge.collections.chapterlist.setMangaId(mangaId);
			netforge.collections.chapterlist.goFetch();
		},

		onRender: function() {
			$('#title-list').find('li:gt(25)').remove();


			$('#title-search').fastLiveFilter('#full-title-list', {
				timeout: 300,

				callback: function(num) {
					$list = $('#full-title-list').clone();
					$list.find('.livefiltered').remove();
					$list.find(':gt(50)').remove();
					$('#title-list').html($list.html());	
				}
			});
			
		}

	});

	return mangaListView;

});