<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Plugin Name: DB Crossing Guard
 * Description: Display various indicators to let admins know whether the DB connection is encrypted
 * Version: 0.2.1
 * Author: Paul V. Biron/Sparrow Hawk Computing
 * Author URI: http://sparrowhawkcomputing.com/
 * Plugin URI: https://github.com/pbiron/db-crossing-guard/
 * Network: true
 *
 * Inspired by the At a Glance item added by the secure-db-connection plugin.
 */

/*
 * @todo suggest that @johnbillion add a filter so that we can add a "Connection" row to
 *       QM's Environment Database panel.  Or maybe he can add it himself.
 */
namespace SHC\DB_CROSSING_GUARD;

use WP_CLI;

defined( 'ABSPATH' ) || die;

require __DIR__ . '/vendor/autoload.php';

/**
 * Main plugin class.
 *
 * @since 0.1.0
 * @since 0.2.0 Plugin renamed to DB Crossing Guard, slug to db-crossing-guard and Namespace to SHC\DB_CROSSING_GUARD
 */
class Plugin extends Singleton {
	/**
	 * Our version number.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const VERSION = '0.2.1';

	/**
	 * The full path to the main plugin file.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const FILE = __FILE__;

	/**
	 * The capability a user must have to see DB connection info.
	 *
	 * @since 0.2.3
	 *
	 * @var string
	 */
	const CAP = 'view_site_health_checks';

	/**
	 * Add hooks.
	 *
	 * @since 0.2.3
	 *
	 * @return void
	 */
	protected function add_hooks() {
		parent::add_hooks();

		add_action( 'after_setup_theme', array( $this, 'maybe_instantiate_admin_bar_node' ) );

		return;
	}

	/**
	 * Instantiate our admin bar class if appropriate.
	 *
	 * @since 0.2.3
	 *
	 * @return void
	 *
	 * @action after_setup_theme
	 */
	public function maybe_instantiate_admin_bar_node() {
		if ( is_admin_bar_showing() && current_user_can( self::CAP ) ) {
			Admin_Bar_Node::get_instance();
		}

		return;
	}

	/**
	 * Perform initialization after all plugins have loaded.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 *
	 * @aciton plugins_loaded
	 */
	public function plugins_loaded() {
		global $pagenow;

		if ( ! ( is_admin() && current_user_can( self::CAP ) ) ) {
			return;
		}

		switch ( $pagenow ) {
			case 'index.php':
				At_A_Glance::get_instance();

				break;
			case 'site-health.php':
			case 'wp-cron.php':
				// Make sure our tests can run when the site health cron job runs.
				Site_Health::get_instance();

				break;
		}

		/**
		 * Fires when db-crossing-guard has been loaded.
		 *
		 * @since 0.2.3
		 */
		do_action( 'db_crossing_guard_loaded' );

		return;
	}

	/**
	 * Initialize WP_CLI.
	 *
	 * @since 0.1.1
	 * @since 0.2.3 Our CLI commands are now sub-commands of the built-in db command.
	 *
	 * @return void
	 *
	 * @action cli_init
	 */
	public function cli_init() {
		WP_CLI::add_command( 'db-crossing-guard connection', __NAMESPACE__ . '\\Connection_Command' );

		return;
	}
}

// Instantiate ourselves.
Plugin::get_instance();
