<?php
/**
 * Plugin Name: Show Adsense
 * Author: wwbewbe
 * Plugin URI: https://github.com/wwbewbe/show-adsense
 * Description: Show Ad Code from settings.
 * Version: 0.9
 * Author URI: http://wwbewbe.com/
 * Text Domain: show-adsense
 * Domain Path: /languages
 * @package Show Adsense
 */

$showadsense = new ShowAdsense();

/**
 * Class ShowAdsense
 */
class ShowAdsense
{
  /**
   * Shortcode tag name.
   * @var string
   */
  private $shortcode_tag  = 'showad';

  /**
     * Holds the values to be used in the fields callbacks
     */
  private $adcode_1;
  private $adoption_1;
  private $adcode_2;
  private $adoption_2;
  private $adcode_header;
  private $adoption_header;
  private $adcode_footer;
  private $adoption_footer;

  /**
   * Start up
   */
  public function __construct()
  {
  add_action( 'wp_enqueue_scripts', array( $this, 'plugin_enqueue_scripts' ) );

  add_action( 'admin_menu', array( $this, 'add_setting_page' ) );
  add_action( 'admin_init', array( $this, 'page_init' ) );
  add_action( 'admin_init', array( $this, 'load_textdomain' ) );
  add_filter( 'the_content', array( $this, 'add_adcode' ) );

  add_shortcode( $this->get_shortcode_tag(), array( $this, 'shortcode' ) );
  }

  /**
   * Plugin enqueue scripts
   */
  public function plugin_enqueue_scripts()
  {
    wp_enqueue_style( 'show-adsense-style', plugins_url( 'style.css', __FILE__ ), array(), date('U') );
  }

  /**
   * Add options page
   */
  public function add_setting_page()
  {
      // This page will be under "Settings"
      add_options_page(
          'Settings Admin',
          'Adsense Settings',
          'manage_options',
          'ad-setting-admin',
          array( $this, 'create_admin_page' )
      );
  }

  /**
   * Options page callback
   */
  public function create_admin_page()
  {
    // Set class property
    $this->adcode_1    = get_option( 'adcode_1' );
    $this->adoption_1  = get_option( 'adoption_1' );
    $this->adcode_2    = get_option( 'adcode_2' );
    $this->adoption_2  = get_option( 'adoption_2' );
    $this->adcode_header  = get_option( 'adcode_header' );
    $this->adoption_header  = get_option( 'adoption_header' );
    $this->adcode_footer  = get_option( 'adcode_footer' );
    $this->adoption_footer  = get_option( 'adoption_footer' );
    ?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2>Show Adsense Settings</h2>
        <form method="post" action="options.php">
        <?php
            // This prints out all hidden setting fields
            settings_fields( 'ad_option_group' );
            do_settings_sections( 'ad-setting-admin' );
            submit_button();
        ?>
        </form>
    </div>
    <?php
  }

