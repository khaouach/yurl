<?php
/** 
 * @package		PWFramework
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

 
 // Import library dependencies
$PW_LOCATION = "/pwframework";
$PW_PATH = JPATH_SITE . $PW_LOCATION;
//JOOMLA definition, required but needs no value


/**
 * Stub the JFactory
 */
class JFactory{
    
    public static function getApplication(){
        return new JApplication();
    }
}

class JInput{
    
      
    public static function get($name){
        $retval = null;
        if(array_key_exists($name, $_REQUEST)){
            $retval =  $_REQUEST[$name];            
        }        
        return $retval;
    }
    
    public function getString($name){
        return $this->get($name);
    }
}


class JApplication{
     var $input = null;
     
     public function __construct(){
         $this->input = new JInput();
     }            
    
    public function enqueueMessage($message, $type=null){
        echo printf("%s , %s", $message , $type);
    }
}


/**
 * overriding a joomla import function thaths used in the plugin
 * @param type $string
 */
function jimport($string){
    
}

class JPlugin{
    
}
		
function loadIfClassExistence($file){
	if(file_exists($file)){
		require_once($file);
	}else{
		echo "Unable to load class with in file :" . $file . " because it does not exist, continueing happily";
	}

}

/*loading required classes and libraries */
require_once ( "pwframework.php");
require_once ( "lib/pw.common.php");
require_once ( "lib/pw.command.php");
