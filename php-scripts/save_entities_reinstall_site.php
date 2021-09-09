<?php 

use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\block_content\Entity\BlockContent;
use Drupal\block\Entity\Block;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;
use Drupal\path_alias\Entity\PathAlias;
use Drupal\redirect\Entity\Redirect;

echo("WARNING: \n");
echo("This process does NOT move over: \n");
echo("Custom vocabularies (created on the site to be reinstalled, but not in the install profile.)\n");
echo("Custom Menus (it'll move over menu items, so that might create errors if the menu doesn't also exist.)\n");
echo("Shortcuts or shortcut sets, if these are setup on any site they should be added to this script.");
echo("Views. if custom views are added they should either be added to this list or working in through configuration files.\n");
echo("If any of the above are needed this or a duplicate script will need to be updated.\n");

$site_name = $_SERVER['argv'][5];
echo("------------------------------------------------------------------------------------\n");
echo("Running script with site name:  ".$site_name."  \n");
echo("If this site name is incorrect cancel now.\n");


//Load all users.
echo("loading users.\n");
$user_result = \Drupal::entityQuery('user')
->condition('uid',5,'>')
->execute();
$users_storage = \Drupal::entityTypeManager()->getStorage('user');
$user_multiple = $users_storage->loadMultiple($user_result);
echo(strval(count($user_multiple))."  Users Loaded. \n");

// Load All nodes.
echo("loading nodes\n");
$node_result = \Drupal::entityQuery('node')->execute();
$nodes_storage = \Drupal::entityTypeManager()->getStorage('node');
$node_multiple = $nodes_storage->loadMultiple($node_result);
echo(strval(count($node_multiple))."  Nodes Loaded. \n\n");

//load taxonomy terms (not vocabularies. custom vocabularies shouldn't necessarily be added to sites.)
echo("loading Taxonomy Terms (But not entire vocabularies!)\n");
$term_result = \Drupal::entityQuery('taxonomy_term')->execute();
$terms_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
$term_multiple = $terms_storage->loadMultiple($term_result);
echo(strval(count($user_multiple))."  Terms Loaded. \n\n");

//Load files
echo("loading files (But NOT the first one!) \n");
$file_result = \Drupal::entityQuery('file')
->condition('fid',1,'<>')
->execute();
$files_storage = \Drupal::entityTypeManager()->getStorage('file');
$file_multiple = $files_storage->loadMultiple($file_result);
echo(strval(count($file_multiple))."  Filess Loaded. \n\n");

//load media
echo("loading media\n");
$media_result = \Drupal::entityQuery('media')->execute();
$media_storage = \Drupal::entityTypeManager()->getStorage('media');
$media_multiple = $media_storage->loadMultiple($media_result);
echo(strval(count($media_multiple))."  Media Loaded. \n\n");

//load paragraphs
echo("loading paragraphs\n");
$par_result = \Drupal::entityQuery('paragraph')->execute();
$par_storage = \Drupal::entityTypeManager()->getStorage('paragraph');
$par_multiple = $par_storage->loadMultiple($par_result);
echo(strval(count($par_multiple))."  Paragraphss Loaded. \n\n");

//load menus
echo("loading menus\n");
$menu_result = \Drupal::entityQuery('menu_link_content')->execute();  
$menu_storage = \Drupal::entityTypeManager()->getStorage('menu_link_content');
$menu_multiple = $menu_storage->loadMultiple($menu_result);
echo(strval(count($menu_multiple))."  Menus Loaded. \n\n");

//load block content
echo("loading block_content\n");
$block_cont_result = \Drupal::entityQuery('block_content')->execute();
$block_cont_storage = \Drupal::entityTypeManager()->getStorage('block_content');
$block_cont_multiple = $block_cont_storage->loadMultiple($block_cont_result);
echo(strval(count($block_cont_multiple))."  Block content Loaded. \n\n");

//load blocks
echo("loading blocks\n");
$blocks_result = \Drupal::entityQuery('block')
->condition('plugin','block_content','STARTS_WITH')
->condition('id','stable_footeraddress','<>')
->execute();
$blocks_storage = \Drupal::entityTypeManager()->getStorage('block');
$blocks_multiple = $blocks_storage->loadMultiple($blocks_result);
echo(strval(count($blocks_multiple))."  Blocks Loaded. \n\n");

//load path aliases  dont' forget I have a seperate script to update then delete duplicate aliases
echo("loading path alises\n");
$path_result = \Drupal::entityQuery('path_alias')->execute();  
$path_storage = \Drupal::entityTypeManager()->getStorage('path_alias');
$path_multiple = $path_storage->loadMultiple($path_result);
echo(strval(count($path_multiple))."  Path Aliases Loaded. \n\n");

