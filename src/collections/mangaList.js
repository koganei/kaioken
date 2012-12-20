define(['libs/backbone', 'src/collections/netforgeCollection'], function(BB, netforgeCollection) {
	
	/**
	 * A simple collection that holds all the vins
	 * @type Backbone.Collection
	 */
	var MangaList = netforgeCollection.extend({

		//url: 'proxy.php?url=' + encodeURIComponent('http://www.mangaeden.com/api/list/0/'),
		url: 'data/mangalist.json',
		eventPrefix: 'mangalist',

		parse: function(resp, xhr) {
			return resp['manga'];
		}
	});

	return MangaList;
});