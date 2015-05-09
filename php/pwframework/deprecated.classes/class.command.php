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
if (!(defined('_VALID_MOS') || defined('_JEXEC'))) {
    die('Restricted access');
}


/**
 *  Version 1.3.0
 *  march 28 2010
 *  wrap adminForm parameter. Allows to wrap the adminForm arround the calling action function
 * 
 * 	version 1.2.1
 * 	sep 14 2007
 * 	added paramters to user function calls
 * 	
 * 	version 1.1
 * 	sep 2 2007
 * 	Copyright by Alex Jonk, pageworks 
 */
/* a crud command can have several commandOptions */
class CrudCommand {

    var $version = "1.3.0";
    var $options = array();
    var $optionUrlTag = "option";
    var $actionUrlTag = "cmd";
    var $startFunction = "init"; //function that is initial called
    var $errorFunction = "error"; //function thats is called upon an error
    /*
     * 	first part of the adress that is rendered in the menu
     * example: index.php&option=company&act=new
     */
    var $preNavUrl = "index.php";

    function version() {
        echo $this->version;
    }

    /*
      author: alex.jonk@pageworks
      replaces a string command into a function call after checking its existstens.
      only registered functions can be called
     */

    function run() {
		if(isset($_REQUEST[$this->optionUrlTag])){
			$optionName = CrudCommand::xss_clean($_REQUEST[$this->optionUrlTag]);
		}
		
		if(isset($_REQUEST[$this->actionUrlTag])){
			$actionName = CrudCommand::xss_clean($_REQUEST[$this->actionUrlTag]);
		}

        if (!isset($optionName)) {
            /* no specific action is called */
            if (function_exists($this->startFunction)) {
                call_user_func($this->startFunction);
                return;
            } else {
                $this->handleError("no start defined, create function called " . $this->startFunction . " or set other point of entry");
                return;
            }
        }

        $action = $this->getAction($optionName, $actionName);
        if ($action == null) {
            echo "sorry no such action for this option!, " . $optionName . "_" . $actionName;
            return;
        }
        //check if the function is defined and exists
        $funname = $optionName . "_" . $action->functionname;
        if (function_exists($funname)) {
            //echo "caling the function";
            //var_dump($action->getParameterValues());
            if ($action->wrapAdminForm) {
                ?>
                <form name="adminForm" id="adminForm" method="post" class="form">
                    <?php
                    call_user_func_array($funname, $action->getParameterValues());
                    if (isset($_REQUEST['option'])) {
                        $option = $_REQUEST['option'];
                    }
                    if (isset($_REQUEST['Itemid'])) {
                        $Itemid = $_REQUEST['Itemid'];
                    }
                    ?>
                    <input type="hidden" name="unit" id="unit"/>
                    <input type="hidden" name="act" id="act" />
                    <input type="hidden" name="option" id="option" value="<?php echo $option; ?>"/>
                    <input type="hidden" name="Itemid" id="Itemid" value="<?php echo $Itemid; ?>"/>
                    <input type="hidden" name="boxchecked" id="boxchecked" />	
                </form>
                <?php

            } else {
                call_user_func_array($funname, $action->getParameterValues());
            }

        } else {
            $this->handleError("undefined function called by reference:" . $funname);
            return;
        }
    }


    function handleError($message = null) {
        if (function_exists($this->errorFunction)) {
            call_user_func($this->errorFunction, $message);
        } else {
            echo $message;
        }
	}

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

    


    /**
      returns the actionObject bases on optionname, actionname
      returns null if the action cant be found
     * */
    function getAction($optionName, $actionName) {

        if (!array_key_exists($optionName, $this->options)) {
            //echo "sorry no such option!";
            return null;
            print_r(array_keys($this->options));
            echo "<b>" . $optionName . "</b>";
        } else {
            //retrieve the option from the array
            $option = $this->options[$optionName];
            //check if the command exists in the array
            if (!array_key_exists($actionName, $option->actions)) {
                return array();
            } else {
                //retrieve the action from the array
                $action = $option->actions[$actionName];
                return $action;
            }
        }
    }

