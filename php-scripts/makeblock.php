<?php

use Drupal\block\Entity\Block;
use Drupal\Core\Block\BlockBase;

use Drupal\block_content\Entity\BlockContent;

//$block_manager = \Drupal::service('plugin.manager.block');
//$config = [];
//$plugin_block = $block_manager->createInstance('content_block',$config);

//$block_test = Block::create([
//    'id'=>'footeraddress',
//    'type'=>'contact_block',
//    'plugin'=>$plugin_block,
//]);

//$block_test->setRegion('footer');
//$block_test->set('field_email',['value'=>'incas@lehigh.edu']);
//$block_test->set('field_telephone',['value'=>'(610) 758-3000']);

//$block_test->save();

//var_dump($block_test);

$block_content = BlockContent::create([
    'type' => 'contact_block',
    'info' => 'Footer Address',
]);
$block_content-> set('field_address',[
'country_code' => 'US',
'administrative_area' => 'PA',
'locality' => 'Bethlehem',
'postal_code' => '18015',
'address_line1' => '27 Memorial Drive West',
]);
$block_content->set('field_email',['value'=>'incas@lehigh.edu']);
$block_content->set('field_telephone',['value'=>'(610) 758-3000']);

$block_content->save();

$block = Block::create([
    'id'=> 'footeraddress',
    'plugin' => 'block_content:'.$block_content->uuid(),
    'theme' => 'layout_theme',
    'region' => 'footer',
    'weight' => -5,
]);
$block->save();

// set fields next