<?php
namespace MailChimpWidget;
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Settings {
    public static function init() {
        add_action('admin_init', function() {
            register_setting(
                'ns-mailchimp-widget',
                'ns-mailchimp-widget');

            add_settings_section(
                'ns-mailchimp-widget',
                null,
                function() {
                    printf(
                        esc_html__("
                            Enter a valid MailChimp API key here to get started. Once you've done that,
                            you can use the MailChimp Widget from the %sWidgets admin page%s.
                            You will need to have at least one MailChimp list set up before the using the
                            widget.",
                            'ns-mailchimp-widget'
                        ),
                        sprintf(
                            '<a href="%s">',
                            get_admin_url(null, 'widgets.php')
                        ),
                        '</a>'
                    );
                },
                'mailchimp-widget-settings');

            add_settings_field(
                'api-key',
                __('MailChimp API Key', 'ns-mailchimp-widget'),
                function() {
                    printf('<input
                        class="regular-text"
                        name="ns-mailchimp-widget[api-key]"
                        type="password"
                        value="%s" />', esc_attr(get_option('ns-mailchimp-widget')['api-key']));
                },
                'mailchimp-widget-settings',
                'ns-mailchimp-widget');

            add_settings_field(
                'styles',
                __('Widget Styles', 'ns-mailchimp-widget'),
                function() {
                    printf('<textarea
                        class="large-text code"
                        cols="50"
                        name="ns-mailchimp-widget[styles]"
                        rows="10">%s</textarea>', esc_textarea(get_option('ns-mailchimp-widget')['styles']));
                },
                'mailchimp-widget-settings',
                'ns-mailchimp-widget');

        });

        add_action('admin_menu', function() {
            add_options_page(
                __('MailChimp Widget Settings', 'ns-mailchimp-widget'),
                __('MailChimp Widget', 'ns-mailchimp-widget'),
                'manage_options',
                'mailchimp-widget-settings',
                function() {
                    printf('
                        <div class="wrap">
                            <h2>%s</h2>
                        </div>
                        <form action="options.php" method="post">',
                       esc_html__('MailChimp Widget Settings', 'ns-mailchimp-widget')
                    );
                    settings_fields('ns-mailchimp-widget');
                    do_settings_sections('mailchimp-widget-settings');
                    submit_button();
                    echo '
                        </form>';
                }
            );
        });
    }
}
