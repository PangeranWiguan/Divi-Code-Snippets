<?php
/**
 * YouTube Player with Custom Modern UI & Playlist
 *
 * Description: Embeds a YouTube player for a specified playlist ID, featuring a custom,
 *              modern user interface for video selection. Playlist items are fetched
 *              via the YouTube Data API v3 and cached for performance.
 *              Styles are output inline and moved to the <head> via JavaScript
 *              to ensure compatibility with various theme environments.
 *
 * Author:      Pangeran Wiguan
 * Author URI:  https://pangeranwiguan.com
 * Version:     1.0.0
 * Date:        21 May 2024
 * Requires:    YouTube Data API v3 Key, YouTube Playlist ID
 * Tested with: WordPress 6.8.1,
 * License:     GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html]
 *
 * Usage:       Intended for use with WPCode (PHP Snippet, Shortcode insertion)
 *              or a custom plugin / theme's functions.php.
 *              Configure API Key and Playlist ID within the snippet.
 *
 * Example URL: https://suzuamane.sukiyo.co/test-youtube-custom-playlist/
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// --- 1. Configuration ---
$youtubePlaylistID = 'youtubeplaylistID'; // <<<--- REPLACE with YouTube Playlist ID, not the VideoID.
$youtubeApiKey = 'youtubeDataAPI3';   // <<<--- REPLACE
$cache_duration = 1 * HOUR_IN_SECONDS;
$max_results_fetch = 25;

// --- 2. Function to fetch playlist items ---
if (!function_exists('get_youtube_playlist_items_with_caching_jsmove')) {
    function get_youtube_playlist_items_with_caching_jsmove($playlist_id, $api_key, $max_results, $cache_time) {
        
        if (empty($playlist_id) || empty($api_key)) { return ['error' => 'Playlist ID or API Key missing.']; }
        $transient_key = 'yt_playlist_items_' . md5($playlist_id . $max_results);
        $cached_data = get_transient($transient_key);
        if (false !== $cached_data) { return $cached_data; }
        $api_url = sprintf(
            'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&maxResults=%d&playlistId=%s&key=%s',
            absint($max_results), esc_attr($playlist_id), esc_attr($api_key)
        );
        $response = wp_remote_get($api_url, ['timeout' => 15]);
        if (is_wp_error($response)) { return ['error' => 'API request failed: ' . $response->get_error_message()]; }
        $body = wp_remote_retrieve_body($response); $data = json_decode($body, true);
        if (isset($data['error'])) { return ['error' => 'API Error: ' . esc_html($data['error']['errors'][0]['message'] ?? 'Unknown API Error')];}
        if (!isset($data['items']) || empty($data['items'])) { set_transient($transient_key, ['error' => 'No items found.'], $cache_time); return ['error' => 'No items found.'];}
        $videos = [];
        foreach ($data['items'] as $item) {
            if (isset($item['snippet']['resourceId']['videoId']) && $item['snippet']['title'] !== 'Private video' && $item['snippet']['title'] !== 'Deleted video') {
                $videos[] = [
                    'videoId' => $item['snippet']['resourceId']['videoId'], 'title' => $item['snippet']['title'],
                    'thumbnail' => $item['snippet']['thumbnails']['medium']['url'] ?? $item['snippet']['thumbnails']['default']['url'] ?? '',
                    'position' => $item['snippet']['position'],
                    'description' => mb_strimwidth(strip_tags($item['snippet']['description']), 0, 100, "..."),
                ];
            }
        }
        if (empty($videos)) { set_transient($transient_key, ['error' => 'No public videos processed.'], $cache_time); return ['error' => 'No public videos processed.'];}
        set_transient($transient_key, $videos, $cache_time); return $videos;
    }
}

// --- Fetch the playlist data ---
$playlistItemsData = get_youtube_playlist_items_with_caching_jsmove($youtubePlaylistID, $youtubeApiKey, $max_results_fetch, $cache_duration);

// --- 3. Generate Unique IDs ---
$instance_id = 'ytp_modern_jsm_' . uniqid(); // jsm for JS Mover
$player_div_id = 'youtube_player_' . $instance_id;
$playlist_ul_id = 'video_playlist_' . $instance_id;
$style_block_id = 'custom_yt_styles_' . $instance_id; // Unique ID for the style block

// --- 4. HTML Structure with INLINE (but hidden) STYLES ---
?>
<!-- Style block initially in body, to be moved by JS -->
<style type="text/css" id="<?php echo esc_attr($style_block_id); ?>" data-is-custom-yt-style="true">
    /* Make it initially hidden to prevent Flash of Unstyled Content (FOUC) */
    /* This is a fallback. Ideally, JS moves it fast enough. */
    .custom-youtube-player-instance[data-styles-pending="<?php echo esc_attr($style_block_id); ?>"] {
        /* visibility: hidden; */ /* This might be better than display:none to reserve space */
    }

    :root {
        --yt-player-primary-text: #ffffff;
        --yt-player-secondary-text: #b3b3b3;
        /* ... rest of :root variables ... */
        --yt-player-background: #121212;
        --yt-player-item-background: #181818;
        --yt-player-item-hover-background: #282828;
        --yt-player-item-active-background: #2a2a2a;
        --yt-player-accent-color: #1DB954;
        --yt-player-border-color: #282828;
        --yt-player-font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    }
    .custom-youtube-player-instance { 
        font-family: var(--yt-player-font-family);
        background-color: var(--yt-player-background);
        color: var(--yt-player-primary-text);
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
        max-width: 700px; 
        margin: 20px auto;
    }
    
    .custom-yt-player-container { width: 100%; background-color: #000; position: relative; }
    .custom-yt-player-container .youtube-iframe-container { width: 100%; aspect-ratio: 16 / 9; overflow: hidden; }
    .custom-yt-player-container .youtube-iframe-container iframe { border: none; }
    .custom-yt-playlist { list-style: none; padding: 8px; margin: 0; max-height: 300px; overflow-y: auto; background-color: var(--yt-player-background); }
    .custom-yt-playlist::-webkit-scrollbar { width: 8px; }
    .custom-yt-playlist::-webkit-scrollbar-thumb { background-color: var(--yt-player-secondary-text); border-radius: 4px; }
    .custom-yt-playlist::-webkit-scrollbar-track { background-color: var(--yt-player-item-background); }
    .custom-yt-playlist li { display: flex; align-items: center; padding: 10px 12px; cursor: pointer; border-radius: 4px; margin-bottom: 1px; transition: background-color 0.15s ease-in-out; }
    .custom-yt-playlist li:last-child { margin-bottom: 0; }
    .custom-yt-playlist li:hover { background-color: var(--yt-player-item-hover-background); }
    .custom-yt-playlist li.active { background-color: var(--yt-player-item-active-background); }
    .custom-yt-playlist li.active .video-title-container .video-title { color: var(--yt-player-accent-color); }
    .custom-yt-playlist li.active .play-indicator-area .playing-icon { display: block; }
    .custom-yt-playlist li.active .play-indicator-area .play-icon { display: none; }
    .custom-yt-playlist li .play-indicator-area { flex-shrink: 0; width: 24px; height: 24px; margin-right: 12px; display: flex; align-items: center; justify-content: center; }
    .custom-yt-playlist li .play-indicator-area svg { width: 18px; height: 18px; fill: var(--yt-player-secondary-text); }
    .custom-yt-playlist li:hover .play-indicator-area .play-icon,
    .custom-yt-playlist li.active .play-indicator-area .playing-icon { fill: var(--yt-player-primary-text); }
    .custom-yt-playlist li.active .play-indicator-area .playing-icon { fill: var(--yt-player-accent-color); }
    .custom-yt-playlist li .play-indicator-area .playing-icon { display: none; }
    .custom-yt-playlist li .video-title-container { flex-grow: 1; overflow: hidden; white-space: nowrap; }
    .custom-yt-playlist li .video-title-container .video-title { display: block; font-size: 14px; font-weight: 400; color: var(--yt-player-primary-text); text-overflow: ellipsis; overflow: hidden; white-space: nowrap; line-height: 1.4; }
    .custom-yt-playlist li:hover .video-title-container .video-title { color: var(--yt-player-primary-text); }
    .yt-player-error-message { color: var(--yt-player-secondary-text); background-color: var(--yt-player-item-hover-background); padding: 20px; text-align: center; border-radius: 0 0 8px 8px; }

</style>

<div class="custom-youtube-player-instance" id="wrapper_<?php echo esc_attr($instance_id); ?>" data-styles-pending="<?php echo esc_attr($style_block_id); ?>">
    <div class="custom-yt-player-container">
        <div id="<?php echo esc_attr($player_div_id); ?>" class="youtube-iframe-container"></div>
    </div>

    <?php if (isset($playlistItemsData['error'])): ?>
        <p class="yt-player-error-message"><?php echo esc_html($playlistItemsData['error']); ?></p>
    <?php elseif (!empty($playlistItemsData)): ?>
        <ul id="<?php echo esc_attr($playlist_ul_id); ?>" class="custom-yt-playlist"></ul>
    <?php else: ?>
        <p class="yt-player-error-message">No videos found or error fetching.</p>
    <?php endif; ?>
</div>

<?php // --- 5. JavaScript Logic (with style mover) --- ?>
<script>
(function() {
    const styleBlockId = '<?php echo esc_js($style_block_id); ?>';
    const playerWrapperId = 'wrapper_<?php echo esc_js($instance_id); ?>';

    // --- Function to move styles to head ---
    // This should run as early as possible.
    // We ensure it runs only once for all style blocks with this data attribute.
    if (!window.customYTStylesMoved) {
        window.customYTStylesMoved = true; // Flag to ensure this logic runs once per page load
        document.addEventListener('DOMContentLoaded', function() {
            const styleBlocks = document.querySelectorAll('style[data-is-custom-yt-style="true"]');
            const head = document.head || document.getElementsByTagName('head')[0];
            if (head) {
                styleBlocks.forEach(function(styleBlock) {
                    // Check if a style block with this ID is already in head (e.g., from another instance)
                    if (!head.querySelector('#' + styleBlock.id)) {
                         head.appendChild(styleBlock.cloneNode(true)); // Clone and append to head
                         console.log('[WPCode YouTube Player JS] Moved style block #' + styleBlock.id + ' to <head>.');
                    }
                    // Optional: remove original from body to avoid duplicate IDs if not cloning, but cloning is safer.
                    // If not cloning, and moving directly:
                    head.appendChild(styleBlock);
                });
            }
            // Remove the data-styles-pending attribute to make player visible if it was hidden by CSS
            const playerInstances = document.querySelectorAll('.custom-youtube-player-instance[data-styles-pending]');
            playerInstances.forEach(function(inst) {
                inst.removeAttribute('data-styles-pending');
            });
        });
    }


    // --- Standard Player JS ---
    const currentPlayerDivId = '<?php echo esc_js($player_div_id); ?>';
    const currentPlaylistUlId = '<?php echo esc_js($playlist_ul_id); ?>';
    const actualYouTubePlaylistID = '<?php echo esc_js($youtubePlaylistID); ?>';
    const fetchedPlaylistItems = <?php echo (isset($playlistItemsData['error']) || empty($playlistItemsData)) ? '[]' : json_encode($playlistItemsData); ?>;
    const instanceUniqueId = '<?php echo esc_js($instance_id); ?>';

    console.log('[WPCode YouTube Player JS] Script for instance ' + instanceUniqueId + ' is EXECUTING.');
    console.log('[WPCode YouTube Player JS] Fetched ' + fetchedPlaylistItems.length + ' items for ' + instanceUniqueId, fetchedPlaylistItems);


    let ytPlayerInstance_<?php echo esc_js($instance_id); ?>;

    function initializeYouTubePlayer_<?php echo esc_js($instance_id); ?>() {
        
        console.log('[WPCode YouTube Player JS] Instance ' + instanceUniqueId + ' - initializeYouTubePlayer CALLED.');
        if (!actualYouTubePlaylistID) { /* ... */ return; }
        ytPlayerInstance_<?php echo esc_js($instance_id); ?> = new YT.Player(currentPlayerDivId, { 
            playerVars: {
                'listType': 'playlist', 'list': actualYouTubePlaylistID, 'playsinline': 1, 'autoplay': 0, 
                'controls': 1, 'rel': 0, 'showinfo': 0, 'modestbranding': 1
            },
            events: {
                'onReady': function(event) { 
                    console.log('[WPCode YouTube Player JS] Instance ' + instanceUniqueId + ' - onPlayerReady EVENT.');
                    onPlayerReady_<?php echo esc_js($instance_id); ?>(event); 
                },
                'onStateChange': function(event) { onPlayerStateChange_<?php echo esc_js($instance_id); ?>(event); }
            }
        });
    }

    function onPlayerReady_<?php echo esc_js($instance_id); ?>(event) {
        
        if (fetchedPlaylistItems.length > 0) {
            populatePlaylistUI_<?php echo esc_js($instance_id); ?>();
            const initialIdx = event.target.getPlaylistIndex();
            highlightCurrentVideoInUI_<?php echo esc_js($instance_id); ?>(initialIdx >= 0 ? initialIdx : 0);
        }
    }

    function onPlayerStateChange_<?php echo esc_js($instance_id); ?>(event) {
        
        if (event.data === YT.PlayerState.PLAYING || event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.BUFFERING ) {
            const currentPlaylistIndex = event.target.getPlaylistIndex();
            highlightCurrentVideoInUI_<?php echo esc_js($instance_id); ?>(currentPlaylistIndex);
        }
    }

    function populatePlaylistUI_<?php echo esc_js($instance_id); ?>() {
        
        console.log('[WPCode YouTube Player JS] Instance ' + instanceUniqueId + ' - populatePlaylistUI CALLED.');
        const playlistElement = document.getElementById(currentPlaylistUlId);
        if (!playlistElement || fetchedPlaylistItems.length === 0) { /* ... */ return; }
        playlistElement.innerHTML = '';
        fetchedPlaylistItems.forEach((video) => {
            const listItem = document.createElement('li'); listItem.dataset.videoId = video.videoId; 
            listItem.dataset.playlistIndex = video.position; listItem.setAttribute('role', 'button'); 
            listItem.setAttribute('tabindex', '0'); listItem.setAttribute('title', video.title);
            const playIndicatorArea = document.createElement('div'); playIndicatorArea.classList.add('play-indicator-area');
            const playIconSVG = `<svg class="play-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M8 6.82v10.36c0 .79.87 1.27 1.54.84l8.14-5.18c.62-.39.62-1.29 0-1.69L9.54 5.98C8.87 5.55 8 6.03 8 6.82z"/></svg>`;
            const playingIconSVG = `<svg class="playing-icon" width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><rect class="eq-bar" x="4" y="8" width="3" height="10" rx="1.5"><animate attributeName="height" values="10;18;10" begin="0s" dur="0.8s" repeatCount="indefinite" /><animate attributeName="y" values="8;4;8" begin="0s" dur="0.8s" repeatCount="indefinite" /></rect><rect class="eq-bar" x="10.5" y="5" width="3" height="16" rx="1.5"><animate attributeName="height" values="16;8;16" begin="-0.2s" dur="0.8s" repeatCount="indefinite" /><animate attributeName="y" values="5;9;5" begin="-0.2s" dur="0.8s" repeatCount="indefinite" /></rect><rect class="eq-bar" x="17" y="10" width="3" height="8" rx="1.5"><animate attributeName="height" values="8;14;8" begin="-0.4s" dur="0.8s" repeatCount="indefinite" /><animate attributeName="y" values="10;6;10" begin="-0.4s" dur="0.8s" repeatCount="indefinite" /></rect></svg>`;
            playIndicatorArea.innerHTML = playIconSVG + playingIconSVG; listItem.appendChild(playIndicatorArea);
            const titleContainer = document.createElement('div'); titleContainer.classList.add('video-title-container');
            const titleSpan = document.createElement('span'); titleSpan.classList.add('video-title'); titleSpan.textContent = video.title;
            titleContainer.appendChild(titleSpan); listItem.appendChild(titleContainer);
            listItem.addEventListener('click', () => { ytPlayerInstance_<?php echo esc_js($instance_id); ?>.playVideoAt(parseInt(video.position)); });
            listItem.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); ytPlayerInstance_<?php echo esc_js($instance_id); ?>.playVideoAt(parseInt(video.position)); }});
            playlistElement.appendChild(listItem);
        });
    }

    function highlightCurrentVideoInUI_<?php echo esc_js($instance_id); ?>(playlistIndexToHighlight) {
        
        const playlistElement = document.getElementById(currentPlaylistUlId); if (!playlistElement) return;
        const items = playlistElement.getElementsByTagName('li');
        for (let i = 0; i < items.length; i++) {
            const playIcon = items[i].querySelector('.play-indicator-area .play-icon');
            const playingIcon = items[i].querySelector('.play-indicator-area .playing-icon');
            if (parseInt(items[i].dataset.playlistIndex) === playlistIndexToHighlight) {
                items[i].classList.add('active'); if (playIcon) playIcon.style.display = 'none'; if (playingIcon) playingIcon.style.display = 'block';
            } else {
                items[i].classList.remove('active'); if (playIcon) playIcon.style.display = 'block'; if (playingIcon) playingIcon.style.display = 'none';
            }
        }
    }

    // --- YouTube API Loading and Initialization (same robust loader) ---
    if (typeof window.onYouTubeIframeAPIReady === 'undefined') {
        window.onYouTubeIframeAPIReady = function() {
            if (window.ytPlayerInitializers && window.ytPlayerInitializers.length) {
                window.ytPlayerInitializers.forEach(initializer => initializer());
                window.ytPlayerInitializers = [];
            }
        };
        var tag = document.createElement('script'); tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        if (firstScriptTag) { firstScriptTag.parentNode.insertBefore(tag, firstScriptTag); } else { document.head.appendChild(tag); }
    }
    if (typeof window.ytPlayerInitializers === 'undefined') { window.ytPlayerInitializers = []; }
    if (typeof YT !== 'undefined' && YT.Player) {
        initializeYouTubePlayer_<?php echo esc_js($instance_id); ?>();
    } else {
        window.ytPlayerInitializers.push(initializeYouTubePlayer_<?php echo esc_js($instance_id); ?>);
    }

})();
</script>