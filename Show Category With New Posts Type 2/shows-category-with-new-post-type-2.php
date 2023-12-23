<?php
/*
	Show Category With New Post Type 2 (No Repeat)
	by Pangeran Wiguan
	https://pangeranwiguan.com
	Since 23rd December 2023

	I use this for a website that show's TV Series.
    	Each episode will be posted inside a category with the TV Series name.
    	I want to pull the category which have latest new episode, and do not want to repeat showing the same category if it have more than 1 new episode.

	TYPE 2
	Doesn't put the image in background, and fix grid to 4K resolutions, and responsive in mobile too.

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


	.latest-blog-module {
		display: grid;
  		grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
   		gap:20px;
	}

	.latest-blog-post {
		border-radius: 5px;
		padding: 15px;
    		background: rgba(0,0,0,0.5);
	}
	/* End of CSS Grid */

	/* Fonts */
	.latest-blog-post h2 {
		display: none;
	}
	.latest-blog-post p {
		font-family: 'Poppins',Helvetica,Arial,Lucida,sans-serif!important;
		color: #ffffff;
    		font-weight: 100;
    		font-size: 12px!important;
    		line-height: 1.7em!important;
	}

	.latest-blog-post .episode-title a {
  		color: #ffffff;
  		font-weight: 600;
		font-size: 14px!important;
  		text-decoration: none;
	}

	/* Image */
	.latest-blog-post img {
		width: 100%;
		height: auto;
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
		
		if ( !in_array($now_cat, $the_cat) and $counter < 12 ) {
		?>
			
			<div class="latest-blog-post">
			<a href="<?php the_permalink(); ?>"><image src="<?php echo get_the_post_thumbnail_url(get_the_ID(),'medium'); ?>" /></a>
				

			<?php
			if ( ! empty( $category ) ) {
							
				echo '<h2><a href="' . esc_url( get_category_link( $category[0]->term_id ) ) . '">' . esc_html( $category[0]->name ) . '</a></h2>';
							
				$the_cat[] = $now_cat;
			}
			?>
									
			<p class="episode-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
			<p class="release-date">Original release date: <?php the_date(); ?></p>
			<p class="post-date">Posted on: <?php the_modified_date(); ?> at <?php the_modified_time(); ?></p>
				
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