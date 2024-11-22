<?php
/**
 * Base class
 *
 * @since 0.1.0
 *
 * @package db-crossing-guard
 */

namespace SHC\DB_CROSSING_GUARD;

defined( 'ABSPATH' ) || die;

/**
 * Abstract base class for all other classes.
 *
 * @since 0.1.0
 */
abstract class Base {
	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed ...$args Optional arguments.  Declaring this here with the spread operator
	 *                       allows sub-classes to declare specific arguments.
	 */
	public function __construct( ...$args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		if ( method_exists( $this, 'setup' ) ) {
			$this->setup();
		}

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
			'after_setup_theme'     => 'after_setup_theme',
			'init'                  => array( 'init', 'register_scripts', 'register_styles', 'register_block_types' ),
			'admin_enqueue_scripts' => array( 'admin_enqueue_scripts', 'admin_enqueue_styles' ),
			'wp_enqueue_scripts'    => array( 'wp_enqueue_scripts', 'enqueue_styles' ),
			'cli_init'              => 'cli_init',
		);
		foreach ( $hooks as $hook => $methods ) {
			foreach ( (array) $methods as $method ) {
				if ( method_exists( $this, $method ) ) {
					add_action( $hook, array( $this, $method ) ); // @phpstan-ignore-line
				}
			}
		}

		return;
	}

	/**
	 * Perform cleanup when our plugin is uninstalled.
	 *
	 * Subclasses that override this method, must call parent::ininstall().
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public static function uninstall() {
		return;
	}

	/**
	 * Unschedule our cron job when we're deactivated.
	 *
	 * @since 0.1.0
	 *
	 * @param bool $network_wide Optional. Whether the plugin was deactivated network wide.  Default false.
	 *
	 * @return void
	 */
	public function deactivate( $network_wide = false ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		return;
	}
}
