<?php

use Drupal\block_content\Entity\BlockContent;
//use Drupal\block\Entity\Block;

echo("deleting menus from the new site.\n");
$new_menu_result = \Drupal::entityQuery('menu_link_content')->execute();
$new_menu_storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
$new_menu_multiple = $new_menu_storage->loadMultiple($new_menu_result);  
foreach($new_menu_multiple as $delete_menu){
     echo($delete_menu->uuid()."   ".$delete_menu->getTitle()."   ".$delete_menu->getMenuName()."\n");
     $delete_menu->delete();
}

#hopefully we don'tneed that cache clear. and if we do we will have to find a way to run it internally.
#exec('drush @casdev.cleanparagraphs -y cr', $myoutput);

echo("deleting nodes from new site\n");
$new_node_result = \Drupal::entityQuery('node')->execute();
$new_nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$new_node_multiple = $new_nodes_storage->loadMultiple($new_node_result);
foreach($new_node_multiple as $dnid => $delete_node) {
     echo("deleted: ".strval($dnid)."    ".$delete_node->uuid()."   ".$delete_node->getTitle()."\n");
     $delete_node->delete();
}

echo("deleting Terms from new site\n");
$new_term_result = \Drupal::entityQuery('taxonomy_term')->execute();
$new_terms_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
$new_term_multiple = $new_terms_storage->loadMultiple($new_term_result);
foreach($new_term_multiple as $tid => $delete_term) {
     echo("deleted: ".strval($tid)."    ".$delete_term->uuid()."   ".$delete_term->getName()."\n");
     $delete_term->delete();
}

\Drupal::cache('menu')->invalidateAll();
\Drupal::service('plugin.manager.menu.link')->rebuild();

echo("deleting contact block_content (and subsequently the block) \n");
$del_block_content = BlockContent::load(1);
$del_block_content->delete();
