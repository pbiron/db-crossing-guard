<?php
/**
 * At_A_Glance class
 *
 * @since 0.1.0
 *
 * @package db-crossing-guard
 */

namespace SHC\DB_CROSSING_GUARD;

defined( 'ABSPATH' ) || die;

/**
 * Class to display the connection status in the At a Glance and Network Right Now
 * dashboard widgets.
 *
 * @since 0.1.0
 *
 * @todo consider adding a filter to change the color used for the dashicon,
 *       in which case we'd have to admin_print_styles to dynamically generate
 *       the stylesheet.
 */
class At_A_Glance extends Connection_Status {
	/**
	 * Add hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function add_hooks() {
		parent::add_hooks();

		add_filter( 'dashboard_glance_items', array( $this, 'dashboard_glance_item' ) );
		add_action( 'mu_rightnow_end', array( $this, 'network_dashboard_glance_item' ) );

		return;
	}

	/**
	 * Generate our 'At a Glance' item.
	 *
	 * @since 0.1.0
	 *
	 * @param string[] $items Array of extra 'At a Glance' widget items.
	 *
	 * @return string[] Array of extra 'At a Glance' widget items.
	 *
	 * @filter dashboard_glance_items
	 */
	public function dashboard_glance_item( $items = array() ) {
		$class   = 'unencrypted';
		$title   = '';
		$message = __( 'DB connection unencrypted', 'db-crossing-guard' );

		$status = $this->get_conn_status();
		if ( $status['ssl_cipher'] ) {
			$class   = 'encrypted';
			$title   = $this->get_conn_status_as_str( $status );
			$message = __( 'DB connection encrypted', 'db-crossing-guard' );
		}

		// @todo find markup/CSS that will allow us to use a longer $message that
		//       includes the encryption used and have it correctly wrap at the icon;
		//       so that the encryption doesn't need to be @title, which is problematic
		//       for mobile (i.e., currently on touch devices, there's no way to find out
		//       what the encryption is).
		$items[] = sprintf(
			'<span class="db-crossing-guard %1$s" title="%2$s">%3$s</span>',
			esc_attr( $class ),
			esc_attr( $title ),
			esc_html( $message )
		);

		return $items;
	}

	/**
	 * Output our Network Right Now item.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 *
	 * @action mu_rightnow_end
	 */
	public function network_dashboard_glance_item() {
		$status = $this->dashboard_glance_item();

		echo '<ul><li>';
		echo wp_kses_post( $status[0] );
		echo '</li></ul>';

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
			'db-crossing-guard-at-a-glance',
			plugins_url( "assets/css/at-a-glance{$suffix}.css", Plugin::FILE ),
			array(),
			Plugin::VERSION
		);
	}

	/**
	 * Enqueue our admin styles.
	 *
	 * @since 0.1.0
	 *
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 *
	 * @action admin_enqueue_scripts
	 */
	public function admin_enqueue_styles( $hook_suffix ) {
		if ( 'index.php' === $hook_suffix ) {
			wp_enqueue_style( 'db-crossing-guard-at-a-glance' );
		}

		return;
	}
}
