<?php
// echo shell_exec("php ./v-1.1.7/artisan storage:link");


$target = './v-1.1.7/storage/app';
$shortcut = 'storage';
symlink($target, $shortcut);