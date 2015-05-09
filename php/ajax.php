<?php

/**
 * This file handles all ajax requests
 */
define('_JEXEC', getcwd());
define('JPATH_SITE', getcwd());
define("JPATH_COMPONENT",  dirname(__FILE__) ."");

require_once("config.php");
require_once("database.php");
require_once("helpers/user.helper.php");

require_once('pwframework/ipwframework.php');

$pwcommand = PWFrameWorks::getCommand();


$pwcommand->unit("link")->action("list_all");
$pwcommand->unit("link")->action("save")->params(array("id", "category_id", "category_name","url", "name", "memo"))->bindToClass("stdClass");
$pwcommand->unit("link")->action("delete")->param("id");
$pwcommand->unit("category")->action("list_all");
$pwcommand->unit("category")->action("delete")->param("id");

$pwcommand->run();