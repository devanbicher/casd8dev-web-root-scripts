<?php

use Drupal\file\Entity\File;

$image = File::load(9);

var_dump($image);