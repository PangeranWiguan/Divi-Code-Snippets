<?php
/*
	Show Category With New Post (No Repeat)
	by Pangeran Wiguan
	https://pangeranwiguan.com
	Since 22nd July 2023 - 2025

	I use this for a website that show's TV Series.
    	Each episode will be posted inside a category with the TV Series name.
    	I want to pull the category which have latest new episode, and do not want to repeat showing the same category if it have more than 1 new episode.

	To be used with WP Code Snippets plugin.
	https://wpcode.com/
	Free version will do.

	OR

	Inside custom plugin.

	OR

	Inside theme function.php files.

*/

// Show recently modified posts
$recently_updated_posts = new WP_Query( array(
	'post_type'		=> 'post',
	'posts_per_page'	=> 100, //Not sure this is a good idea but I sometimes bulk post into 1 category over 50 posts. So this is my need.
	'orderby'        	=> 'modified',
	'order'			=> 'DESC',
	'no_found_rows'		=> true, // speed up query when we don't need pagination.
) );
	
	
$counter = 0;
$now_cat = "Now";
//$prev_cat = "Prev";
$the_cat = array();

?>

<style>
	/*
		The CSS Grid 
		This style shows 5 columns and responsive in mobile.
	*/
	* {
		box-sizing: border-box;
		margin: 0;
		padding: 0;
		font-family: "Poppins";
	}

	.latest-blog-container {
		margin: 0 auto;
	}

	.latest-blog-module {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
		gap: 20px;
	}

	.latest-blog-post {
		height: 20vh;
		font-size: 10vh;
		line-height: 20vh;
		text-align: center;
		border-radius: 5px;
		color: #fff;
		padding: 15px;
		-webkit-background-size: cover!important;
		-moz-background-size: cover!important;
		-o-background-size: cover!important;
		background-size: cover!important;
		background-size:100%!important;
		-webkit-transition: all 1s ease-in-out!important;
		-moz-transition: all 1s ease-in-out!important;
		-o-transition:all 1s ease-in-out!important;
		transition: all 1s ease-in-out!important;
	}
	/* End of CSS Grid */

	/* Fonts */
	.latest-blog-post-content h2 {
		font-family: 'Poppins',Helvetica,Arial,Lucida,sans-serif!important;
    		font-weight: 600!important;
		color: #ffffff;
    		font-size: 20px!important;
    		line-height: 1.1em!important;
    		text-align: center!important;
	}
	.latest-blog-post-content p, .latest-blog-post-content .custom-btn span {
		font-family: 'Poppins',Helvetica,Arial,Lucida,sans-serif!important;
    		font-weight: 500!important;
		text-align: center;
		color: #ffffff;
    		font-size: 14px!important;
    		line-height: 1.1em!important;
    		text-align: center!important;
	}

	/* Background Image Animation Effect */
	.latest-blog-post:hover {
		background-size:150%!important;
	}
</style>

<div class="latest-blog-container">
	<div class="latest-blog-module">

<?php 
if ( $recently_updated_posts->have_posts() ) :
	
	while( $recently_updated_posts->have_posts() ) : $recently_updated_posts->the_post(); ?>
		
		<?php
		$category = get_the_category();
		$now_cat = esc_html( $category[0]->name );
		
		if ( !in_array($now_cat, $the_cat) and $counter < 10 ) {
		?>
			
			<div class="latest-blog-post"
			style="
			background: linear-gradient(
				rgba(0, 0, 0, .6),
				rgba(0, 0, 0, .5)
				),
				url(<?php echo get_the_post_thumbnail_url(get_the_ID(),'medium'); ?>) 50% / cover no-repeat;
			height: 100%;
			padding: 0.5em;
			">
				<div class="latest-blog-post-content">

						<?php
						if ( ! empty( $category ) ) {
							
							echo '<h2><a href="' . esc_url( get_category_link( $category[0]->term_id ) ) . '">' . esc_html( $category[0]->name ) . '</a></h2>';
							
							$the_cat[] = $now_cat;
						}
						?>
									
									
						<p>What's new?</p>
						<p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
						<p>Original release date: <?php the_date(); ?></p>
						<p>Updated on: <?php the_modified_date(); ?> at <?php the_modified_time(); ?></p>
				</div>
			</div>
			
			<?php
			$counter++;
		}
		
		else {
			// Do Nothing.
		}
		?>
					
	<?php endwhile; ?>
		
	<?php wp_reset_postdata(); ?>
		
<?php endif; ?>

	</div>
</div>