<?php
/**
 * UserRegistration Admin Assets
 *
 * Load Admin Assets.
 *
 * @class    UR_Admin_Assets
 * @version  1.0.0
 * @package  UserRegistration/Admin
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * UR_Admin_Assets Class
 */
class UR_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles.
	 */
	public function admin_styles() {
		global $wp_scripts;

		$screen         = get_current_screen();
		$screen_id      = $screen ? $screen->id : '';
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		// Register admin styles
		wp_register_style( 'user-registration-main', UR()->plugin_url() . '/assets/css/admin.css', array( 'nav-menus' ), UR_VERSION );
		wp_register_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );

		wp_register_style( 'user-registration-modules', UR()->plugin_url() . '/assets/css/admin-modules.css', array(), UR_VERSION );

		// Add RTL support for admin styles
		wp_style_add_data( 'user-registration-main', 'rtl', 'replace' );

		// Admin styles for UR pages only
		if ( in_array( $screen_id, ur_get_screen_ids() ) ) {
			wp_enqueue_style( 'user-registration-main' );
			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register Scripts
		wp_register_script( 'user-registration-admin', UR()->plugin_url() . '/assets/js/admin/admin' . $suffix . '.js', array(
			'jquery',
			'select2',
			'jquery-blockui',
			'jquery-tiptip',
			'jquery-ui-sortable',
			'jquery-ui-widget',
			'jquery-ui-core',
			'jquery-ui-tabs',
			'jquery-ui-draggable',
			'jquery-ui-droppable',
			'jquery-tiptip',
			'ur-backbone-modal',
			'ur-enhanced-select',
		), UR_VERSION );
		wp_register_script( 'jquery-blockui', UR()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		wp_register_script( 'jquery-tiptip', UR()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), UR_VERSION, true );
		wp_register_script( 'ur-backbone-modal', UR()->plugin_url() . '/assets/js/admin/backbone-modal' . $suffix . '.js', array(
			'underscore',
			'backbone',
			'wp-util',
		), UR_VERSION );
		wp_register_script( 'select2', UR()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '3.5.4' );
		wp_register_script( 'ur-enhanced-select', UR()->plugin_url() . '/assets/js/admin/enhanced-select' . $suffix . '.js', array(
			'jquery',
			'select2',
		), UR_VERSION );
		wp_register_script( 'user-registration-modules-script', UR()->plugin_url() . '/assets/js/admin/admin-modules' . $suffix . '.js', array(
			'jquery',
			'select2',
		), UR_VERSION );


		wp_localize_script( 'ur-enhanced-select', 'ur_enhanced_select_params', array(
			'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'user-registration' ),
			'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'user-registration' ),
			'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'user-registration' ),
			'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'user-registration' ),
			'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'user-registration' ),
			'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'user-registration' ),
			'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'user-registration' ),
			'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'user-registration' ),
			'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'user-registration' ),
			'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'user-registration' ),
		) );
		if ( 'user-registration_page_user-registration-modules' === $screen_id ) {

			wp_enqueue_style( 'user-registration-modules' );

			wp_enqueue_script( 'user-registration-modules-script' );


			wp_localize_script( 'user-registration-modules-script', 'user_registration_module_params', array(
				'ajax_url'                => admin_url( 'admin-ajax.php' ),
				'error_could_not_install' => __( 'Could not install.', 'user-registration' )

			) );

		}
		// UserRegistration admin pages
		if ( in_array( $screen_id, ur_get_screen_ids() ) ) {
			wp_enqueue_script( 'user-registration-admin' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'jquery-ui-widget' );

			$params = array(
				'required_form_html'             => self::get_form_required_html(),
				'ajax_url'                       => admin_url( 'admin-ajax.php' ),
				'user_input_dropped'             => wp_create_nonce( 'user_input_dropped_nonce' ),
				'ur_form_save'                   => wp_create_nonce( 'ur_form_save_nonce' ),
				'number_of_grid'                 => UR_Config::$ur_form_grid,
				'active_grid'                    => UR_Config::$default_active_grid,
				'is_edit_form'                   => isset( $_GET['edit-registration'] ) ? true : false,
				'post_id'                        => isset( $_GET['edit-registration'] ) ? $_GET['edit-registration'] : 0,
				'admin_url'                      => admin_url( 'admin.php?page=add-new-registration&edit-registration=' ),
				'form_required_fields'           => ur_get_required_fields(),
				'form_one_time_draggable_fields' => ur_get_one_time_draggable_fields(),
				'i18n_admin'                     => self::get_i18n_admin_data(),

			);

			wp_localize_script( 'user-registration-admin', 'user_registration_admin_data', $params );
		}
	}

	/**
	 * @return string
	 */
	public static function get_form_required_html() {

		if ( isset( $_GET['edit-registration'] ) ) {

			return '';
		}

		$form_html = '';

		$required_fields = ur_get_required_fields();

		foreach ( $required_fields as $field ) {

			$class_name = ur_load_form_field_class( $field );

			if ( $class_name !== null ) {

				$template = '<div class="ur-selected-item">';

				$template .= '<div class="ur-action-buttons"><span title="' . __( 'Clone', 'user-registration' ) . '" class="dashicons dashicons-admin-page ur-clone"></span><span title="' . __( 'Trash', 'user-registration' ) . '" class="dashicons dashicons-trash ur-trash"></span></div>';

				$template .= $class_name::get_instance()->get_admin_template();

				$template .= '</div>';

				$form_html .= $template;
			}
		}

		return $form_html;
	}

	/**
	 * @description localize admin data
	 * @return array
	 */
	public static function get_i18n_admin_data() {

		$i18n = array(

			'i18n_are_you_sure_want_to_delete'                       => _x( 'Are you sure want to delete ?', 'user registration admin', 'user-registration' ),
			'i18n_at_least_one_row_need_to_select'                   => _x( 'At least one row need to choose.', 'user registration admin', 'user-registration' ),
			'i18n_user_required_field_already_there'                 => _x( 'User required field is already there, could not dragged.', 'user registration admin', 'user-registration' ),
			'i18n_user_required_field_already_there_could_not_clone' => _x( 'User required field is already there, could not clone.', 'user registration admin', 'user-registration' ),
			'i18n_form_successfully_saved'                           => _x( 'Form successfully saved.', 'user registration admin', 'user-registration' ),
			'i18n_success'                                           => _x( 'Success', 'user registration admin', 'user-registration' ),
			'i18n_error'                                             => _x( 'Error', 'user registration admin', 'user-registration' ),
			'i18n_at_least_one_field_need_to_select'                 => _x( 'At least one field  need to select.', 'user registration admin', 'user-registration' ),
			'i18n_empty_form_name'                                   => _x( 'Empty form name.', 'user registration admin', 'user-registration' ),
			'i18n_previous_save_action_ongoing'                      => _x( 'Previous save action on going.', 'user registration admin', 'user-registration' ),
			'i18n_duplicate_field_name'                              => _x( 'Duplicate field name.', 'user registration admin', 'user-registration' ),
			'i18n_empty_field_label'                                 => _x( 'Empty field label.', 'user registration admin', 'user-registration' ),
			'i18n_invald_field_name'                                 => _x( 'Invalid field name. Please do not use space, empty or special character, you can use underscore.', 'user registration admin', 'user-registration' ),
			'i18n_multiple_field_key'                                => _x( 'Multiple field key ', 'user registration admin', 'user-registration' ),
			'i18n_at_least_one_field_is_required'                    => _x( 'At least one field is required, field ', 'user registration admin', 'user-registration' ),
			'i18n_drag_your_first_item_here'                         => _x( 'Drag your first form item here.', 'user registration admin', 'user-registration' ),

		);

		return $i18n;

	}
}

new UR_Admin_Assets();
