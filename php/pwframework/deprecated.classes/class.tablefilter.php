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
 * Creates a filter to be used to filter data in a table 
 * or database.
 * In Order to reset the search values delete the cookie
 * with name "filter" . $this->name
 * @author Alex
 *
 */
class TableFilter extends Form{		
	
	var $input_elements = array();
	
	function render($table = true){	
		$this->id = "pwfilter";	
		$this->determineValues();		
		parent::render();	
	}
	
	/* determining the values */
	function determineValues(){ 
		$from_request = false; 		// if a value has been found on the request it it set to true
		
		/* loop through the element on the form */
		foreach($this->elements as $row){
			foreach($row->elements as $element){				
				if($element!=null 
					&& class_exists(get_class($element)) 
					&& get_parent_class("Element")					
					){			
					
					/* if it contains an Hidden field it will be ignored from the query */
					if(get_class($element)=="InputHidden") continue;
					
					/* since strpos even returns 0 when the string is not found ?
					 * I had to trick around it by adding a space in front
					 * So if Input is found it is never found at position 0
					 * " InputText" instead of "InputText"
					 * 
					 * Filtering on Input, Select and DatePicker excludes Button
					 * We don't want the value of a button
					 */ 					 
					$pos = intval(strpos(" " . get_class($element),"Input"));
					if($pos==0){
						// Do select box 
						$pos = intval(strpos(" " . get_class($element),"Select"));
					}
					
					if($pos==0){
						// Do DatePicker 
						$pos = intval(strpos(" " . get_class($element),"DatePicker"));						
					}
					if($pos > 0){
																	
						/* preserve the found element to store data in cookies later */						
						$this->input_elements[] = $element;													
						/* get anything off the request */
						if(isset($_REQUEST[$element->name])){
							
							$req_val= $_REQUEST[$element->name];
						
							if(strlen($req_val) > 0){
								$element->value = $req_val; 							
								$from_request = true;
							}									
						}
					}
				}
			}			
		}
		if($from_request==true){
			$this->store_cookies($this->input_elements);
		}else{
			$this->read_cookies($this->input_elements);
		}
	}
	/**
	 * stores cookies with the values form the elements
	 * @param $input_elements
	 */
	function store_cookies($input_elements){
		$arr = array();
		foreach($input_elements as $element){
			$e = null;
			$e->name = $element->name;	
			$e->value = $element->value;
			$arr[] = $e;	
		}
		$cookie_value = json_encode($arr);
//		echo $cookie_value;
		setcookie("filter" . $this->name ,$cookie_value);
	}
	
	/**
	 * sets the values of the elemnts with the data from the cookies
	 * @param $input_elements
	 */
	function read_cookies($input_elements){
		if(isset($_COOKIE['filter' . $this->name])){			
			$cookie = $_COOKIE['filter' . $this->name];
			$cookie_values = json_decode(stripslashes($cookie));
			if(count($cookie_values) === 0) return;
			if(count($input_elements) > 0){
				foreach($input_elements as $element){
					foreach($cookie_values as $value_obj){
						if($value_obj->name == $element->name){
							$element->value = $value_obj->value;
							break;
						}
					}				
				}
			}
		}
	}
	
	/**
	 * Use after calling render() method
	 */
	function getSqlWhereClause(){
		$sql = " 1=1 ";
		foreach($this->input_elements as $element){
			//print_r($element);
			if(strlen($element->value) > 0 ){
			  $sql .=" and " . $element->name . " like '%" . $element->value . "%'"; 			
			}
		}
		return $sql;
	}
	
	/**
	 * Returns array with fields and values
	 */
	function getFieldValues(){
		$retval = array();		
		foreach($this->input_elements as $element){
			if(strlen($element->value) > 0 ){			
			  $retval[$element->name] = $element->value; 			
			}
		}
		return $retval;
	}
	
	/**
	 * Returns the value for the specified field.
	 */
	function getFieldValue($var_name){
		$retval = array();		
		foreach($this->input_elements as $element){
			if(strlen($element->value) > 0 && $element->name ==$var_name){							
			  return $element->value; 			
			}
		}
		return "";
	}
}