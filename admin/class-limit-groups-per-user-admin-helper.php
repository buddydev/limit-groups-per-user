<?php
/**
 * Admin helper class for Limit groups per user
 *
 * @package limit-groups-per-users
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Press_Themes\PT_Settings\Page;

/**
 * Class Limit_Groups_Per_User_Admin_Helper
 */
class Limit_Groups_Per_User_Admin_Helper {

	/**
	 * What menu slug we will need
	 *
	 * @var string
	 */
	private $menu_slug;

	/**
	 * Used to keep a reference of the Page, It will be usde in rendering the view.
	 *
	 * @var \Press_Themes\PT_Settings\Page
	 */
	private $page;

	/**
	 * Limit_Groups_Per_User_Admin_Helper constructor.
	 */
	public function __construct() {
		$this->menu_slug = 'limit-groups-per-user-settings';
	}

	/**
	 * Callbacks for admin
	 */
	public function setup() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
	}

	/**
	 * Show/render the setting page
	 */
	public function render() {
		$this->page->render();
	}

	/**
	 * Is it the setting page?
	 *
	 * @return bool
	 */
	private function needs_loading() {

		global $pagenow;

		// We need to load on options.php otherwise settings won't be reistered.
		if ( 'options.php' === $pagenow ) {
			return true;
		}

		if ( isset( $_GET['page'] ) && $_GET['page'] === $this->menu_slug ) {
			return true;
		}

		return false;
	}

	/**
	 * Initialize the admin settings panel and fields
	 */
	public function init() {

		if ( ! $this->needs_loading() ) {
			return;
		}

		// The page 'pt-example-settings' is the option key.
		$page = new Page( 'limit-groups-per-user-settings' );

		// Add a panel to to the admin
		// A panel is a Tab and what comes under that tab.
		$panel = $page->add_panel( 'general', _x( 'General', 'Admin settings panel title', 'limit-groups-per-user' ) );

		$section = $panel->add_section( 'general_settings', _x( 'General settings', 'Section title', 'limit-groups-per-user' ) );

		$section->add_field( array(
			'name'  => 'redirect_url',
			'label' => _x( 'Redirect url', 'Admin settings', 'limit-groups-per-user' ),
			'type'  => 'text',
			'desc'  => __( 'Redirect url when user reached their limit', 'limit-groups-per-user' ),
		) );

		$roles = $this->get_roles();

		foreach ( $roles as $role => $label ) {
			// A panel can contain one or more sections.
			$section = $panel->add_section( $role . '_settings', $label );

			$section->add_fields(
				array(
					array(
						'name'    => 'restrict_role_' . $role,
						'label'   => _x( 'Restrict', 'Admin settings', 'limit-groups-per-user' ),
						'type'    => 'checkbox',
						'default' => 0,
						'desc'    => __( 'Limits will be applied only if this checkbox is ticked.', 'limit-groups-per-user' ),
					),
					array(
						'name'    => $role . '_threshold_limit',
						'label'   => _x( 'Limit', 'Admin settings', 'limit-groups-per-user' ),
						'type'    => 'text',
						'default' => 5,
					),
				)
			);
		}
		// Save page for future reference.
		$this->page = $page;

		do_action( 'limit_groups_per_user_settings', $page );

		// allow enabling options.
		$page->init();
	}

	/**
	 * Add Menu
	 */
	public function add_menu() {
		add_options_page(
			_x( 'Limit Groups Per User', 'Admin settings page title', 'limit-groups-per-user' ),
			_x( 'Limit Groups Per User', 'Admin settings menu label', 'limit-groups-per-user' ),
			'manage_options',
			$this->menu_slug,
			array( $this, 'render' )
		);
	}

	/**
	 * Get roles details
	 *
	 * @return array
	 */
	private function get_roles() {
		$editable_roles = get_editable_roles();

		$roles = array();
		foreach ( $editable_roles as $role => $role_detail ) {
			$roles[ $role ] = $role_detail['name'];
		}

		return $roles;
	}
}
