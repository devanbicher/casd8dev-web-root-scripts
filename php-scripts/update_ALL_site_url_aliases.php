<?php

  echo("by design this script updates all paths, not just updated ones. There is another script for that.");

  $entities = [];
  // Load All nodes.
  $result = \Drupal::entityQuery('node')->execute();
  $entity_storage = \Drupal::entityTypeManager()->getStorage('node');
  $entities = array_merge($entities, $entity_storage->loadMultiple($result));


  // Update URL aliases.
  foreach ($entities as $entity) {
    \Drupal::service('pathauto.generator')->updateEntityAlias($entity, 'update');
  }
