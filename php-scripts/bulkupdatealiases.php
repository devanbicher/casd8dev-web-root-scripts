<?php

use Drupal\path_alias\Entity\PathAlias;

echo("first Re-generate new/updated path aliases.\n");
echo("DONT FORGET: This script only regenerates paths for nodes, to add users, media, taxonomies, etc in there an update is needed.\n");

// Load All nodes.
$result = \Drupal::entityQuery('node')->execute();
$entity_storage = \Drupal::entityTypeManager()->getStorage('node');
$entities =  $entity_storage->loadMultiple($result);


// Update URL aliases.
foreach ($entities as $entity) {
    \Drupal::service('pathauto.generator')->updateEntityAlias($entity, 'update');
}


echo("loading all path alises\n");
$path_result = \Drupal::entityQuery('path_alias')->execute();  
$path_storage = \Drupal::entityTypeManager()->getStorage('path_alias');
$path_multiple = $path_storage->loadMultiple($path_result);

echo("Number of path aliases:  ".strval(count($path_multiple))."\n");

$unique_aliases = [];

echo("creating list of unique alias to paths\n");
foreach($path_multiple as $pid => $one_path){
    $path_values = $one_path->toArray();
    $path_alias = $path_values['alias'][0]['value'];
    $path_node = $path_values['path'][0]['value'];
    if(!in_array($path_alias, $unique_aliases))  {
        $unique_aliases[$path_alias] = array($path_node,$pid);
    }

}

echo("Deleting non-unique path/alias entities\n\n");
foreach($unique_aliases as $alias => $values){
    $path_node = $values[0];
    $pid = $values[1];

    $paths_w_alias = Drupal::entityQuery('path_alias')
    ->condition('alias',$alias,'=')
    ->condition('path',$path_node,'=')
    ->condition('id',$pid,'<>')
    ->execute();
    
    echo("number of: ".$path_node." :  ".$alias."   ".count($paths_w_alias)."\n");
    foreach($paths_w_alias as $pid){    
        $delete_path = PathAlias::Load($pid);
        $path_array = $delete_path->toArray();
        echo("Deleting: ".$pid."  ".$delete_path->uuid()."   ".$path_array['path'][0]['value']."    ".$path_array['alias'][0]['value']."\n");

        $delete_path->delete();
    }
    echo("\n");
}
echo("Done deleting duplicate aliases.\n");


$path_result = \Drupal::entityQuery('path_alias')->execute();  
$path_multiple = $path_storage->loadMultiple($path_result);

echo("NEW Number of path aliases:  ".strval(count($path_multiple))."\n");

echo("DONE\n");