<?php
/**
 * @package		Rokin Gallery
 * @author    	Pageworks http://www.pageworks.nl
 * @copyright	Copyright (c) 2006 - 2010 Pageworks. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */
if (!defined('_JEXEC')) {
    die('Restricted access');
}


abstract class PWController {

    public $option = null;
    public $unit = null;
    public $action = null;
    public $Itemid = null;
    public $model = null;
    public $view = null;

}

class PWAction {

    private $name = null;
    private $params = array();
    private $bindClassName =null;
    private $wrapAdminForm = false;
    private $unit_name = null;
    private $token = false;
    private $initial = false;

    public function __construct($unit_name, $name) {
        $this->name = $name;
        $this->unit_name = $unit_name;
    }

    /**
     * if set to true a joomla security token is checked on execution
     * @param type $token
     * @return \PWAction
     */
    public function token($token = false) {
        $this->token = $token;
        return $this;
    }

    public function execute($option, $Itemid) {

        /* validate joomla security token */
        if ($this->token) {
            if (!(JSession::checkToken() || JSession::checkToken('get'))) {
                JFactory::getApplication()->enqueueMessage("Invalid token");
                return false;
            }
        }

        /**
         * Controllers are mandetory items since they steir the MVC
         */
        $controller = PWFrameWorks::getCommand()->getController($this->unit_name);
        //setting class basic value
        $controller->action = $this->name;
        $controller->unit = $this->unit_name;
        $controller->option = $option;
        $controller->Itemid = $Itemid;
        $controller->jinput = JFactory::getApplication()->input;

        if (method_exists($controller, $this->name())) {
            $params = $this->getParameterValues();
            call_user_func_array(array($controller, $this->name()), $params);
            
            if($controller->view !=null && method_exists ($controller->view, $this->name())){
                call_user_func_array(array($controller->view, $this->name()), $params);
            }
            
            /* so the controller has done its magic, see if there is a view to be called*/
            
        } else {
            
            JFactory::getApplication()->enqueueMessage("class for " . $this->unit_name . " with method " . $this->name . " could not be called because it does not exists");
            return;
        }
        
        
        
    }

    /**
     * Add a parameter
     * @param type $param
     * @return \PWAction
     */
    public function param($param) {
        $this->params[] = $param;
        return $this;
    }
    
    /**
     * Bind specified params to a class
     * @param type $className
     */
    public function bindToClass($className){
        $this->bindClassName = $className;
        return $this;
    }
    
    
    /**
     * when this flag is set to true, it is called when no unit or act can be found in the request
     * @param type $initial
     * @return \PWAction
     */
    public function initial($initial){
        $this->initial = $initial;
        return $this;
    }
    
    public function isInitial(){
        return $this->initial;
    }

    /**
     * set an array of parameters
     * @param type $params
     * @return \PWAction11
     */
    public function params($params) {
        $this->params = $params;
        return $this;
    }

    public function name() {
        return $this->name;
    }

    public function wrapAdminForm($wrap) {
        $this->wrapAdminForm = $wrap;
        return $this;
    }

    private function getParameterValues() {

        $jinput = JFactory::getApplication()->input;

        if ($this->params == null) {
            return array();
        }

        if (!is_array($this->params)) {
            die("false parameter array supplied for " . $this->name);
        }
        $retval = array();

        foreach ($this->params as $param) {
            /* arrays need different approach */
            $param_value = $jinput->getString($param);
            if (array_key_exists($param, $_REQUEST)) {
                //if parameters is set				
                if (is_array($param_value)) {
                    /* iterate trough array */
                    $tmp = $param_value;
                    $rt = array();
                    foreach ($tmp as $key => $t) {
                        $rt[$key] = htmlspecialchars("" . $t);
                    }
                    $retval[] = $rt;
                } else {
                    $retval[$param] = htmlspecialchars("" . $param_value);
                }
            } else {
                //oops parameter not set
                $retval[] = null;
            }
        }
       
        /** instead of multipole parameters return a defined object */
        if (isset($this->bindClassName)) {
            $clazzName = $this->bindClassName;
            //create class if exists oterwise just a standard class object
            if(class_exists($clazzName)){
                $clazz = new $clazzName();                 
            }else{
                 $clazz = new stdClass();
            }
            
            foreach ($retval as $key => $value) {
                $clazz->{$key} = $value;
            }
            $retval = array($clazz);            
        }

        return $retval;
    }

}

class PWUnit {

    private $name = null;
    
    /* laods a class and instantiates if it exists */
    private function checkAndCreateClass($fileName, $clazzName){
        if(file_exists($fileName)){
             require_once $fileName;
             
             if(class_exists($clazzName)){
                $clazzName = new $clazzName();                 
             }else{
                 if(PWCommand::isDebug()){
                    sysout("Class not found : " . $clazzName);
                }
                $clazzName = null;
             }
        }else{
            if(PWCommand::isDebug()){
                sysout("File not found : " . $fileName);
            }
            $clazzName = null;
        }
        return $clazzName;
    }

