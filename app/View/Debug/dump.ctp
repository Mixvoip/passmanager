<?php

//Debug Database dump

ini_set('xdebug.var_display_max_depth', -1);
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);

var_dump($data1);
var_dump($data2);
echo $this->element('sql_dump');