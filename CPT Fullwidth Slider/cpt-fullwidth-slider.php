<?php
/*
    Full Width Post Slider for "Release" CPT
    Uses Slick Slider, YouTube Iframe API, Font Awesome, SimpleParallax.js
    To be used with WPCode or similar.
*/

/*
	Full Width Post Slider that show custom post type "release".
	by Pangeran Wiguan
	https://pangeranwiguan.com
	Since 11th May 2025

	I use this at https://suzuamane.sukiyo.co front page to show "release" custom post type which is
	the song released CPT.

	It use Custom Post Field, the built in one, not the ACF to get custom post field for the YouTube
	and the music buttons.

	Works on mobile too.

	To be used with WP Code Snippets plugin.
	https://wpcode.com/
	Free version will do.

	OR

	Inside custom plugin.

	OR

	Inside theme function.php files.

*/

// 1. Fetch the posts
$slider_posts_query_args = array(
    'post_type'      => 'release', // Change this to the desired custom post type.
    'posts_per_page' => 5, // How many posts should be in the slider?
    'orderby'        => 'date', // Or 'modified', 'rand', etc.
    'order'          => 'DESC',
    'no_found_rows'  => true, // Speed up query
    'post_status'    => 'publish', // Explicitly get published posts
);
$slider_posts_query = new WP_Query($slider_posts_query_args);

// --- DEBUG LINE ---
// To see this, you might need to temporarily comment out the ob_start() and related ob_get_clean()
// or check your PHP error log / enable WP_DEBUG_DISPLAY.
// error_log('WPCODE SLIDER PHP: Found ' . $slider_posts_query->post_count . ' posts for "release" CPT.');
// --- END DEBUG LINE ---

$slider_id_php = 'release-slider-' . uniqid();
$all_slides_data_php_arr = [];

ob_start(); // Start output buffering for the slider HTML
?>

<?php if ($slider_posts_query->have_posts()) : ?>
    <div id="<?php echo esc_attr($slider_id_php); ?>" class="release-post-slider">
        <?php
        $slide_index = 0;
        while ($slider_posts_query->have_posts()) : $slider_posts_query->the_post();
            $post_id = get_the_ID();
            $featured_img_url = get_the_post_thumbnail_url($post_id, 'full'); // Use 'full' or 'large' for background
            $video_id_custom_field = get_post_meta($post_id, 'videoID', true);
            $release_date = get_the_date(); // Get the post's original publish date

            // Music platform links (assuming these custom field keys)
            $spotify_url = get_post_meta($post_id, 'spotify_url', true) ?: '#';
            $applemusic_url = get_post_meta($post_id, 'applemusic_url', true) ?: '#';
            $youtubemusic_url = get_post_meta($post_id, 'youtubemusic_url', true) ?: '#';
            $amazonmusic_url = get_post_meta($post_id, 'amazonmusic_url', true) ?: '#';

            $all_slides_data_php_arr[$slide_index] = [
                'videoId' => $video_id_custom_field,
                'playerId' => 'youtube-player-' . $post_id . '-' . $slide_index,
                'slideIndex' => $slide_index
            ];
        ?>
            <div class="rps-slide">
                <?php if ($featured_img_url) : ?>
                    <img class="rps-parallax-bg" src="<?php echo esc_url($featured_img_url); ?>" alt="Background for <?php the_title_attribute(); ?>">
                <?php endif; ?>
                <div class="rps-slide-overlay">
                    <div class="rps-slide-content">
                        <h2 class="rps-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php if ($release_date) : ?>
                            <p class="rps-release-date">Released: <?php echo esc_html($release_date); ?></p>
                        <?php endif; ?>

                        <?php if ($video_id_custom_field) : ?>
                            <div class="rps-video-wrapper">
                                <div id="<?php echo esc_attr($all_slides_data_php_arr[$slide_index]['playerId']); ?>" class="rps-youtube-player"></div>
                            </div>
                        <?php endif; ?>

                        <div class="rps-music-buttons">
                            <?php if ($spotify_url !== '#') : ?><a href="<?php echo esc_url($spotify_url); ?>" target="_blank" class="rps-button rps-spotify"><i class="fab fa-spotify"></i> Spotify</a><?php endif; ?>
                            <?php if ($applemusic_url !== '#') : ?><a href="<?php echo esc_url($applemusic_url); ?>" target="_blank" class="rps-button rps-applemusic"><i class="fab fa-apple"></i> Apple Music</a><?php endif; ?>
                            <?php if ($youtubemusic_url !== '#') : ?><a href="<?php echo esc_url($youtubemusic_url); ?>" target="_blank" class="rps-button rps-youtubemusic"><i class="fab fa-youtube"></i> YouTube Music</a><?php endif; ?>
                            <?php if ($amazonmusic_url !== '#') : ?><a href="<?php echo esc_url($amazonmusic_url); ?>" target="_blank" class="rps-button rps-amazonmusic"><i class="fab fa-amazon"></i> Amazon Music</a><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
            $slide_index++;
        endwhile;
        wp_reset_postdata(); // Good practice
        ?>
    </div>
