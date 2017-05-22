<?php
namespace MailChimpWidget;

class Settings {
    public static function init() {
        add_action('admin_init', function() {
            register_setting(
                NS_MAILCHIMP_WIDGET,
                NS_MAILCHIMP_WIDGET);

            add_settings_section(
                NS_MAILCHIMP_WIDGET,
                null,
                function() {
                    printf
                        (__("
                            Enter a valid MailChimp API key here to get started. Once you've done that,
                            you can use the MailChimp Widget from the <a href='%s'>Widgets admin page</a>.
                            You will need to have at least one MailChimp list set up before the using the
                            widget.",
                            NS_MAILCHIMP_WIDGET
                        ),
                        get_admin_url(null, 'widgets.php'));
                },
                'mailchimp-widget-settings');

            add_settings_field(
                'api-key',
                __('MailChimp API Key', NS_MAILCHIMP_WIDGET),
                function() {
                    printf('<input
                        class="regular-text"
                        name="ns-mailchimp-widget[api-key]"
                        type="password"
                        value="%s" />', esc_attr(get_option(NS_MAILCHIMP_WIDGET)['api-key']));
                },
                'mailchimp-widget-settings',
                NS_MAILCHIMP_WIDGET);

        });

        add_action('admin_menu', function() {
            add_options_page(
                __('MailChimp Widget Settings', NS_MAILCHIMP_WIDGET),
                __('MailChimp Widget', NS_MAILCHIMP_WIDGET),
                'manage_options',
                'mailchimp-widget-settings',
                function() {
                    printf("
                        <div class=\"wrap\">
                            <h2>%s</h2>
                        </div>
                        <form action=\"options.php\" method=\"post\">",
                        __('MailChimp Widget Settings', NS_MAILCHIMP_WIDGET)
                    );
                    settings_fields(NS_MAILCHIMP_WIDGET);
                    do_settings_sections('mailchimp-widget-settings');
                    submit_button();
                    echo '
                        </form>';
                }
            );
        });
    }
}