  /**
   * Register and add settings
   */
  public function page_init()
  {
    add_settings_section(
      'ad_setting_section', // ID
      'Adsense Settings',   // Title
      array( $this, 'print_section_info' ), // Callback
      'ad-setting-admin'    // Page
    );

    /**
     * adcode_1 by Text Area
     */
    register_setting(
      'ad_option_group', // Option group
      'adcode_1' // Option name
    );
    add_settings_field(
      'adcode_1',
      'Ad Code 1',
      array( $this, 'adcode_1_callback' ),
      'ad-setting-admin',
      'ad_setting_section'
    );

    /**
     * adoption_1 by Check Box
     */
    register_setting(
      'ad_option_group', // Option group
      'adoption_1' // Option name
    );
    add_settings_field(
      'adoption_1',
      'Show Ad 1 (ON/OFF)',
      array( $this, 'adoption_1_callback' ),
      'ad-setting-admin',
      'ad_setting_section'
    );

    /**
     * adcode_2 by Text Area
     */
    register_setting(
      'ad_option_group', // Option group
      'adcode_2' // Option name
    );
    add_settings_field(
      'adcode_2',
      'Ad Code 2',
      array( $this, 'adcode_2_callback' ),
      'ad-setting-admin',
      'ad_setting_section'
    );

    /**
     * adoption_2 by Check Box
     */
    register_setting(
      'ad_option_group', // Option group
      'adoption_2' // Option name
    );
    add_settings_field(
      'adoption_2',
      'Show Ad 2 (ON/OFF)',
      array( $this, 'adoption_2_callback' ),
      'ad-setting-admin',
      'ad_setting_section'
    );

    /**
     * adcode_header by Text Area
     */
    register_setting(
      'ad_option_group', // Option group
      'adcode_header' // Option name
    );
    add_settings_field(
      'adcode_header',
      'Ad Code Header',
      array( $this, 'adcode_header_callback' ),
      'ad-setting-admin',
      'ad_setting_section'
    );

    /**
     * adoption_header by Check Box
     */
    register_setting(
      'ad_option_group', // Option group
      'adoption_header' // Option name
    );
    add_settings_field(
      'adoption_header',
      'Show Ad Header (ON/OFF)',
      array( $this, 'adoption_header_callback' ),
      'ad-setting-admin',
      'ad_setting_section',
      array(
        'options'  => array(
          'show'  => 'header ad show or not',
          'post'  => 'header ad show in post or not',
          )
      )
    );

    /**
     * adcode_footer by Text Area
     */
    register_setting(
      'ad_option_group', // Option group
      'adcode_footer' // Option name
    );
    add_settings_field(
      'adcode_footer',
      'Ad Code Footer',
      array( $this, 'adcode_footer_callback' ),
      'ad-setting-admin',
      'ad_setting_section'
    );

    /**
     * adoption_footer by Check Box
     */
    register_setting(
      'ad_option_group', // Option group
      'adoption_footer' // Option name
    );
    add_settings_field(
      'adoption_footer',
      'Show Ad Footer (ON/OFF)',
      array( $this, 'adoption_footer_callback' ),
      'ad-setting-admin',
      'ad_setting_section',
      array(
        'options'  => array(
          'show'  => 'footer ad show or not',
          'post'  => 'footer ad show in post or not',
        )
      )
    );

  }

  /**
   * Print the Section text
   */
    public function print_section_info()
    {
      print 'Enter your settings below:';
    }

  /**
   * Get the settings option and print its values (Ad Code 1)
   */
  public function adcode_1_callback()
  {
    printf(
        '<textarea id="adcode_1" name="adcode_1" class="regular-text">%s</textarea>',
        isset( $this->adcode_1 ) ? esc_attr( $this->adcode_1 ) : ''
    );
  }

  /**
   * Get the settings option and print its values (Ad Option 1)
   */
  public function adoption_1_callback()
  {
    echo '<input type="checkbox" id="adoption_1" name="adoption_1" value="1" ' . checked( 1, $this->adoption_1, false ) . ' /> ad 1 show or not.';
  }

  /**
   * Get the settings option and print its values (Ad Code 2)
   */
  public function adcode_2_callback()
  {
    printf(
        '<textarea id="adcode_2" name="adcode_2" class="regular-text">%s</textarea>',
        isset( $this->adcode_2 ) ? esc_attr( $this->adcode_2 ) : ''
    );
  }

  /**
   * Get the settings option and print its values (Ad Option 2)
   */
  public function adoption_2_callback()
  {
    echo '<input type="checkbox" id="adoption_2" name="adoption_2" value="1" ' . checked( 1, $this->adoption_2, false ) . ' /> ad 2 show or not.';
  }

  /**
   * Get the settings option and print its values (Ad Code Header)
   */
  public function adcode_header_callback()
  {
    printf(
        '<textarea id="adcode_header" name="adcode_header" class="regular-text">%s</textarea>',
        isset( $this->adcode_header ) ? esc_attr( $this->adcode_header ) : ''
    );
  }

