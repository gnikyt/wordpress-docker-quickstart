<?php
define('DEV', $_SERVER['HTTP_HOST'] == 'localhost:8080');

function asset($path, $echo = true) {
  $path_string = get_template_directory_uri() . "/assets/{$path}";
  if ($echo) {
    echo $path_string;
  } else {
    return $path_string;
  }
}

function register_scripts() {
  wp_register_script(
    'scripts',
    asset('javascripts/scripts.js', false),
    ['jquery'],
    '1.0.0',
    true
  );

  wp_register_script(
    'scripts-vendors',
    asset('javascripts/scripts-vendors.js', false),
    ['scripts', 'jquery'],
    '1.0.0',
    true
  );

  wp_enqueue_script('scripts');
  wp_enqueue_script('scripts-vendors');
}

function register_styles() {
  wp_register_style(
    'styles',
    asset('stylesheets/styles.css', false),
    [],
    DEV ? time() : '1.0.0'
  );

  wp_enqueue_style('styles');
}

function remove_width_attribute($html) {
  return preg_replace('/(width|height)="\d*"\s/', '', $html);
}

function remove_thumbnail_dimensions($html) {
  return preg_replace('/(width|height)=\"\d*\"\s/', '', $html);
}

function add_slug_to_body_class($classes) {
  global $post;

  if (is_home()) {
    $key = array_search('blog', $classes);
    if ($key > -1) {
      unset($classes[$key]);
    }
  } elseif (is_page()) {
    $classes[] = sanitize_html_class($post->post_name);
  } elseif (is_singular()) {
    $classes[] = sanitize_html_class($post->post_name);
  }

  return $classes;
}

function disable_wp_emojicons() {
  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}


function theme_options($wp_customize) {
  /** Social Section **/
  $wp_customize->add_section('social_section', [
    'title' => 'Social Options',
  ]);

  $socials = ['twitter', 'facebook', 'linkedin', 'youtube'];
  foreach ($socials as $social) {
    $wp_customize->add_setting("{$social}_url", [
      'default' => "https://{$social}.com/",
    ]);
    $wp_customize->add_control("{$social}_url", [
      'label' => $social,
      'section' => 'social_section',
      'type' => 'text',
    ]);
  }

  $wp_customize->add_setting('mail_url', [
    'default' => 'you@domain.com',
  ]);
  $wp_customize->add_control('mail_url', [
    'label' => 'mail',
    'section' => 'social_section',
    'type' => 'text',
  ]);
}

function main_nav() {
  wp_nav_menu([
    'theme_location'  => 'header-menu',
    'menu'            => '',
    'container'       => '',
    'container_class' => 'menu-{menu slug}-container',
    'container_id'    => '',
    'menu_class'      => 'menu',
    'menu_id'         => '',
    'echo'            => true,
    'fallback_cb'     => 'wp_page_menu',
    'before'          => '',
    'after'           => '',
    'link_before'     => '',
    'link_after'      => '',
    'items_wrap'      => '<ul>%3$s</ul>',
    'depth'           => 0,
    'walker'          => ''
  ]);
}

function register_menus() {
  register_nav_menus([
    'header-menu' => 'Header Menu'
  ]);
}

add_theme_support('post-thumbnails');
add_image_size('large', 700, '', true); // Large Thumbnail
add_image_size('medium', 250, '', true); // Medium Thumbnail
add_image_size('small', 120, '', true); // Small Thumbnail

add_theme_support('automatic-feed-links');

add_action('init', 'register_scripts');
add_action('init', 'disable_wp_emojicons');
add_action('init', 'register_menus'); 
add_action('wp_enqueue_scripts', 'register_styles');
add_action('customize_register', 'theme_options');

remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version

add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('post_thumbnail_html', 'remove_width_attribute', 10 ); // Remove width and height dynamic attributes to post images
add_filter('image_send_to_editor', 'remove_width_attribute', 10 ); // Remove width and height dynamic attributes to post images
add_filter('emoji_svg_url', '__return_false');

remove_filter('the_excerpt', 'wpautop');
