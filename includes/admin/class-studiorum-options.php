<?php
	
	/**
	 * Class to manage our options
	 *
	 * @package     Studiorum
	 * @subpackage  Admin/Options
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       0.1.0
	 */

	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ){
		exit;
	}


	if( !class_exists( 'Studiorum_Options' ) && class_exists( 'AdminPageFramework' ) ) :

		class Studiorum_Options extends AdminPageFramework
		{

			/**
			 * Create our settings pages and menu items
			 *
			 * @since 0.1
			 *
			 * @param null
			 * @return null
			 */

			function setUp()
			{

				// Run an action so we can hook in here to adjust filters
				do_action( 'studiorum_settings_setup_start' );

				// Filter to determine where the main settings tabs are
				$rootMenuPage 		= $this->getRootMenuPage();

				// Filter to determine the settings page slug
				$settingsPageSlug 	= $this->getSettingsPageSlug();

				// Filter for the sub menu items
				$subMenuItems 		= $this->getSubMenuItems();



				// Let's make sure we have some options to output
				if( !$rootMenuPage ){
					return false;
				}

				if( !$subMenuItems || !is_array( $subMenuItems ) || empty( $subMenuItems ) ){
					return false;
				}

				// OK, we have some, create the page
				$this->setRootMenuPage( $rootMenuPage );


				// OK we definitely have sub menu items, let's add it/them
				foreach( $subMenuItems as $key => $subMenuItem ){
					$this->addSubMenuItem( $subMenuItem );
				}

				// Grab our settings tabs
				$studiorumSettingsTabs = $this->getSettingsTabs();

				

				if( !$studiorumSettingsTabs || !is_array( $studiorumSettingsTabs ) || empty( $studiorumSettingsTabs ) ){
					return false;
				}

				foreach( $studiorumSettingsTabs as $key => $inPageTab ){
					$this->addInPageTabs( $settingsPageSlug, $inPageTab );
				}


				// Grab any help tabs we have defined
				$helpTabs = $this->getHelpTabs();
				
				if( $helpTabs && is_array( $helpTabs ) && !empty( $helpTabs ) )
				{
					foreach( $helpTabs as $key => $helpTab ){
						$this->addHelpTab( $helpTab );
					}

				}

				// Add our settings sections
				$settingsSections = $this->getSettingSections();

				if( $settingsSections && is_array( $settingsSections ) && !empty( $settingsSections ) )
				{

					foreach( $settingsSections as $key => $settingsSection ){
						$this->addSettingSections( $settingsPageSlug, $settingsSection );
					}

				}


				// Grab our settings fields
				$settingsFields = $this->getSettingsFields();

				if( $settingsFields && is_array( $settingsFields ) && !empty( $settingsFields ) )
				{

					foreach( $settingsFields as $key => $settingsField ){
						$this->addSettingFields( $settingsField );
					}

				}

			}/* setUp() */


			/**
			 * Called as part of the AP Framework on each page slug.
			 *
			 * @since 0.1
			 *
			 * @param string $param description
			 * @return string|int returnDescription
			 */

			public function do_studiorum_settings()
			{

				$this->showSubmitButton();

			}/* do_studiorum_settings() */


			/**
			 * Add the submit button
			 *
			 * @since 0.1
			 *
			 * @param null
			 * @return null
			 */

			public function showSubmitButton()
			{

				submit_button();

			}/* showSubmitButton() */


			/**
			 * The root menu page (i.e. where the options live)
			 *
			 * @since 0.1
			 *
			 * @param null
			 * @return string the root menu page, ran through a filter
			 */

			public function getRootMenuPage()
			{

				return apply_filters( 'studiorum_settings_root_menu_page', 'Settings' );

			}/* rootMenuPage() */


			/**
			 * The settings page slug for our options
			 *
			 * @since 0.1
			 *
			 * @param null
			 * @return string the settings page slug, ran through a filter
			 */

			public function getSettingsPageSlug()
			{

				return apply_filters( 'studiorum_settings_page_slug', 'studiorum_settings' );

			}/* getSettingsPageSlug() */


			/**
			 * A method to return the sub menu items for our options page
			 *
			 * @since 0.1
			 *
			 * @param null
			 * @return array $subMenuItems - an array of sub menu items, ran through a filter
			 */

			public function getSubMenuItems()
			{

				$settingsPageSlug = $this->getSettingsPageSlug();

				$subMenuItems = array( 
					array( 
						'title' 	=> __( 'Studiorum', 'studiorum' ),
						'page_slug' => $settingsPageSlug
					)
				);

				$subMenuItems = apply_filters( 'studiorum_settings_sub_menu_items', $subMenuItems );

				return $subMenuItems;

			}/* getSubMenuItems() */


			/**
			 * The tabs which are available on our settings panel
			 *
			 * @since 0.1
			 *
			 * @param null
			 * @return array An array of tabs available on our settings panel, run through a filter
			 */

			public function getSettingsTabs()
			{

				$studiorumSettingsTabs = array(

					array(
						'tab_slug'	=>	'basic',	// avoid hyphen(dash), dots, and white spaces
						'title'		=>	__( 'Basic', 'studiorum' )
					)

				);

				$studiorumSettingsTabs = apply_filters( 'studiorum_settings_in_page_tabs', $studiorumSettingsTabs );

				return $studiorumSettingsTabs;

			}/* getSettingsTabs() */


			/**
			 * Help tabs displayed in the top right hand corner
			 *
			 * @since 0.1
			 *
			 * @param null
			 * @return array an array of arrays, each sub-array contains an individual help tab and content; ran through a filter
			 */

			public function getHelpTabs()
			{

				$settingsPageSlug = $this->getSettingsPageSlug();

				$helpTabs = array( 
					array(
						'page_slug'					=>	$settingsPageSlug,
						// 'page_tab_slug'			=>	null,	// ( optional )
						'help_tab_title'			=>	'Admin Page Framework',
						'help_tab_id'				=>	'admin_page_framework',	// ( mandatory )
						'help_tab_content'			=>	__( 'This contextual help text can be set with the <code>addHelpTab()</code> method.', 'studiorum' ),
						'help_tab_sidebar_content'	=>	__( 'This is placed in the sidebar of the help pane.', 'studiorum' ),
					)
				);

				$helpTabs = apply_filters( 'studiorum_settings_help_tabs', $helpTabs, $settingsPageSlug );

				return $helpTabs;

			}/* getHelpTabs() */


			/**
			 * Fetch our settings sections - several sections in the settings option which delineates what each group of options is for
			 *
			 * @since 0.1
			 *
			 * @param null
			 * @return string|int returnDescription
			 */

			public function getSettingSections()
			{

				$settingsSections = array(

					array(
						'section_id'		=>	'text_fields',	// avoid hyphen(dash), dots, and white spaces
						// 'page_slug'		=>	'apf_builtin_field_types',	// <-- the method remembers the last used page slug and the tab slug so they can be omitted from the second parameter.
						'tab_slug'		=>	'basic',
						'title'			=>	__( 'Text Fields', 'studiorum' ),
						'description'	=>	__( 'These are text type fields.', 'studiorum' ),	// ( optional )
						'order'			=>	10,	// ( optional ) - if you don't set this, an index will be assigned internally in the added order
					)

				);

				$settingsSections = apply_filters( 'studiorum_settings_settings_sections', $settingsSections );

				return $settingsSections;

			}/* getSettingSections() */


			/**
			 * Return our individual settings fields. Each needs to be attached to a settings section (outlined in getSettingsSections() )
			 * The 'section_id' attribute of each sub-array is required. As is the 'field_id'. Avoid hyphens, dots and white space.
			 *
			 * @since 0.1
			 *
			 * @param null
			 * @return array $settingsFields - an array of arrays of individual settings fields, ran through a filter
			 */

			public function getSettingsFields()
			{

				$settingsFields = array(

					array(	// Single text field
						'field_id'	=>	'text',
						'section_id'	=>	'text_fields',
						'title'	=>	__( 'Text', 'studiorum' ),
						'description'	=>	__( 'Type something here. This text is inserted with the <code>description</code> key in the field definition array.', 'studiorum' ),
						'help'	=>	__( 'This is a text field and typed text will be saved. This text is inserted with the <code>help</code> key in the field definition array.', 'studiorum' ),
						'type'	=>	'text',
						'order'	=>	1,	// ( optional )
						'default'	=>	123456,
						'attributes'	=>	array(
							'size'	=>	40,
						),
					),

					array(	// Single text field
						'field_id'	=>	'another_text',
						'section_id'	=>	'text_fields',
						'title'	=>	__( 'Another Text', 'studiorum' ),
						'description'	=>	__( 'Option description', 'studiorum' ),
						'help'	=>	__( '.', 'studiorum' ),
						'type'	=>	'text',
						'order'	=>	2,	// ( optional )
						'default'	=>	'Sausage',
						'attributes'	=>	array(
							'size'	=>	20,
						),
					)

				);

				$settingsFields = apply_filters( 'studiorum_settings_settings_fields', $settingsFields );

				return $settingsFields;

			}/* getSettingsFields() */

		}/* class Studiorum_Options */

	endif;

	global $Studiorum_Options;
	$Studiorum_Options = new Studiorum_Options;