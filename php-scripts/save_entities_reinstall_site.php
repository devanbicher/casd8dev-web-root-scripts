<?php 

//here's how to start compiling a list of entities. 

// Load All nodes.
$node_result = \Drupal::entityQuery('node')->execute();
$nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$nodes = $nodes_storage->loadMultiple($node_result);

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

foreach($nodes as $node) {
     $newnode = \Drupal::entityTypeManager()->getStorage('node')->create($node);
} 

/* # entities:
(roughly in order)
file
media
paragraphs
nodes
menu_links
block_content
blocks

 */

// code snippet to get all entity types:
//drushl eval "print_r(array_keys(\Drupal::entityTypeManager()->getDefinitions()));"
//var_dump($entities[5]);


