<?php
/**
 * Plugin name: Limit Groups per User
 * description: Limit the number of groups a user can create.
 * Author: BuddyDev
 * Author URI: https://buddydev.com
 * Plugin URI: https://buddydev.com/buddypress/limit-groups-per-user-plugin-for-buddypress/
 * Version: 2.0.2
 * License: GPL
 */

// Exit if file access directly over web.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Limit_Groups_Per_User_Helper
 */
class BP_Limit_Groups_Per_User_Helper {

	/**
	 * Plugin directory path
	 *
	 * @var string
	 */
	private $path;

	/**
	 * Class instance
	 *
	 * @var BP_Limit_Groups_Per_User_Helper
	 */
	private static $instance;

	/**
	 * BP_Limit_Groups_Per_User_Helper constructor.
	 */
	private function __construct() {
		$this->path = plugin_dir_path( __FILE__ );

		$this->setup();
	}

	/**
	 * Get singleton instance.
	 *
	 * @return BP_Limit_Groups_Per_User_Helper
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Callbacks to necessaries actions
	 */
	public function setup() {
		register_activation_hook(
			__FILE__,
			array( $this, 'on_activation' )
		);

		add_filter(
			'bp_user_can_create_groups',
			array( $this, 'filter_create_groups_permission' )
		);

		add_action( 'bp_actions', array( $this, 'groups_action_create_redirect' ), 9 );

		add_action( 'plugins_loaded', array( $this, 'load_admin' ), 9996 );
	}

	/**
	 * On activation
	 */
	public function on_activation() {
		// delete old option.
		delete_site_option( 'limit-groups-creation-per-user' );

		if ( is_multisite() ) {
			// yes, it is written correctly. Don't feel strange.
			delete_option( 'limit-groups-creation-per-user' );
		}
	}


	/**
	 * Filter permission.
	 *
	 * @param bool $can is allowed tpo create.
	 *
	 * @return bool
	 */
	public function filter_create_groups_permission( $can ) {

		if ( ! is_user_logged_in() || is_super_admin() || ( bp_is_group_create() && ! bp_is_action_variable( 'group-details', 1 ) ) ) {
			return $can;
		}

		$user_id = get_current_user_id();
		// if the user is restricted and has exceeded limit, do not allow.
		if ( self::is_user_restricted( $user_id ) && self::has_exceeded_limit( $user_id ) ) {
			$can = false;
		}

		return $can;
	}


	/**
	 * Get the total allowed no. of groups for user
	 *
	 * @param int $user_id User id.
	 *
	 * @return int
	 */
	public static function get_allowed_group_count( $user_id ) {
		$allowed_count = apply_filters( 'limit_groups_get_allowed_group_count', self::get_user_limit( $user_id ), $user_id );

		return absint( $allowed_count );
	}

	/**
	 * Modify group redirect
	 */
	public function groups_action_create_redirect() {
		// If we're not at domain.org/groups/create/ then return false.
		if ( ! bp_is_groups_component() || ! bp_is_current_action( 'create' ) ) {
			return false;
		}

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$redirect_url = trim( self::get_option( 'redirect_url' ) );

		if ( ! bp_user_can_create_groups() && $redirect_url ) {
			bp_core_add_message( __( 'Sorry, you are not allowed to create groups.', 'buddypress' ), 'error' );
			bp_core_redirect( $redirect_url );
		}
	}

	/**
	 * Load admin
	 */
	public function load_admin() {
		if ( is_admin() && ! wp_doing_ajax() ) {
			require_once $this->path . 'admin/pt-settings/pt-settings-loader.php';
			require_once $this->path . 'admin/class-limit-groups-per-user-admin-helper.php';
			$admin_helper = new Limit_Groups_Per_User_Admin_Helper();
			$admin_helper->setup();
		}
	}

	/**
	 * Get setting value
	 *
	 * @param string $key Option key.
	 *
	 * @return mixed
	 */
	public static function get_option( $key ) {
		$settings = get_option( 'limit-groups-per-user-settings' );

		if ( isset( $settings[ $key ] ) ) {
			return $settings[ $key ];
		}

		return null;
	}

	/**
	 * Check if user group creation limited exceeded or not
	 *
	 * @param int $user_id User id.
	 *
	 * @return bool
	 */
	public static function has_exceeded_limit( $user_id ) {
		$has_exceeded = false;

		$allowed_limit = self::get_allowed_group_count( $user_id );
		$count_created = self::get_user_groups_created_count( $user_id );
		// In case of new group creation, do not prevent steps.
		if ( bp_get_new_group_id() ) {
			$count_created--;
		}

		if ( $count_created >= $allowed_limit ) {
			$has_exceeded = true;
		}

		return $has_exceeded;
	}

	/**
	 * Get groups count created by user
	 *
	 * @param int $user_id User id.
	 *
	 * @return int
	 */
	public static function get_user_groups_created_count( $user_id ) {
		global $wpdb;

		$table = $wpdb->prefix . 'bp_groups';

		if ( empty( $user_id ) ) {
			return 0;
		}

		$query = $wpdb->prepare( "SELECT COUNT('*') FROM {$table} WHERE creator_id = %d", absint( $user_id ) );

		$count = $wpdb->get_var( $query );

		return (int) $count;
	}

	/**
	 * Get user group creation limit
	 *
	 * @param int $user_id User id.
	 *
	 * @return int
	 */
	public static function get_user_limit( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		if ( ! $user ) {
			return 0;
		}

		$allowed_count = 0;
		foreach ( $user->roles as $role ) {
			if ( strpos( $role, 'bbp_' ) === 0 ) {
				continue;
			}
			$role_allowed_count = self::get_option( "{$role}_threshold_limit" );

			// Increase user threshold to max of his role threshold.
			if ( $role_allowed_count > $allowed_count ) {
				$allowed_count = $role_allowed_count;
			}
		}

		return absint( $allowed_count );
	}

	/**
	 * Check if user is restricted or not
	 *
	 * @param int $user_id user id.
	 *
	 * @return boolean
	 */
	public static function is_user_restricted( $user_id ) {
		$is_restricted = false;

		$user = get_user_by( 'id', $user_id );

		if ( empty( $user ) ) {
			return $is_restricted;
		}

		foreach ( $user->roles as $role ) {
			if ( strpos( $role, 'bbp_' ) === 0 ) {
				continue;
			}

			// if any role is not restricted, do not restrict.
			if ( self::get_option( "restrict_role_{$role}" ) ) {
				$is_restricted = true;
				break;
			}
		}

		return $is_restricted;
	}
}

BP_Limit_Groups_Per_User_Helper::get_instance();
