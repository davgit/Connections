<?php

/**
 * Class for registering and displaying action/error messages.
 *
 * @package     Connections
 * @subpackage  Messages
 * @uses		WP_Error
 * @copyright   Copyright (c) 2013, Steven A. Zahm
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.7.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class cnMessage extends WP_Error {

	/**
	 * Stores the instance of this class.
	 *
	 * @access private
	 * @since 0.7.5
	 * @var (object)
	*/
	private static $instance;

	/**
	 * The meta_key name that the user messages are stored in in the usermeta table.
	 *
	 * @access private
	 * @since 0.7.5
	 * @var (string)
	 */
	private static $meta_key;

	/**
	 * The current user ID.
	 *
	 * @access private
	 * @since 0.7.5
	 * @var (int)
	 */
	private static $id;

	/**
	 * A dummy constructor to prevent the class from being loaded more than once.
	 *
	 * @access public
	 * @since 0.7.5
	 * @see cnMessage::getInstance()
	 * @see cnMessage();
	 */
	public function __construct() { /* Do nothing here */ }

	/**
	 * Setup the class, if it has already been initialized, return the intialized instance.
	 *
	 * @access public
	 * @since 0.7.5
	 */
	public static function init() {

		if ( ! isset( self::$instance ) ) {

			self::$instance = new self;

			self::$meta_key = 'connections_messages';

			/*
			 * Add the error codes and messages.
			 */
			self::addCoreMessages();

			/*
			 * Setup the current user ID so messages can be stored to their user meta.
			 */
			self::setUserID();

			/*
			 * Add any stored admin notices to the admin_notices action hook.
			 */
			self::display();

		}

	}

	/**
	 * Return an instance of the class.
	 *
	 * @access public
	 * @since 0.7.5
	 * @return object cnMessage
	 */
	public static function getInstance() {

		return self::$instance;
	}

	/**
	 * Add all the predefined action/error messages to the WP_Error class.
	 *
	 * @access private
	 * @since 0.7.5
	 * @return void
	 */
	private static function addCoreMessages() {

		// Bring a copy of this into scope.
		$instance = self::getInstance();

		/**
		 * Add the error messages.
		 */
		$instance->add( 'capability_view_entry_list', __( 'You are not authorized to view the entry list. Please contact the admin if you received this message in error.', 'connections' ) );

		$instance->add( 'capability_add', __( 'You are not authorized to add entries. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'capability_delete', __( 'You are not authorized to delete entries. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'capability_edit', __( 'You are not authorized to edit entries. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'capability_categories', __( 'You are not authorized to edit the categories. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'capability_settings', __( 'You are not authorized to edit the settings. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'capability_roles', __( 'You are not authorized to edit role capabilities. Please contact the admin if you received this message in error.', 'connections' ) );

		$instance->add( 'category_duplicate_name', __( 'The category you are trying to create already exists.', 'connections' ) );
		$instance->add( 'category_self_parent', __( 'Category can not be a parent of itself.', 'connections' ) );
		$instance->add( 'category_delete_uncategorized', __( 'The Uncategorized category can not be deleted.', 'connections' ) );
		$instance->add( 'category_update_uncategorized', __( 'The Uncategorized category can not be altered.', 'connections' ) );
		$instance->add( 'category_add_uncategorized', __( 'The Uncategorized category already exists.', 'connections' ) );
		$instance->add( 'category_add_failed', __( 'Failed to add category.', 'connections' ) );
		$instance->add( 'category_update_failed', __( 'Failed to update category.', 'connections' ) );
		$instance->add( 'category_delete_failed', __( 'Failed to delete category.', 'connections' ) );

		$instance->add( 'entry_added_failed', __( 'Entry could not be added.', 'connections' ) );
		$instance->add( 'entry_updated_failed', __( 'Entry could not be updated.', 'connections' ) );

		$instance->add( 'entry_preferred_overridden_address', __( 'Your preferred setting for a address was overridden because another address that you are not permitted to view or edit is set as the preferred address. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'entry_preferred_overridden_phone', __( 'Your preferred setting for a phone was overridden because another phone number that you are not permitted to view or edit is set as the preferred phone number. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'entry_preferred_overridden_email', __( 'Your preferred setting for an email address was overridden because another email address that you are not permitted to view or edit is set as the preferred email address. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'entry_preferred_overridden_im', __( 'Your preferred setting for a IM Network was overridden because another IM Network that you are not permitted to view or edit is set as the preferred IM Network. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'entry_preferred_overridden_social', __( 'Your preferred setting for a social media network was overridden because another social media network that you are not permitted to view or edit is set as the preferred social media network. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'entry_preferred_overridden_link', __( 'Your preferred setting for a link was overridden because another link that you are not permitted to view or edit is set as the preferred link. Please contact the admin if you received this message in error.', 'connections' ) );
		$instance->add( 'entry_preferred_overridden_date', __( 'Your preferred setting for a date was overridden because another date that you are not permitted to view or edit is set as the preferred date. Please contact the admin if you received this message in error.', 'connections' ) );

		$instance->add( 'image_upload_failed', __( 'Image upload failed.', 'connections' ) );
		$instance->add( 'image_uploaded_failed', __( 'Uploaded image could not be saved to the destination folder.', 'connections' ) );
		$instance->add( 'image_profile_failed', __( 'Profile image could not be created and/or saved to the destination folder.', 'connections' ) );
		$instance->add( 'image_entry_failed', __( 'Entry image could not be created and/or saved to the destination folder.', 'connections' ) );
		$instance->add( 'image_thumbnail_failed', __( 'Thumbnail image could not be created and/or saved to the destination folder.', 'connections' ) );

		$instance->add( 'template_install_failed', __( 'The template installation has failed.', 'connections' ) );
		$instance->add( 'template_delete_failed', __( 'The template could not be deleted.', 'connections' ) );

		$instance->add( 'image_path_exists_failed', __( 'Path ../wp-content/connection_images does not seem to exist. Please try deactivating and reactivating Connections.', 'connections' ) );
		$instance->add( 'image_path_writeable_failed', __( 'Path ../wp-content/connection_images does not seem to be writeable.', 'connections' ) );

		$instance->add( 'template_path_exists_failed', __( 'Path ../wp-content/connections_templates does not seem to exist. Please try deactivating and reactivating Connections.', 'connections' ) );
		$instance->add( 'template_path_writeable_failed', __( 'Path ../wp-content/connections_templates does not seem to be writeable.', 'connections' ) );

		$instance->add( 'cache_path_exists_failed', __( 'Path ../wp-content/plugins/connections/cache does not seem to exist. Please try deactivating and reactivating Connections.', 'connections' ) );
		$instance->add( 'cache_path_writeable_failed', __( 'Path ../wp-content/plugins/connections/cache does not seem to be writeable.', 'connections' ) );

		$instance->add( 'home_page_set_failed', __( 'The Connections directory home page has not been set. Please set it now on the Connections : Settings page under the General tab.', 'connections' ) );

		/**
		 * Add the success messages.
		 */
		$instance->add( 'form_entry_delete', __( 'The entry has been deleted.', 'connections' ) );
		$instance->add( 'form_entry_delete_bulk', __( 'Entry(ies) have been deleted.', 'connections' ) );
		$instance->add( 'form_entry_pending', __( 'The entry status have been set to pending.', 'connections' ) );
		$instance->add( 'form_entry_pending_bulk', __( 'Entry(ies) status have been set to pending.', 'connections' ) );
		$instance->add( 'form_entry_approve', __( 'The entry has been approved.', 'connections' ) );
		$instance->add( 'form_entry_approve_bulk', __( 'Entry(ies) have been approved.', 'connections' ) );
		$instance->add( 'form_entry_visibility_bulk', __( 'Entry(ies) visibility have been updated.', 'connections' ) );

		$instance->add( 'category_deleted', __( 'Category(ies) have been deleted.', 'connections' ) );
		$instance->add( 'category_updated', __( 'Category has been updated.', 'connections' ) );
		$instance->add( 'category_added', __( 'Category has been added.', 'connections' ) );

		$instance->add( 'entry_added', __( 'Entry has been added.', 'connections' ) );
		$instance->add( 'entry_added_moderated', __( 'Pending review entry will be added.', 'connections' ) );
		$instance->add( 'entry_updated', __( 'Entry has been updated.', 'connections' ) );
		$instance->add( 'entry_updated_moderated', __( 'Pending review entry will be updated.', 'connections' ) );

		$instance->add( 'image_uploaded', __( 'Uploaded image saved.', 'connections' ) );
		$instance->add( 'image_profile', __( 'Profile image created and saved.', 'connections' ) );
		$instance->add( 'image_entry', __( 'Entry image created and saved.', 'connections' ) );
		$instance->add( 'image_thumbnail', __( 'Thumbnail image created and saved.', 'connections' ) );

		$instance->add( 'role_settings_updated', __( 'Role capabilities have been updated.', 'connections' ) );

		$instance->add( 'template_change_active', __( 'The default active template has been changed.', 'connections' ) );
		$instance->add( 'template_installed', __( 'A new template has been installed.', 'connections' ) );
		$instance->add( 'template_deleted', __( 'The template has been deleted.', 'connections' ) );

		/*
		 * DB update message.
		 */
		$instance->add( 'db_update_required', __( 'Connections database requires updating.', 'connections' ) . ' ' . '<a class=\"button\" href=\"admin.php?page=connections_manage\">' . __( 'START', 'connections' )  . '</a>' );

	}

	/**
	 * Setup the current user ID.
	 *
	 * @access private
	 * @since 0.7.5
	 * @uses wp_get_current_user()
	 * @return void
	 */
	private static function setUserID() {

		if ( ! isset( self::$id ) ) {
			// Setup the current user object
			$current_user = wp_get_current_user();
			self::$id = $current_user->ID;
		}

		return self::$id;
	}

	/**
	 * Display the stored action/error messages.
	 *
	 * @access private
	 * @since 0.7.5
	 * @return (string) The action/error message created to match the admin notices style.
	 */
	private static function display() {

		$messages = self::get();

		if ( ! empty( $messages ) ) {

			foreach ( $messages as $message ) {

				foreach ( $message as $type => $code ) {

					self::create( $type, $code );
				}
			}
		}

		self::reset();
	}

	/**
	 * Create an admin message adding to the admin_notices action hook.
	 *
	 * @access public
	 * @since 0.7.5
	 * @uses add_action()
	 * @param  (string) $type The $type must be either "error" or "success" or "notice".
	 * @param  (string) $message The message to be displayed. || A message code registered in self::init().
	 * @return (string) The name of the lamda function.
	 */
	public static function create( $type , $message ) {

		// Bring a copy of this into scope.
		$instance = self::getInstance();

		// Check to see if $message is one of the registered message codes and if it is, set $message to the actual message rather than the message code.
		if ( 0 < strlen( $instance->get_error_message( $message ) ) ) $message = $instance->get_error_message( $message );

		switch ( $type ) {
			case 'error':
				$lamda = create_function( '' , 'echo "<div id=\"message\" class=\"error\"><p><strong>' . __( 'ERROR', 'connections' ) . ': </strong>' . $message . '</p></div>";' );
				break;

			case 'success':
				$lamda = create_function( '' , 'echo "<div id=\"message\" class=\"updated fade\"><p><strong>' . __( 'SUCCESS', 'connections' ) . ': </strong>' . $message . '</p></div>";' );
				break;

			case 'notice':
				$lamda = create_function( '' , 'echo "<div id=\"message\" class=\"updated fade\"><p><strong>' . __( 'NOTICE', 'connections' ) . ': </strong>' . $message . '</p></div>";' );
				break;

			default:
				$lamda = create_function( '' , 'echo "<div id=\"message\" class=\"updated fade\"><p>' . $message . '</p></div>";' );
				break;
		}

		add_action( 'admin_notices' , $lamda );

		return $lamda;
	}

	/**
	 * Display an action/error message.
	 *
	 * @access public
	 * @since 0.7.5
	 * @return (string) The action/error message created to match the admin notices style.
	 */
	public static function render( $type, $message ) {

		// Bring a copy of this into scope.
		$instance = self::getInstance();

		// Check to see if $message is one of the registered message codes and if it is, set $message to the actual message rather than the message code.
		if ( 0 < strlen( $instance->get_error_message( $message ) ) ) $message = $instance->get_error_message( $message );

		switch ( $type ) {
			case 'error':
				echo '<div id="message" class="error"><p><strong>' . __( 'ERROR', 'connections' ) . ': </strong>' . $message . '</p></div>';
				break;

			case 'success':
				echo '<div id="message" class="updated fade"><p><strong>' . __( 'SUCCESS', 'connections' ) . ': </strong>' . $message . '</p></div>';
				break;

			case 'notice':
				echo '<div id="message" class="updated fade"><p><strong>' . __( 'NOTICE', 'connections' ) . ': </strong>' . $message . '</p></div>';
				break;

			default:
				echo '<div id="message" class="updated fade"><p>' . $message . '</p></div>';
				break;
		}

	}

	/**
	 * Store a custom action/error message.
	 *
	 * @access public
	 * @since 0.7.5
	 * @param  (string) $type The $type must be either "error_runtime" or "success_runtime".
	 * @param  (string) $message The message to be stored.
	 * @return void
	 */
	public static function runtime( $type , $message ) {

		self::store( array( $type => $message ) );
	}

	/**
	 * Store a predefined action/error message.
	 *
	 * @access public
	 * @since 0.7.5
	 * @param (string) $type The $type must be either "error" or "success" or "notice".
	 * @param (string) $code The message code as registered in the constructor.
	 * @return void
	 */
	public static function set( $type , $code ) {

		$messages = self::get();

		switch ( $type ) {
			case 'error':
				// If the error message is already stored, no need to store it twice.
				if ( ! in_array( array( 'error' => $code ) , $messages ) ) self::store( array( 'error' => $code ) );
				break;

			case 'success':
				// If the success message is slready stored, no need to store it twice.
				if ( ! in_array( array( 'success' => $code ) , $messages ) ) self::store( array( 'success' => $code ) );
				break;

			case 'notice':
				// If the notice message is slready stored, no need to store it twice.
				if ( ! in_array( array( 'notice' => $code ) , $messages ) ) self::store( array( 'notice' => $code ) );
				break;
		}

	}

	/**
	 * Store the message in the current user meta.
	 *
	 * @access private
	 * @since 0.7.5
	 * @uses get_user_meta()
	 * @uses update_user_meta()
	 * @param  (string) $message
	 * @return void
	 */
	private static function store( $message ) {

		$user_meta = get_user_meta( self::$id, self::$meta_key, TRUE );

		$user_meta['messages'][] = $message;

		update_user_meta( self::$id, self::$meta_key, $user_meta );
	}

	/**
	 * Get the messages stored in the user meta.
	 *
	 * @access private
	 * @since 0.7.5
	 * @uses get_user_meta()
	 * @return (array)
	 */
	private static function get() {

		$user_meta = get_user_meta( self::$id, self::$meta_key, TRUE );

		if ( ! empty( $user_meta['messages'] ) ) {
			return $user_meta['messages'];
		} else {
			return array();
		}
	}

	/**
	 * Remove any messages stored in the user meta.
	 *
	 * @access public
	 * @since 0.7.5
	 * @uses get_user_meta()
	 * @uses update_user_meta()
	 * @return void
	 */
	public static function reset() {

		$user_meta = get_user_meta( self::$id, self::$meta_key, TRUE );

		if ( isset( $user_meta['messages']) ) unset( $user_meta['messages'] );

		update_user_meta( self::$id, self::$meta_key, $user_meta );
	}

}