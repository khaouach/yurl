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

require_once("class.element.php");

class table extends element {
	
	var $version = '1.0.2';
	var $dataArray=array();
	var $headers;
	var $debug = false;
	var $columns = array();
	var $footer = array();
	var $styleClass;
	var $padding;
	var $spacing;
	var $oddRowStyleClass;
	var $evenRowStyleClass;
	var $style;
	var $caption=null;
	var $groupBy = array();
        var $align = null;

	function version(){
		echo $version;
	}
	
    function table($styleClass="cstp-tabular", $padding="0", $spacing="0", $style=null) {
		$this->styleClass = $styleClass;
		$this->style = $style;
		$this->padding = $padding;
		$this->spacing = $spacing;
    }

    function setTableHeaders($headers = array(), $align="left"){
    	$this->headers = $headers;
        $this->align = $align;
    }

    function setData($dataObj = array()){
    	$this->dataArray  =$dataObj;
    }

    function setEvenRowStyleClass($styleClass){
    	$this->evenRowStyleClass = $styleClass;
    }

    function setOddRowStyleClass($styleClass){
    	$this->oddRowStyleClass = $styleClass;
    }

    function renderHeader(){
		echo "<thead><tr class=\"col-headings\" >";
		foreach($this->headers as $header){
			echo "<th style='text-align:$this->align;'>$header</th>";
		}
		echo "</tr></thead> \r\n";

    }

	/**
	* add a column
	* $table->addColumn(new Column("{task_id}"));
	$table->addColumn(new Column(new Element()));
	*/
    function addColumn($columnObj){
    	$this->columns[] = $columnObj;
    }

	/**
	* add footer class i.e. FooterSum
	*/
	function addFooter($foot){
		$this->footer[] = $foot;
	}
	
	/*
	 * give the fields in an array as string to group on.
	 * @param array of fields to group on. 
	 * @param $subFooter when true renders the footer on each group
	 */
	function groupBy($array, $subFooter = true){
		$this->groupBy = $array;
		$this->subFooter = $subFooter;		
	}
	
	/*
	 * subFooter is true when this method is called during grouping.
	 */
	function renderFooter($subFooter = false){
		if(count($this->footer) > 0){
			if(!$subFooter){
				echo "<tfoot>";
				$styleClass = ""; 
			}else{
				$styleClass = " class=\"table-row-group\" ";
			}
			echo "<tr $styleClass >";
			foreach($this->footer as $foot){

				echo "<td ";	
				/* add alignment */ 	
				if (isset($foot->align)){
					echo " align='$foot->align' ";
				}				
				echo " >";
				
				$foot->render();
				if($subFooter){
					$foot->reset();
				}
				echo "</td>";
			}	
			echo "</tr>";
			if(!$subFooter) 
				echo "</tfoot> \r\n";
		}
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

    function render(){
		$rowCount = 0;
		echo "<table ";
		if(isset($this->styleClass)){
			echo " class=\"" . $this->styleClass . "\" ";
		}
		
		if(isset($this->style)){
			echo " style=\"" . $this->style . "\" ";
		}
		
		
		if(isset($this->id)){
			echo " id=\"" . $this->id . "\" ";
		}

		if(isset($this->padding)){
			echo "cellpadding=\"" . $this->padding. "\" ";
		}
		if(isset($this->spacing)){
			echo "cellspacing=\"" . $this->spacing . "\" ";
		}

		echo "> \n";
		if($this->caption!=null){
			echo "<caption>$this->caption</caption>";
		}
		$this->renderHeader();
		
		/* used for grouping determination */
		$prevRow = null;
		
		if(is_array($this->dataArray)){
			if($this->debug) echo print_r($this->dataArray) . " <br><br>" ;
			echo "<tbody>";
			foreach($this->dataArray as $row){
				$rowCount++;
				
				
				/* see if we can group */
				if(count($this->groupBy) > 0 && $prevRow!=null){
					/* lets determine if we need grouping */
					foreach($this->groupBy as $grouper){
						if($prevRow->{$grouper} <> $row->{$grouper}){
							/* yes grouping is required */
							if($this->subFooter==false){
								echo "<tr class=\"table-row-group\">";
								echo "<td colspan=\"" . count($this->columns)  . "\" >";
								echo "&nbsp;</td></tr> \r\n";
							}else{
								/* render footer for this group */
								$this->renderFooter($this->subFooter);
							}								
						}
					}
				}
				
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
				echo $this->renderRow($row) ;
				echo "</tr> \r\n";
				
				/* call footer functions to do stuff like summerizing */
				if(count($this->footer) > 0){
					foreach($this->footer as $foot){
						if($foot!=null){
							$foot->execute($row);
						}
					}
				}
				
				
				/* used for grouping */	
				$prevRow = $row;
			}			
			echo "</tbody>";
			
    	}else if($this->debug){
    		echo "<em style=\"color:red;\">ERROR data provided is this is not array!</em>";
    	}		
		if(isset($this->subFooter)){
			$this->renderFooter($this->subFooter);			
		}
		
    	echo "</table>";
    }
}

class Footer{
	
