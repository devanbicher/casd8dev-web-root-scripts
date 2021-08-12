<?php

#field.storage.paragraph.field_callout_block_deco

$check_callout_deco_list = \Drupal::configFactory()->getEditable('field.storage.paragraph.field_callout_block_deco');

var_dump($check_callout_deco_list->get('settings.allowed_values'));