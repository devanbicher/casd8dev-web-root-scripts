<?php

use Drupal\Component\Utility\UrlHelper;

$myvar=is_callable('Utlity\isExternal');

dump($myvar);

dump(UrlHelper::isExternal('http://cas.lehigh.edu'));