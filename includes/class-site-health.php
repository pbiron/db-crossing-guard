<?php

namespace SHC\SSL_DB_CONNECTION_INDICATOR;

defined( 'ABSPATH' ) || die;

/**
 * Class to augment Site Health with connection information.
 *
 * @since 0.1.0
 */
class Site_Health extends Connection_Status {
	/**
	 * Our default status when our test fails.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const DEFAULT_FAILED_STATUS = 'recommended';

	/**
	 * Our default badge color when our test fails.
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	const DEFAULT_FAILED_BADGE_COLOR = 'blue';

	/**
	 * Add hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	protected function add_hooks() {
		parent::add_hooks();

		add_filter( 'site_status_tests', array( $this, 'add_tests' ) );
		add_filter( 'debug_information', array( $this, 'debug_information' ) );

		return;
	}

	/**
	 * Add our tests.
	 *
	 * @since 0.1.0
	 *
	 * @param array $tests
	 * @return array
	 *
	 * @filter site_status_tests
	 */
	public function add_tests( $tests ) {
		$tests['direct']['ssl-db-connection-indicator-connection-test'] = array(
			'label' => __( 'Database Connection', 'ssl-db-connection-indicator' ),
			'test'  => array( $this, 'connection_test' ),
		);

		return $tests;
	}

	/**
	 * Our connection test.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function connection_test() {
		$failed_status = apply_filters( 'ssl-db-connection-indicator-site-health-failed-status', self::DEFAULT_FAILED_STATUS );
		if ( ! in_array( $failed_status, array( 'good', 'recommedned', 'critical' ), true ) ) {
			$failed_status = self::DEFAULT_FAILED_STATUS;
		}

		$failed_badge_color = apply_filters( 'ssl-db-connection-indicator-site-health-failed-badge-color', self::DEFAULT_FAILED_BADGE_COLOR );
		if ( ! in_array( $failed_badge_color, array( 'blue', 'green', 'red', 'orange', 'purple', 'gray' ), true ) ) {
			$failed_badge_color = self::DEFAULT_FAILED_BADGE_COLOR;
		}

		$result = array(
			'label'       => __( 'Database connection is unencrypted', 'ssl-db-connection-indicator' ),
			'status'      => $failed_status,
			'description' => sprintf(
				'<p>%s</p>',
				__( 'SSL is not enabled.  SSL database connections improve security and privacy.', 'ssl-db-connection-indicator' )
			),
			'badge'       => array(
				'label' => __( 'Security' ),
				'color' => $failed_badge_color,
			),
			'test'        => 'ssl-db-connection-indicator',
			'actions'     => '',
		);

		$status = $this->get_conn_status();
		if ( $status['ssl_version'] ) {
			$result['label']          = __( 'Database connection is encrypted', 'ssl-db-connection-indicator' );
			$result['status']         = 'good';
			$result['description']    = sprintf(
				'<p>%1$s</p><p>%2$s</p>',
				sprintf(
					__( 'The database connection is %s.', 'ssl-db-connection-indicator' ),
					$this->get_conn_status_as_str( $status )
				),
				__( 'This helps to enhance security and privacy.', 'ssl-db-connection-indicator' )
			);
			$result['badge']['color'] = 'blue';
		}

		return $result;
	}

	/**
	 * Add our Site Health debug info.
	 *
	 * @since 0.1.0
	 *
	 * @param array $debug_info
	 * @return array
	 *
	 * @filter debug_information
	 */
	public function debug_information( $debug_info ) {
		$debug_info['wp-database']['fields']['connection'] = array(
			'label'   => __( 'Connection', 'ssl-db-connection-indicator' ),
			'value'   => __( 'Unencrypted', 'ssl-db-connection-indicator' ),
		);

		$status = $this->get_conn_status();
		if ( $status['ssl_cipher'] ) {
			$debug_info['wp-database']['fields']['connection']['value'] = $this->get_conn_status_as_str( $status );
		}

		return $debug_info;
	}
}
