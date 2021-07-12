<?php

use Drupal\block\Entity\Block;
use Drupal\block_content\Entity\BlockContentType;

//$block_test = Block::load('footeraddress');
//$plugin = $block_test->getPlugin();
$block_test = BlockContentType::load('footeradress');


if ($block_test != NULL){
    var_dump($block_test);
}
else{
    echo "no footeraddress block! (hopefully that's a good thing for you, bud.\n";
}
//var_dump($block_test);