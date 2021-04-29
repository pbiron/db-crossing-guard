<?php
/**
 * Command class.
 *
 * @since 0.1.1
 *
 * @package db-crossing-guard
 */

namespace SHC\DB_CROSSING_GUARD;

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
		if ( __( 'Unencrypted', 'db-crossing-guard' ) === $status ) {
			WP_CLI::warning( $status );
		} else {
			WP_CLI::success( $status );
		}

		return;
	}
}
