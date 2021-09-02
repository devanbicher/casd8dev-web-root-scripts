<?php 

//here's how to start compiling a list of entities. 

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\block_content\Entity\BlockContent;
use Drupal\block\Entity\Block;
use Drupal\taxonomy\Entity\Term;
use \Drupal\user\Entity\User;

echo("WARNING: \n");
echo("This process does NOT move over: \n");
echo("Custom vocabularies (created on the site to be reinstalled, but not in the install profile.)\n");
echo("Custom Menus (it'll move over menu items, so that might create errors if the menu doesn't also exist.)\n");
echo("Shortcuts or shortcut sets, if these are setup on any site they should be added to this script.");
echo("Views. if custom views are added they should either be added to this list or working in through configuration files.\n");
echo("If any of the above are needed this or a duplicate script will need to be updated.\n");

echo("loading users.\n");
$user_result = \Drupal::entityQuery('user')
->condition('uid',5,'>')
->execute();
$users_storage = \Drupal::entityTypeManager()->getStorage('user');
$user_multiple = $users_storage->loadMultiple($user_result);

// Load All nodes.
echo("loading nodes\n");
$node_result = \Drupal::entityQuery('node')->execute();
$nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$node_multiple = $nodes_storage->loadMultiple($node_result);

echo("loading Taxonomy Terms (But not entire vocabularies!)\n");
$term_result = \Drupal::entityQuery('taxonomy_term')->execute();
$terms_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
$term_multiple = $terms_storage->loadMultiple($term_result);

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

echo("recreating users:  ".strval(count($user_multiple))."\n");
foreach($user_multiple as $user => $one_user){
     $newuser = User::create($one_user->toArray());
     $newuser->save();
}

#files
echo("recreating files:  ".strval(count($file_multiple))."\n");
foreach($file_multiple as $file => $one_file) {
     //print_r($one_file->toArray());
     $newfile = File::create($one_file->toArray());
     $newfile->save();
}

#taxonomy terms
echo("recreating taxonomy terms:  ".strval(count($term_multiple))."\n");
foreach($term_multiple as $tid => $one_term){
     $newterm = Term::create($one_term->toArray());
     $newterm->save();
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

echo("printing out existing blocks to be deleted.\n");
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
X taxonomy_term
X users

redirects

I don't think I need the path aliaes, just the redirects
path aliases
 */

// code snippet to get all entity types:
//drushl eval "print_r(array_keys(\Drupal::entityTypeManager()->getDefinitions()));"


