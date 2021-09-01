<?php 

//here's how to start compiling a list of entities. 

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\block_content\Entity\BlockContent;
use Drupal\block\Entity\Block;

// Load All nodes.
echo("loading nodes\n");
$node_result = \Drupal::entityQuery('node')->execute();
$nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$node_multiple = $nodes_storage->loadMultiple($node_result);

echo("loading files (But NOT the first one!) \n");
$file_result = \Drupal::entityQuery('file')
->condition('fid',1,'<>')
->execute();
$files_storage = \Drupal::entityTypeManager()->getStorage('file');
$file_multiple = $files_storage->loadMultiple($file_result);

echo("loading media\n");
$media_result = \Drupal::entityQuery('media')->execute();
$media_storage = \Drupal::entityTypeManager()->getStorage('media');
$media_multiple = $media_storage->loadMultiple($media_result);

echo("loading paragraphs\n");
$par_result = \Drupal::entityQuery('paragraph')->execute();
$par_storage = \Drupal::entityTypeManager()->getStorage('paragraph');
$par_multiple = $par_storage->loadMultiple($par_result);

echo("loading menus\n");
$menu_result = \Drupal::entityQuery('menu_link_content')->execute();  
//if you want to not get the social media menu:
//->condition('bundle', ['social-media'],'NOT IN')     
$menu_storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
$menu_multiple = $menu_storage->loadMultiple($menu_result);

echo("loading block_content\n");
$block_cont_result = \Drupal::entityQuery('block_content')->execute();
$block_cont_storage = \Drupal::entityTypeManager()->getStorage('block_content');
$block_cont_multiple = $block_cont_storage->loadMultiple($block_cont_result);

echo("loading blocks\n");
$blocks_result = \Drupal::entityQuery('block')
->condition('plugin','block_content','STARTS_WITH')
->condition('id','stable_footeraddress','<>')
->execute();
$blocks_storage = \Drupal::entityTypeManager()->getStorage('block');
$blocks_multiple = $blocks_storage->loadMultiple($blocks_result);


############# loaded everthing. now reinstalling site. ##################
$myoutput = [];
//exec('sh /var/www/casdev/web/scripts/bash-scripts/cas_department_install4overwrite.sh cleanparagraphs',$myoutput);
echo("reinstalling site\n");

exec('$(drush @casdev.cleanparagraphs sql:connect) < /var/www/casdev/web/sites/cleanparagraphs/files/private/backup_migrate/cleanparagraphs-reinstall-1027-090121.sql');

foreach($myoutput as $output) {
     echo($output."\n");
}

echo("reinstall complete. cleaing caches\n");

exec('drush @casdev.cleanparagraphs -y cr', $myoutput);
//\Drupal::cache('menu')->invalidateAll();
//\Drupal::service('plugin.manager.menu.link')->rebuild();

echo("running other script.\n");

exec('drush @casdev.cleanparagraphs scr scripts/php-scripts/delete_menus_nodes.php',$myoutput);

foreach($myoutput as $output) {
     echo($output."\n");
}


#files
echo("recreating files:  ".strval(count($file_multiple))."\n");
foreach($file_multiple as $file => $one_file) {
     //print_r($one_file->toArray());
     $newfile = File::create($one_file->toArray());
     $newfile->save();
}

#media
echo("reacreating media\n");
foreach($media_multiple as $media => $one_media) {
     //print_r($one_file->toArray());
     $newmedia = Media::create($one_media->toArray());
     $newmedia->save();
}

#paragraphs
echo("recreating paragraphs\n");
foreach($par_multiple as $pid => $one_par) {
     //print_r($one_par->toArray());
     $newpar = Paragraph::create($one_par->toArray());
     $newpar->save();
}

#node 
echo("recreating nodes\n");
foreach($node_multiple as $nid => $one_node) {
     $newnode = Node::create($one_node->toArray());
     $newnode->save();
} 

#menu
echo("recreating menus\n");
foreach($menu_multiple as $menu => $one_menu) {
     //print_r($one_node->toArray());
     $newmenu = MenuLinkContent::create($one_menu->toArray());
     $newmenu->save();
}

//block content
echo("recreating block content\n");
foreach($block_cont_multiple as $block_content => $one_block_content){
     $new_block_content = BlockContent::create($one_block_content->toArray());
     $new_block_content->save();
}

echo("printing out existing blocks\n");
$new_blocks_result = \Drupal::entityQuery('block')
->condition('plugin','block_content','STARTS_WITH')
->condition('id','stable_footeraddress','<>')
->execute();
$new_blocks_storage = \Drupal::entityTypeManager()->getStorage('block');
$new_blocks_multiple = $new_blocks_storage->loadMultiple($new_blocks_result);

foreach($new_blocks_multiple as $new_bid => $one_new_block){
    echo("deleting:  ".$one_new_block->id()."   ".$one_new_block->uuid()."\n");
    $one_new_block->delete();
}

//blocks
echo("recreating blocks:  number:  ".strval(count($blocks_multiple))."\n");
foreach($blocks_multiple as $block => $one_block){
     $new_block = Block::create($one_block->toArray());
     $new_block->save();
}

echo("regenerating path urls\n");
foreach($node_multiple as $nid => $one_node) {
     \Drupal::service('pathauto.generator')->updateEntityAlias($one_node, 'update');
}

echo("clearing caches one more time.\n");

exec('drush @casdev.cleanparagraphs -y cr', $myoutput);

/* # entities:
(roughly in order)
X file
X media
X paragraphs
X nodes
X menu_links
X block_content
X blocks

taxonomy_vocabulary
taxonomy_term

 */

// code snippet to get all entity types:
//drushl eval "print_r(array_keys(\Drupal::entityTypeManager()->getDefinitions()));"
//var_dump($entities[5]);


