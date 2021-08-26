<?php
$myoutput = [];

exec('pwd', $myoutput);

foreach($myoutput as $output) {
    echo($output."\n");
}