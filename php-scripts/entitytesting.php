<?php 

//here's how to start compiling a list of entities. 

use Drupal\node\Entity\Node;


// Load All nodes.

$node_result = \Drupal::entityQuery('block')->execute();
//->condition('plugin','block_content','STARTS_WITH')
//->condition('id','stable_footeraddress','<>')
//->execute();

$blocks_storage = \Drupal::entityTypeManager()->getStorage('block');
$blocks = $blocks_storage->loadMultiple($node_result);

echo(count($blocks)."\n");
//print_r($blocks);
foreach($blocks as $bid => $block) {
    print($bid."\n");
    //print_r($block->toArray());
    //print_r($block->toArray());

}

print_r($blocks['topnavigation']->toArray());