    function menu($menulevel, $alignment) {
        /* check what kind of glue we need */
        if (stristr($this->preNavUrl, '?') === FALSE) {
            $preNavUrl = $this->preNavUrl . "?";
        } else {
            $preNavUrl = $this->preNavUrl . "&";
        }

        $curOption = $_REQUEST['option'];
        /* vertical */
        if ($alignment == 1) {
            echo "<ul class=\"mainlevel\">";
            foreach ($this->options as $option) {
                foreach ($option->actions as $action) {
                    if ($action->menulevel == $menulevel) {
                        if ($curOption == $option->name) {
                            echo "<li><a class='active' href=\"" . $preNavUrl . $this->optionUrlTag . "=" . $option->name . "&" . $this->actionUrlTag . "=" . $action->name . "\">" . $action->label . "</a></li>";
                        } else {
                            echo "<li><a class=\"button\" href=\"" . $preNavUrl . $this->optionUrlTag . "=" . $option->name . "&" . $this->actionUrlTag . "=" . $action->name . "\">" . $action->label . "</a></li>";
                        }
                    }
                }
            }
            echo "</ul>";

            /* horizontal */
        } if ($alignment == 2) {
            echo "<table><tr class=\"mainlevel\">";

            foreach ($this->options as $option) {
                foreach ($option->actions as $action) {
                    if ($action->menulevel == $menulevel) {
                        if ($curOption == $option->name) {
                            echo "<td><a class='active' href=\"" . $preNavUrl . $this->optionUrlTag . "=" . $option->name . "&" . $this->actionUrlTag . "=" . $action->name . "\">" . $action->label . "</a></td>";
                        } else {
                            echo "<td><a class=\"button\" href=\"" . $preNavUrl . $this->optionUrlTag . "=" . $option->name . "&" . $this->actionUrlTag . "=" . $action->name . "\">" . $action->label . "</a></td>";
                        }
                    }
                }
            }
            echo "</tr></table>";
        }

    }

    function getTemplate() {

        $action = $this->getAction($this->getOptionValue(), $this->getActionValue());
        if ($action == null) {
            echo "sorry no such action for this option!, " . $optionName . "_" . $actionName;
        } else {
            return $action->template;
        }
    }

    /**
     * renders a menu specific to the currenct option name
     */
    function menuByOption($menulevel, $alignment) {
        //check the url for the option
        if (count($this->options[$this->getOptionValue()]) <= 0)
            return;

        $option = $this->options[$this->getOptionValue()];
        $preNavUrl = urlGlue($this->preNavUrl);

        /* vertical */
        if ($alignment == 1) {
            echo "<ul class=\"mainlevel\" >";
            foreach ($option->actions as $action) {
                if ($action->menulevel == $menulevel) {
                    $onclickevent = $preNavUrl . $this->optionUrlTag . "=" . $option->name . "&" . $this->actionUrlTag . "=" . $action->name;
                    ?><li><a href="<?php echo $onclickevent; ?>" class="button"><?php echo $action->label; ?></a><?php
                }
            }
            echo "</ul>";

            /* horizontal */
        } else if ($alignment == 2) {
            echo "<table border='0'><tr>";
            foreach ($option->actions as $action) {
                if ($action->menulevel == $menulevel) {
                    $onclickevent = $preNavUrl . $this->optionUrlTag . "=" . $option->name . "&" . $this->actionUrlTag . "=" . $action->name;
                    ?><td><a href="<?php echo $onclickevent; ?>" class="button"><?php echo $action->label; ?></a></td><?php
                    }
                }
                echo "</tr></table>";
            } else {
                echo "invalid alignment value: 1= vertical 2= Horizontal";
            }
        }

        /* renders a set of buttons with the label of the action */

        function crudButtons($menulevel, $alignment, $optionname = null) {
            if ($optionname == null) {
                if (count($option = $this->options[$this->getOptionValue()]) <= 0)
                    return;

            }else {
                if (count($option = $this->options[$optionname]) <= 0)
                    return;

            }

            /* vertical */
            if ($alignment == 1) {
                echo "<ul class=\"mainlevel\" >";
                foreach ($option->actions as $action) {
                    if ($action->menulevel == $menulevel) {
                        $onclickevent = $option->name . "_" . $action->name . "();";
                        ?><li><a href="javascript:<?php echo $onclickevent; ?>" class="button"><?php echo $action->label; ?></a></li><?php
                    }
                }
                echo "</ul>";
            } else if ($alignment == 2) {
                echo "<table border='0'><tr>";
                foreach ($option->actions as $action) {
                    if ($action->menulevel == $menulevel) {
                        $onclickevent = $option->name . "_" . $action->name . "();";
                        ?><td><a href="javascript:<?php echo $onclickevent; ?>" class="button"><?php echo $action->label; ?></a></td><?php
                }
            }
            echo "</tr></table>";
        } else {
            echo "invalid alignment value: 1= vertical 2= Horizontal";
        }
    }

