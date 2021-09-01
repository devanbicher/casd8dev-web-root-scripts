<?php 

//here's how to start compiling a list of entities. 

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\menu_link_content\Entity\MenuLinkContent;

// Load All nodes.
echo("loading nodes\n");
$node_result = \Drupal::entityQuery('node')->execute();
$nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$node_multiple = $nodes_storage->loadMultiple($node_result);

echo("loading files\n");
$file_result = \Drupal::entityQuery('file')->execute();
$files_storage = \Drupal::entityTypeManager()->getStorage('file');
$file_multiple = $files_storage->loadMultiple($file_result);

echo("loading media");
$media_result = \Drupal::entityQuery('media')->execute();
$media_storage = \Drupal::entityTypeManager()->getStorage('media');
$media_multiple = $media_storage->loadMultiple($media_result);

echo("loading paragraphs");
$par_result = \Drupal::entityQuery('paragraph')->execute();
$par_storage = \Drupal::entityTypeManager()->getStorage('paragraph');
$par_multiple = $par_storage->loadMultiple($par_result);

$menu_result = \Drupal::entityQuery('menu_link_content')
->condition('bundle', ['social-media'],'NOT IN')
->execute();
$menu_storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
$menu_multiple = $menu_storage->loadMultiple($menu_result);

############# loaded everthing. now reinstalling site. ##################
$myoutput = [];
exec('sh /var/www/casdev/web/scripts/bash-scripts/cas_department_install4overwrite.sh cleanparagraphs',$myoutput);

foreach($myoutput as $output) {
     echo($output."\n");
}

exec('drush @casdev.cleanparagraphs -y cr', $myoutput);


foreach($myoutput as $output) {
     echo($output."\n");
}

#delete nodes that already from the new profile.
echo("deleting nodes from new site");
$new_node_result = \Drupal::entityQuery('node')->execute();
$new_nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$new_node_multiple = $new_nodes_storage->loadMultiple($new_node_result);
foreach($new_node_multiple as $delete_node) {
     $delete_node->delete();
}

#files
echo("recreating files");
foreach($file_multiple as $file => $one_file) {
     //print_r($one_file->toArray());
     $newfile = File::create($one_file->toArray());
     $newfile->save();
}

#media
echo("reacreating media");
foreach($media_multiple as $media => $one_media) {
     //print_r($one_file->toArray());
     $newmedia = Media::create($one_media->toArray());
     $newmedia->save();
}

#paragraphs
echo("recreating paragraphs");
foreach($par_multiple as $pid => $one_par) {
     //print_r($one_par->toArray());
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

#menu
echo("recreating menus");
foreach($menu_multiple as $menu => $one_menu) {
     //print_r($one_node->toArray());
     $newmenu = MenuLinkContent::create($one_menu->toArray());
     $newmenu->save();
}


/* # entities:
(roughly in order)
file
media
X paragraphs
X nodes
menu_links
block_content
blocks

 */

// code snippet to get all entity types:
//drushl eval "print_r(array_keys(\Drupal::entityTypeManager()->getDefinitions()));"
//var_dump($entities[5]);


