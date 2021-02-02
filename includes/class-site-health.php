<?php
/**
 * Site_Heath class.
 *
 * @package ssl-db-connection-indicator
 * @since 0.1.0
 */

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
	 * @param array $tests {
	 *     An associative array, where the `$test_type` is either `direct` or
	 *     `async`, to declare if the test should run via Ajax calls after page load.
	 *
	 *     @type array $identifier {
	 *         `$identifier` should be a unique identifier for the test that should run.
	 *         Plugins and themes are encouraged to prefix test identifiers with their slug
	 *         to avoid any collisions between tests.
	 *
	 *         @type string   $label             A friendly label for your test to identify it by.
	 *         @type mixed    $test              A callable to perform a direct test, or a string AJAX action
	 *                                           to be called to perform an async test.
	 *         @type boolean  $has_rest          Optional. Denote if `$test` has a REST API endpoint.
	 *         @type boolean  $skip_cron         Whether to skip this test when running as cron.
	 *         @type callable $async_direct_test A manner of directly calling the test marked as asynchronous,
	 *                                           as the scheduled event can not authenticate, and endpoints
	 *                                           may require authentication.
	 *     }
	 * }
	 * @return array {
	 *     An associative array, where the `$test_type` is either `direct` or
	 *     `async`, to declare if the test should run via Ajax calls after page load.
	 *
	 *     @type array $identifier {
	 *         `$identifier` should be a unique identifier for the test that should run.
	 *         Plugins and themes are encouraged to prefix test identifiers with their slug
	 *         to avoid any collisions between tests.
	 *
	 *         @type string   $label             A friendly label for your test to identify it by.
	 *         @type mixed    $test              A callable to perform a direct test, or a string AJAX action
	 *                                           to be called to perform an async test.
	 *         @type boolean  $has_rest          Optional. Denote if `$test` has a REST API endpoint.
	 *         @type boolean  $skip_cron         Whether to skip this test when running as cron.
	 *         @type callable $async_direct_test A manner of directly calling the test marked as asynchronous,
	 *                                           as the scheduled event can not authenticate, and endpoints
	 *                                           may require authentication.
	 *     }
	 * }
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
	 * @return array {
	 *     The result of our Test.
	 *
	 *     @type string $label What the header of the section should say.
	 *     @type string $status Section the result should be displayed in. Possible values are good, recommended, or critical.
	 *     @type string[] $badge {
	 *         The badge to display on the results page.
	 *
	 *         @type string $label What the badge should say.
	 *         @type string $color Applies a CSS class with this value to the badge. Core styles support blue, green, red, orange, purple and gray.
	 *     }
	 *     @type string $description Additional details about the results of the test.
	 *     @type string $actions     A link or button to allow the end user to take action on the result.
	 *     @type string $test        The name of the test.
	 * }
	 */
	public function connection_test() {
		/**
		 * Filters the status used for test failure.
		 *
		 * @param string $failed_status The status to use for test failure.  Default is 'recommeneded'.
		 *                              Accepts 'recommended' and 'critical'.
		 */
		$failed_status = apply_filters( 'ssl_db_connection_indicator_site_health_failed_status', self::DEFAULT_FAILED_STATUS );
		if ( ! in_array( $failed_status, array( 'recommedned', 'critical' ), true ) ) {
			$failed_status = self::DEFAULT_FAILED_STATUS;
		}

		/**
		 * Filters the color used for the badge on test failure.
		 *
		 * @param string $failed_badge_color The color to use for the badge on test failure.
		 *                                   Default is 'blue'.  Accepts 'blue', 'green',
		 *                                   'red', 'orange', 'purple' and 'gray'.
		 */
		$failed_badge_color = apply_filters( 'ssl_db_connection_indicator_site_health_failed_badge_color', self::DEFAULT_FAILED_BADGE_COLOR );
		if ( ! in_array( $failed_badge_color, array( 'blue', 'green', 'red', 'orange', 'purple', 'gray' ), true ) ) {
			$failed_badge_color = self::DEFAULT_FAILED_BADGE_COLOR;
		}

		$result = array(
			'label'       => __( 'Database connection is unencrypted', 'ssl-db-connection-indicator' ),
			'status'      => $failed_status,
			'description' => sprintf(
				'<p>%1$s %2$s</p><p>%3$s</p>',
				__( 'The database connection is not encrypted.', 'ssl-db-connection-indicator' ),
				__( 'An encrypted database connection helps protect the security and privacy of the information stored in your WordPress database.', 'ssl-db-connection-indicator' ),
				__( 'Explainng how to establish an encrypted database connection beyond what can be described here.', 'ssl-db-connection-indicator' ),
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
			$result['label']       = __( 'Database connection is encrypted', 'ssl-db-connection-indicator' );
			$result['status']      = 'good';
			$result['description'] = sprintf(
				'<p>%1$s</p><p>%2$s</p>',
				sprintf(
					/* translators: the SSL/TLS version number and encryption cipher used for the database connection */
					__( 'The database connection is %s.', 'ssl-db-connection-indicator' ),
					$this->get_conn_status_as_str( $status )
				),
				__( 'Encrypted database connections help to enhance security and privacy.', 'ssl-db-connection-indicator' )
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
	 * @param array $debug_info {
	 *     The debug information to be added to the core information page.
	 *
	 *     This is an associative multi-dimensional array, up to three levels deep. The topmost array holds the sections.
	 *     Each section has a `$fields` associative array (see below), and each `$value` in `$fields` can be
	 *     another associative array of name/value pairs when there is more structured data to display.
	 *
	 *     @type string  $label        The title for this section of the debug output.
	 *     @type string  $description  Optional. A description for your information section which may contain basic HTML
	 *                                 markup, inline tags only as it is outputted in a paragraph.
	 *     @type boolean $show_count   Optional. If set to `true` the amount of fields will be included in the title for
	 *                                 this section.
	 *     @type boolean $private      Optional. If set to `true` the section and all associated fields will be excluded
	 *                                 from the copied data.
	 *     @type array   $fields {
	 *         An associative array containing the data to be displayed.
	 *
	 *         @type string  $label    The label for this piece of information.
	 *         @type string  $value    The output that is displayed for this field. Text should be translated. Can be
	 *                                 an associative array that is displayed as name/value pairs.
	 *         @type string  $debug    Optional. The output that is used for this field when the user copies the data.
	 *                                 It should be more concise and not translated. If not set, the content of `$value` is used.
	 *                                 Note that the array keys are used as labels for the copied data.
	 *         @type boolean $private  Optional. If set to `true` the field will not be included in the copied data
	 *                                 allowing you to show, for example, API keys here.
	 *     }
	 * }
	 * @return array {
	 *     The debug information to be added to the core information page.
	 *
	 *     This is an associative multi-dimensional array, up to three levels deep. The topmost array holds the sections.
	 *     Each section has a `$fields` associative array (see below), and each `$value` in `$fields` can be
	 *     another associative array of name/value pairs when there is more structured data to display.
	 *
	 *     @type string  $label        The title for this section of the debug output.
	 *     @type string  $description  Optional. A description for your information section which may contain basic HTML
	 *                                 markup, inline tags only as it is outputted in a paragraph.
	 *     @type boolean $show_count   Optional. If set to `true` the amount of fields will be included in the title for
	 *                                 this section.
	 *     @type boolean $private      Optional. If set to `true` the section and all associated fields will be excluded
	 *                                 from the copied data.
	 *     @type array   $fields {
	 *         An associative array containing the data to be displayed.
	 *
	 *         @type string  $label    The label for this piece of information.
	 *         @type string  $value    The output that is displayed for this field. Text should be translated. Can be
	 *                                 an associative array that is displayed as name/value pairs.
	 *         @type string  $debug    Optional. The output that is used for this field when the user copies the data.
	 *                                 It should be more concise and not translated. If not set, the content of `$value` is used.
	 *                                 Note that the array keys are used as labels for the copied data.
	 *         @type boolean $private  Optional. If set to `true` the field will not be included in the copied data
	 *                                 allowing you to show, for example, API keys here.
	 *     }
	 * }
	 *
	 * @filter debug_information
	 */
	public function debug_information( $debug_info ) {
		$debug_info['wp-database']['fields']['connection'] = array(
			'label' => __( 'Connection', 'ssl-db-connection-indicator' ),
			'value' => __( 'Unencrypted', 'ssl-db-connection-indicator' ),
		);

		$status = $this->get_conn_status();
		if ( $status['ssl_cipher'] ) {
			$debug_info['wp-database']['fields']['connection']['value'] = $this->get_conn_status_as_str( $status );
		}

		return $debug_info;
	}
}
