<?php 

//here's how to start compiling a list of entities. 

$entities = [];
// Load All nodes.
$result = \Drupal::entityQuery('node')->execute();
$entity_storage = \Drupal::entityTypeManager()->getStorage('node');
$entities = array_merge($entities, $entity_storage->loadMultiple($result));

//probably need to create one array for each entity type so that it can be loaded individually.

/*1. load all entities, save them to an array or several arrays
     you have to retain the node ids
2. run the reinstallation script
3. reload the entities into the site.
*/

/* # entities:
(roughly in order)
paragraphs
nodes
menu_links
blocks
block_content
 */

// code snippet to get all entity types:
// drushl eval "print_r(array_keys(\Drupal::entityTypeManager()->getDefinitions()));"


