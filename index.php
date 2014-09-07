<?php
include "bliss/http-app.php";

define("APP_PATH", dirname(__FILE__));

app()->registerModulesDirectory(APP_PATH ."/modules");
app()->modules(\Users\Module::NAME)->disable();
app()->run();