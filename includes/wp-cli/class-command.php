<?php
/**
 * Command class.
 *
 * @package ssl-db-connection-indicator
 * @since 0.1.1
 */

namespace SHC\SSL_DB_CONNECTION_INDICATOR;

use WP_CLI;
use WP_CLI_Command;

defined( 'ABSPATH' ) || die;

/**
 * Class to WP_CLI Command(s).
 *
 * @since 0.1.1
 */
class Command extends WP_CLI_Command {
	/**
	 * Get the connection status (encrypted vs unencrypted).
	 *
	 * @since 0.1.1
	 */
	public function status() {
		$conn_status = Connection_Status::get_instance();

		$status = $conn_status->get_conn_status_as_str( $conn_status->get_conn_status() );
		if ( __( 'Unencrypted', 'ssl-db-connection-indicator' ) === $status ) {
			WP_CLI::warning( $status );
		} else {
			WP_CLI::success( $status );
		}

		return;
	}
}