	var $align=null;
	var $value = null; 
	
	function __construct($value, $align="left"){
		$this->value = $value;
		$this->align = $align;
	}
	/**
	 * write it to the output
	 * @return unknown_type
	 */
	function render(){
		
	}
	
	/**
	* @param $data is the data object that represents the data row of the table/
	* do calculations 
	*/
	function execute($data){
		//ignore
	}
	
	/** reset any calculations */
	function reset(){
		
	}
}

class FooterHTML extends Footer{

	function render(){
		echo $this->value;
	}
}

class FooterSum extends Footer{

	var $paramName = null; //the name of the paramater to summerize
	var $sum = 0;
	
	function __construct($paramName, $align="left"){
		$this->paramName = $paramName;
		$this->align = $align;
	}
	/**
	* @param $data is the data object that represents the data row of the table/
	*/
	function execute($data){
		$this->sum =  $this->sum + $data->{$this->paramName};
	}
	
	function reset(){
		$this->sum = 0;
	}

	function render(){
		echo $this->sum;
	}
}

/**
 * Like footer sum but formats is as an amount
 * @author Alex
 *
 */
class FooterSumAmount extends FooterSum{

	function render(){
		$locale_info = localeconv();    	
		echo $locale_info->currency_symbol . "" .number_format($this->sum, 2, $locale_info->decimal_point, $locale_info->thousands_sep);
	}
}


class FooterAvg extends Footer{

	var $paramName = null; //the name of the paramater to summerize
	var $sum = 0;
	var $count = 0;
	
	function __construct($paramName, $align="left"){
		$this->paramName = $paramName;
		$this->align = $align;
	}
	/**
	* @param $data is the data object that represents the data row of the table/
	*/
	function execute($data){
		$this->sum =  $this->sum + $data->{$this->paramName};
		$this->count++;
	}
	
	function reset(){
		$this->sum = 0;
		$this->count = 0;
	}

	function render(){
		echo $this->sum / $this->count;
	}
}

class BootstrapTable extends element {

    	
        var $responsive = null;
        var $striped = null;
        var $bordered = null;
        var $hover = null;
        var $condensed = null;
        var $version = '3';
	var $dataArray=array();
	var $headers;
        var $headers_align= "left";
	var $debug = false;
	var $columns = array();
	var $footer = array();
	var $styleClass;
	var $style;
	var $caption=null;
	var $groupBy = array();
        var $align = null;

	function version(){
		echo $version;
	}
	
    function table($styleClass="table", $padding_left="0", $padding_right="0", $style=null, $responsive=null, $striped=null, $bordered=null, $hover=null, $condensed=null, $order_column=null){
                $this->padding_left = $padding_left;
                $this->padding_right = $padding_right;
                $this->style = $style;
		$this->responsive = $responsive;
		$this->striped = $striped;
		$this->bordered = $bordered;
		$this->hover = $hover;
                $this->condensed = $condensed;
                $this->order_column = $order_column;
    }
    /**
     * 
     * @param type $valuefieldName
     * @param type $idOfHiddenValueInput
     */
    function addRadioColumn($valuefieldName="id", $idOfHiddenValueInput= "the_id"){
        //$id_name = sprintf("cb{%s}", $field);
        $cb_name = sprintf("cb{%s}", $valuefieldName);
        $selBox = new InputRadioButton("cid",'',$cb_name); //name, value, id
	$selBox->setOnClickEvent(sprintf("jQuery('#%s').val('{%s}');",$idOfHiddenValueInput, $valuefieldName, $this->id));
	$this->addColumn(new PWColumn($selBox));	
    }
        //$selBox = new InputRadioButton("cid",'',"cb{id}"); //name, value, id
	//$selBox->setOnClickEvent("jQuery('#the_id').val('{id}'); jQuery('tr.selected').removeClass('selected'); jQuery('#projects_table input[name=cid]:checked').closest('tr').addClass('selected');");
	//$table->addColumn(new Column($selBox));	
    
    
    
    function setTableHeaders($headers = array(), $align="left", $headerStyle=null, $styledHeaders= array("")){
    	$this->headers = $headers;
        $this->align = $align;
        $this->headerStyle = $headerStyle;
        $this->styledHeaders = $styledHeaders;
    }

    function setData($dataObj = array()){
    	$this->dataArray  =$dataObj;
    }

