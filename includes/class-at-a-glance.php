<?php

namespace SHC\SSL_DB_CONNECTION_INDICATOR;

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
	 * Output our At a Glance item.
	 *
	 * @since 0.1.0
	 *
	 * @param array $items
	 * @return array
	 *
	 * @filter dashboard_glance_items
	 */
	function dashboard_glance_item( $items = array() ) {
		$class   = 'nossl';
		$title   = '';
		$message = __( 'DB conn unencrypted', 'ssl-db-connection-indicator' );

		$status = $this->get_conn_status();
		if ( $status['ssl_cipher'] ) {
			$class   = 'ssl';
			$title   = $this->get_conn_status_as_str( $status );
			$message = __( 'DB conn encrypted', 'ssl-db-connection-indicator' );
		}

		printf(
			'<li class="ssl-db-connection-indicator %1$s"><span title="%2$s">%3$s</span></li>',
			$class,
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
		echo '<ul>';
		$this->dashboard_glance_item();
		echo '</ul>';

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
			'ssl-db-connection-indicator-at-a-glnace',
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
	 * @param string $hook_suffix
	 * @return void
	 *
	 * @action admin_enqueue_scripts
	 */
	public function admin_enqueue_styles( $hook_suffix ) {
		if ( 'index.php' === $hook_suffix ) {
			wp_enqueue_style( 'ssl-db-connection-indicator-at-a-glnace' );
		}

		return;
	}
}
