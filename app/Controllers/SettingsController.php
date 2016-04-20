<?php namespace BongardeTracker\Controllers;
class SettingsController
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin',
            'Bongarde Tracker Settings',
            'manage_options',
            'bongardetracker-index',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'bongarde_tracker_options' );
        ?>
        <div class="wrap">
            <h2>Bongarde Tracker Settings</h2>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
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
        register_setting(
            'my_option_group', // Option group
            'bongarde_tracker_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );

        add_settings_field(
            'totango_sid', // ID
            'Totango ID', // Title
            array( $this, 'totango_sid_callback' ), // Callback
            'my-setting-admin', // Page
            'setting_section_id' // Section
        );

        add_settings_field(
            'product_name',
            'Product Name',
            array( $this, 'product_name_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );

        add_settings_field(
            'sf_login',
            'Salesforce Login',
            array( $this, 'sf_login_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );

        add_settings_field(
            'sf_pass',
            'Salesforce Password',
            array( $this, 'sf_pass_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );

        add_settings_field(
            'sf_token',
            'Salesforce Auth Token',
            array( $this, 'sf_token_callback' ),
            'my-setting-admin',
            'setting_section_id'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {

        $new_input = array();
        if( isset( $input['id_number'] ) )
            $new_input['id_number'] = absint( $input['id_number'] );

        if( isset( $input['title'] ) )
            $new_input['title'] = sanitize_text_field( $input['title'] );

        //return $new_input;
        return $input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function totango_sid_callback()
    {
        printf(
            '<input type="text" id="totango_sid" name="bongarde_tracker_options[totango_sid]" value="%s" />',
            isset( $this->options['totango_sid'] ) ? esc_attr( $this->options['totango_sid']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function product_name_callback()
    {
        printf(
            '<input type="text" id="name" name="bongarde_tracker_options[product_name]" value="%s" />',
            isset( $this->options['product_name'] ) ? esc_attr( $this->options['product_name']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function sf_login_callback()
    {
        printf(
            '<input type="text" id="sf_login" name="bongarde_tracker_options[sf_login]" value="%s" />',
            isset( $this->options['sf_login'] ) ? esc_attr( $this->options['sf_login']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function sf_pass_callback()
    {
        printf(
            '<input type="password" id="sf_pass" name="bongarde_tracker_options[sf_pass]" value="%s" />',
            isset( $this->options['sf_pass'] ) ? esc_attr( $this->options['sf_pass']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function sf_token_callback()
    {
        printf(
            '<input type="text" id="sf_token" name="bongarde_tracker_options[sf_token]" value="%s" />',
            isset( $this->options['sf_token'] ) ? esc_attr( $this->options['sf_token']) : ''
        );
    }
}
