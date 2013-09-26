define(['libs/backbone',

/**
			//Frontend Javascript
			'js/frontend',
			'js/functions',

			// Backbone Files
			'src/models/business',
			'src/models/profile',
			'src/models/intellisenseResult',
			
			'src/collections/businessList',
			'src/collections/profileContainer',
			'src/collections/intellisenseResultList',

			'src/views/SearchView',
			'src/views/SponsorsView',
			'src/views/ListingsView',
			'src/views/ListingDetail',
			'src/views/userView',
			'src/views/editProfileView',
			'src/views/intellisenseView',
**/
			'js/functions',

			'src/collections/netforgeCollection',
				'src/views/netforgeView',

			'src/collections/mangaList',
				'src/views/mangaListView',

			'src/collections/chapterList',
				'src/views/chapterListView',

			'src/collections/pageList',
				'src/views/pageListView',

			// GUI dependencies
			'libs/jquery-scrolltofixed-min',
			'libs/jquery.scrollTo-1.4.2-min',
			'libs/jquery.spritely-0.6.1',
			'libs/bootstrap.min',
			'libs/jquery.fastLiveFilter',
			'libs/jquery.alterclass'
			], function(BB,
/**
				Frontend,
				Functions, 

				Business,
				Profile,
				IntellisenseResult,

				BusinessList,
				ProfileContainer,
				IntellisenseResultList,

				SearchView,
				SponsorsView,
				ListingsView,
				ListingDetailView,
				userView,
				EditProfileView,
				IntellisenseView,
	**/			
				Functions,
				netforgeCollection,
				netforgeView,
				MangaList,
				MangaListView,
				ChapterList,
				ChapterListView,
				PageList,
				PageListView,

				ScrollToFixed, ScrollTo, Spritely, Bootstrap, Fast, Alterclass
				) {


/**
 * WebsiteView View
 *
 * contains all the basic events we want
 *
 * 
 */

var WebsiteView = Backbone.View.extend({
	

	initialize: function() {
		$("#kaiokentag").tooltip({
                  'selector': '',
                  'html': true,
                  'delay': { 'show': 0, 'hide': 1000 },
                  'placement': 'bottom'
                });

		$('.statusbar').hide('slow');
		
		netforge.functions = Functions;

		// We load up some objects from the database
		netforge.collections = {};

		
		// Businesses holder
		
		netforge.collections.mangalist = new MangaList({
			model: Backbone.Model
		});

		netforge.collections.chapterlist = new ChapterList({
			model: Backbone.Model,
			autoFetch: false
		});

		netforge.collections.pagelist = new PageList({
			model: Backbone.Model,
			autoFetch: false
		});


	// We load up some views
	netforge.views = {};

		netforge.views.mangalist = new MangaListView({
			el: '#mangalist-wrapper',
			collection: netforge.collections.mangalist
		});

		netforge.views.chapterlist = new ChapterListView({
			el: '#chapterlist-wrapper',
			collection: netforge.collections.chapterlist
		});

		netforge.views.pagelist = new PageListView({
			el: '#page-wrapper',
			collection: netforge.collections.pagelist
		});


		this.bindAll(); // bind events

	},


	// let's try and keep the number of things here low

	/**
	 * >>> BINDING-RELATED FUNCTIONS
	 */


	bindAll : function() {


		$('#options').on('change', function(event) {
			var option = $(event.currentTarget).val();
			$('#page-wrapper').alterClass('options-*', 'options-' + option);
		});

		$(window).on('keydown', function(event) {
			// Guards
			if( $(event.target).is( $(':input') ) ) {
				return true;
			}

			// Events
			if(event.keyCode == 81) { // 'q'
				netforge.website.toggleSidebar();
				
			} else if(event.keyCode == 65){ // 'a' will be previous chapter
				// do previous chapter stuff
				
				// get current chapter
				var currentChapter = netforge.collections.pagelist.chapterId;
				if(currentChapter) {
					// check if previous chapter is available
					var prevChapter = $("#chapters-select option[value='" + currentChapter  + "']").prev();
					if(prevChapter.length > 0) {
						$("#chapters-select").val(prevChapter.val());
						$("#chapters-select").trigger('change');
					
					// go there
					} else { netforge.functions.status('There are no more new chapters of this manga!'); }
				}

				// if there are no current chapters, we do nothing
				// we could at some point load the last loaded chapter or something
				
				

			} else if(event.keyCode == 68){ // 'd' will be next chapter
				// do next chapter stuff
				
				// get current chapter
				var currentChapter = netforge.collections.pagelist.chapterId;
				if(currentChapter) {
					// check if next chapter is available
					var nextChapter = $("#chapters-select option[value='" + currentChapter  + "']").next();
					if(nextChapter.length > 0) {
						console.log(nextChapter, nextChapter.val());
						$("#chapters-select").val(nextChapter.val());
						$("#chapters-select").trigger('change');
					
					// go there
					} else { netforge.functions.status('This is the first chapter of this manga!'); }
				}

				// if there are no current chapters, we do nothing
				// we could at some point load the last loaded chapter or something

			} else if(event.keyCode == 83){ // 's' will be next page
				// do next page stuff
				netforge.views.pagelist.nextPage();
				event.keyCode = 40;

			} else if(event.keyCode == 87){ // 'w' will be previous page
				// do previous page stuff
				netforge.views.pagelist.prevPage();
				event.keyCode = 38;

			} else if(event.keyCode == 69){ // 'e' will be search
				// do focus on search box
				$('#sidebar').show();
				$('#page-wrapper').removeClass('span12');
				$('#page-wrapper').addClass('span8');

				$('#title-search').focus();
				event.preventDefault();
			}

		});
	},

	toggleSidebar: function() {
		if(netforge.sidebar) {
			$('#sidebar').addClass('closed');
			// $('#page-wrapper').removeClass('span8');
			// $('#page-wrapper').addClass('span12');
			netforge.sidebar = false;
		} else {
			$('#sidebar').removeClass('closed');
			// $('#page-wrapper').removeClass('span12');
			// $('#page-wrapper').addClass('span8');
			netforge.sidebar = true;
		}
	},


	callAtScroll : function(height, fn, args, leeway) {
		if(!leeway) { leeway = 100; }

		var currentScroll = $(window).scrollTop();

		if(height - leeway < currentScroll  &&
			height + (leeway * 2) > currentScroll ) {
				
				fn.call(fn, args);

		}
	},

	changeFavicon : function(src) {
		var link = document.createElement('link'),
			oldLink = document.getElementById('dynamic-favicon');
		link.id = 'dynamic-favicon';
		link.rel = 'shortcut icon';
		link.href = src.data;
		if (oldLink) {
			document.head.removeChild(oldLink);
		}
		document.head.appendChild(link);
	},

	/**
	 * This function is throttled to make sure that not too many of them get sent out in a row
	 * 
	 */
	onWindowScroll: function(event) {
		// called on every window scrolled (if bound)
		// this is where you do things like
		// callAtScroll($("#evenements").offset().top, initEvenements);
		var $this = netforge.website;


	}

});


return WebsiteView;


});