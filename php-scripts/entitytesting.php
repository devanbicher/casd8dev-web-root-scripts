<?php 

//here's how to start compiling a list of entities. 

// Load All nodes.
$node_result = \Drupal::entityQuery('node')->execute();
$nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$nodes = $nodes_storage->loadMultiple($node_result);

foreach($nodes as $node) {
    print_r($node);
}