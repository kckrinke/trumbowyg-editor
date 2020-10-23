<?php
/**
 * Classic Editor
 *
 * Plugin Name: Trumbowyg Editor
 * Plugin URI:  https://wordpress.org/plugins/trumbowyg-editor/
 * Description: Enables the Trumbowyg editor in WordPress.
 * Version:     1.0
 * Author:      Kevin C. Krinke
 * Author URI:  https://github.com/kckrinke/trumbowyg-editor/
 * License:     GPLv2
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: trumbowyg-editor
 * Domain Path: /languages
 * Requires at least: 4.9
 * Requires PHP: 5.2.4
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation. You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if (defined('WP_DEBUG') && WP_DEBUG) {
  define('TRUMBOWYG_EDITOR_VERSION',md5(time()));
} else {
  define('TRUMBOWYG_EDITOR_VERSION','1.0.0');
}

include_once(dirname(__FILE__).'/includes/class--trumbowygeditorsettings.php');

// disable rich text editor for all users
add_action('init','trumbowyg_editor__init',100);
function trumbowyg_editor__init() {
  add_filter('user_can_richedit', '__return_false', 100);
  add_filter('use_block_editor_for_post', '__return_false', 100);
  remove_action('try_gutenberg_panel', 'wp_try_gutenberg_panel');
}

add_filter('wp_editor_settings', 'trumbowyg_editor__editor_settings', 100);
function trumbowyg_editor__editor_settings($settings) {
  $settings['quicktags'] = false;
  return $settings;
}

add_action('admin_enqueue_scripts', 'trumbowyg_editor__admin_enqueue_scripts', 100);
function trumbowyg_editor__admin_enqueue_scripts() {
  wp_enqueue_media();
  $url = plugin_dir_url( __FILE__ );
  $turl = $url . "trumbowyg/dist";
  wp_enqueue_script('trumbowyg-js', "{$turl}/trumbowyg.min.js");
  wp_enqueue_style('trumbowyg-css', "{$turl}/ui/trumbowyg.min.css");
  /* 
   *   wp_enqueue_script('trumbowyg-cleanpaste', "{$turl}/plugins/cleanpaste/trumbowyg.cleanpaste.min.js", array('trumbowyg-js'));
   *   wp_enqueue_script('trumbowyg-preformatted', "{$turl}/plugins/preformatted/trumbowyg.preformatted.min.js", array('trumbowyg-js'));
   *   wp_enqueue_script('trumbowyg-specialchars', "{$turl}/plugins/specialchars/trumbowyg.specialchars.min.js", array('trumbowyg-js'));
   *   wp_enqueue_style('trumbowyg-specialchars-css', "{$turl}/plugins/specialchars/ui/trumbowyg.specialchars.min.css");*/
}

add_action('admin_footer', 'trumbowyg_editor__admin_head');
function trumbowyg_editor__admin_head() {
  echo '
<style>
.wp-editor-expand #wp-content-editor-tools { border: none; }
.wp-editor-expand #wp-content-editor-container { border: none; background: none; }
</style>
';
}

add_action('admin_footer', 'trumbowyg_editor__admin_footer');
function trumbowyg_editor__admin_footer() {
  $options = get_option( 'trumbowyg_settings' );
  echo '
<script>
(function($){
      $("textarea.wp-editor-area").trumbowyg({
        btnsDef: {
          extras: {
            dropdown: ["superscript","subscript","preformatted","removeformat"],
            title: "Extras",
            ico: "removeformat",
            hasIcon: true,
          },
          blocks: {
            dropdown: ["p","h1","h2","h3","h4","h5","h6"],
            title: "Sections",
            ico: "p",
            hasIcon: true,
          },
        },
        btns: [
          ["bold","italic","underline"],
          ["justifyLeft","justifyCenter","justifyRight"],
          ["unorderedList","orderedList"],
          ["extras","blocks","link"],
          ["insertImage"],
          ["viewHTML","fullscreen"]
        ],
        hideButtonTexts: false,
        autogrow: false,
        autogrowOnEnter: false,
        resetCss: true,
        semantic: true,
        semanticKeepAttributes: false,
        tabToIndent: true,
        imageWidthModalEdit: '.(empty($options['imageWidthModalEdit']) ? 'false' : 'true').',
        imageFloatModalEdit: '.(empty($options['imageFloatModalEdit']) ? 'false' : 'true').',
      });
})(jQuery);
</script>';
}
