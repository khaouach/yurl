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
$pwcommand->unit("link")->action("save")->params(array("id", "category_id", "category_name","url", "name", "memo", "url_username","url_password"))->bindToClass("stdClass");
$pwcommand->unit("link")->action("delete")->param("id");
$pwcommand->unit("category")->action("list_all");
$pwcommand->unit("category")->action("delete")->param("id");

$pwcommand->run();


function output_json($object) {
    
    header('Content-type: application/json');
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
    
    $json = json_encode($object);
    
   // cors();
    # JSON if no callback
    if (!isset($_REQUEST['callback']))
        exit($json);

    # JSONP if valid callback
    if (is_valid_callback($_REQUEST['callback']))
        exit("{$_REQUEST['callback']}($json)");

    # Otherwise, bad request
    header('status: 400 Bad Request', true, 400);
    JFactory::getApplication()->close();
}

function is_valid_callback($subject) {
    return true;  //todo
    $identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

    $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
        'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
        'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
        'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
        'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
        'private', 'public', 'yield', 'interface', 'package', 'protected',
        'static', 'null', 'true', 'false');

    return preg_match($identifier_syntax, $subject) && !in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
}