<?php
/*
	Show Category In Tag Page (No Repeat)
	by Pangeran Wiguan
	https://pangeranwiguan.com
	Since 23rd December 2023

	I use this for a website that show's TV Series.
    	Each episode will be posted inside a category with the TV Series name.
    	
	Each episode also is "categories" using tags, such as year of release, genre, etc.

	Normal WordPress behaviour will show all of the post tagged with the tag when viewing the tag page.
	Because I use some of the tag as navigation menu, I don't want WordPress to show all posts, but instead show only 1 posts from the same categories (The TV series) so it appear that the TV series post won't appear repeatedly, and only shown once.

	When visitors click it, it will bring them to the Category page (TV Series page) where it will lists all the individual posts (Episodes posts).

	To be used with WP Code Snippets plugin.
	https://wpcode.com/
	Free version will do.

	OR

	Inside custom plugin.

	OR

	Inside theme function.php files.

*/

// Get The Tag
$get_the_tag = get_queried_object();
$the_tag = $get_the_tag->slug;

// Show recently modified posts
$recently_updated_posts = new WP_Query( array(
	'tag'			=> $the_tag, //Filter out that the query only shows posts from the current tag page.
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


	.tag-latest-blog-module {
		display: grid;
  		grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
   		gap:20px;
	}

	.tag-latest-blog-post {
		border-radius: 5px;
		padding: 15px;
    		background: rgba(0,0,0,0.5);
	}
	/* End of CSS Grid */

	/* Fonts */
	.tag-latest-blog-post h2 {
		font-family: 'Poppins',Helvetica,Arial,Lucida,sans-serif!important;
		color: #ffffff;
  		font-weight: 600;
		font-size: 14px!important;
  		text-decoration: none;
	}
	.tag-latest-blog-post p {
		font-family: 'Poppins',Helvetica,Arial,Lucida,sans-serif!important;
		color: #ffffff;
    		font-weight: 100;
    		font-size: 12px!important;
    		line-height: 1.7em!important;
	}

	.tag-latest-blog-post .tag-episode-title a {
  		display:none;
	}

	/* Image */
	.tag-latest-blog-post img {
		width: 100%;
		height: auto;
	}
</style>

<div class="tag-latest-blog-container">
	<div class="tag-latest-blog-module">

<?php 
if ( $recently_updated_posts->have_posts() ) :
	
	while( $recently_updated_posts->have_posts() ) : $recently_updated_posts->the_post(); ?>
		
		<?php
		$category = get_the_category();
		$now_cat = esc_html( $category[0]->name );
		
		if ( !in_array($now_cat, $the_cat) and $counter < 12 ) {
		?>
			
			<div class="tag-latest-blog-post">
				

			<?php
			if ( ! empty( $category ) ) {
				
				echo '<a href="' . esc_url( get_category_link( $category[0]->term_id ) ) . '"><img src="' . get_the_post_thumbnail_url(get_the_ID(),'medium') . '/></a>';

				echo '<h2><a href="' . esc_url( get_category_link( $category[0]->term_id ) ) . '">' . esc_html( $category[0]->name ) . '</a></h2>';
							
				$the_cat[] = $now_cat;
			}
			?>
									
			<p class="tag-episode-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
			<p class="tag-release-date">Original release date: <?php the_date(); ?></p>
			<p class="tag-post-date">Posted on: <?php the_modified_date(); ?> at <?php the_modified_time(); ?></p>
				
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