  /**
   * Get the settings option and print its values (Ad Option Header)
   */
  public function adoption_header_callback( $args )
  {
    $optname  = 'adoption_header';
    $html    = '';
    foreach ( $args['options'] as $val => $title ) {
      if (isset($this->adoption_header) && is_array($this->adoption_header)) {
        $checked = in_array($val, $this->adoption_header) ? 'checked="checked"' : '';
        $html .= sprintf( '<input type="checkbox" id="%2$s" name="%1$s[%2$s]" value="%2$s" %3$s />', $optname, $val, $checked );
      } else {
        $html .= sprintf( '<input type="checkbox" id="%2$s" name="%1$s[%2$s]" value="%2$s" />', $optname, $val );
      }
      $html .= sprintf( '<label for="%1$s[%2$s]"> %3$s</label><br />', $optname, $val, $title );
    }

    $html .= sprintf( '<span class="description"> %s</label>', 'Set adsense code show or not in header' );

    echo $html;
  }

  /**
   * Get the settings option and print its values (Ad Code Footer)
   */
  public function adcode_footer_callback()
  {
    printf(
        '<textarea id="adcode_footer" name="adcode_footer" class="regular-text">%s</textarea>',
        isset( $this->adcode_footer ) ? esc_attr( $this->adcode_footer ) : ''
    );
  }

  /**
   * Get the settings option and print its values (Ad Option Footer)
   */
  public function adoption_footer_callback( $args )
  {
    $optname  = 'adoption_footer';
    $html    = '';
    foreach ( $args['options'] as $val => $title ) {
      if (isset($this->adoption_footer) && is_array($this->adoption_footer)) {
        $checked = in_array($val, $this->adoption_footer) ? 'checked="checked"' : '';
        $html .= sprintf( '<input type="checkbox" id="%2$s" name="%1$s[%2$s]" value="%2$s" %3$s />', $optname, $val, $checked );
      } else {
        $html .= sprintf( '<input type="checkbox" id="%2$s" name="%1$s[%2$s]" value="%2$s" />', $optname, $val );
      }
      $html .= sprintf( '<label for="%1$s[%2$s]"> %3$s</label><br />', $optname, $val, $title );
    }

    $html .= sprintf( '<span class="description"> %s</label>', 'Set adsense code show or not in footer' );

    echo $html;
  }

  /**
   * Get shortcode tag.
   *
   * @return mixed|void
   */
  private function get_shortcode_tag()
  {
    return apply_filters( 'ad_shortcode_tag', $this->shortcode_tag );
  }

  /**
   * Load textdomain.
   */
  public function load_textdomain()
  {
    load_plugin_textdomain(
      'show-adsense',
      false,
      plugin_basename( dirname( __FILE__ ) ) . '/languages'
    );
  }

  /**
   * Output tags at footer.
   * @param array $params ID.
   * @return string|void
   */
  public function shortcode( $params = array() )
  {
    $params = shortcode_atts( array(
              'id' => '1', // Ad Code #
            ), $params, $this->shortcode_tag );

    $title = '<div class="adsense-title">'.esc_html(__('Sponsored Links', 'show-adsense')).'</div>';
    $adcode = get_option( sprintf( 'adcode_%s', $params['id'] ) );
    $adoption = sprintf( 'adoption_%s', $params['id'] );
    $flag = get_option( $adoption );
    if ( is_array( $flag ) ) {
      if ( ( !empty( $flag['show'] ) ) && !empty( $adcode ) ) {
        return $title.'<div class="adsense-code">'.$adcode.'</div>';
      }
    } elseif ( !empty( $flag ) && !empty( $adcode ) ) {
      return $title.'<div class="adsense-code">'.$adcode.'</div>';
    }
    return;
  }

  /**
   * Add adcode in post content
   */
  public function add_adcode( $content )
  {
    $title = '<div class="adsense-title">'.esc_html(__('Sponsored Links', 'show-adsense')).'</div>';
    $adcode_header = get_option( 'adcode_header' );
    $adoption_header = get_option( 'adoption_header' );
    $adcode_footer = get_option( 'adcode_footer' );
    $adoption_footer = get_option( 'adoption_footer' );
    if ( is_single() ) {
      if ( !empty( $adoption_header['post'] ) && !empty( $adcode_header ) ) {
        $content = $title.'<div class="adsense-code">'.$adcode_header.'</div>'.$content;
      }
      if ( !empty( $adoption_footer['post'] ) && !empty( $adcode_footer ) ) {
        $content .= $title.'<div class="adsense-code">'.$adcode_footer.'</div>';
      }
    }
    return $content;
  }
}
