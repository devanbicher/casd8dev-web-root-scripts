<?php

use Drupal\path_alias\Entity\PathAlias;

echo("loading all path alises\n");
$path_result = \Drupal::entityQuery('path_alias')->execute();  
$path_storage = \Drupal::entityTypeManager()->getStorage('path_alias');
$path_multiple = $path_storage->loadMultiple($path_result);

echo("Number of path aliases:  ".strval(count($path_multiple))."\n");

$unique_aliases = [];

foreach($path_multiple as $pid => $one_path){
    $path_values = $one_path->toArray();
    $path_alias = $path_values['alias'][0]['value'];
    $path_node = $path_values['path'][0]['value'];
    if(!in_array($path_alias, $unique_aliases))  {
        $unique_aliases[$path_alias] = array($path_node,$pid);
    }
    
    //$new_path = PathAlias::create($one_path->toArray());
    // $new_path->save();
}

foreach($unique_aliases as $alias => $values){
    $path_node = $values[0];
    $pid = $values[1];

    $paths_w_alias = Drupal::entityQuery('path_alias')
    ->condition('alias',$alias,'=')
    ->condition('path',$path_node,'=')
    ->condition('id',$pid,'<>')
    ->execute();

    foreach($paths_w_alias as $pid){    
        $delete_path = PathAlias::Load($pid);
        echo("Deleting: ".$pid."  ".$delete_path->uuid()."   ".$delete_path->get('path')."   ".$delete_path->get('alias')."\n");
        //$delete_path->delete();
    }
    
    //query
    //alias
    //not pid
    //if alias and path are the same
    //delete the entity

}