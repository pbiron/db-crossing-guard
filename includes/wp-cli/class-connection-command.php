<?php
/**
 * Connection_Command class.
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
 * Get information about the connection to the database.
 *
 * @since 0.1.1
 * @since 0.2.3 Class name changed to Connection_Command.
 */
class Connection_Command extends WP_CLI_Command {
	/**
	 * Display the connection status (encrypted vs unencrypted).
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp db connection status
	 *     Success: TLSv1.2 using the ECDHE-RSA-AES128-GCM-SHA256 cipher
	 *
	 *     $ wp db connection status
	 *     Warning: Unencrypted
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
	 *     # 0 indicates the connection is encrypted, 1 indicates the connection is not encrypted.
	 *     $ wp db connection is-encrypted
	 *     # or echo %errorlevel% on Windows.
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
