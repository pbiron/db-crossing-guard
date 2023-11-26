<?php
/**
 * Base class.
 *
 * @since 0.2.3
 *
 * @package db-crossing-guard
 */

namespace SHC\DB_CROSSING_GUARD;

defined( 'ABSPATH' ) || die;

/**
 * Abstract base class for all other classes.
 *
 * @since 0.2.3
 *
 * @phpstan-consistent-constructor
 */
abstract class Base {
	/**
	 * Constructor.
	 *
	 * @since 0.2.3
	 *
	 * @param mixed ...$args Optional arguments.  Declaring this here with the spread operator
	 *                       allows sub-classes to declare specific arguments.
	 */
	public function __construct( ...$args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		if ( method_exists( $this, '_setup' ) ) {
			$this->_setup();
		}

		$this->add_hooks();
	}

	/**
	 * Add hooks.
	 *
	 * Sublcasses that override this method **must** call `parent::add_hooks()`.
	 *
	 * @since 0.2.3
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
			'admin_enqueue_scripts' => 'admin_enqueue_scripts',
			'wp_enqueue_scripts'    => 'wp_enqueue_scripts',
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
}
