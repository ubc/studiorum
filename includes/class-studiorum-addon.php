<?php 

	/**
	 * A stub class for studiorum add-ons. Needs PHP 5.3 for late static binding
	 *
	 * @package     Studiorum
	 * @subpackage  Admin/Studiorum-Addon
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       0.1.0
	 */

	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ){
		exit;
	}

	class Studiorum_Addon
	{

		private static $reg = array();

		/**
		 * Actions and filters
		 *
		 * @since 0.1
		 *
		 * @param null
		 * @return null
		 */
		
		public static function init()
		{

			add_action( 'plugins_loaded', array( static::instance(), '_setup' ) );

		}/* init() */

		public static function instance()
		{

			$cls = get_called_class();
			!isset( self::$reg[$cls] ) && self::$reg[$cls] = new $cls;

			return self::$reg[$cls];

		}

		public function _setup()
		{

			// Ensure we have our global set
			if( !isset( $GLOBALS['studiorum_addons'] ) ){
				$GLOBALS['studiorum_addons'] = array();
			}

		}

	}/* class Studiorum_Addon */

	// Initiate
	$GLOBALS['studiorum_addons'] = Studiorum_Addon::init();