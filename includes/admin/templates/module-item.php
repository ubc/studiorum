<?php

	/**
	 * Template for an individual module displayed on the Studiorum settings home page
	 *
	 * @since 0.1
	 *
	 * @var array $module 	- 	Details about this particular module
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
	 * @var array $allStudiorumPlugins - A list of all available (installed) studiorum plugins
	 * @var array $allActiveStudiorumPlugins - A list of all *active* studiorum plugins
	 * @var bool $active - If this plugin is active or not
	 * @var bool $new - If this plugin is new or not
	 * @var bool $comingSoon - If this plugin coming soon or not
	 * @var string $classes - Classes to add to this module
	 */

	$buttonText = 'Activate';

	if( $active ){
		$buttonText = 'Already Active';
	}

	if( $comingSoon ){
		$buttonText = 'Coming Soon';
	}

?>

	<li class="module <?php echo $classes; ?>" data-name="<?php echo $module['title']; ?>">
		
		<h3 class="icon <?php echo $module['icon']; ?>"><?php echo $module['title']; ?></h3>
		<p><?php echo $module['excerpt']; ?></p>

		<div class="active-inactive <?php if( $active ){ echo 'active'; } ?>">
			<p><?php echo $buttonText; ?></p>
		</div><!-- .active-inactive -->

	</li>