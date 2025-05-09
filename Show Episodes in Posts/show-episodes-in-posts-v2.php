<?php
/*
	Show Episodes in Posts
	by Pangeran Wiguan
	https://pangeranwiguan.com
	Since 23rd December 2023 - 2025

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
?>

<!-- Debug Note: The Code snippet is loaded. -->
<!-- Style -->
<style>
	/* The Container */
	#ep-btn-container {
		height: 100%;
		width: 100%;
	}

	.ep-list-item {
		float: left;
		margin-right: 5%;
		margin-bottom: 25%;
	}
	.ep-list-item .the-button {
		padding: 5px 20px;
		background-size: cover;
		background-position: center;
		font-weight: bold;
		font-size: 2vw;
		text-decoration: none;
		color: #ffffff;
		cursor: pointer;
		border: none;
		border-radius: 8px;
		box-shadow: 0 9px #999;
		-webkit-text-stroke: 3px black;
	}

	.ep-list-item .the-button:hover {
		backround-color: #000000;
	}

	.ep-list-item .the-button:active {
		background-color: #3e8e41;
		box-shadow: 0 5px #666;
		transform: translateY(4px);
	}
</style>

<div id="ep-btn-container">
	<?php
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

			$ep_counter = 1;

			while ( $query_res->have_posts() ) {

				$query_res->the_post();

				?>

				<div class="ep-list-item">
					
					<a class="the-button" href="<?php the_permalink(); ?>" style="background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(),'medium'); ?>)"><?php echo $ep_counter; ?></a>
				</div>

				<?php

				$ep_counter++;
		
			}

		} else {
			
			// Do nothing.
		
		}
		
		wp_reset_postdata();

	}
	?>
</div>