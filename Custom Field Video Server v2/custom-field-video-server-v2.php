<?php
/*
	Custom Field Video Server v2
	by Pangeran Wiguan
	https://pangeranwiguan.com
	Since 23rd December 2023 - 2025

	I use this for a website that show's TV Series.
    	
	Each post will be embeded with video embed code from different provider (server).
	The viewers can choose which server they want to use.

	Using custom field, we can embed the video direct embed (source) url, and let the code handle the embed code so even though they come from different provider, they appear the same in our website.

	To be used with WP Code Snippets plugin.
	https://wpcode.com/
	Free version will do.

	OR

	Inside custom plugin.

	OR

	Inside theme function.php files.

*/

// Get the custom field value from the post.
// The custom field value suppose to be the direct embed url to the video. For example, https://domain.com/video.mp4
global $wp_query;
$postid = $wp_query->post->ID;
$embed1 = get_post_meta($postid, 'Server1', true);
$embed2 = get_post_meta($postid, 'Server2', true);
$embed3 = get_post_meta($postid, 'Server3', true);
$embed4 = get_post_meta($postid, 'Server4', true);

wp_reset_query(); ?>

<script>
	theTabs('evt', 'tabName');

	function theTabs(evt, tabName) {

		// Declare all variables
		var i, tabcontent, tablinks;

		// Get all elements with class="tabcontent" and hide them
		tabcontent = document.getElementsByClassName("tabcontent");

		for (i = 0; i < tabcontent.length; i++) {
			tabcontent[i].style.display = "none";
		}

		// Get all elements with class="tablinks" and remove the class "active"
		tablinks = document.getElementsByClassName("tablinks");

		for (i = 0; i < tablinks.length; i++) {
			tablinks[i].className = tablinks[i].className.replace(" active", "");
		}


		// Show the current tab, and add an "active" class to the link that opened the tab
		document.getElementById(tabName).style.display = "block";

		evt.currentTarget.className += " active";

	}
</script>

<style>
	.mainTabs{
  		width:100%;  margin:5% auto;
		padding:10px;
		border-radius:10px;
		box-shadow: 2px 4px 8px 10px #00000020;
	}
	.tab {
		overflow: hidden;
		background-color: #00000020;
		border-radius:10px;
		display:flex;
		justify-content:center;
	}

	/* Style the buttons inside the tab */
	.tab button {
		width:200px;
		background-color: #dddddd00;
		border-radius: 10px;
		float: left;
		border: none;
		outline: none;
		cursor: pointer;
		color:#fff;
		padding: 10px 22px;
		transition: 0.3s;
		font-size: 17px;
		margin:5px 5px 5px 5px;

	}

	/* Change background color of buttons on hover */
	.tab button:hover {
		color:#fff;
		border-radius: 10px;
		margin-left:5px;
		margin-right:5px;
	}

	/* Create an active/current tablink class */
	.tab button.active {
		background-color: #4158D0;    
		color:#ffffff;
		border-radius: 10px;
		font-weight:700;
	}

	/* Style the tab content */
	.tabcontent {
		display: none;
		color:#273342;
		padding: 6px 12px;
		border: 1px solid #dddddd40;
		margin-top:10px;
		background: transparent;
		border-radius:10px;
	}

	@media (max-width: 600px) {
		.tab {
			flex-wrap: wrap;
		}

		.tablinks {
			flex: 100%;
		}
	}
</style>


<div class="mainTabs">
	<div class="tab">
		<?php
		if (!empty($embed1)) { ?>

			<button class="tablinks active" onclick="theTabs(event, 'tab1')">Server 1</button>

		<?php
		} else {
			//Do nothing.
		}

		if (!empty($embed2)) { ?>

			<button class="tablinks" onclick="theTabs(event, 'tab2')">Server 2</button>

		<?php
		} else {
			//Do nothing.
		}

		
		if (!empty($embed3)) { ?>

			<button class="tablinks" onclick="theTabs(event, 'tab3')">Server 3</button>

		<?php
		} else {
			//Do nothing.
		}

		
		if (!empty($embed4)) { ?>

			<button class="tablinks" onclick="theTabs(event, 'tab4')">Server 4</button>

		<?php
		} else {
			//Do nothing.
		}
		?>
		
	</div>

	
	<?php
	//Check if there's direct embed url or not.
	if (!empty($embed1)) { ?>

		<div id="tab1" class="tabcontent" style="display: block;">

			<div style="position:relative;padding-bottom:56%;padding-top:20px;height:0;">
				<iframe src="<?php echo $embed1; ?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>
			</div>

		</div>
			
		<?php
		} else {
			//Do nothing.
		}
	
	if (!empty($embed2)) { ?>

		<div id="tab2" class="tabcontent">

			<div style="position:relative;padding-bottom:56%;padding-top:20px;height:0;">
				<iframe src="<?php echo $embed2; ?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>
			</div>

		</div>
			
		<?php
		} else {
			//Do nothing.
		}
	
	if (!empty($embed3)) { ?>

		<div id="tab3" class="tabcontent">

			<div style="position:relative;padding-bottom:56%;padding-top:20px;height:0;">
				<iframe src="<?php echo $embed3; ?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>
			</div>

		</div>
			
		<?php
		} else {
			//Do nothing.
		}

	if (!empty($embed4)) { ?>

		<div id="tab4" class="tabcontent">

			<div style="position:relative;padding-bottom:56%;padding-top:20px;height:0;">
				<iframe src="<?php echo $embed4; ?>" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe>
			</div>

		</div>
			
		<?php
		} else {
			//Do nothing.
		}
		?>
		
</div>