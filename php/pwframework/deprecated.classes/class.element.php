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
/**
*10-juli-2007
v1..1..0
Took the Javascript to its onw class so it can be used to extend by  other classes than the element class
*/

if(!(defined('_VALID_MOS') || defined( '_JEXEC' ))){
	die( 'Restricted access' );
}

class JavaScriptEvents{
	var $version = "1.1.0";
	var $onChange;
	var $onClick;
	var $onBlur;
	var $onFocus;
	var $onDblClick;
	var $onMouseDown;
	var $onMouseUp;
	var $onMouseOver;
	var $onMouseMove;
	var $onMouseOut;
	var $onKeyPress;
	var $onKeyDown;
	var $onKeyUp;
	
	function version(){
		echo $version;
	}

	function setOnClickEvent($function) {
		$this->onClick = " onclick=\"" . $function . "\"";
	}

	function getOnClickEvent() {
		return $this->onClick;
	}

	function setOnChangeEvent($function) {
		$this->onChange = " onchange=\"" . $function . "\"";
	}

	function getOnChangeEvent() {
		return $this->onChange;
	}


	/**
	 * on blur
	 */
	function setOnBlurEvent($function) {
		$this->onBlur = " onblur=\"" . $function . "\"";
	}
	function getOnBlurEvent() {
		return $this->onBlur;
	}

	/**
	 * on focus
	 */
	function setOnFocusEvent($function) {
		$this->onFocus = " onfocus=\"" . $function . "\"";
	}
	function getOnFocusEvent() {
		return $this->onFocus;
	}


	/**
	 * double click
	 */
	function setOnDblClickEvent($function){
		$this->onDblClick = " onDblClick=\"" . $function . "\"";
	}
	function getOnDblClickEvent(){
		return $this->onDblClick;
	}

	/**
	 * mouse down
	 */
	function setOnMouseDownEvent($function){
		$this->onMouseDown = " onMouseDown=\"" . $function . "\"";
	}
	function getOnMouseDownEvent(){
		return $this->onMouseDown;
	}

	/**
	 * mouse up
	 */
	function setOnMouseUpEvent($function){
		$this->onMouseUp = " onMouseUp=\"" . $function . "\"";
	}
	function getOnMouseUpEvent(){
		return $this->onMouseUp;
	}

	/**
	 * mouse over
	 */
	function setOnMouseOverEvent($function){
		$this->onMouseOver = " onMouseOver=\"" . $function . "\"";
	}
	function getOnMouseOverEvent(){
		return $this->onMouseOver;
	}

	/**
	 * mouse move
	 */	
	function setOnMouseMoveEvent($function){
		$this->onMouseMove = " onMouseMove=\"" . $function . "\"";
	}
	function getOnMouseMoveEvent(){
		return $this->onMouseMove;
	}

	/**
	 * mouse out
	 */
	function setOnMouseOutEvent($function){
		$this->onMouseOut = " onMouseOut=\"" . $function . "\"";
	}
	function getOnMouseOutEvent(){
		return $this->onMouseOut;
	}

	/**
	 * keypress
	 */
	function setOnKeyPressEvent($function){
		$this->onKeyPress = " onKeyPress=\"" . $function . "\"";
	}
	function getOnKeyPressEvent(){
		return $this->onKeyPress;
	}

	/**
	 * keyDown
	 */
	function setOnKeyDownEvent($function){
		$this->onKeyDown = " onKeyDown=\"" . $function . "\"";
	}
	function getOnKeyDownEvent(){
		return $this->onKeyDown;
	}

	/**
	 * Key up
	 */
	function setOnKeyUpEvent($function){
		$this->onKeyUp = " onKeyUp=\"" . $function . "\"";
	}
	function getOnKeyUpEvent(){
		return $this->onKeyUp;
	}

	/*
	* renders all existing events
	*/
	function renderEvents(){
		if(isset($this->onChange)) echo $this->onChange;
		if(isset($this->onClick)) echo $this->onClick;
		if(isset($this->onBlur)) echo $this->onBlur;
		if(isset($this->onDblClick)) echo $this->onDblClick;
		if(isset($this->onMouseDown)) echo $this->onMouseDown;
		if(isset($this->onMouseUp)) echo $this->onMouseUp;
		if(isset($this->onMouseOver))echo $this->onMouseOver;
		if(isset($this->onMouseMove)) echo $this->onMouseMove;
		if(isset($this->onMouseOut)) echo $this->onMouseOut;
		if(isset($this->onKeyPress))echo $this->onKeyPress;
		if(isset($this->onKeyDown))echo $this->onKeyDown;
		if(isset($this->onKeyUp))echo $this->onKeyUp;
	}
}

/*
* Created on 30-okt-2006 By alex jonk
*/
class Element extends JavaScriptEvents {

	var $name;
	var $id;
	var $value;
	var $styleClass;
	var $style;
	var $title;

	function Element($name, $id, $value, $styleClass) {
		$this->name = $name;
		$this->id = $id;
		$this->value = $value;
		$this->styleClass = $styleClass;
	}

	function getName(){
		return $this->name;
	}

	function setName($name){
		$this->name =  $name;
	}

	function getId(){
		return $this->id;
	}

	function setId($id){
		$this->id =  $id;
	}

	function getValue(){
		return $this->value;
	}
	
	function setTitle($title){
		$this->title = $title;
	}
	
	function getTitle(){
		return $this->title;
	}

	function setValue($value){
		$this->value =  $value;
	}
	function getStyleClass(){
		return $this->styleClass;
	}

	function setStyleClass($styleClass){
		$this->styleClass =  $styleClass;
	}
	function getStyle(){
		return $this->style;
	}

	function setStyle($style){
		$this->style =  $style;
	}

	function render(){

		if(isset($this->id)){
			echo " id=\"" . $this->id ."\" ";
		}
				
		if(isset($this->style)){
			echo " style=\"" . $this->style . "\"";
		}

		if (isset($this->styleClass)){
			echo " class=\"$this->styleClass\" ";
		}
		
		if (isset($this->title)){
			echo " title=\"$this->title\" ";
		}



	}
}