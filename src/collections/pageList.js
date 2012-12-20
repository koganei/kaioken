define(['libs/backbone', 'src/collections/netforgeCollection'], function(BB, netforgeCollection) {
	
	/**
	 * A simple collection that holds all the vins
	 * @type Backbone.Collection
	 */
	var PageList = netforgeCollection.extend({
		chapterId: "",
		url: 'proxy.php?url=' + encodeURIComponent('http://www.mangaeden.com/api/chapter/0/'),
		eventPrefix: 'pagelist',

		onInit: function() {
			this.makeURL();
		},

		setChapterId: function(chapterId) {
			this.chapterId = chapterId;
			this.makeURL();
		},

		makeURL: function() {
			this.url = 'proxy.php?url=' + encodeURIComponent('http://www.mangaeden.com/api/chapter/'+this.chapterId+'/');
		}

		// parse: function(resp, xhr) {
		//	return resp.manga;
		// }
	});

	return PageList;
});