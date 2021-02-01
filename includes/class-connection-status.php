<?php

namespace SHC\SSL_DB_CONNECTION_INDICATOR;

defined( 'ABSPATH' ) || die;

/**
 * Abstract parent class of all other classes that need know what
 * the current connection status is.
 *
 * @since 0.1.0
 */
abstract class Connection_Status extends Singleton {
	/**
	 * Get the connection status.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 *
	 * @todo write a proper hash for the return type
	 */
	protected function get_conn_status() {
		global $wpdb;

		$results = $wpdb->get_results(
			"SHOW SESSION STATUS WHERE variable_name IN ( 'Ssl_cipher', 'Ssl_version' )"
		);

		$return = array();
		foreach ( $results as $row ) {
			$key            = strtolower( $row->Variable_name );
			$return[ $key ] = $row->Value;
		}

		return $return;
	}

	/**
	 * Get the connection status as a string.
	 *
	 * @since 0.1.0
	 *
	 * @param array $status
	 * @return string
	 *
	 * @todo write a proper hash for the $status param
	 */
	protected function get_conn_status_as_str( $status ) {
		$str = __( 'Unencrypted', 'ssl-db-connection-indicator' );

		if ( $status['ssl_version'] ) {
			$str = sprintf(
				__( 'SSL (%1$s) encrypted via %2$s', 'ssl-db-connection-indicator' ),
				$status['ssl_version'],
				$status['ssl_cipher']
			);
		}

		return $str;
	}
}
