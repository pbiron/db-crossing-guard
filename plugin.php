<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Plugin Name: SSL DB Connection Indicator
 * Description: Display various indicators about whether the DB connection is over SSL or not
 * Version: 0.1.0
 * Author: Paul V. Biron/Sparrow Hawk Computing
 * Author URI: http://sparrowhawkcomputing.com/
 * Network: true
 *
 * Inspired by the At a Glance item added by the secure-db-connection plugin.
 */

/*
 * @todo suggest that @johnbillion add a filter so that we can add a "Connection" row to
 *       QM's Environment Database panel.  Or maybe he can add it himself.
 */
namespace SHC\SSL_DB_CONNECTION_INDICATOR;

defined( 'ABSPATH' ) || die;

require __DIR__ . '/vendor/autoload.php';

/**
 * Main plugin class.
 *
 * @since 0.1.0
 */
class Plugin extends Singleton {
	/**
	 * Our version number.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const VERSION = '0.1.0';

	/**
	 * The full path to the main plugin file.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const FILE = __FILE__;

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

		if ( ! ( current_user_can( 'administrator' ) || ( is_multisite() && is_super_admin() ) ) ) {
			return;
		}

		switch ( $pagenow ) {
			case 'index.php':
				At_A_Glance::get_instance();

				break;
			case 'site-health.php':
				Site_Health::get_instance();

				break;
		}

		// @todo find an appropriate action that isn't too early nor too late,
		//       so that Admin_Bar_Node only gets instantiated when is_admin_bar_showing()
		//       returns true.
		Admin_Bar_Node::get_instance();

		return;
	}
}

// Instantiate ourselves.
Plugin::get_instance();
