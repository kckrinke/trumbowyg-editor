<?php
defined('ABSPATH') or die('asking questions later!');

class TrumbowygEditorSettings
{
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;

  /**
   * Start up
   */
  public function __construct() {
    add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
    add_action( 'admin_init', array( $this, 'page_init' ) );
  }

  /**
   * Add options page
   */
  public function add_plugin_page() {
    // This page will be under "Settings"
    add_options_page(
      'Trumbowyg Editor',
      'Trumbowyg',
      'manage_options',
      'trumbowyg-settings',
      array( $this, 'create_admin_page' )
    );
  }

  /**
   * Options page callback
   */
  public function create_admin_page() {
    // Set class property
    $this->options = get_option( 'trumbowyg_settings' );
    echo '
  <div class="wrap">
    <h1>Trumbowyg</h1>
    <form method="post" action="options.php">
';
    // This prints out all hidden setting fields
    settings_fields( 'trumbowyg_settings_group' );
    do_settings_sections( 'trumbowyg-settings' );
    submit_button();
    echo '
    </form>
  </div>
';
  }

  /**
   * Register and add settings
   */
  public function page_init() {
    register_setting(
      'trumbowyg_settings_group', // Option group
      'trumbowyg_settings', // Option name
      array( $this, 'sanitize' ) // Sanitize
    );

    add_settings_section(
      'trumbowyg_settings_section', // ID
      'Settings', // Title
      array( $this, 'print_section_info' ), // Callback
      'trumbowyg-settings' // Page
    );

    add_settings_field(
      'imageWidthModalEdit',
      'Image Width Modal Edit',
      array( $this, 'cb__imageWidthModalEdit' ), // Callback
      'trumbowyg-settings', // Page
      'trumbowyg_settings_section' // Section
    );

    add_settings_field(
      'imageFloatModalEdit',
      'Image Float Modal Edit',
      array( $this, 'cb__imageFloatModalEdit' ), // Callback
      'trumbowyg-settings', // Page
      'trumbowyg_settings_section' // Section
    );

  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function sanitize( $input ) {
    $new_input = array();

    if( isset( $input['imageWidthModalEdit'] ) )
      $new_input['imageWidthModalEdit'] = (empty( $input['imageWidthModalEdit'] ) === false);

    if( isset( $input['imageFloatModalEdit'] ) )
      $new_input['imageFloatModalEdit'] = (empty( $input['imageFloatModalEdit'] ) === false);

    return $new_input;
  }

  /**
   * Print the Section text
   */
  public function print_section_info() {
  }

  /**
   * Get the settings option array and print one of its values
   */
  public function cb_checkbox($id) {
    printf(
      '<input type="checkbox" id="'.$id.'" name="trumbowyg_settings['.$id.']"%s />',
      isset( $this->options[$id] ) ? ' checked' : ''
    );
  }

  public function cb__imageWidthModalEdit() {
    return $this->cb_checkbox('imageWidthModalEdit');
  }

  public function cb__imageFloatModalEdit() {
    return $this->cb_checkbox('imageFloatModalEdit');
  }
}

if( is_admin() )
  $my_settings_page = new TrumbowygEditorSettings();
