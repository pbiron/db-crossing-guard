<?php
/**
 * Connection_Status class.
 *
 * @since 0.1.0
 *
 * @package db-crossing-guard
 */

namespace SHC\DB_CROSSING_GUARD;

defined( 'ABSPATH' ) || die;

/**
 * Parent class of all other classes that need know what
 * the current connection status is.
 *
 * @since 0.1.0
 */
class Connection_Status extends Singleton {
	/**
	 * Get the connection status.
	 *
	 * @since 0.1.0
	 *
	 * @return array {
	 *     XXX
	 *
	 *     @type string $ssl_version The SSL/TLS version used.
	 *     @type string $ssl_cipher  The encryption cipher used.
	 * }
	 *
	 * @todo write a proper hash for the return type
	 */
	public function get_conn_status() {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$results = $wpdb->get_results(
			"SHOW SESSION STATUS WHERE variable_name IN ( 'Ssl_cipher', 'Ssl_version' )"
		);

		$return = array();
		foreach ( $results as $row ) {
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			$return[ strtolower( $row->Variable_name ) ] = $row->Value;
		}

		return $return;
	}

	/**
	 * Get the connection status as a string.
	 *
	 * @since 0.1.0
	 *
	 * @param array $status {
	 *     The connection status array.
	 *
	 *     @type string $ssl_version The SSL/TLS version used.
	 *     @type string $ssl_cipher  The encryption cipher used.
	 * }
	 * @return string
	 *
	 * @todo write a proper hash for the $status param
	 */
	public function get_conn_status_as_str( $status ) {
		$str = __( 'Unencrypted', 'db-crossing-guard' );

		if ( $status['ssl_version'] ) {
			$str = sprintf(
				/* translators: $1 SSL/TLS version number, $2 encryption cipher used */
				__( 'SSL (%1$s) encrypted via %2$s', 'db-crossing-guard' ),
				$status['ssl_version'],
				$status['ssl_cipher']
			);
		}

		return $str;
	}
}
