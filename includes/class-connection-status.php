<?php
/**
 * Connection_Status class
 *
 * @since 0.1.0
 *
 * @package db-crossing-guard
 */

namespace SHC\DB_CROSSING_GUARD;

defined( 'ABSPATH' ) || die;

/**
 * Parent class of all other classes that need know what the current connection status is.
 *
 * @since 0.1.0
 */
class Connection_Status extends Singleton {
	/**
	 * Get the connection status.
	 *
	 * @since 0.1.0
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 *
	 * @return array {
	 *     The connection status array.
	 *
	 *     @type string $ssl_version The SSL/TLS version used.
	 *     @type string $ssl_cipher  The encryption cipher used.
	 * }
	 *
	 * @phpstan-return array{
	 *     ssl_version: string,
	 *     ssl_cipher: string
	 * }
	 */
	public function get_conn_status() {
		global $wpdb;

		$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			"SHOW SESSION STATUS WHERE variable_name IN ( 'Ssl_cipher', 'Ssl_version' )"
		);

		$return = array();
		foreach ( $results as $row ) {
			$return[ strtolower( $row->Variable_name ) ] = $row->Value; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
		}

		return $return; // @phpstan-ignore-line
	}

	/**
	 * Get the connection status as a string.
	 *
	 * @since 0.1.0
	 *
	 * @param array $status {
	 *     The connection status array.  Default null.  If not provided, $this->get_conn_status() will be called.
	 *
	 *     @type string $ssl_version The SSL/TLS version used.
	 *     @type string $ssl_cipher  The encryption cipher used.
	 * }
	 *
	 * @return string The connection status.  If encrypted, will include the SSLT/TLS version and cipher.
	 *
	 * @phpstan-param array{
	 *     ssl_version: string,
	 *     ssl_cipher: string
	 * } $status
	 */
	public function get_conn_status_as_str( $status = null ) {
		if ( ! $status ) {
			$status = $this->get_conn_status();
		}

		$str = __( 'Unencrypted', 'db-crossing-guard' );

		if ( $status['ssl_version'] ) {
			$str = sprintf(
				/* translators: $1 SSL/TLS version number, $2 encryption cipher used */
				__( '%1$s using the %2$s cipher', 'db-crossing-guard' ),
				$status['ssl_version'],
				$status['ssl_cipher']
			);
		}

		return $str;
	}
}
