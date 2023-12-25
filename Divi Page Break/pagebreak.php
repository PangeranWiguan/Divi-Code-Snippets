<?php
/*
	Divi Page Break
	by Pangeran Wiguan
	https://pangeranwiguan.com
	Since 25rd December 2023

	By default, Divi break the WordPress page break functionality (<!--nextpage-->) in Divi Theme Builder.
	This code snippet is to atleast solve the problem.
	
	It shows the next page link pagination so the readers can click and read the next splitted post.


	To be used with WP Code Snippets plugin.
	https://wpcode.com/
	Free version will do.

	OR

	Inside custom plugin.

	OR

	Inside theme function.php files.

*/
?>
<style>
.divi-post-pagination {
  padding: 8px;
  margin:20px 0px 20px 0px;
}
.divi-page-links {
  padding: 8px;
  text-align: center;
}
.divi-page-links .page-text {
  font-weight: bold;
  font-size: 1.5em;
  color: #fff;
  margin-right: 10px;
  -webkit-text-stroke: 1px black;
}
.divi-page-links .post-page-numbers,
.divi-page-links .post-page-numbers a
{
  background-color: #7f8fa6;
  padding: 9px 15px 9px 15px;
  color: #000;
  text-decoration: none;
  border-radius: 8px;
  margin-right: 10px;
  font-size: 1.3em;
}
.divi-page-links .post-page-numbers:hover,
.divi-page-links .post-page-numbers a:hover
{
  background: #dcdde1;
}

.divi-page-links .post-page-numbers:active,
.divi-page-links .post-page-numbers a:active
{
		background-color: #f5f6fa;
		box-shadow: 0 5px #666;
		transform: translateY(4px);
    border: solid 1px;
    border-color: #dcdde1;
}
.divi-page-links .current {
    background-color: #dcdde1;
    cursor: not-allowed;
}
</style>
<?php
/*
	Determines whether or not the current post is a paginated post.
	This function should only run once, or exist once in the page.
	Do not add more than one code module consisting this same function.

	Change the function name 'divi_is_paginated_post' to 'divi_is_paginated_post2'
	if you want to reusethe code so that it can appear at different position without
	triggring php fatal error.

	@return   boolean    True if the post is paginated; false, otherwise.
	@package  includes
	@since    1.0.0
 */
function divi_is_paginated_post() {

	global $multipage;
	return 0 !== $multipage;

} // end divi_is_paginated_post
?>

<?php
/* 
	If the current page is a paginated post, means using <!--nextpage-->,
	then show the paginated navigation so that the reader can see the next page.
*/
if ( divi_is_paginated_post() ) { ?>

	<div class="divi-post-pagination">
		<?php wp_link_pages('before=<div class="divi-page-links"><span class="page-text">Page: </span>&after=</div>&link_before=&link_after='); ?>
	</div><!-- .post-pagination -->

<?php } else {

	echo "<!-- This Page is not Paginated -->";

} // End if ?>