    /* retrieve the option value from the request */

    function getOptionValue() {
        $optionName = $_REQUEST[$this->optionUrlTag];
        return $optionName;
    }

    /* retrieve the action value from the request */

    function getActionValue() {
        $actionName = $_REQUEST[$this->actionUrlTag];
        return $actionName;
    }


    function addCommand($optionName, $action) {
        $this->newCommand($optionName, $action);
    }


    /**
     * @deprecated use addCommand instead
     *  add a new action to an otpion 
     * */

    function newCommand($optionName, $action) {
        //check if action is valid;
        if ($action == '' || !is_object($action)) {
            echo "invalid action object!";
            exit;
        }

        //check if option already exists
        if (array_key_exists($optionName, $this->options)) {
            //retrieve existing option
            $option = $this->options[$optionName];
            //add a new action to it
            //$option->addAction($action);
            $option->actions[$action->name] = $action;
            /* reset the option */
            $this->options[$option->name] = $option;
        } else {
            $option = new commandOption($optionName, $action);
            $this->options[$option->name] = $option;
        }
        //print_r($this);
    }

}

/* a command option can have several actions */

class CommandOption {

    var $version = "1.0";
    var $name = null;
    var $actions = array();

    function version() {
        echo $version;
    }

    function commandOption($name, $action) {
        $this->name = $name;
        //add action to the array
        $this->actions[$action->name] = $action;
    }

    function addAction($action) {
        if ($action == '' || !is_object($action)) {
            echo "invalid action object!";
            exit;
        }
        foreach ($this->actions as $act) {
            echo $act->name . "<br>";
        }
//			print_r($this->actions);


        //print $action->name;
        echo "$action->name<br><hr>";
        $this->actions["" . $action->name] = $action;
    }

}

/*
  an action connects it all together. directs a request to a function name, defines which template
  to use, and is used to generate the menusystem
 */

class CommandAction {

    var $version = "1.0";
    var $name = null; // action which will be pplied on the option (new , edit, delete or view etc...)
    var $functionname = null;
    var $label = null; //later to be used to generate in the menu system
    var $menulevel;
    var $template = 'default';
    var $params = null;
    var $menuImage = null;
    var $wrapAdminForm = null;

    function version() {
        echo $this->version;
    }

    /**
     * 
     * @param $actionName		 
     * @param $params
     * @param $wrapAdminForm when set to true it wraps andmin form arrund this command function
     */
    function commandAction($actionName, $params = array(), $wrapAdminForm = false) {
        $this->name = $actionName;
        $this->functionname = $actionName;
        $this->params = $params;
        $this->wrapAdminForm = $wrapAdminForm;
    }

    /*
     * this function tries the get values from the rquest as they are defined in the $params  field and will be return in an array.
     */

    function getParameterValues() {
        $db = JFactory::getDbo();
        //check if any parameters are defined			
//			print_r($this->params);
        if ($this->params == null)
            return array();
        if (!is_array($this->params)) {
            die("false parameter array supplied for " . $this->name);
        }
        $retval = array();

        foreach ($this->params as $param) {
            /* arrays need different approach */
            if (isset($_REQUEST[$param])) {
                //if parameters is set				
                if (is_array($_REQUEST[$param])) {
                    /* iterate trough array */
					$tmp = CrudCommand::xss_clean($_REQUEST[$param]);
						$rt = array();
						foreach ($tmp as $key => $t){
							$rt[$key] = CrudCommand::xss_clean($db->escape(htmlspecialchars("" . $t)));
						}
						$retval[] = $rt;
					}else{
						$retval[] = $db->escape(htmlspecialchars("" . $_REQUEST[$param]));
					}
            } else {
                //oops parameter not set
                $retval[] = null;
            }

        }
        return $retval;
    }

}
