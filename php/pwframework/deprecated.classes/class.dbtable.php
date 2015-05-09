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

/* ************************************************************************************************
 *  Copyrighted by
*  Alex Jonk  @Pageworks, 2007
 * All rights reserved.
 * this code may only be distributed with software of Pageworks.
 * If you wish to use use this code with any other project or product
*  please contact alex.jonk@pageworks.nl for further information

* version information
* version 1.00
* date 20-09-2007
 **************************************************************************************************/

require_once("class.element.php");

class dbtable extends element {
	var $version = "1.0";
	var $dataArray=array();
	var $headers;
	var $debug = false;
	var $columns = array();
	var $styleClass;
	var $padding = "0";
	var $spacing = "0";
	var $oddRowStyleClass;
	var $evenRowStyleClass;
	var $style;
	var $align;
	var $db_table;
	var $db_sql_where;
	var $database;
	var $items_per_page = 20;
	var $record_low; //calculated
	var $display_rownum =true; //shows the rownum if true

	function version(){
		echo $version;
	}
	
    function dbtable($styleClass="cstp-tabular", $padding="0", $spacing="0", $style=null, $align=null) {
		$this->styleClass = $styleClass;
		$this->style = $style;
		$this->padding = $padding;
		$this->spacing = $spacing;
		$this->align = $align;
    }

    function setTableHeaders($headers = array()){
    	$this->headers = $headers;
    }

    function setDataTable($dataObj){
    	$this->db_table = $dataObj;
    }
    
    function setSqlWhereClause($where){
    	$this->db_sql_where;
    }
    
    function setDisplayRows($rows){
    	$this->items_per_page = $rows;
    }

    function setDatabase(&$database){
    	$this->database = $database;
    }
    
    function setEvenRowStyleClass($styleClass){
    	$this->evenRowStyleClass = $styleClass;
    }

    function setOddRowStyleClass($styleClass){
    	$this->oddRowStyleClass = $styleClass;
    }

    function renderHeader(){
		echo "<tr class=\"col-headings\">";
		//render an empty column header for the rownumbers
		if($this->display_rownum){
			echo "<th></th>";
		}

		foreach($this->headers as $header){
			echo "<th>$header</th>";
		}
		echo "</tr>";

    }

    function addColumn($columnObj){
    	$this->columns[] = $columnObj;
    }

    //will render any element events added to the column
    function renderColumnEvents(){

    }


    /**
     * renders a single row
     */
    function renderRow(&$obj){
    	if(class_exists(get_class($obj))){
			foreach($this->columns as $column){
				$column->setRowData($obj);
				$column->render();
			}
    	}else if($this->debug){
    		echo "<em style=\"color:red;\">class".  get_class($obj).  " of object not found!</em>";
    	}
    }

    //returns the user where clause
    function where(){
    	if(isset($this->db_sql_where)){
    		return  " and "  . $this->db_sql_where;
    	}
    }
    
    function render(){
    	//first count the number of records
    	$sql = "select count(*) from " . $this->db_table . " where 1";
		$sql .= $this->where();    	
    	
    	$this->database->setQuery($sql);
    	$total_records = $this->database->loadResult();
    	    	
    	//then create a selection based on the current page and the number of items to display
    	$cur_page = $_REQUEST['pwg'];
    	if(!isset($cur_page)) $cur_page = 1;
    	    	
		/*
		* rendering page header
		*/
		
		$pager = new pager($total_records);
		$pager->itemsPerPage = $this->items_per_page;
		$sql_limit = $pager->render($total_records);
		//$sql_limit = $this->table_list_navigation_bar($total_records,$cur_page);
		//create the query
		$sql = "select * from " . $this->db_table . " where 1 ";
    	$sql .= $this->where();		
    	$sql .= $sql_limit;
    	
		$this->database->setQuery($sql);
		
		if(!$this->database->query()){
			error('an error occured! while running query'. $this->database->getErrorMsg());
		}
		
    	//run the query and return the resulst in the array
    	$this->dataArray = $this->database->loadObjectList();
		
    	
    	
		echo "<table width=\"100%\" ";
		if(isset($this->styleClass)){
			echo " class=\"" . $this->styleClass . "\" ";
		}
		
		if(isset($this->style)){
			echo " style=\"" . $this->style . "\" ";
		}

		if(isset($this->padding)){
			echo "cellpadding=\"" . $this->padding. "\" ";
		}
		if(isset($this->spacing)){
			echo "cellspacing=\"" . $this->spacing . "\" ";
		}

		if(isset($this->align)){
			echo "align=\"" . $this->align . "\" ";
		}

		echo "> \n";
		
		
		/*
		* render the columnheadings
		*/
		$this->renderHeader();
		
		/*
			rendering the rest of the table
		*/
		if(is_array($this->dataArray)){
			if($this->debug) echo print_r($this->dataArray) . " <br><br>" ;
			foreach($this->dataArray as $row){
				
				$rowCount++;
				echo "<tr ";
				
				if($rowCount % 2 ==0){
					if(isset($this->evenRowStyleClass)){
						echo " class=\"" . $this->evenRowStyleClass . "\"";
					}
				}else{
					if(isset($this->oddRowStyleClass)){
						echo " class=\"" . $this->oddRowStyleClass . "\"";
					}
				}
				
				echo ">";
				
				//display the rownumbers
				if($this->display_rownum){
					echo "<td class=\"rownum\">" . ($this->record_low + $rowCount)  . "</td>";				
				}
				
				echo $this->renderRow($row) . "\n";
				
			echo "</tr>";
			}
    	}else if($this->debug){
    		echo "<em style=\"color:red;\">ERROR data provided is this is not array!</em>";
    	}
    	echo "</table>";
    }	
}