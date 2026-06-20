jQuery(document).ready(function() {
	var revapi;

/**	MEDIA ELEMENTS
*************************************************** **/
	if(jQuery().mediaelementplayer && jQuery('video').length > 0 && jQuery(".fullscreenbanner video").length < 1 && jQuery(".fullwidthbanner video").length < 1) { // exclude revolution slider videos
		jQuery('video').mediaelementplayer({
			// if the <video width> is not specified, this is the default
			defaultVideoWidth: 480,
			// if the <video height> is not specified, this is the default
			defaultVideoHeight: 270,
			// if set, overrides <video width>
			videoWidth: '100%', // -1
			// if set, overrides <video height>
			videoHeight: '100%', // -1
			// width of audio player
			audioWidth: 400,
			// height of audio player
			audioHeight: 30,
			// initial volume when the player starts
			startVolume: 0.8,
			// useful for <audio> player loops
			loop: true,
			// enables Flash and Silverlight to resize to content size
			enableAutosize: true,
			// the order of controls you want on the control bar (and other plugins below)
			features: ['playpause','progress','current','duration','tracks','volume','fullscreen'],
			// Hide controls when playing and mouse is not over the video
			alwaysShowControls: false,
			// force iPad's native controls
			iPadUseNativeControls: false,
			// force iPhone's native controls
			iPhoneUseNativeControls: false, 
			// force Android's native controls
			AndroidUseNativeControls: false,
			// forces the hour marker (##:00:00)
			alwaysShowHours: false,
			// show framecount in timecode (##:00:00:00)
			showTimecodeFrameCount: false,
			// used when showTimecodeFrameCount is set to true
			framesPerSecond: 25,
			// turns keyboard support on and off for this instance
			enableKeyboard: true,
			// when this player starts, it will pause other players
			pauseOtherPlayers: true,
			// array of keyboard commands
			keyActions: []

		});

		setTimeout('eventClickTrigger()', 1000);
		function eventClickTrigger() {
			jQuery('video').trigger('click');
			// resizeToCover();
		}

		// VOVER STYLE
		var min_w = 300; // minimum video width allowed
		var vid_w_orig;  // original video dimensions
		var vid_h_orig;

		jQuery(function() { // runs after DOM has loaded
			vid_w_orig = parseInt(jQuery('video, source').attr('width'));
			vid_h_orig = parseInt(jQuery('video, source').attr('height'));
			jQuery(window).resize(function () { resizeToCover(); });
		});

		function resizeToCover() {

			// set the video viewport to the window size
			jQuery('.video-wrap').width(jQuery(window).width());
			jQuery('.video-wrap').height(jQuery(window).height());

			// use largest scale factor of horizontal/vertical
			var scale_h = jQuery(window).width() / vid_w_orig;
			var scale_v = jQuery(window).height() / vid_h_orig;
			var scale = scale_h > scale_v ? scale_h: scale_v;

			// don't allow scaled width < minimum video width
			if (scale * vid_w_orig < min_w) {scale = min_w / vid_w_orig;};

			// now scale the video
			jQuery('video, source').width(scale * vid_w_orig);
			jQuery('video, source').height(scale * vid_h_orig);

			// and center it by scrolling the video viewport
			jQuery('.video-wrap').scrollLeft((jQuery('video').width() - jQuery(window).width()) / 2);
			jQuery('.video-wrap').scrollTop((jQuery('video').height() - jQuery(window).height()) / 2);
		}	

	}	
	
	/**
		@HALFSCREEN SLIDER
	**/
	if(jQuery(".fullwidthbanner").length > 0) {

	   revapi = jQuery('.fullwidthbanner').revolution({
			delay:9000,
			startwidth:1170,
			startheight:500,
			hideThumbs:10,

			thumbWidth:100,
			thumbHeight:50,
			thumbAmount:5,

			navigationType:"both",
			navigationArrows:"solo",
			navigationStyle:"round",

			touchenabled:"on",
			onHoverStop:"on",

			navigationHAlign:"center",
			navigationVAlign:"bottom",
			navigationHOffset:0,
			navigationVOffset:0,

			soloArrowLeftHalign:"left",
			soloArrowLeftValign:"center",
			soloArrowLeftHOffset:20,
			soloArrowLeftVOffset:0,

			soloArrowRightHalign:"right",
			soloArrowRightValign:"center",
			soloArrowRightHOffset:20,
			soloArrowRightVOffset:0,

			shadow:1,
			fullWidth:"on",
			fullScreen:"off",

			stopLoop:"off",
			stopAfterLoops:-1,
			stopAtSlide:-1,


			shuffle:"off",

			autoHeight:"off",
			forceFullWidth:"off",

			hideThumbsOnMobile:"off",
			hideBulletsOnMobile:"on",
			hideArrowsOnMobile:"on",
			hideThumbsUnderResolution:0,

			hideSliderAtLimit:0,
			hideCaptionAtLimit:768,
			hideAllCaptionAtLilmit:0,
			startWithSlide:0,
			fullScreenOffsetContainer: "",
			flatTransitions: '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45'
		});

		// Used by styleswitcher onle - delete this on production!
		//jQuery("#is_wide, #is_boxed").bind("click", function() { revapi.revredraw(); });
	}


	/**
		@FULLSCREEN SLIDER
	**/
	if(jQuery(".fullscreenbanner").length > 0) {

		var tpj=jQuery;				
		tpj.noConflict();				
		var revapi25;
		
		tpj(document).ready(function() {
						
			if(tpj('.fullscreenbanner').revolution == undefined) {
				revslider_showDoubleJqueryError('.fullscreenbanner');
			} else {
				revapi25 = tpj('.fullscreenbanner').show().revolution({
					delay:9000,
					startwidth:1200,
					startheight:700,
					hideThumbs:10,
					
					thumbWidth:100,
					thumbHeight:50,
					thumbAmount:4,
					
					navigationType:"none",
					navigationArrows:"none",
					navigationStyle:"round",
					
					touchenabled:"on",
					onHoverStop:"on",
					
					navigationHAlign:"center",
					navigationVAlign:"bottom",
					navigationHOffset:0,
					navigationVOffset:0,

					soloArrowLeftHalign:"left",
					soloArrowLeftValign:"center",
					soloArrowLeftHOffset:20,
					soloArrowLeftVOffset:0,

					soloArrowRightHalign:"right",
					soloArrowRightValign:"center",
					soloArrowRightHOffset:20,
					soloArrowRightVOffset:0,
							
					shadow:1,
					fullWidth:"off",
					fullScreen:"on",

					stopLoop:"off",
					stopAfterLoops:-1,
					stopAtSlide:-1,

					
					shuffle:"off",
					
											
					forceFullWidth:"on",						
					fullScreenAlignForce:"off",						
					hideThumbsOnMobile:"off",
					hideBulletsOnMobile:"on",
					hideArrowsOnMobile:"on",
					hideThumbsUnderResolution:0,
					
					hideSliderAtLimit:0,
					hideCaptionAtLimit:768,
					hideAllCaptionAtLilmit:0,
					startWithSlide:0,
					fullScreenOffsetContainer: "header, .pagetitlewrap"	
				});

			// Used by styleswitcher onle - delete this on production!
			//jQuery("#is_wide, #is_boxed").bind("click", function() { revapi25.revredraw(); });

			}
		});	//ready

	}


	/**
		@KEN BURNS
	**/
	if(jQuery(".fullscreenbanner.ken-burns").length > 0) {

		revapi = jQuery('.fullwidthbanner').revolution({
			dottedOverlay:"none",
			delay:9000,
			startwidth:1170,
			startheight:400,
			hideThumbs:200,
			
			thumbWidth:100,
			thumbHeight:50,
			thumbAmount:5,
			
			navigationType:"bullet",
			navigationArrows:"solo",
			navigationStyle:"round",
			
			touchenabled:"on",
			onHoverStop:"off",
			
			navigationHAlign:"center",
			navigationVAlign:"bottom",
			navigationHOffset:0,
			navigationVOffset:0,

			soloArrowLeftHalign:"left",
			soloArrowLeftValign:"center",
			soloArrowLeftHOffset:20,
			soloArrowLeftVOffset:0,

			soloArrowRightHalign:"right",
			soloArrowRightValign:"center",
			soloArrowRightHOffset:20,
			soloArrowRightVOffset:0,
					
			shadow:1,
			fullWidth:"on",
			fullScreen:"off",

			stopLoop:"off",
			stopAfterLoops:-1,
			stopAtSlide:-1,

			
			shuffle:"off",
			
			autoHeight:"off",						
			forceFullWidth:"off",						
									
			hideThumbsOnMobile:"off",
			hideBulletsOnMobile:"off",
			hideArrowsOnMobile:"off",
			hideThumbsUnderResolution:0,
			
			hideSliderAtLimit:0,
			hideCaptionAtLimit:0,
			hideAllCaptionAtLilmit:0,
			startWithSlide:0,
			videoJsPath:"http://server.local/revslider/wp-content/plugins/revslider/rs-plugin/videojs/",
			fullScreenOffsetContainer: ""
		});

		// Used by styleswitcher onle - delete this on production!
		//jQuery("#is_wide, #is_boxed").bind("click", function() { revapi.revredraw(); });

	}
	


});	//ready
