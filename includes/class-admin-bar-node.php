<?php
/**
 * Admin_Bar_Node class.
 *
 * @package ssl-db-connection-indicator
 * @since 0.1.0
 */

namespace SHC\SSL_DB_CONNECTION_INDICATOR;

use WP_Admin_Bar;

defined( 'ABSPATH' ) || die;

/**
 * Class to add an admin bar node to display the connection status.
 *
 * @since 0.1.0
 *
 * @todo consider adding a filter to change the colors used,
 *       in which case we'd have to admin_print_styles to dynamically generate
 *       the stylesheet.
 */
class Admin_Bar_Node extends Connection_Status {
	/**
	 * Add hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function add_hooks() {
		parent::add_hooks();

		add_action( 'admin_bar_menu', array( $this, 'add_node' ), 999 );

		return;
	}

	/**
	 * Add our admin bar node.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Admin_Bar $wp_admin_bar {@link https://developer.wordpress.org/reference/classes/wp_admin_bar/ WP_Admin_Bar} instance, passed by reference.
	 * @return void
	 *
	 * @action admin_bar_menu
	 *
	 * @todo figure out how to get the node to display on small screens.
	 */
	public function add_node( $wp_admin_bar ) {
		wp_enqueue_style( 'ssl-db-connection-indicator-admin-bar-node' );

		$status = $this->get_conn_status();

		$args = array(
			'id'    => 'ssl_db_connection_indicator',
			'title' => __( 'DB connection unencrypted', 'ssl-db-connection-indicator' ),
			'meta'  => array( 'class' => 'nossl' ),
		);

		if ( $status['ssl_version'] ) {
			$args['title']         = __( 'DB connection encrypted', 'ssl-db-connection-indicator' );
			$args['meta']['class'] = 'ssl';
		}

		$args['title'] = "<span class='ab-icon'></span>{$args['title']}";

		$wp_admin_bar->add_node( $args );

		if ( $status['ssl_version'] ) {
			// add a child node that displays the encryption.
			$args = array(
				'id'     => 'ssl_db_connection_indicator_ssl',
				'parent' => 'ssl_db_connection_indicator',
				'title'  => $this->get_conn_status_as_str( $status ),
			);

			$wp_admin_bar->add_node( $args );
		}

		return;
	}

	/**
	 * Register our styles.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 *
	 * @action init
	 */
	public function register_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style(
			'ssl-db-connection-indicator-admin-bar-node',
			plugins_url( "assets/css/admin-bar-node{$suffix}.css", Plugin::FILE ),
			array(),
			Plugin::VERSION
		);
	}
}
