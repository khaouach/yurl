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
class PWColumn{
	var $value;
	var $styleClass;
	var $rowData;
	var $style;
	var $align;
        var $valueObj = null;
		
	function version(){
		$version = "1.1.1";
		echo $version;
	}
	/**
	 * Column constructor
	 *
	 * @param String or Object of subclass Element $value
	 * @param String $styleClass
	 * @return Column
	 */


	function PWColumn($valueObj, $styleClass=null, $style=null, $align=null){
		$this->valueObj = $valueObj;
		$this->styleClass = $styleClass;		
		$this->style = $style;
		$this->align = $align;
		
	}
	
	/* clone function for php4 and up ofcourse */
	function cloneobj($obj){		
		$cls = get_class($obj);
		$n = null; //fixes pass by argument error
		$retval = new $cls($n,$n,$n,$n);
		foreach (get_object_vars($obj) as $k => $v) {
			if( substr( $k, 0, 1 ) != '_' ) {			// internal attributes of an object are ignored
				$retval->{$k} = $v;
			}
		}
		return $retval;
		//return $obj;
	}

	/**
	 * render the current column
	 *
	 */
	function render(){
		echo "<td ";
		if (isset($this->styleClass)){
			echo " class='$this->styleClass' ";
		}
		//echo $this->style;
		if (isset($this->style)){
			echo " style='$this->style' ";
		}
		
			//echo $this->style;
		if (isset($this->align)){
			echo " align='$this->align' ";
		}
		
		echo " >";
		
		if(is_object($this->valueObj)){
			if(is_subclass_of($this->valueObj, "Element")){
				/*
				* since the vars are a template we need to clone the object instead of change the
				* the original vars with the replaced value
				*/
				$elem  = $this->cloneobj($this->valueObj);
				//print_r($elem);
				/*
				 * replace all vars of the cloned element with values
				 * form the rowData
				 */
				$vars= get_object_vars($elem);
				foreach ($vars as $name => $value) {
					/* parse all vars */
					$elem->{$name} =$this->parseString($value);
				}
				$elem->render();
			}
		}else if(is_array($this->valueObj)){
			if($this->hasVar());
			
		}else{
			//column  has no element
			if(is_string($this->valueObj)){
				echo $this->parseString($this->valueObj);
			}else {
				echo ("unknown element");
				
			}
		}
                echo "</td>";
	}
	/**
	 * Set an object which contains row specific data
	 *
	 * @param object $rowData
	 */
	function setRowData($rowData){
		$this->rowData = $rowData;
	}

	/**
	 * @param String $str
	 * checks if a string contains a var name
	 */
	function hasVar($str){
		$pos1 = stripos("" . $str, "{");
		$pos2 = stripos("" . $str, "}");

		if($pos1!== false || $pos2 !== false ){
			return true;
		} else{
			return false;
		}
	}

	function parseString($str){
		if(!is_string($str)){
			return $str;
		}
		$rest=$str;
		$pos1=true;
		$pos2=0;
		$lBrackets = substr_count($str,"{");
		$rBrackets = substr_count($str, "}");
		if($lBrackets==$rBrackets){
			while($this->hasVar($rest)){
				$pos1 = stripos("" .$rest, "{");
				$first = substr ($rest,  0 , $pos1);
				$pos2 = stripos("" .$rest, "}");
				$var =  substr ($rest,  $pos1 + 1,($pos2 -1)-$pos1);
				$retval = $this->getVarValueFromObject($var);
				$rest =  $first . $retval . substr ($rest,  $pos2 + 1);
			}
			return $rest;
		}else{
			echo "ERROR: unequal bracketing!.". $str;
			return null;
		}
	}
	/**
	 * retrieves the variable name from the string: "{id}" will return "id"
	 */
	function getVar($val){

		$rest=$val;
		/* zoek eerste positie van var en kijk of er een { in zit */
		$pos1 = stripos("" .$val, "{");
		$pos2 = stripos("" .$val, "}");
		$var= substr ($rest,  $pos1 + 1, ($pos2-$pos1)-1);
		return $var;
	}

	/**
	 * Retrieve the value from the row data object from the given parameter
	 */
	function getVarValueFromObject($var){
		return $this->rowData->{$var};
	}
}


/*
a label that can be used in the amdmin environment of Joomla. It has a feature of a mouse over help feature
*/
class ColumnDateFormatter extends PWColumn {

	var $value;
	var $format;
	var $align;

	function __construct($value, $format,$styleClass=null, $style=null, $align="center"){
		$this->value = $value;
		$this->format = $format;
		$this->styleClass = $styleClass;		
		$this->style = $style;
		$this->align = $align;
	}

	function render(){
		$param = $this->parseString($this->value);
		
		$date_value = JFactory::getDate($param);
		
		

		if(!isset($this->format)){
			echo "<td style=\"color:red;\">Format not set</td>";
		}
		
		echo "<td";	
		
		if (isset($this->styleClass)){
			echo " class='$this->styleClass' ";
		}
		//echo $this->style;
		if (isset($this->style)){
			echo " style='$this->style' ";
		}
		
		
		if (isset($this->align)){
			echo " align='$this->align' ";
		}
		
		echo ">";
		
		echo  $date_value->toFormat($this->format) . "</td>";
		
	}
}



/*
a label that can be used in the amdmin environment of Joomla. It has a feature of a mouse over help feature
*/
class ColumnAmountFormatter extends PWColumn {

	var $value;
	var $format;
	var $align;

	function __construct($value, $locale=null, $styleClass=null, $style=null, $align="right"){
		$this->value = $value;
		$this->locale = $locale;
		$this->styleClass = $styleClass;		
		$this->style = $style;
		$this->align = $align;
	}

	function render(){
		$param = $this->parseString($this->value);
		
		echo "<td";	
		
		if (isset($this->styleClass)){
			echo " class='$this->styleClass' ";
		}
		//echo $this->style;
		if (isset($this->style)){
			echo " style='$this->style' ";
		}
		
		
		if (isset($this->align)){
			echo " align='$this->align' ";
		}
		
		echo ">";
		//echo $this->locale;
		if(isset($this->locale)){
			setlocale(LC_MONETARY, $this->locale);
		}		
		$locale_info = localeconv();    	
    	//print_r($locale_info);
		echo   $locale_info->currency_symbol . "" .number_format($param, 2, $locale_info->decimal_point, $locale_info->thousands_sep);
		echo "</td>";
		
	}
}
