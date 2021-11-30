<?php

use Drupal\path_alias\Entity\PathAlias;


$path_result = \Drupal::entityQuery('path_alias')->execute(); 
$path_storage = \Drupal::entityTypeManager()->getStorage('path_alias');
$path_multiple = $path_storage->loadMultiple($path_result);

foreach ($path_multiple as $single_path){
    var_dump($single_path->toArray()['alias'][0]['value']);
    var_dump($single_path->toArray()['path'][0]['value']);
    var_dump($single_path->toArray()['status'][0]['value']);

}    

