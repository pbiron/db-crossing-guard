<?php
/**
 * Singleton class
 *
 * @since 0.1.0
 *
 * @package db-crossing-guard
 */

namespace SHC\DB_CROSSING_GUARD;

defined( 'ABSPATH' ) || die;

/**
 * Abstract base class for singletons.
 *
 * @since 0.1.0
 */
abstract class Singleton {
	/**
	 * Our static instances.
	 *
	 * @since 0.1.0
	 *
	 * @var array Singleton subclasses
	 */
	public static $instances = array();

	/**
	 * Get our static instance.
	 *
	 * @since 0.1.0
	 *
	 * @return Singleton sub-class instance.
	 */
	public static function get_instance() {
		// get "Late Static Binding" class name.
		$class = get_called_class();

		if ( ! isset( self::$instances[ $class ] ) ) {
			self::$instances[ $class ] = new $class();
		}

		return self::$instances[ $class ];
	}

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {
		// get "Late Static Binding" class name.
		$class = get_called_class();

		if ( isset( self::$instances[ $class ] ) ) {
			return self::$instances[ $class ];
		}

		self::$instances[ $class ] = $this;

		$this->add_hooks();
	}

	/**
	 * Add hooks.
	 *
	 * Sublcasses that override this method **must** call `parent::add_hooks()`.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function add_hooks() {
		// these are methods that I know from experience I often define in classes,
		// hence, we automatically hook them (if they exist) instead of having to
		// do it explicitly in the add_hooks() method of each class that defines them.
		$hooks = array(
			'plugins_loaded'        => 'plugins_loaded',
			'init'                  => array( 'init', 'register_scripts', 'register_styles' ),
			'admin_init'            => 'admin_init',
			'admin_enqueue_scripts' => array( 'admin_enqueue_scripts', 'admin_enqueue_styles' ),
			'wp_enqueue_scripts'    => array( 'wp_enqueue_scripts', 'wp_enqueue_styles' ),
			'cli_init'              => 'cli_init',
		);
		foreach ( $hooks as $hook => $methods ) {
			foreach ( (array) $methods as $method ) {
				if ( method_exists( $this, $method ) ) {
					add_action( $hook, array( $this, $method ) );
				}
			}
		}

		return;
	}
}
