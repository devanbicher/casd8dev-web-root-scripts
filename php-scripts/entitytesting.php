<?php 

//here's how to start compiling a list of entities. 

use Drupal\node\Entity\Node;


// Load All nodes.
$node_result = \Drupal::entityQuery('node')
->condition('nid',13,'=')
->execute();

$a_node = Node::load(1);

if($a_node){
    echo("can use this as a boolean");
}

//print_r($a_node);


$nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$nodes = $nodes_storage->loadMultiple($node_result);

foreach($nodes as $nid => $node) {
    print($nid."\n");
}