//load redirects
echo("loading redirects\n");
$redirect_result = \Drupal::entityQuery('redirect')->execute();  
$redirect_storage = \Drupal::entityTypeManager()->getStorage('redirect');
$redirect_multiple = $redirect_storage->loadMultiple($redirect_result);
echo(strval(count($redirect_multiple))."  Redirects Loaded. \n\n");

############# loaded everthing. now reinstalling site. ##################
$myoutput = [];
echo("reinstalling site\n");

exec('sh /var/www/casdev/web/scripts/bash-scripts/cas_department_install4save_reinstall.sh '.$site_name,$myoutput);

foreach($myoutput as $output) {
     echo($output."\n");
}

echo("reinstall complete. cleaing caches\n");

$myoutput = [];
exec('drush @casdev.'.$site_name.' -y cr', $myoutput);

// Gonna leave this here.  Its how to clear menu caches then rebuild.  I had menu issues originally. Now I just delete then rebuild them.
//\Drupal::cache('menu')->invalidateAll();
//\Drupal::service('plugin.manager.menu.link')->rebuild();

echo("\n running other script.\n");

$myoutput = [];
exec('drush @casdev.'.$site_name.' scr scripts/php-scripts/delete_menus_nodes.php',$myoutput);

foreach($myoutput as $output) {
     echo($output."\n");
}

echo("\n Recreating content now\n");

//recreate users
echo("recreating users:  ".strval(count($user_multiple))."\n");
foreach($user_multiple as $user => $one_user){
     $newuser = User::create($one_user->toArray());
     $newuser->save();
}

//files
echo("recreating files:  ".strval(count($file_multiple))."\n");
foreach($file_multiple as $file => $one_file) {
     $newfile = File::create($one_file->toArray());
     $newfile->save();
}

//taxonomy terms
echo("recreating taxonomy terms:  ".strval(count($term_multiple))."\n");
foreach($term_multiple as $tid => $one_term){
     $newterm = Term::create($one_term->toArray());
     $newterm->save();
}

//media
echo("reacreating media:  ".strval(count($media_multiple))."\n");
foreach($media_multiple as $media => $one_media) {
     $newmedia = Media::create($one_media->toArray());
     $newmedia->save();
}

//paragraphs
echo("recreating paragraphs:  ".strval(count($par_multiple))."\n");
foreach($par_multiple as $pid => $one_par) {
     $newpar = Paragraph::create($one_par->toArray());
     $newpar->save();
}

//node 
echo("recreating nodes:  ".strval(count($node_multiple))."\n");
foreach($node_multiple as $nid => $one_node) {
     $newnode = Node::create($one_node->toArray());
     $newnode->save();
} 

//menu
echo("recreating menus:  ".strval(count($menu_multiple))."\n");
foreach($menu_multiple as $menu => $one_menu) {
     $newmenu = MenuLinkContent::create($one_menu->toArray());
     $newmenu->save();
}

//block content
echo("recreating block content:  ".strval(count($block_cont_multiple))."\n");
foreach($block_cont_multiple as $block_content => $one_block_content){
     $new_block_content = BlockContent::create($one_block_content->toArray());
     $new_block_content->save();
}

//delete blocks because things wouldn't work otherwise.
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
echo("recreating blocks:  ".strval(count($blocks_multiple))."\n");
foreach($blocks_multiple as $block => $one_block){
     $new_block = Block::create($one_block->toArray());
     $new_block->save();
}

//path aliases
echo("recreating path aliases:  ".strval(count($path_multiple))."\n");
foreach($path_multiple as $path => $one_path){
     $new_path = PathAlias::create($one_path->toArray());
     $new_path->save();
}

//redirects
echo("recreating redirects:  ".strval(count($redirect_multiple))."\n");
foreach($redirect_multiple as $redirect => $one_redirect){
     $new_redirect = Redirect::create($one_redirect->toArray());
     $new_redirect->save();
}

//// any other clean up that needs to happen.

echo("clearing caches one more time.\n");

exec('drush @casdev.'.$site_name.' -y cr', $myoutput);

/* # entities:
(roughly in order)
X users
X file
X taxonomy_term
X media
X paragraphs
X nodes
X menu_links
X block_content
X blocks
X redirects
X path aliases
 */

// code snippet to get all entity types:
//drushl eval "print_r(array_keys(\Drupal::entityTypeManager()->getDefinitions()));"


