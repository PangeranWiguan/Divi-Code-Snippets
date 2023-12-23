<?php
/*
	Show Episodes in Posts
	by Pangeran Wiguan
	https://pangeranwiguan.com
	Since 23rd December 2023

	I use this for a website that show's TV Series.
    	
	This code snippets lists all the posts within the same category of current posts, thus the "episodes".

	To be used with WP Code Snippets plugin.
	https://wpcode.com/
	Free version will do.

	OR

	Inside custom plugin.

	OR

	Inside theme function.php files.

*/

// Show that this snippet is working.
echo "<h3>Watch Other Episodes</h3>";
echo "<ol class=\"episode-list\">";

while ( have_posts() ) {
	
	the_post();

	// Show posts of current post categories
    $post_id = get_the_ID();
    $post_categories = wp_get_post_categories( $post_id );

    $query_args = array(
    	'post_type' => 'post',
        'post_status' => 'publish',
        'category__in' => $post_categories,
		'orderby' => 'date',
		'order' => 'ASC',
	);

	$query_res = new WP_Query($query_args);

    if ( $query_res->have_posts() ) {

    	while ( $query_res->have_posts() ) {

			$query_res->the_post();
			the_title(sprintf( '<li class="episode-list-item"><a href="%s" target="_blank">', esc_attr( esc_url( get_permalink() ) ) ),
  '</a></li>');
            
		}

	} else {
		
		echo '<li>There are no other episode in this series. This might be a a movie, not a series!</li>';
	
	}
	
	wp_reset_postdata();

}

echo "</ol>";