<?php
require __DIR__ . '/constants.php';
require __DIR__ . '/vendor/autoload.php';

delete_option(NS_MAILCHIMP_WIDGET);
unregister_widget('MailChimpWidget\\Widget');