    public function __construct($name) {
        $this->name = $name;
        
        /* check if a model exists if so, load the model */
        $modelFile = JPATH_COMPONENT . "/models/" . $name . ".model.php";
        $modelClazzName = "PWModel" . ucfirst($name);
        $modelClazz = $this->checkAndCreateClass($modelFile, $modelClazzName);
        
        /**
         * check and load the controller
         */
        $controllerFile = JPATH_COMPONENT . "/controllers/" . $name . ".controller.php";
        $controllerClazzName = "PWController" . ucwords($name);
        $controllerClazz = $this->checkAndCreateClass($controllerFile, $controllerClazzName);
        if($controllerClazz==null){
            die("Controller Class not found : " . $controllerClazzName);
        }
        $controllerClazz->model = $modelClazz;
        
          /**
         * check and load the view for this controller
         */
        $viewFile = JPATH_COMPONENT . "/views/" . $name . "/index.php";
        $viewClazzName = "PWView" . ucwords($name);
        $viewClazz = $this->checkAndCreateClass($viewFile, $viewClazzName);
        
        /* set the view on the controller too */
        $controllerClazz->view = $viewClazz;
        PWFrameWorks::getCommand()->setView($name, $viewClazz);
        PWFrameWorks::getCommand()->setController($name, $controllerClazz);
            
    }

    private $actions = array();

    public function action($action) {
        if (!array_key_exists($action, $this->actions)) {
            $this->actions[$action] = new PWAction($this->name, $action);
        }
        return $this->actions[$action];
    }
    
    public function getActions(){
        return $this->actions;
    }

    public function getAction($action) {
        if (array_key_exists($action, $this->actions)) {
            return $this->actions[$action];
        }
        return null;
    }

    public function name() {
        return $this->name;
    }

}

class PWCommand {

    private $units = array();
    private $_unit = null;
    private $_action = null;
    
    /* contains instantitions of controllers */
    private $controllers = array();
    private $views = array();
    
    private static $debug = false;

    public function __construct() {
        
    }
    
    /**
     * returns an instantation of a controller
     * @param type $name
     */
    public function getController($name){
        
        if(array_key_exists($name, $this->controllers)){
            return $this->controllers[$name];            
        }else{
            if(PWCommand::isDebug()){
                sysout("Controller not found" . $name);
            }
            return null;
        }
    }
    
    /**
     * Set an instantiation of a controller
     * @param type $name
     * @param type $controller
     */
    public function setController($name, $controller){
        $this->controllers[$name] = $controller;
    }
    
     /**
     * returns an instantation of a view
     * @param type $name
     */
    public function getView($name){
        
        if(array_key_exists($name, $this->views)){
            return $this->views[$name];            
        }else{
            if(PWCommand::isDebug()){
                sysout("View not found" . $name);
            }
            return null;
        }
    }
    
    /**
     * Set an instantiation of a controller
     * @param type $name
     * @param type $views
     */
    public function setView($name, $views){
        $this->views[$name] = $views;
    }


    public static function debug($debug){
        self::$debug = $debug;
    }
    
    public static function isDebug(){
        return self::$debug;
    }
        

    public function unit($unit) {
        /* check if unit exists if not create new one*/
        if (!array_key_exists($unit, $this->units)) {
            $this->units[$unit] = new PWUnit($unit);
        }
        /* return the unit */
        
        return $this->units[$unit];
    }

    public function run($unit_name = null, $act_name = null) {
        $jinput = JFactory::getApplication()->input;
        
    
            if ($unit_name == null) {
                $unit_name = $jinput->getString('unit');
            }
            if ($act_name == null) {
                $act_name = $jinput->getString('act');
            }
            
            if ($unit_name == null && $act_name==null) {
                //look up initial action
                //in joomla via menu a view is set if, so use that as unit
                $view = $jinput->get('view');
                if($view==null){
                    $units = $this->units;
                }else{
                    $units = array();
                    $units[] = $this->units[$view];
                }
                
                //find units intial action
                foreach($units as $unit){
                    foreach($unit->getActions() as $action){
                        if($action->isInitial()){
                            $this->_unit = $unit;
                            $this->_action = $action;
                            break;
                        }
                    }
                }
            }else{
                $this->_unit = $this->unit($unit_name);
                $this->_action = $this->_unit->getAction($act_name);
            }

        /** getting some handy values */
        $option = $jinput->get('option');
        $Itemid = $jinput->get('Itemid');
      

        if ($this->_action == null) {
            echo "sorry no such action for this option!, " . $unit_name . " => " . $act_name;
            return;
        }

        /* execute the action */
        $this->_action->execute($option, $Itemid);
    }
    
    /**
     * Cleans xss actions from a string
     * @param type $data
     * @return type
     */
    static function xss_clean($data){
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do
        {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);

        // we are done...
        return $data;
    }

}

class PWFrameWorks {
    
    private static $command = null;
    
    public function __construct() {
        
    }

    public static function getCommand() {
        if (self::$command == null) {
            self::$command = new PWCommand();
        }
        return self::$command;
    }

}