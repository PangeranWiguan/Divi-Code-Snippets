<?php
/*
	Show Custom Post Type.
	by Pangeran Wiguan
	https://pangeranwiguan.com
	Since 11th May 2025

	Show Custom Post Type for Divi.
	Minimal.
	No Excerpt.
	HTML5 compliance.

	To be used with WP Code Snippets plugin.
	https://wpcode.com/
	Free version will do.

	OR

	Inside custom plugin.

	OR

	Inside theme function.php files.

    This version uses JavaScript to inject styles into the <head>.
*/

// --- JavaScript Style Injection ---

// We'll use a PHP static variable to ensure the <script> tag itself
// is only output once by PHP, even if the shortcode is on the page multiple times.
// The JavaScript then has its own check using the style tag's ID.
static $pangeran_js_style_script_outputted = false;

if ( ! $pangeran_js_style_script_outputted ) {
    // Define your CSS rules here as a PHP string.
    // Using NOWDOC for multiline string to avoid escaping issues with CSS.
    $pangeran_css_rules = <<<'CSS'
/* Pangeran Custom Post Styles - Injected by JS */
.latest-blog-module {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap:20px;
}
.latest-blog-post {
    padding: 15px;
}
.latest-blog-post h2 {
    display: none;
}
.latest-blog-post p {
    font-family: 'Poppins',Helvetica,Arial,Lucida,sans-serif!important;
    color: #ffffff; /* Ensure parent container has a dark background for this to be visible */
    font-weight: 100;
    font-size: 12px!important;
    line-height: 1.7em!important;
}
.latest-blog-post .episode-title a {
    color: #ffffff; /* Ensure parent container has a dark background */
    font-weight: 600;
    font-size: 14px!important;
    text-decoration: none;
}
.latest-blog-post img {
    width: 100%;
    height: auto;
    border-radius: 15px;
    box-shadow: 6px 6px 18px 0px rgba(0,0,0,0.3);
    transition: color 300ms ease, background-color 300ms ease;
    cursor: pointer;
}
CSS;

    // Escape the CSS for safe inclusion in a JavaScript string literal
    $escaped_pangeran_css_rules = esc_js($pangeran_css_rules);
?>
<script type="text/javascript" id="pangeran-custom-post-style-injector-script">
    (function() { // IIFE to avoid polluting global scope
        const pangeranStyles = `<?php echo $escaped_pangeran_css_rules; ?>`;
        const pangeranStyleTagId = 'pangeran-custom-post-styles'; // Unique ID for your style tag

        // Check if the style tag already exists
        if (!document.getElementById(pangeranStyleTagId)) {
            const styleTag = document.createElement('style');
            styleTag.type = 'text/css';
            styleTag.id = pangeranStyleTagId;
            styleTag.appendChild(document.createTextNode(pangeranStyles));
            
            // Append to document.head
            // We need to ensure head is available. DOMContentLoaded is a good event for this.
            if (document.head) {
                document.head.appendChild(styleTag);
            } else {
                // Fallback if document.head is not immediately available (less common for inline scripts)
                document.addEventListener('DOMContentLoaded', function() {
                    if (document.head && !document.getElementById(pangeranStyleTagId)) { // Re-check in case another script added it
                         document.head.appendChild(styleTag);
                    }
                });
            }
        }
    })();
</script>
<?php
    $pangeran_js_style_script_outputted = true;
}
// --- End of JavaScript Style Injection ---


// --- Your Original WordPress Loop (unchanged) ---
// Show recently modified posts
$recently_updated_posts = new WP_Query( array(
	'post_type'		=> 'release',
	'posts_per_page'	=> 5, //Not sure this is a good idea but I sometimes bulk post into 1 category over 50 posts. So this is my need.
	'orderby'        	=> 'modified',
	'order'			=> 'DESC',
	'no_found_rows'		=> true, // speed up query when we don't need pagination.
) );
?>

<div class="latest-blog-container">
	<div class="latest-blog-module">
    <?php
    if ( $recently_updated_posts->have_posts() ) :
        while( $recently_updated_posts->have_posts() ) : $recently_updated_posts->the_post();
            $category_list = get_the_category();
            $now_cat = '';
            if ( ! empty( $category_list ) ) {
                $now_cat = esc_html( $category_list[0]->name );
            }
            ?>
            <div class="latest-blog-post">
                <a href="<?php the_permalink(); ?>"><img src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'medium'); ?>" alt="<?php the_title_attribute(); ?>" /></a>
                <p class="episode-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                <?php if ( $now_cat ) : ?>
                    <p class="release-date"><?php echo $now_cat; ?></p>
                <?php endif; ?>
                <p class="release-date">Original release date: <?php the_date(); ?></p>
            </div>
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>
	</div>
</div>