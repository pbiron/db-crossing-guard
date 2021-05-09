<?php
/**
 * Command class.
 *
 * @package db-crossing-guard
 * @since 0.1.1
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

	/**
	 * Detects whether the connection is encrypted.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp db-crossing-guard is-encrypted
	 *     $ echo $?
	 *     1
	 *
	 * @subcommand is-encrypted
	 *
	 * @since 0.2.3
	 */
	public function is_encrypted() {
		$conn_status = Connection_Status::get_instance();

		$status = $conn_status->get_conn_status();

		WP_CLI::halt( $status['ssl_version'] ? 0 : 1 );
	}
}