    function renderHeader(){
		echo "<thead><tr class=\"col-headings\" >";
		foreach($this->headers as $header){
                    echo '<th class="sorting" style="';
                    if ($this->styledHeaders!=null && in_array($header, $this->styledHeaders)){ 
			echo 'text-align:'. $this->align . ';' . $this->headerStyle;
                    }else{
			echo 'text-align:' . $this->align . ';';
                    }
                    if ($this->condensed !=null){  
                        echo 'padding-left: '. $this->padding_left .'px; padding-right: '. $this->padding_right .'px;';
                    } 
                    echo  ' ">'.$header .'</th>';
                    
                }
		echo "</tr></thead> \r\n";

    }

	/**
	* add a column
	* $table->addColumn(new Column("{task_id}"));
	$table->addColumn(new Column(new Element()));
	*/
        function addColumn($columnObj){
    	$this->columns[] = $columnObj;
        }

	/**
	* add footer class i.e. FooterSum
	*/
	function addFooter($foot){
		$this->footer[] = $foot;
	}
	
	/*
	 * give the fields in an array as string to group on.
	 * @param array of fields to group on. 
	 * @param $subFooter when true renders the footer on each group
	 */
	function groupBy($array, $subFooter = true){
		$this->groupBy = $array;
		$this->subFooter = $subFooter;		
	}
	
