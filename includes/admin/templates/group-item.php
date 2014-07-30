<?php

	/**
	 * Template for an individual plugin group item shown on the Studiorum admin home page
	 *
	 * @since 0.1
	 *
	 * @var array $groupDetails - 	Details about this particular module
	 * 							'id' 				: Unique identifier
	 * 							'plugin_slug' 		: The full slug of the plugin
	 *							'title' 			: The title of this module
	 *							'icon' 				: The icon to show in the main list
	 *							'excerpt' 			: A brief description of this module
	 *							'image' 			: A 310x162px image of this module
	 *							'link' 				: A URL to further information about this module
	 *							'content' 			: A fuller description of this module shown in an overlay
	 *							'content_sidebar'	: In the content overlay this is shown alongside the content
	 *							'date'				: The date the module was added
	 *							'comingsoon'		: If this plugin is coming soon
	 *							'plugins'			: An array of plugin slugs (path and file) which make up this group
	 *							'examples'			: An array of urls used to give examples of where this group is being used
	 * @var string $groupID - The ID/slug of this group
	 * @var array $allActiveStudiorumPlugins - A list of all *active* studiorum plugins
	 */



?>

	<li class="plugin-group" data-name="<?php echo $groupDetails['title']; ?>">
		
		<div class="group-image">
			<img src="<?php echo $groupDetails['image']; ?>" alt="<?php echo $groupDetails['title']; ?>" />
		</div><!-- .group-image -->

		<div class="group-details">
			<h3 class="icon <?php echo $groupDetails['icon']; ?>"><?php echo $groupDetails['title']; ?></h3>
			<p><?php echo $groupDetails['excerpt']; ?></p>
		</div><!-- .group-details -->

	</li>