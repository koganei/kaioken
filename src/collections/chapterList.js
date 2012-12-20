define(['libs/backbone', 'src/collections/netforgeCollection'], function(BB, netforgeCollection) {
	
	/**
	 * A simple collection that holds all the vins
	 * @type Backbone.Collection
	 */
	var ChapterList = netforgeCollection.extend({
		mangaId: "",
		url: 'proxy.php?url=' + encodeURIComponent('http://www.mangaeden.com/api/manga/0/'),
		eventPrefix: 'chapterlist',

		onInit: function() {
			this.makeURL();
		},

		setMangaId: function(mangaId) {
			this.mangaId = mangaId;
			this.makeURL();
		},

		makeURL: function() {
			this.url = 'proxy.php?url=' + encodeURIComponent('http://www.mangaeden.com/api/manga/'+this.mangaId+'/');
		}

		// parse: function(resp, xhr) {
		//	return resp.manga;
		// }
	});

	return ChapterList;
});