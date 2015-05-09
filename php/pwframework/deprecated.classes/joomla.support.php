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

if(!(defined('_VALID_MOS') || defined( '_JEXEC' ))){
	die( 'Restricted access' );
}

/**
 * initializes the framework
 */
loadFrameWork();

function isJoomlaOne(){
	if(defined('_JEXEC')){
		return false;
	}
	return true;
}


function getDBase(){
	if(isJoomlaOne()){
		global $database;
		return $database;
	}else{
		return JFactory::getDBO();
	}
}

function getLanguageName(){
	if(isJoomlaOne()){
		return $GLOBALS['mosConfig_lang'];
	}else{
		$ln = JFactory::getLanguage();
		return $ln->_metadata['backwardlang'];
	}
}


function redirect($url){
	if(isJoomlaOne()){
		mosRedirect($url);
	}else{
		$app = JFactory::getApplication();
		$app->redirect($url);
	}
}

function getLiveSite(){	
	if(isJoomlaOne()){
		global $mosConfig_live_site;
		return $mosConfig_live_site;
	}else{
		global $mainframe;
		return JURI::root();
	}
}

function getAbsPath(){
	if(isJoomlaOne()){
		global $mosConfig_absolute_path;
		return $mosConfig_absolute_path;
	}else{
		return  JPATH_SITE;
	}
}


/* loads the framework */
function loadFrameWork(){
	
	$frameworkfile = getAbsPath() . "/administrator/components/com_pwframework/pwframework.php";
	if(file_exists($frameworkfile)){
		$cssTheme = "pw3d";		
		require_once($frameworkfile);
		require_once(get_pwframework_path() . "/lib/common.php");		
	}
}

function get_pwframework_path(){	
	return getAbsPath() . "/administrator/components/com_pwframework";
}

function get_pwframework_site(){
	return JURI::root() . "/administrator/components/com_pwframework";
}

/* returns the user object*/
function getJoomlaUser(){
	if(isJoomlaOne()){
		global $my;
		return $my;		
	}else{
		$user = JFactory::getUser();
		return $user;
	}		
	
} 



function toMySQLDate($phpdate){
	return date( 'Y-m-d H:i:s', $phpdate );
}

function fromMySQLDate($mysqldate){
	return strtotime( $mysqldate );
}

/**
 * formats a DD-MM-YYYY year date to a mysql approved date
 * @param unknown_type $date
 * @return unknown_type
 */
function formatStringDateToMySql($date){
	$datum = explode("-", $date);
	return toMySQLDate(mktime(0,0,0,$datum[1],$datum[0], $datum[2]));
}

/**
 * formats a DD-MM-YYYY String from a mysql supplied date
 * @param unknown_type $date
 * @return unknown_type
 */
function formatStringDateFromMySql($date){
	return date('d-m-Y', strtotime($date) );
}



class JoomlaDatePicker extends Element{

	var $format;
	var $attributes;
	
	function JoomlaDatePicker($name, $value, $id = null, $format = 'd-m-Y', $attributes=null){
		$this->name = $name;
		$this->id = $id;
		$this->format = $format;
		$this->value = $value;
		$this->attributes = $attributes;
	}
		
	function render(){
		if(isset($this->value) && strcmp($this->value, "0000-00-00") <> 0 && $this->value !=null)	{
			try
			{
				$displayDate = JFactory::getDate($this->value);
				$datef = $displayDate->format($this->format);
			}
			catch (Exception $e)
			{
				$datef = null;
			}
			//echo "datumpie[" . $this->value . "]";
			//echo "datumpie[" . $datef. "]";
		}else{
			$datef = null;
		}
		echo JHTML::calendar($datef, $this->name, $this->id, "%d-%m-%Y", $this->attributes);		
	}
	
}



/* element extension for the framework*/
class Joomla15Editor extends Element {
	function Joomla15Editor($name, $width, $height, $value, $cols, $rows){
		$this->name = $name;
		$this->width = $width;
		$this->height = $height;
		$this->value = $value;
		$this->cols = $cols;
		$this->rows = $rows;
		$this->params = null;
		$this->displayButtons = false;
	}
	
	function render(){
		$editor =& JFactory::getEditor();				
		echo $editor->display($this->name,$this->value,$this->width, $this->height, $this->cols, $this->rows, $this->displayButtons, $this->params);
	}
}

/**
 * parses a joomla formated date and returns a JDate object
 * @param $item the item to set the date to. 
 * @param $date_field_name name of the field in the form that needs to be parsed
 * @param $format the date format
 * @return a Joomla JDate object
 */
function set_mysql_date($item , $date_field_name, $format){
	/* reformat time */
	$the_date = JArrayHelper::getValue($_REQUEST,$date_field_name);
	if(isset($the_date)){
		$date = strtotime($the_date);
		//echo $date;
		$the_date =  JFactory::getDate($date);	
		
		$item->{$date_field_name} = $the_date->toMySQL();
	}else{
		$item->{$date_field_name} = null;
	}	
}