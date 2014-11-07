<?php

	/**
	 * Template for the Studiorum settings home page. Loads the 3 main sections - the intro and then the
	 * groups and individual modules templates.
	 *
	 * @since 0.1
	 *
	 * @var array $modules 						- Metadata about each Studiorum module 
	 * @var array $allStudiorumPlugins 			- All available Studiorum plugins
	 * @var array $allActiveStudiorumPlugins	- All *active* studiorum plugins
	 * @var array $pluginGroups					- Metadata about our plugin groups
	 */

?>

	<div id="studiorum_home_wrap">

		<div class="studiorum_home_header">

			<div class="studiorum_home_header_intro">
				
				<h1><?php _e( 'Create powerful self-hosted higher education websites simply by selecting the features you want.', 'studiorum' ); ?></h1>

			</div><!-- .studiorum_home_header_intro -->

		</div><!-- .studiorum_home_header -->


		<div class="studiorum_home_inner">

			<div class="home_section_wrap highlight">

				<div class="home_section" id="studiorum_modules_groups">
					
					<h2><?php _e( 'Module Groups', 'studiorum' ); ?></h2>

					<ul class="studiorum_groups">
						
					<?php foreach( $pluginGroups as $groupID => $groupDetails ) : ?>
						<?php $nonce = wp_create_nonce( 'studiorum_group_action_nonce' ); ?>
						<?php require( Studiorum_Utils::locateTemplateInPlugin( STUDIORUM_PLUGIN_DIR, 'includes/admin/templates/group-item.php' ) ); ?>
					<?php endforeach; ?>

					</ul>

				</div><!-- #studiorum_modules_groups -->

			</div><!-- .home_section_wrap -->

			<div class="home_section_wrap">

				<div class="home_section" id="studiorum_modules_full_list">
					
					<h2><?php _e( 'Available Modules', 'studiorum' ); ?></h2>

					<ul class="studiorum_modules">
					<?php foreach( $modules as $key => $module ) : ?>

						<?php
							$slug 		= ( isset( $module['plugin_slug'] ) ) ? $module['plugin_slug'] : false;
							$active 	= ( Studiorum_Utils::isStudiorumPluginActive( $slug, $allActiveStudiorumPlugins ) ) ? true : false;
							$new 		= ( isset( $module['date'] ) && !isset( $module['coming_soon'] ) && Studiorum_Utils::isPluginNew( $module['date'] ) ) ? true : false;
							$comingSoon = ( isset( $module['coming_soon'] ) ) ? true : false;
							$classes 	= '';
							if( $new ){ $classes .= ' new'; }
							if( $comingSoon ){ $classes .= ' comingsoon'; }
						?>

						<?php require( Studiorum_Utils::locateTemplateInPlugin( STUDIORUM_PLUGIN_DIR, 'includes/admin/templates/module-item.php' ) ) ?>

					<?php endforeach; ?>
					</ul>

				</div><!-- #studiorum_modules_full_list -->

			</div><!-- .home_section_wrap -->

		</div><!-- .studiorum_home_inner -->

	</div><!-- #studiorum_home_wrap -->