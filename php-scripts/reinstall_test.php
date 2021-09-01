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

############# loaded everthing. now reinstalling site. ##################
$myoutput = [];
//exec('sh /var/www/casdev/web/scripts/bash-scripts/cas_department_install4overwrite.sh cleanparagraphs',$myoutput);
echo("reinstalling site");
exec('$(drush @casdev.cleanparagraphs sql:connect) < /var/www/casdev/web/sites/cleanparagraphs/files/private/backup_migrate/cleanparagraphs-reinstall-1027-090121.sql');

foreach($myoutput as $output) {
     echo($output."\n");
}

echo("reinstall complete. cleaing caches");

exec('drush @casdev.cleanparagraphs -y cr', $myoutput);
//\Drupal::cache('menu')->invalidateAll();
//\Drupal::service('plugin.manager.menu.link')->rebuild();

echo("running other script.");

exec('drush @casdev.cleanparagraphs scr scripts/php-scripts/delete_menus_nodes.php',$myoutput);

foreach($myoutput as $output) {
     echo($output."\n");
}

#delete nodes that already from the new profile.

//what if we create a new node, add it to the main navigation, then don't delete just tha

/*
echo("deleting menus from the new site.");
$new_menu_result = \Drupal::entityQuery('menu_link_content')->execute();
$new_menu_storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
$new_menu_multiple = $new_menu_storage->loadMultiple($new_menu_result);  
foreach($new_menu_multiple as $delete_menu){
     echo($delete_menu->uuid()."   ".$delete_menu->getTitle()."   ".$delete_menu->getMenuName()."\n");
     $delete_menu->delete();
}

exec('drush @casdev.cleanparagraphs -y cr', $myoutput);

echo("deleting nodes from new site");
$new_node_result = \Drupal::entityQuery('node')->execute();
$new_nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$new_node_multiple = $new_nodes_storage->loadMultiple($new_node_result);
krsort($new_node_multiple);
foreach($new_node_multiple as $dnid => $delete_node) {
     echo("deleted: ".strval($dnid));
     $delete_node->delete();
     \Drupal::cache('menu')->invalidateAll();
     \Drupal::service('plugin.manager.menu.link')->rebuild();
}
*/


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
     /*
     //print_r($one_node->toArray());
     
     //$node_result = \Drupal::entityQuery('node')
     //->condition('nid',$nid,'=')
     //->execute();
     
     $old_node = Node::load($nid);
     if($old_node){
          $old_node = $one_node;
          $old_node->save();
     }
     else{
     //if noode (from $nid) already exists
     //load node
     //set that node equal to $one_node
     //save node
     //else : the stuff before.


          $newnode = Node::create($one_node->toArray());
          $newnode->save();
     }         
     */
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
X file
X media
X paragraphs
X nodes
X menu_links
block_content
blocks
taxonomy_vocabulary
taxonomy_term

 */

// code snippet to get all entity types:
//drushl eval "print_r(array_keys(\Drupal::entityTypeManager()->getDefinitions()));"
//var_dump($entities[5]);


