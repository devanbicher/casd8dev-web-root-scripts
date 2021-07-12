<?php

$update_disable_messages = \Drupal::configFactory()->getEditable('disable_messages.settings');
$update_disable_messages->set('disable_messages_page_filter_paths',"admin/reports/dblog\r\nadmin/reports/updates\r\nadmin/reports/status");
$update_disable_messages->set('disable_messages_ignore_patterns',".*There are security updates available for one or more of your modules or themes.*\r\n.*There is a security update available for your version of Drupal.*");
$update_disable_messages->set('disable_messages_ignore_regex', array("/^.*There are security updates available for one or more of your modules or themes.*$/i" , '/^.*There is a security update available for your version of Drupal.*$/i'));
$update_disable_messages->save(TRUE);

