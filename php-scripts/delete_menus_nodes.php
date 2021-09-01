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

exec('drush @casdev.cleanparagraphs -y cr', $myoutput);

echo("deleting nodes from new site\n");
$new_node_result = \Drupal::entityQuery('node')->execute();
$new_nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$new_node_multiple = $new_nodes_storage->loadMultiple($new_node_result);
krsort($new_node_multiple);
foreach($new_node_multiple as $dnid => $delete_node) {
     echo("deleted: ".strval($dnid)."    ".$delete_node->uuid()."   ".$delete_node->getTitle()."\n");
     $delete_node->delete();
    \Drupal::cache('menu')->invalidateAll();
    \Drupal::service('plugin.manager.menu.link')->rebuild();
}

echo("deleting contact block_content\n");
$del_block_content = BlockContent::load(1);
$del_block_content->delete();

/*
echo("loading blocks\n");
$new_blocks_result = \Drupal::entityQuery('block')
->condition('plugin','block_content','STARTS_WITH')
->condition('id','stable_footeraddress','<>')
->execute();
$new_blocks_storage = \Drupal::entityTypeManager()->getStorage('block');
$new_blocks_multiple = $new_blocks_storage->loadMultiple($new_blocks_result);

foreach($new_blocks_multiple as $bid => $one_block){
    echo($one_block->id()."   ".$one_block->uuid()."\n");
}

echo('deleting footeraddress block');
$delete_block = Block::load('footeraddress');
$delete_block->delete();
*/