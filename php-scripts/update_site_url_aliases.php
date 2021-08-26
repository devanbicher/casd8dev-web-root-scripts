<?php

echo("This script updates all nodes, it should only maybe update nodes that have been updated and their children");

  $entities = [];
  // Load All nodes.
  $result = \Drupal::entityQuery('node')->execute();
  $entity_storage = \Drupal::entityTypeManager()->getStorage('node');
  $entities = array_merge($entities, $entity_storage->loadMultiple($result));


  // Update URL aliases.
  foreach ($entities as $entity) {
    \Drupal::service('pathauto.generator')->updateEntityAlias($entity, 'update');
  }
