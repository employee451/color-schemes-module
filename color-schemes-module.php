<?php
/*
Plugin Name: Color Schemes Module
Plugin URI: http://employee451.com/
Description: This plugin adds support for color scheme settings for Employee 451 Pixelarity themes.
Author: Employee 451
Author URI: http://employee451.com/
Version: 1.0
GitHub Plugin URI: employee451/color-schemes-module
*/

$color_schemes_module_enabled = true;

// Generate Color Schemes Function
function generate_color_schemes( $default_hex_color, $color_schemes ) {
  add_action( 'customize_register', function( $wp_customize ) use( $default_hex_color, $color_schemes ) {
    /* Settings & Controls */
      // Setting: Select Color Scheme
      $wp_customize->add_setting( 'color_schemes_module_selector', array(
        'capability'           => 'edit_theme_options',
        'default'              => $default_hex_color,
        'sanitize_callback'    => 'color_schemes_module_sanitize_select'
      ) );
      // Control: Select Color Scheme
      $wp_customize->add_control( 'color_schemes_module_selector', array(
        'type'      => 'select',
        'section'   => 'colors',
        'label'     => __( 'Theme Color Scheme', 'color-schemes-module' ),
        'choices'   => $color_schemes
      ) );

      // Setting: Enable Custom Color Scheme
      $wp_customize->add_setting( 'color_schemes_module_enable_custom', array(
        'default'              => false,
        'sanitize_callback'    => 'absint',
      ) );
      // Control: Enable Custom Color Scheme
      $wp_customize->add_control( 'color_schemes_module_enable_custom', array(
        'label'       => __( 'Enable Custom Color Scheme', 'color-schemes-module' ),
        'section'     => 'colors',
        'type'        => 'checkbox'
      ) );

      // Setting: Custom Color Scheme
      $wp_customize->add_setting( 'color_schemes_module_custom', array(
        'sanitize_callback'    => 'sanitize_hex_color',
        'default'              => '#' . $default_hex_color
      ) );
      // Control: Custom Color Scheme
      $wp_customize->add_control(
        new WP_Customize_Color_Control(
          $wp_customize,
          'color_schemes_module_custom',
          array(
            'label'     => __( 'Custom Color Scheme', 'color-schemes-module' ),
            'section'   => 'colors',
            'settings'  => 'color_schemes_module_custom',
            'active_callback' => 'color_scheme_module_custom_is_enabled'
          )
        )
      );

    /* Customizer Functions */
      // Active Callback Functions
        function color_scheme_module_custom_is_enabled() {
          return get_theme_mod( 'color_schemes_module_enable_custom', false );
        }

      // Sanitizer Functions
        function color_schemes_module_sanitize_select( $input, $setting ) {
          // Ensure input is a slug.
          $input = sanitize_key( $input );

          // Get list of choices from the control associated with the setting.
          $choices = $setting->manager->get_control( $setting->id )->choices;

          // If the input is a valid key, return it; otherwise, return the default.
          return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
        }
  } );

  /* Hex to RGB Color Value Function */
  function hex2rgb( $color ) {
    list( $r, $g, $b ) = array(
      $color[0].$color[1],
      $color[2].$color[3],
      $color[4].$color[5]
    );
    $r = hexdec( $r );
    $g = hexdec( $g );
    $b = hexdec( $b );
    return $r . ', ' . $g . ', ' . $b;
  }

  function get_color_scheme_values() {
    $color_scheme_values = array();

    if( get_theme_mod( 'color_schemes_module_enable_custom' ) && get_theme_mod( 'color_schemes_module_custom' ) ) {
      $color_scheme_values['color_scheme'] = str_replace( '#', '', get_theme_mod( 'color_schemes_module_custom', '#' . $default_hex_color ) );
      $color_scheme_values['rgb_color_scheme'] = hex2rgb( $color_scheme_values['color_scheme'] );
    } else {
      $color_scheme_values['color_scheme'] = get_theme_mod( 'color_schemes_module_selector', $default_hex_color );
      $color_scheme_values['rgb_color_scheme'] = hex2rgb( $color_scheme_values['color_scheme'] );
    }

    return $color_scheme_values;
  }
}
