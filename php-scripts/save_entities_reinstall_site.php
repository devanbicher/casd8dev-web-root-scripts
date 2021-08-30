<?php 

//here's how to start compiling a list of entities. 

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\menu_link_content\Entity\MenuLinkContent;

// Load All nodes.
echo("loading nodes");
$node_result = \Drupal::entityQuery('node')->execute();
$nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$node_multiple = $nodes_storage->loadMultiple($node_result);

echo("Loading paragraphs");
$par_result = \Drupal::entityQuery('paragraph')->execute();
$par_storage = \Drupal::entityTypeManager()->getStorage('paragraph');
$par_multiple = $par_storage->loadMultiple($par_result);

echo("loading menus");
$menu_result = \Drupal::entityQuery('menu_link_content')->execute();
$menu_storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
$menu_multiple = $menu_storage->loadMultiple($menu_result);

/*
$first_nid = array_keys($node_multiple)[0];
print_r($first_nid);
echo("\n");
print_r($node_multiple[$first_nid]->toArray());
*/

//probably need to create one array for each entity type so that it can be loaded individually.

/*1. load all entities, save them to an array or several arrays
     you have to retain the node ids
     1.a. 
2. run the reinstallation script
3. reload the entities into the site.
*/


$myoutput = [];
exec('sh /var/www/casdev/web/scripts/bash-scripts/cas_department_install4overwrite.sh cleanparagraphs',$myoutput);

foreach($myoutput as $output) {
     echo($output."\n");
}

#delete nodes that already from the new profile.
echo("deleting nodes from new site");
$new_node_result = \Drupal::entityQuery('node')->execute();
//$new_nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
//$new_node_multiple = $new_nodes_storage->loadMultiple($new_node_result);
$multipleloaded = Node::loadMultiple($new_node_result);
foreach($multipleloaded as $delnid => $delete_node) {
     print_r($delete_node);
     $delete_node->delete();
}

#paragraphs
echo("recreating paragraphs");
foreach($par_multiple as $pid => $one_par) {
     print_r($one_par->toArray());
     $newpar = Paragraph::create($one_par->toArray());
     $newpar->save();
}

#node 
echo("recreating nodes");
foreach($node_multiple as $nid => $one_node) {
     //print_r($one_node->toArray());
     $newnode = Node::create($one_node->toArray());
     $newnode->save();
} 

/*
#menu
echo("recreating menus");
foreach($menu_multiple as $menu => $one_menu) {
     print_r($one_menu->toArray());
     $newmenu = MenuLinkContent::create($one_menu->toArray());
     $newmenu->save();
}
*/

//MenuLinkContent::create(


/* # entities:
(roughly in order)
file
media
paragraphs
X nodes
menu_links
block_content
blocks

 */

// code snippet to get all entity types:
//drushl eval "print_r(array_keys(\Drupal::entityTypeManager()->getDefinitions()));"
//var_dump($entities[5]);


