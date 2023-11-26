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
 *
 * @phpstan-consistent-constructor
 */
abstract class Singleton extends Base {
	/**
	 * Our static instances.
	 *
	 * @since 0.1.0
	 *
	 * @var array<string,static>
	 */
	public static $instances = array();

	/**
	 * Get our static instance.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed ...$args Optional arguments.  Declaring this here with the spread operator
	 *                       allows sub-classes to declare specific arguments.
	 *
	 * @return static
	 */
	public static function get_instance( ...$args ) {
		if ( ! isset( self::$instances[ static::class ] ) ) {
			self::$instances[ static::class ] = new static( ...$args );
		}

		return self::$instances[ static::class ];
	}

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed ...$args Optional arguments.  Declaring this here with the spread operator
	 *                       allows sub-classes to declare specific arguments.
	 */
	protected function __construct( ...$args ) {
		if ( isset( self::$instances[ static::class ] ) ) {
			return;
		}

		parent::__construct( ...$args );

		self::$instances[ static::class ] = $this;
	}
}
