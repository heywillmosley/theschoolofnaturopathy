YUI({'logExclude': { 'yui': true } }).use('fl-slideshow', function(Y) {

	if( null === Y.one('.fl-node-5e0e5c66291fc .fl-bg-slideshow') ) {
		return;
	}

	var oldSlideshow = Y.one('.fl-node-5e0e5c66291fc .fl-bg-slideshow .fl-slideshow'),
		newSlideshow = new Y.FL.Slideshow({
			autoPlay            : true,
			bgslideshow         : true,
			crop                : true,
			loadingImageEnabled : false,
			randomize           : false,
			responsiveThreshold : 0,
			touchSupport        : false,
			source              : [{type: "urls", urls:[{
thumbURL: "https://theschoolofnaturopathy.xyz/wp-content/uploads/2019/12/Screenshot-2019-12-31-15.29.11-150x150.png",largeURL: "https://theschoolofnaturopathy.xyz/wp-content/uploads/2019/12/Screenshot-2019-12-31-15.29.11-1024x435.png",x3largeURL: "https://theschoolofnaturopathy.xyz/wp-content/uploads/2019/12/Screenshot-2019-12-31-15.29.11.png",caption: "",alt: ""}]}],
			speed               : 6000,
			stretchy            : true,
			stretchyType        : 'contain',
			transition          : 'fade',
			transitionDuration  : 4		});

	if(oldSlideshow) {
		oldSlideshow.remove(true);
	}

	jQuery( '.fl-node-5e0e5c66291fc' ).imagesLoaded( function(){
		newSlideshow.render('.fl-node-5e0e5c66291fc .fl-bg-slideshow');
	} );
});
		
/* Start Global Node Custom JS */

/* End Global Node Custom JS */


/* Start Layout Custom JS */

/* End Layout Custom JS */

