<?php

echo("deleting menus from the new site.");
$new_menu_result = \Drupal::entityQuery('menu_link_content')->execute();
$new_menu_storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
$new_menu_multiple = $new_menu_storage->loadMultiple($new_menu_result);  
foreach($new_menu_multiple as $delete_menu){
     echo($delete_menu->uuid()."   ".$delete_menu->getTitle()."   ".$delete_menu->getMenuName()."\n");
     $delete_menu->delete();
}

exec('drush @casdev.cleanparagraphs -y cr', $myoutput);

echo("deleting nodes from new site");
$new_node_result = \Drupal::entityQuery('node')->execute();
$new_nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$new_node_multiple = $new_nodes_storage->loadMultiple($new_node_result);
krsort($new_node_multiple);
foreach($new_node_multiple as $dnid => $delete_node) {
     echo("deleted: ".strval($dnid)."    ".$delete_node->uuid()."   ".$delete_node->getTitle());
     $delete_node->delete();
    \Drupal::cache('menu')->invalidateAll();
    \Drupal::service('plugin.manager.menu.link')->rebuild();
}