	/*
	 * subFooter is true when this method is called during grouping.
	 */
	function renderFooter($subFooter = false){
		if(count($this->footer) > 0){
			if(!$subFooter){
				echo "<tfoot>";
				$styleClass = ""; 
			}else{
				$styleClass = " class=\"table-row-group\" ";
			}
			echo "<tr $styleClass >";
			foreach($this->footer as $foot){

				echo "<td ";	
				/* add alignment */ 	
				if (isset($foot->align)){
					echo " align='$foot->align' ";
				}				
				echo " >";
				
				$foot->render();
				if($subFooter){
					$foot->reset();
				}
				echo "</td>";
			}	
			echo "</tr>";
			if(!$subFooter){ 
				echo "</tfoot> \r\n";
                        }
		}
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

    function render(){
		$rowCount = 0;
                
                //-----------------
                if ($this->responsive!= null){
                    echo '<div class="table-responsive">';
                }
                ?>
                <table 
                    <?php
                    if(isset($this->id)){
			echo " id=\"" . $this->id . "\" ";
		}
                    ?>
                    class="
                       <?php echo $this->styleClass; ?> 
                       <?php if ($this->bordered !=null){  echo " table-bordered";}?> 
                       <?php if ($this->striped !=null){  echo " table-striped";}?> 
                       <?php if (isset($this->hover) && $this->hover !=null){  echo " table-hover";}?> 
                       <?php if (isset($this->condensed) && $this->condensed !=null){  echo " table-condensed";}?> 
                       <?php if (isset($this->order_column) && $this->order_column !=null){  echo " order-column";}?>             
                    "
                >
                <?php
                //-----------------

		if($this->caption!=null){
			echo "<caption>$this->caption</caption>";
		}
		$this->renderHeader();
		
		/* used for grouping determination */
		$prevRow = null;
		
		if(is_array($this->dataArray)){
			if($this->debug) echo print_r($this->dataArray) . " <br><br>" ;
			echo "<tbody>";
			foreach($this->dataArray as $row){
				$rowCount++;
				
				
				/* see if we can group */
				if(count($this->groupBy) > 0 && $prevRow!=null){
					/* lets determine if we need grouping */
					foreach($this->groupBy as $grouper){
						if($prevRow->{$grouper} <> $row->{$grouper}){
							/* yes grouping is required */
							if($this->subFooter==false){
								echo "<tr class=\"table-row-group\">";
								echo "<td colspan=\"" . count($this->columns)  . "\" >";
								echo "&nbsp;</td></tr> \r\n";
							}else{
								/* render footer for this group */
								$this->renderFooter($this->subFooter);
							}								
						}
					}
				}
				
				echo "<tr ";
				echo ">";
				echo $this->renderRow($row) ;
				echo "</tr> \r\n";
				
				/* call footer functions to do stuff like summerizing */
				if(count($this->footer) > 0){
					foreach($this->footer as $foot){
						if($foot!=null){
							$foot->execute($row);
						}
					}
				}
				
				
				/* used for grouping */	
				$prevRow = $row;
			}			
			echo "</tbody>";
			
    	}else if($this->debug){
    		echo "<em style=\"color:red;\">ERROR data provided is this is not array!</em>";
    	}		
		if(isset($this->subFooter)){
			$this->renderFooter($this->subFooter);			
		}
		
    	echo "</table>";
        if ($this->responsive!= null){
                    echo '</div>';
                }
                
        ?>
        <script>
                    
                jQuery(function(){
			jQuery("input.display_only[type=checkbox]:unchecked").parent().html("<div class='unchecked'></div>");
			jQuery("input.display_only[type=checkbox]:checked").parent().html("<div class='checked'></div>");
			//jQuery("input[type=checkbox]").attr('readonly', 'readonly');				
		});
                
          
        </script>            
        <?php
    }
    
    /**
     * creates the html for the row cell radiobutton used for a table
     * @param type $tableId
     * @param type $rowId
     * @param type $idFieldId
     * @return type
     */
    public static function getRadioButton($tableId, $rowId, $idFieldId="the_id"){
        $button = sprintf('<input type="radio" name="cid" id="cb%d" onclick="jQuery(\'#%s\').val(%d);">'
                    , $rowId, $idFieldId,$rowId,$tableId);
        
        return $button;
    }
    
    public static function dataTableize($table_id, $options=null){
        if($options == null){
           $options = new DataTableOptions();
        }
            
        if(count($options->aoColumns)==0){
            unset($options->aoColumns);
        }
        printf("jQuery('#%s').dataTable(%s);", $table_id, json_encode($options));
               
    }
    
    /**
     * creates javascript code that enabled single click for the radio button
     * @param type $tableId
     * @param type $idFieldId
     */
    public static function rowCheckJavascript($tableId, $idFieldId="the_id"){
        ?>        
         // to enable selecting an item by clicking anywhere in the row:
        jQuery("#<?php echo $tableId;?> tr td").mousedown(function (e) {
            var row = jQuery(this).parent();
             switch (event.which) {
            case 1:  //left mouse
                if (jQuery(row).hasClass('selected') ) {
                    jQuery(row).removeClass('selected');
                    jQuery('#<?php echo $idFieldId;?>').val(-1);
                    jQuery(row).find('input:radio').prop('checked', false);
                    jQuery('#<?php echo $tableId;?> input[name=cid]:checked').closest('tr').removeClass('selected');
                }else {
                    jQuery(row).find('input:radio').prop('checked', true);
                    var intRegex = /[0-9 -()+]+$/;  
                    var the_value = parseInt(jQuery(row).find('input:radio').attr('id').match(intRegex));
                    jQuery('#<?php echo $idFieldId;?>').val(the_value);
                    jQuery('tr.selected').removeClass('selected');
                    jQuery('#<?php echo $tableId;?> input[name=cid]:checked').closest('tr').addClass('selected');
                }
                break;
            case 3: //right buttons
                  jQuery(row).find('input:radio').prop('checked', true);
                    var intRegex = /[0-9 -()+]+$/;  
                    var the_value = parseInt(jQuery(row).find('input:radio').attr('id').match(intRegex));
                    jQuery('#<?php echo $idFieldId;?>').val(the_value);
                    jQuery('tr.selected').removeClass('selected');
                    jQuery('#<?php echo $tableId;?> input[name=cid]:checked').closest('tr').addClass('selected');
            }
        });
        <?php
        
    }
    /**
     * creates javascript code that enabled single click for the checkbox button
     * @param type $tableId
     * @param type $idFieldId
     */
    public static function multirowCheckJavascript($tableId, $idFieldId="the_id"){
        ?>        
         // to enable selecting an item by clicking anywhere in the row:
         
        /* Click event handler */
               
        jQuery("#<?php echo $tableId;?> tr td").live('click',(function (e) {
            var row = jQuery(this).parent();
            var intRegex = /[0-9 -()+]+$/;  
            var the_value = parseInt(jQuery(row).find('input:checkbox').attr('id').match(intRegex));
            var id = the_value;
            // console.log("id ="+id);
            var index = jQuery.inArray(id, aSelected);
            // console.log("index = "+index);
            if (jQuery(row).hasClass('selected') ) {
                aSelected.splice( index, 1 );
                jQuery(row).removeClass('selected');
                jQuery('#<?php echo $idFieldId;?>').val(-1);
                jQuery(row).find('input:checkbox').prop('checked', false);
            }else {
                aSelected.push( id );
                jQuery(row).find('input:checkbox').prop('checked', true);
                jQuery('#<?php echo $idFieldId;?>').val(the_value);
                jQuery('#<?php echo $tableId;?> input[name=cid]:checked').closest('tr').addClass('selected');
            }
            // console.log(aSelected);
            // console.log("the number of selected items = "+aSelected.length);
        }));
       
        <?php     
    }
}

class DataTableOptions{
     
    var $iDisplayLength = 25;
    var $bAutoWidth = false;
    var $order = array(array(1, "asc"));            
    var $aoColumns = array();
    var $ordering = true;
    
    public function addColumnDefinition($size){
        for($i = 0 ; $i < $size; $i++){
            $this->aoColumns[] = null;
        }
    }
    public function addColumn($bSearchable=false, $bSortable=false){
        $column = new stdClass;
        $column->bSearchable = $bSearchable;
        $column->bSortable = $bSortable;                
        $this->aoColumns[] = $column;
    }
            
}