<?php else : ?>
    <p style="text-align:center; padding: 20px; border: 1px solid red; background: #ffe0e0;">
        WPCODE SLIDER DEBUG: No posts found for Custom Post Type "release". Please check if posts are published with this CPT.
    </p>
<?php endif; ?>

<?php
$slider_html_output = ob_get_clean(); // Get buffered HTML
?>
<script>
    //<![CDATA[
    console.log('WPCODE SLIDER: Script block execution started. V4');

    (function() {
        const sliderIdJS = '<?php echo esc_js($slider_id_php); ?>';
        const slidesDataJS = <?php echo json_encode(array_values($all_slides_data_php_arr)); ?>;
        console.log('WPCODE SLIDER: Initial JS Data:', { sliderIdJS, slidesDataJS });


        let librariesStatus = { jquery: false, slick: false, simpleParallax: false, youtubeAPI_script_loaded: false, youtubeAPI_ready_callback_fired: false };
        
        function updateLibraryStatus() { 
            librariesStatus.jquery = typeof jQuery !== 'undefined'; 
            librariesStatus.slick = librariesStatus.jquery && typeof jQuery.fn.slick !== 'undefined'; 
            librariesStatus.simpleParallax = typeof simpleParallax !== 'undefined'; 
            librariesStatus.youtubeAPI_script_loaded = typeof YT !== 'undefined' && typeof YT.Player !== 'undefined';
        }

        function addLinkToHead(href, integrity, crossorigin, id, rel = 'stylesheet', type = null) { 
            if (document.getElementById(id) || document.querySelector(`link[href="${href}"]`)) return; 
            const link = document.createElement('link'); link.rel = rel; if (id) link.id = id; link.href = href; 
            if (type) link.type = type; if (integrity) link.integrity = integrity; if (crossorigin) link.crossOrigin = crossorigin; 
            if (rel === 'stylesheet' && crossorigin) link.referrerPolicy = 'no-referrer'; 
            document.head.appendChild(link); console.log('WPCODE SLIDER: Appended link:', id || href);
        }

        function loadStaticAssets() { 
            console.log('WPCODE SLIDER: loadStaticAssets CALLED'); 
            if (!document.querySelector('link[href*="fontawesome.com/releases/v"], link[href*="font-awesome"]')) { 
                 addLinkToHead('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', 'sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==', 'anonymous', 'custom-font-awesome-css');
            } 
            addLinkToHead('https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', null, null, 'slick-css'); 
            addLinkToHead('https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css', null, null, 'slick-theme-css'); 
            
            const customSliderStyles = ` 
                .release-post-slider { width: 100%; margin: 0 auto; position: relative; /* Ensure this is relative for z-indexing */ } 
                .rps-slide { height: 80vh; position: relative; overflow: hidden !important; color: #fff; display: flex !important; align-items: center; justify-content: center; } 
                .rps-parallax-bg { position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; object-fit: cover !important; z-index: 1 !important; display: block !important; } 
                .rps-slide .simpleParallax { width:100% !important; height:100% !important; overflow: hidden !important; position:absolute !important; top:0 !important; left:0 !important; z-index: 0 !important; } 
                .rps-slide .simpleParallax > img { object-fit: cover !important; width: 100% !important; height: 100% !important; display:block !important; position:absolute !important; top:0 !important; left:0 !important; } 
                .rps-slide-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); display: flex; align-items: center; justify-content: center; padding: 20px; box-sizing: border-box; z-index: 2; } 
                .rps-slide-content { text-align: center; max-width: 800px; width: 90%; position: relative; z-index: 3; } 
                .rps-post-title { font-size: 2.5em; margin-bottom: 10px; font-weight: bold; } .rps-post-title a { color: #fff; text-decoration: none; } .rps-post-title a:hover { text-decoration: none; } 
                .rps-release-date { font-size: 0.9em; color: rgba(255,255,255,0.8); margin-bottom: 20px; } 
                .rps-video-wrapper { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; background: #000; margin-bottom: 20px; } 
                .rps-youtube-player { position: absolute; top: 0; left: 0; width: 100%; height: 100%; } 
                .rps-music-buttons { margin-top: 25px; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; } 
                .rps-button { background-color: #ffffff; border: none; border-radius: 15px; font-size: 18px; font-weight: 500; padding: 0.6em 0.7em 0.6em 1em; text-decoration: none; display: inline-flex; align-items: center; text-align: center; gap: 8px; box-shadow: 6px 6px 18px 0px rgba(0,0,0,0.3); transition: color 300ms ease, background-color 300ms ease; cursor: pointer; } 
                .rps-button i { font-size: 1.2em; } .rps-spotify { color: #1ed760; } .rps-spotify:hover { background-color: #1ed760; color: #fff; } .rps-applemusic { color: #ff0436; } .rps-applemusic:hover { background-color: #ff0436; color: #fff; } .rps-youtubemusic { color: #e10000; } .rps-youtubemusic:hover { background-color: #e10000; color: #fff; } .rps-amazonmusic { color: #000000; } .rps-amazonmusic:hover { background-color: #000000; color: #fff; } 
                .release-post-slider .slick-prev, .release-post-slider .slick-next { z-index: 10; width: 40px; height: 40px; } .release-post-slider .slick-prev:before, .release-post-slider .slick-next:before { font-size: 40px; color: #fff; opacity: 0.75; } .release-post-slider .slick-prev { left: 25px; } .release-post-slider .slick-next { right: 25px; } .release-post-slider .slick-prev:hover:before, .release-post-slider .slick-next:hover:before { opacity: 1; } .slick-dots {padding-bottom: 40px !important; }
                .release-post-slider .slick-dots li button:before { font-size: 12px; color: #fff; opacity: 0.5; } .release-post-slider .slick-dots li.slick-active button:before { opacity: 1; color: #fff; } 
                @media (max-width: 768px) { .rps-post-title { font-size: 1.8em; } .rps-release-date { font-size: 0.8em; } .rps-button { font-size: 16px; padding: 0.5em 0.6em 0.5em 1.5em; } .rps-slide { height: 80vh; } } 
            `; 
            const styleTagId = 'custom-release-slider-styles'; 
            if (!document.getElementById(styleTagId)) { 
                const styleTag = document.createElement('style'); styleTag.type = 'text/css'; styleTag.id = styleTagId; 
                styleTag.appendChild(document.createTextNode(customSliderStyles)); document.head.appendChild(styleTag); 
            }
        }

        function loadScript(src, id, testFnRelevant, callback, integrity = null, crossorigin = null, useDefer = true) { 
            updateLibraryStatus(); 
            if (testFnRelevant && testFnRelevant()) { 
                console.log(`WPCODE SLIDER: Library for ${id} already present. Calling callback.`); 
                if (callback) setTimeout(callback, 0); return; 
            } 
            if (document.getElementById(id)) { 
                 console.log(`WPCODE SLIDER: Script tag ${id} already exists.`); 
                const es = document.getElementById(id); 
                if (es.dataset.loaded === 'true' && callback) { setTimeout(callback,0); } 
                else if (callback) { const oo = es.onload; es.onload = function() { if (oo) oo.call(this); console.log(`WPCODE SLIDER: ${id} (existing) loaded.`); es.dataset.loaded = 'true'; updateLibraryStatus(); setTimeout(callback,0); };} 
                return; 
            } 
            console.log('WPCODE SLIDER: Loading script:', id, src); 
            const s = document.createElement('script'); s.id = id; s.src = src; 
            if (integrity) s.integrity = integrity; if (crossorigin) s.crossOrigin = crossorigin; s.defer = useDefer; 
            s.onload = () => { console.log(`WPCODE SLIDER: SCRIPT ${id} LOADED.`); s.dataset.loaded = 'true'; updateLibraryStatus(); if (callback) setTimeout(callback,0); }; 
            s.onerror = (e) => { console.error(`WPCODE SLIDER: SCRIPT ${id} FAILED. Src: ${src}`,e);}; 
            document.head.appendChild(s); 
        }
        
        let ytPlayers = {}; let currentPlayingPlayer = null; let parallaxInstances = [];
        
        window.onYouTubeIframeAPIReady = function() { 
            console.log('WPCODE SLIDER: GLOBAL onYouTubeIframeAPIReady CALLED.'); 
            librariesStatus.youtubeAPI_ready_callback_fired = true; 
            updateLibraryStatus(); 
            if (!slidesDataJS || slidesDataJS.length === 0) { console.warn("WPCODE SLIDER: No slidesDataJS for YT init."); return; }
            slidesDataJS.forEach((slide) => { 
                if (slide.videoId) { 
                    const pE = document.getElementById(slide.playerId); 
                    if (pE) { 
                        try { ytPlayers[slide.slideIndex] = new YT.Player(slide.playerId, { 
                            height: '100%', width: '100%', videoId: slide.videoId, 
                            playerVars: { 'playsinline': 1, 'autoplay': 0, 'controls': 1, 'modestbranding': 1, 'rel': 0, 'showinfo': 0, 'loop': 0 }, 
                            events: { 'onReady': (event) => onPlayerReady(event, slide.slideIndex) } 
                        }); } catch (e) { console.error('WPCODE SLIDER: Error creating YT.Player:', e);}
                    } else { console.warn('WPCODE SLIDER: Player element NOT FOUND for YT:', slide.playerId);}
                }
            }); 
            if (librariesStatus.slick && jQuery('#' + sliderIdJS).hasClass('slick-initialized') && slidesDataJS[0] && slidesDataJS[0].videoId) { 
                if (ytPlayers[0] && ytPlayers[0].getPlayerState && ytPlayers[0].getPlayerState() !== YT.PlayerState.PLAYING && ytPlayers[0].getPlayerState() !== YT.PlayerState.BUFFERING) { 
                    onPlayerReady({ target: ytPlayers[0] }, 0);
                }
            }
        };
        
        function onPlayerReady(event, slideIndex) { 
            console.log('WPCODE SLIDER: YT Player READY for slide:', slideIndex); 
            const p = event.target; 
            if (p && typeof p.setVolume === 'function') {p.unMute(); p.setVolume(30); console.log('WPCODE SLIDER: Player volume set to 30 for slide ' + slideIndex);} 
            if (slideIndex === 0 && jQuery('#'+sliderIdJS).hasClass('slick-initialized')) {p.playVideo(); currentPlayingPlayer=p;}
        }
        function playVideoForSlide(slideIndex) { 
            console.log("WPCODE SLIDER: PLAY video " + slideIndex); 
            if (ytPlayers[slideIndex] && typeof ytPlayers[slideIndex].playVideo === 'function') { 
                const p = ytPlayers[slideIndex]; 
                if(typeof p.isMuted === 'function' && p.isMuted()){ p.unMute(); } 
                if(typeof p.setVolume === 'function'){ p.setVolume(30); } 
                p.playVideo(); currentPlayingPlayer = p; 
            } else {console.warn("WPCODE SLIDER: No player for slide " + slideIndex);}
        }
        function pauseVideoForSlide(slideIndex) { if (ytPlayers[slideIndex] && typeof ytPlayers[slideIndex].pauseVideo === 'function') { ytPlayers[slideIndex].pauseVideo(); }}
        function muteVideoForSlide(slideIndex) { if (ytPlayers[slideIndex] && typeof ytPlayers[slideIndex].pauseVideo === 'function') { ytPlayers[slideIndex].pauseVideo(); }}

        function forceInitParallax() { 
            console.log('WPCODE SLIDER: PARALLAX: forceInitParallax CALLED. SimpleParallax lib status: ' + librariesStatus.simpleParallax);
            updateLibraryStatus(); // Ensure latest status
            if (!librariesStatus.simpleParallax) {
                console.warn('WPCODE SLIDER: PARALLAX: simpleParallax lib still NOT READY for forceInit.');
                return;
            }
            parallaxInstances.forEach(inst => { if(inst && typeof inst.destroy === 'function') inst.destroy(); }); 
            parallaxInstances = []; 
            const images = document.querySelectorAll('#' + sliderIdJS + ' .rps-parallax-bg');
            console.log('WPCODE SLIDER: PARALLAX: Found .rps-parallax-bg images in forceInit:', images.length, images);
            
            if(images.length === 0) {
                console.warn('WPCODE SLIDER: PARALLAX: No .rps-parallax-bg images found in forceInit.');
                return;
            }
            images.forEach((img, index) => { 
                if (img.closest('.simpleParallax')) { return; } 
                try { 
                    console.log('WPCODE SLIDER: PARALLAX: FORCE Initializing simpleParallax for image index ' + index + ':', img); 
                    const instance = new simpleParallax(img, { scale: 1.7, delay: 0, orientation: 'down', overflow: false }); 
                    parallaxInstances.push(instance); 
                } catch (e) { console.error("WPCODE SLIDER: PARALLAX: FORCE Error initializing for image index " + index + ":", e); } 
            }); 
            console.log('WPCODE SLIDER: PARALLAX: Parallax instances created (forceInit):', parallaxInstances.length);
            if (parallaxInstances.length > 0) {
                console.log('WPCODE SLIDER: PARALLAX: Dispatching synthetic scroll (forceInit).');
                setTimeout(() => { window.dispatchEvent(new CustomEvent('scroll')); }, 300); // Increased delay
            }
        }

        function mainApplicationInit() {
            console.log('WPCODE SLIDER: mainApplicationInit CALLED. Lib Status:', JSON.parse(JSON.stringify(librariesStatus)));
            if (!librariesStatus.jquery || !librariesStatus.slick ) {
                console.warn('WPCODE SLIDER: jQuery or Slick not ready in mainApplicationInit. Retrying.'); 
                setTimeout(mainApplicationInit, 200); // Retry
                return; 
            }
            
            const sliderElement = jQuery('#' + sliderIdJS);
            if (sliderElement.length === 0) { console.error('WPCODE SLIDER: Slider element NOT FOUND in mainApplicationInit.'); return; }
            if (sliderElement.hasClass('slick-initialized')) { console.log('WPCODE SLIDER: Slider already initialized.'); return; }
            
            console.log('WPCODE SLIDER: Initializing Slick on:', sliderElement[0]); // Log the DOM element
            try {
                sliderElement.slick({ 
                    dots: true, infinite: true, speed: 500, fade: true, cssEase: 'linear',
                    slidesToShow: 1, slidesToScroll: 1, autoplay: false, arrows: true, adaptiveHeight: false
                });
            } catch (e) {
                console.error('WPCODE SLIDER: ERROR during Slick initialization:', e);
                return; // Stop if Slick fails
            }

            // Check if the 'init' event listener is being set up correctly
            if (sliderElement.length > 0 && typeof sliderElement.on === 'function') {
                console.log('WPCODE SLIDER: Attaching Slick event listeners.');
                sliderElement.on('init', function(event, slick) {
                    console.log('WPCODE SLIDER: Slick EVENT: init FIRED. Slides in Slick: ' + (slick ? slick.slideCount : 'N/A'));
                    if (librariesStatus.simpleParallax) {
                        console.log('WPCODE SLIDER: Slick init - simpleParallax IS ready, calling forceInitParallax.');
                        forceInitParallax(); 
                    } else {
                        console.warn('WPCODE SLIDER: Slick init - simpleParallax NOT YET ready. Will try fallback.');
                        let pWait = 0; const pInt = setInterval(() => { updateLibraryStatus(); if(librariesStatus.simpleParallax){ console.log('WPCODE SLIDER: simpleParallax ready AFTER Slick init.'); forceInitParallax(); clearInterval(pInt); } else if (pWait++ > 20) { clearInterval(pInt); console.error('WPCODE SLIDER: simpleParallax TIMEOUT after Slick init.');}}, 100);
                    }
                    if (slidesDataJS.length > 0 && slidesDataJS[0].videoId && librariesStatus.youtubeAPI_ready_callback_fired) { if(ytPlayers[slidesDataJS[0].slideIndex]){ playVideoForSlide(slidesDataJS[0].slideIndex); } else { console.warn("WPCODE SLIDER: Player 0 not ready at Slick init (API CB fired).");}} else if (slidesDataJS.length > 0 && slidesDataJS[0].videoId) { console.warn("WPCODE SLIDER: YT API not ready (CB not fired) at slick init for video 0.");}
                });
                sliderElement.on('beforeChange', function(event, slick, currentSlideIdx, nextSlideIdx) { const cs = slidesDataJS.find(s=>s.slideIndex===currentSlideIdx); if(cs && cs.videoId && librariesStatus.youtubeAPI_ready_callback_fired){pauseVideoForSlide(cs.slideIndex); }});
                sliderElement.on('afterChange', function(event, slick, currentSlideIdx) { console.log('WPCODE SLIDER: Slick EVENT: afterChange to ' + currentSlideIdx); const cs = slidesDataJS.find(s=>s.slideIndex===currentSlideIdx); if(cs && cs.videoId && librariesStatus.youtubeAPI_ready_callback_fired){ if(ytPlayers[cs.slideIndex]){playVideoForSlide(cs.slideIndex);}else{console.warn("WPCODE SLIDER: Player for " + cs.slideIndex + " not ready at afterChange (API CB fired).");} } });
            } else {
                console.error('WPCODE SLIDER: sliderElement is not a valid jQuery object or .on is not a function.');
            }
        }

        function checkAndProceed() { 
            updateLibraryStatus(); 
            console.log('WPCODE SLIDER: checkAndProceed. Status:', JSON.parse(JSON.stringify(librariesStatus))); 
            if (librariesStatus.jquery && librariesStatus.slick && librariesStatus.simpleParallax) { 
                console.log('WPCODE SLIDER: Core libraries (jQuery, Slick, SimpleParallax) are ready.'); 
                if (!librariesStatus.youtubeAPI_script_loaded && !librariesStatus.youtubeAPI_ready_callback_fired) { 
                    console.log('WPCODE SLIDER: YouTube API script not loaded & callback not fired. Attempting to load script.'); 
                    loadScript('https://www.youtube.com/iframe_api', 'youtube-iframe-api', function() { updateLibraryStatus(); return librariesStatus.youtubeAPI_script_loaded; }, function() { console.log("WPCODE SLIDER: YouTube API script loaded. Waiting for global callback."); setTimeout(() => { updateLibraryStatus(); if (librariesStatus.youtubeAPI_script_loaded && !librariesStatus.youtubeAPI_ready_callback_fired) { console.warn("WPCODE SLIDER: YT.Player exists but onYouTubeIframeAPIReady wasn't called. Manually invoking."); if(typeof window.onYouTubeIframeAPIReady === 'function') window.onYouTubeIframeAPIReady(); } }, 2000); }, null, null, true ); 
                } else if (librariesStatus.youtubeAPI_script_loaded && !librariesStatus.youtubeAPI_ready_callback_fired) { 
                    console.warn("WPCODE SLIDER: YT API script loaded by other, but our callback not fired. Will try manual call."); 
                    setTimeout(() => { updateLibraryStatus(); if (librariesStatus.youtubeAPI_script_loaded && !librariesStatus.youtubeAPI_ready_callback_fired) { console.warn("WPCODE SLIDER: Forcing onYouTubeIframeAPIReady call."); if(typeof window.onYouTubeIframeAPIReady === 'function') window.onYouTubeIframeAPIReady(); } }, 2500); 
                } 
                if (document.readyState === 'loading') { 
                    document.addEventListener('DOMContentLoaded', mainApplicationInit); 
                } else { 
                    mainApplicationInit(); 
                } 
            } else { 
                console.log('WPCODE SLIDER: Core libraries not yet ready. Polling checkAndProceed again.'); 
                setTimeout(checkAndProceed, 200); 
            } 
        }
        
        console.log('WPCODE SLIDER: Initializing asset loading.');
        loadStaticAssets(); 
        loadScript('https://code.jquery.com/jquery-3.6.0.min.js', 'jquery-js', function() { return typeof jQuery !== 'undefined'; }, function() { updateLibraryStatus(); console.log('WPCODE SLIDER: jQuery confirmed ready.'); loadScript('https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', 'slick-carousel-js', function() { return typeof jQuery !== 'undefined' && typeof jQuery.fn.slick !== 'undefined'; }, function() { updateLibraryStatus(); console.log('WPCODE SLIDER: Slick confirmed ready.'); loadScript( 'https://cdn.jsdelivr.net/npm/simple-parallax-js@5.6.2/dist/simpleParallax.min.js', 'simple-parallax-js', function() { return typeof simpleParallax !== 'undefined'; }, function() { updateLibraryStatus(); console.log('WPCODE SLIDER: simpleParallax confirmed ready.'); 
        if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', function() { console.log('WPCODE SLIDER: DOMContentLoaded - EARLY PARALLAX ATTEMPT.'); forceInitParallax(); }); } else { console.log('WPCODE SLIDER: DOM already ready - EARLY PARALLAX ATTEMPT.'); forceInitParallax(); }
        checkAndProceed(); }, 'sha256-GBIPMHSjsTxzIyJuhuk7wWz8z2oKeev8qW/c3IgOeVQ=', 'anonymous', true ); }, null, null, true ); }, null, null, false );

        window.addEventListener('load', function() { console.log('WPCODE SLIDER: WINDOW LOAD event. Attempting forceInitParallax if not already working.'); forceInitParallax(); });

    })();
    //]]>
</script>

<?php
echo $slider_html_output;
?>