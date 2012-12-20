define(['src/views/netforgeView'], function(NetforgeView) {

	var pageListView = NetforgeView.extend({
		templateURL : 'templates/page.html',
		prefix: 'pagelist',
		handles: 'pagelist',
		selectedPage : 0,

		events: {
			"click .next":"nextChapter"
		},

		onRender: function() {
			var $this = netforge.views.pagelist;
			$this.selectPage();
		},

		selectPage: function(pagenumber) {
			var $this = netforge.views.pagelist;

				if(pagenumber) { $this.selectedPage = pagenumber; }
				$pages = $('#page-wrapper .page');
				if($pages.length > 0) {
					$pages.removeClass('selected');
					$page = $pages.filter('.page[data-page="'+$this.selectedPage+'"]');
					$page.addClass('selected');
					console.log($pages, $page);
					$.scrollTo($page);
				}
		},

		nextPage: function() {
			var $this = netforge.views.pagelist;
			var collection = $this.collection;
			if($this.selectedPage > collection.models[0].attributes.images.length) {
				netforge.functions.status('This is the last page of this chapter!');

			} else {
				$this.selectPage($this.selectedPage + 1);
			}

		},

		prevPage: function() {
			var $this = netforge.views.pagelist;
			var collection = $this.collection;
			if($this.selectedPage <= 0) {
				netforge.functions.status('This is the first page of this chapter!');

			} else {
				$this.selectPage($this.selectedPage - 1);
			}
		},



		curate: function(models) {
			if(models.length > 0 && models[0].attributes.images) {
				for (var i = models[0].attributes.images.length - 1; i >= 0; i--) {
					var page = models[0].attributes.images[i];
					page['number'] = page[0];
					page['img'] = page[1];
					page['width'] = page[2];
					page['height'] = page[3];
				}

				models[0].attributes.images = _.sortBy(models[0].attributes.images, function(image) {
					return image.number;
				});
			}

			return models;
		},

		nextChapter: function(event) {
			// var target = event.currentTarget;
			// var chapterId = $(target).data('i');

			// netforge.collections.pagelist.setchapterId(chapterId);
			// netforge.collections.pagelist.goFetch();
		}

	});

	return pageListView;

});