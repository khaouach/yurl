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

/* the button */	

class ButtonBarButton{
	var $label = null;
	var $act = null;
	var $script = null;
	var $title = null;
	var $image_url = null;
	var $styleClass = "pw_button";
	
	function __construct($label, $title, $act, $script=null, $image_url =null ){
		$this->label = $label;
		$this->act = $act;
		$this->title = $title;
		$this->script = $script;
		$this->image_url = $image_url;
	}
	
	function render($unit){
		echo '<div style="float:left;" >'
				,'<a class="', $this->styleClass, '" title="', $this->title ,'" href="javascript:bb_', $unit . "_", $this->act, '()">';
				
				$style="";
				if(isset($this->image_url)){
					$style="style=\"background-image: url(" . $this->image_url .");\""; 
				}					
				?>				
				<span <?php echo $style;?> ></span>
				<?php echo $this->label;?>
			</a>
		</div>
		<?php 
	}
	
	function renderScript($unit_id, $act_id, $unit, $formname){
		/** render custom script */
		if(strlen($this->script) > 0){
			echo $this->script;
			return;
		}
                printf("function bb_%s_%s(){ 
                        jQuery(\"#%s\").val(\"%s\");
			jQuery(\"#%s\").val(\"%s\");		
			document.%s.submit();
                        }", $unit, $this->act,
                        $unit_id, $unit,
                        $act_id, $this->act,
                        $formname);
                }
//	}
}

/**
* bar that contains the buttons 
*/
class ButtonBar{
	var $unit;                
        var $unit_id = "unit";  
        var $act_id = "act";
	var $buttons = array();
        var $formname;
	
	function addButton($button){
		$this->buttons[] = $button;
	}
	
	function __construct($unit, $formname="adminForm"){
		$this->unit = $unit;
                $this->formname = $formname;
	}
	
	function render(){
		?><div class="pw-buttonbar" id="bb-<?php echo $this->unit;?>"><?php 
		foreach ($this->buttons as $button){
			$button->render($this->unit, $this->formname);
		}
		?>
		</div>
		<div style="clear:both"></div>
		<script type="text/javascript">
			<?php 
				foreach ($this->buttons as $button){                                
                                            $button->renderScript($this->unit_id, $this->act_id, $this->unit, $this->formname);                                       
                                }
			?>
		</script>
		<?php 	
	}
}

/**
* bar that contains the buttons 
*/
class BootButtonBar{
	var $unit;                
        var $unit_id = "unit";  
        var $act_id = "act";
	var $buttons = array();
        var $formname;
        var $size = "btn-group-md";
		
	function __construct($unit, $formname="adminForm", $size = "btn-group-md"){
		$this->unit = $unit;
                $this->formname = $formname;
                $this->size = $size;
	}
        
        function addButton($button){
		$this->buttons[] = $button;
	}
	
	function render(){
		?><div class="btn-group <?php echo $this->size;?>" id="bb-<?php echo $this->unit;?>"><?php 
		foreach ($this->buttons as $button){
			$button->render($this->unit, $this->formname, $this->size);
		}
		?>
		</div>
                <div id="crud-clear"></div>
		<script type="text/javascript">
			<?php 
				foreach ($this->buttons as $button){                                
                                            $button->renderScript($this->unit_id, $this->act_id, $this->unit, $this->formname);                                       
                                }
			?>
		</script>
		<?php 	
	}
        
        public static function CrudBar($unit, $formName="adminForm", $size = "btn-group-md", $unit_id="unit", $act_id="act", $create=true, $edit=true, $delete=true, $more_buttons = null){
            $buttonbar = new BootButtonBar($unit, $formName, $size);
            $buttonbar->unit_id = $unit_id;
            $buttonbar->act_id = $act_id;

                                            //$label, $title, $act, $script;
            $b4 = new BootButtonBarButton(JText:: _('JACTION_CREATE'), JText:: _('JACTION_CREATE'), "create", null, "glyphicon-plus", "btn-success", null);
            $b5 = new BootButtonBarButton(JText:: _('JACTION_EDIT'), JText:: _('JACTION_EDIT'), "edit", null, "glyphicon-pencil", "btn-default", null);
            $b6 = new BootButtonBarButton(JText:: _('JACTION_DELETE'), JText:: _('JACTION_DELETE'), "delete"
                                                , 'function bb_'. $unit. '_delete(){
                                                    if(typeof aSelected != "undefined" && aSelected.length >1){
                                                    bootbox.alert(_select_only_one);
                                                    }else{
                                                    bootbox.confirm("'. JText:: _("COM_STORYBOARD_REMOVE_ARE_YOU_SURE") . '", function(result){
                                                            if(result === true){
                                                                jQuery("#' . $unit_id . '").val("' . $unit . '");
                                                                jQuery("#' . $act_id . '").val("delete");		
                                                                document.'.  $formName . '.submit();   
                                                            }else{
                                                                return;
                                                            }
                                                    });
                                                }}'
                                                , "glyphicon-trash", "btn-default", null);

            if($create){$buttonbar->addButton($b4);}
            if($edit){$buttonbar->addButton($b5);}
            if($delete){$buttonbar->addButton($b6);}
            
            //add custom buttons
            if(is_array($more_buttons)){
                foreach($more_buttons as $button){
                    $buttonbar->addButton($button);
                }
            }elseif ($more_buttons!=null){
                   $buttonbar->addButton($more_buttons);
            }
            $buttonbar->render();
        }
}
class BootButtonBarButton{
        
	var $label = null;
	var $act = null;
	var $script = null;
	var $title = null;
	var $glyphicon = null;
	var $styleClass = "btn-default";
        var $width = null;
	
	function __construct($label, $title, $act, $script=null, $glyphicon =null, $styleClass="btn-default", $width=null ){
               
                $this->label = $label;
		$this->act = $act;
		$this->title = $title;
		$this->script = $script;
		$this->glyphicon = $glyphicon;
                $this->styleClass = $styleClass;
                $this->width = $width;
	}
	
	function render($unit){
            ?>
		<!--<div class="<?php echo $this->width ?>" >-->
                        <a class="btn <?php echo $this->styleClass; ?>" title="<?php echo $this->title; ?>" href="javascript:bb_<?php echo $unit . '_' . $this->act .'()'; ?>">
				<?php
				if($this->glyphicon !=null){
                                    ?>
                                    <span class="glyphicon <?php echo $this->glyphicon; ?>"></span>
                                    <?php
				}					
				?>				
			<?php echo $this->label;?></a>
		<!--</div> -->
		<?php 
	}
	
	function renderScript($unit_id, $act_id, $unit, $formname){
		/** render custom script */
		if(strlen($this->script) > 0){
			echo $this->script;
			return;
		}
                printf("function bb_%s_%s(){ 
                        if(typeof aSelected != 'undefined' && aSelected.length >1){
                        bootbox.alert(_select_only_one);
                        }else{
                        jQuery(\"#%s\").val(\"%s\");
			jQuery(\"#%s\").val(\"%s\");		
			document.%s.submit();
                        }}", $unit, $this->act,
                        $unit_id, $unit,
                        $act_id, $this->act,
                        $formname);
                }
//	}
}