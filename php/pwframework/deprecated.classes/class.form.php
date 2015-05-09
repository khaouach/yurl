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

/*
  v1.0.10 - 12/4/2012
  fix Composite value error
 * 
  v1.0.9
  + added style to all elements (super class)

  v1.0.8
 * Changed some comapares wicht fixed readonly and disabled attributes
 * fixed image alternate namel
  15-03-2008

  V 1.0.6
  date 5-11-2007
  + added File element


 * ##
  V 1.0.6
 * Added the label class to the InputText elemnt, Now you can add your custom Label class and render a custom label
 *  added the multi option for the select boxes


 * version 1.05
 * date 23-06-2007
 * + added InputButton

 * version 1.04
 * date 16-06-2007
 * + added readonly and disabled feature for
 * InputText
 * InputTextArea
 * SelectBox
 * SelectBoxObject

 * version information
 * version 1.03
 * date 26-05-2007
 * changed selection error on combo box
 * + added InputRadioButton widget





 * Created on 21-aug-2006
 *
 */

class Form extends Composite {

    var $elements = array(); //hierin wordt de form gebouwd
    var $name = null;
    var $id;
    var $formLabel = null;
    var $method = 'POST'; //default post
    var $actionUrl = '';
    var $onSubmitEvent;
    var $styleClass;

    function version() {
        $version = "1.0.8";
        echo $version;
    }

    function Form($name, $value = null, $id = null, $actionUrl = null, $method = 'POST') {
        $this->name = $name;
        $this->value = $value;
        $this->id = $id;
        $this->actionUrl = $actionUrl;
        $this->method = $method;
    }

    //schrijft de form
    function render() {
        ?>
        <form 
            name="<?php echo $this->name; ?>" 
            <?php
            if (isset($this->actionUrl)) {
                echo " action=\"" . $this->actionUrl . "\" ";
            }
            if (isset($this->method)) {
                echo " method=\"" . $this->method . "\" ";
            }
            if (isset($this->style)) {
                echo " style=\"" . $this->style . "\" ";
            }

            if (isset($this->styleClass)) {
                echo " class=\"" . $this->styleClass . "\" ";
            }

            if (isset($this->id)) {
                echo " id=\"" . $this->id . "\" ";
            }

            if (isset($this->onSubmit)) {
                $this->onSubmit;
            }
            ?>

            ><?php
        parent::render();
            ?>
        </form>
        <?php
    }

    //element specific event which is triggered when a form is submitted
    function addOnSubmitEvent($function) {
        $this->onSubmit = " onSubmit=\"" . $function . "\"";
    }

}

//represents a row on the form
class FormRow {

    var $elements = array();
    var $size;

    function FormRow($elements) {
        if (!is_array($elements)) {
            error("invalid version of pwframework, please upgrade!");
            //print_r($elements);
            exit;
        }

        $this->elements = $elements;

        for ($i = (count($this->elements) - 1); $i >= 0; $i--) {
            //find the first not null element, thats the size if this array.
            //null objects are considrerd empty spaces unless they are at the end
            //  null | null | InputText | null | null  -> size is 3
            if ($this->elements[$i] != null) {
                $this->size = $i;
                break;
            }
        }
    }

}

class Composite {

    var $elements = array(); //hierin wordt de form gebouwd
    var $styleClass;
    var $id;
    var $style;
    var $value;

    function version() {
        $version = "1.0.8";
        echo $version;
    }

    function Composite($id = null, $styleClass = null) {
        $this->styleClass = $styleClass;
        $this->id = $id;
    }

    //schrijft de form
    function render() {
        ?>
        <fieldset >
            <?php
            if (isset($this->value)) {
                echo '<legend>', $this->value, '</legend>';
            }
            ?>
            <table cellpadding="0" cellspacing="0"
            <?php
            if (isset($this->style)) {
                echo " style=\"" . $this->style . "\" ";
            }

            if (isset($this->styleClass)) {
                echo " class=\"" . $this->styleClass . "\" ";
            }
            ?>			

                   >
                       <?php
                       //determine max colomns
                       $maxsize = 0;
                       foreach ($this->elements as $row) {

                           //check if the element is  a form row
                           if (strtolower(get_class($row)) != "formrow") {
                               error("invalid version of pwframework, please upgrade!");
//					echo get_class($row);
                               exit;
                           }
                           if ($row->size > $maxsize)
                               $maxsize = $row->size;
                       }

                       //render the form
                       foreach ($this->elements as $row) {

                           echo "<tr> \r\n";
                           for ($i = 0; $i <= $maxsize; $i++) {
                               $element = $row->elements[$i];
                               echo "\t <td class=\"column$i\" >\r\n";
                               if ($element != null && class_exists(get_class($element)) && get_parent_class("Element")) {
                                   $element->render();
                               } else {
                                   if ($element != null) {
                                       $callers=debug_backtrace();
                                       echo "died on method/function : ". $callers[1]['function'];
                                       error("invalid object of class " . get_class($element));
                                       die();
                                   } else {
                                       echo "&nbsp;";
                                   }
                               }
                               echo "\t </td> \r\n";
                           }
                           echo "</tr> \r\n";
                       }
                       ?>
            </table>
        </fieldset>		
        <?php
    }

    function writeStyle() {
        ?><style>
            div.label{
                width: 200px;
                float:left;
            }			
            li {
                padding-top: 3px;				
                list-style:none;			
            }
            input{
                padding-left:3px;
            }

        </style>


        <?php
    }

    function writeStyleHorizontal() {
        ?><style>
            div.label{
                padding: 4px;
                float:left;
                font-size:8pt;
            }
            ul{
                display:block;
                padding-left:15px;
                padding-right:15px;	
            }		
            li {
                padding-top: 3px;				
                list-style:none;			
                float:left;
            }
            input{
                padding-left:3px;
            }

        </style>


        <?php
    }

    /* only first elements is  mandetory */

    function addElement($elem1, $elem2 = null, $elem3 = null, $elem4 = null, $elem5 = null, $elem6 = null) {
        $elem = array($elem1, $elem2, $elem3, $elem4, $elem5, $elem6);
        $this->elements[] = new FormRow($elem);
    }

}

/**
 *
 * */
class InputText extends Element {

    var $version = "1.1.0";
    var $maxsize;
    var $size;
    var $readonly;
    var $disabled;

	function InputText($name, $value, $id=null,  $styleClass=null, $size=null, $maxsize=null, $readonly=null, $disabled=null, $style=null){
        $this->name = $name;
        $this->id = $id;
        $this->value = $value;
        $this->styleClass = $styleClass;
        $this->size = $size;
        $this->maxsize = $maxsize;
        $this->disabled = $disabled;
        $this->readonly = $readonly;
        $this->style = $style;
    }

    function render() {
        ?>
        <input type="text" name="<?php echo $this->name; ?>" 
        <?php
        if (isset($this->value)) {
            echo " value=\"" . $this->value . "\" ";
        }

        parent::render();

        if (isset($this->size)) {
            echo " size='$this->size' ";
        }
        if (isset($this->maxsize)) {
            echo " maxlength='$this->maxsize' ";
        }

        if (isset($this->readonly) && $this->readonly == true) {
            echo " readonly=\"readonly\" ";
        }

        if (isset($this->disabled) && $this->disabled == true) {
            echo " DISABLED ";
        }
        

        echo $this->renderEvents();
        echo "/>";
    }

}

class InputPassword extends Element {

    var $version = "1.1.0";
    var $maxsize;
    var $size;
    var $readonly;
    var $disabled;

    function InputPassword($name, $value, $id = null, $styleClass = null, $size = null, $maxsize = null, $readonly = null, $disabled = null) {
        $this->name = $name;
        $this->id = $id;
        $this->value = $value;
        $this->styleClass = $styleClass;
        $this->size = $size;
        $this->maxsize = $maxsize;
        $this->disabled = $disabled;
        $this->readonly = $readonly;
    }

    function render() {
        ?>
               <input type="password" name="<?php echo $this->name; ?>" 
               <?php
               if (isset($this->value)) {
                   printf(' value="%s" autocomplete="false" ' ,  $this->value);
               }

               parent::render();

               if (isset($this->size)) {
                   echo " size='$this->size' ";
               }
               if (isset($this->maxsize)) {
                   echo " maxlength='$this->maxsize' ";
               }

               if (isset($this->readonly) && $this->readonly == true) {
                   echo " readonly=\"readonly\" ";
               }

               if (isset($this->disabled) && $this->disabled == true) {
                   echo " DISABLED ";
               }

               echo $this->renderEvents();
               echo "/>";
           }

       }

       class InputTextArea extends Element {

           var $version = "1.1.0";
           var $rows;
           var $columns;
           var $readonly;
           var $disabled;

           function InputTextArea($name, $value, $id = null, $styleClass = null, $rows = null, $columns = null, $readonly = null, $disabled = null) {
               $this->name = $name;
               $this->id = $id;
               $this->value = $value;
               $this->styleClass = $styleClass;
               $this->rows = $rows;
               $this->columns = $columns;
               $this->readonly = $readonly;
               $this->disabled = $disabled;
           }

           function render() {
               echo "<textarea name=\"" . $this->name . "\" ";

               parent::render();

               if (isset($this->rows)) {
                   echo " rows='$this->rows' ";
               }

               if (isset($this->columns)) {
                   echo " cols='$this->columns' ";
               }
               if (isset($this->readonly) && $this->readonly == true) {
                   echo " readonly='readonly' ";
               }

               if (isset($this->disabled) && $this->disabled == true) {
                   echo " DISABLED ";
               }

               $this->renderEvents();
               echo ">";

               if (isset($this->value)) {
                   echo $this->value;
               }
               echo "</textarea>";
           }

       }

       class SelectBox extends Element {

           var $version = "1.1.0";
           var $arrayValue;
           var $arrayLabel;
           var $multi;
           var $readonly;
           var $disabled;

           function SelectBox($name, $value = null, $id = null, $arrayValue, $arrayLabel, $styleClass = null, $multi = null, $readonly = null, $disabled = null) {
               $this->name = $name;
               $this->id = $id;
               $this->arrayValue = $arrayValue;
               $this->arrayLabel = $arrayLabel;
               $this->styleClass = $styleClass;
               $this->value = $value;
               $this->multi = $multi;
               $this->readonly = $readonly;
               $this->disabled = $disabled;
           }

           function render() {
               echo "<select name=\"" . $this->name . "\" ";

               parent::render();

               if (isset($this->multi)) {
                   echo " multiple =\"MULTIPLE\" ";
               }

               if (isset($this->readonly) && $this->readonly == true) {
                   echo " readonly='readonly' ";
               }
               if (isset($this->disabled) && $this->disabled == true) {
                   echo " DISABLED ";
               }

               $this->renderEvents();
               //CLOSE IT
               echo " >";

               for ($i = 0; $i < count($this->arrayLabel); $i++) {
                   echo "<option value=\"" . $this->arrayValue[$i] . "\"";
                   if ($this->arrayValue[$i] == $this->value)
                       echo "selected=\"selected\" ";
                   ?> >
            <?php echo $this->arrayLabel[$i]; ?></option><?php
            echo "\n";
        }
        echo "</select> \n";
    }

}

/**
 * Same as SelectBox but now the array key surves as the value and value as label
 */
class SelectBoxArray extends Element {

    var $version = "1.1.0";
    var $arrayValue;
    var $multi;
    var $readonly;
    var $disabled;

    function SelectBoxArray($name, $value = null, $id = null, $arrayValue, $styleClass = null, $multi = null, $readonly = null, $disabled = null) {
        $this->name = $name;
        $this->id = $id;
        $this->arrayValue = $arrayValue;
        $this->styleClass = $styleClass;
        $this->value = $value;
        $this->multi = $multi;
        $this->readonly = $readonly;
        $this->disabled = $disabled;
    }

    function render() {
        echo "<select name=\"" . $this->name . "\" ";

        parent::render();

        if (isset($this->multi)) {
            echo " multiple =\"MULTIPLE\" ";
        }

        if (isset($this->readonly) && $this->readonly == true) {
            echo " readonly='readonly' ";
        }
        if (isset($this->disabled) && $this->disabled == true) {
            echo " DISABLED ";
        }

        $this->renderEvents();
        //CLOSE IT
        echo " >";

        foreach ($this->arrayValue as $key => $value) {
            echo "<option value=\"" . $key . "\"";

            if ($key == $this->value)
                echo "selected=\"selected\" ";

            echo " >";
            echo $value, "</option>", "\n";
        }
        echo "</select> \n";
    }

}

class SelectBoxObject extends Element {

    var $version = "1.1.0";
    var $valueId;
    var $labelId;
    var $object;
    var $multi;
    var $disabled;

    /**
     *  as SelectBox but now you pass an object instead of an array. Also pass 2 parameters which represent the object vars
     *
     * @param string $name
     * @param string $value
     * @param string $id
     * @param object $object
     * @param unknown_type $valueId
     * @param string $labelId
     * @param string $styleClass
     * @param string $label
     * @return SelectBoxObject
     */
    function SelectBoxObject($name, $value = null, $id = null, $object, $valueId, $labelId, $styleClass = null, $multi = null, $disabled = null) {
        $this->name = $name;
        $this->id = $id;
        $this->valueId = $valueId;
        $this->labelId = $labelId;
        $this->styleClass = $styleClass;
        $this->value = $value;
        $this->object = $object;
        $this->multi = $multi;
        $this->disabled = $disabled;
    }

    function render() {
        echo "<select name=\"" . $this->name . "\" ";

        parent::render();

        if (isset($this->multi)) {
            echo " multiple =\"MULTIPLE\" ";
        }

        if (isset($this->disabled) && $this->disabled == true) {
            echo " DISABLED ";
        }

        $this->renderEvents();
        //CLOSE IT
        echo " >";
        /* if multi select is true we have to convert the value */
        if ($this->multi == true) {
            $this->value = explode(',', $this->value);
        }
        //print_r($this->value);
        foreach ($this->object as $item) {
            if (is_object($item)) {
                $vars = get_object_vars($item);
                foreach ($vars as $name => $val) {
                    if ($name == $this->valueId) {
                        echo "<option value=\"" . $val . "\" ";
                        if ($this->isSelected($val)) {
                            echo " selected=\"selected\" >";
                        } else {
                            echo ">";
                        }
                        continue; //breaks the foreach itteration
                    }
                }
                foreach ($vars as $name => $val) {
                    if ($name == $this->labelId) {
                        echo $val . "</option> \n";
                        continue; //breaks the foreach itteration
                    }
                }
            }
        }
        echo "</select> \n";
    }

    function isSelected($val) {
        if ($this->multi == true) {
            if (in_array($val, $this->value)) {
                return true;
            }
        } else if ($val == $this->value) {
            return true;
        }

        return false;
    }

}

class InputCheckBox extends Element {

    var $version = "1.0";
    var $label;

    function InputCheckBox($name, $value, $id = null, $styleClass = null, $replace_by_image = null) {
        $this->name = $name;
        $this->id = $id;
        $this->value = $value;
        $this->styleClass = $styleClass;
        $this->replace_by_image = $replace_by_image;
    }

    function render() {
        if($this->replace_by_image == true){
                echo " <span ";
                if (isset($this->value)) {
                    if ($this->value == '1' || $this->value == 'on' || $this->value == true || $this->value == 'CHECKED') {
                        echo " class='glyphicon glyphicon-ok' ";
                    }else{
                        echo " class='glyphicon glyphicon-remove' "; 
                    }
                }
                echo "></span>";
            }else{
                echo "<input type=\"checkbox\" name=\"" . $this->name . "\" ";

                parent::render();

                if (isset($this->value)) {
                    if ($this->value == '1' || $this->value == 'on' || $this->value == true || $this->value == 'CHECKED') {
                        echo " checked='checked' ";
                    }

                }
                $this->renderEvents();
                echo ">";
            }
        
    }

}

class InputHidden extends Element {

    var $version = "1.0";

    function InputHidden($name, $value, $id) {
        $this->name = $name;
        $this->id = $id;
        $this->value = $value;
    }

    function render() {
        echo "<input type=\"hidden\" name=\"" . $this->name . "\" ";

        if (isset($this->value)) {
            echo " value=\"$this->value\" ";
        }

        parent::render();

        echo " />";
    }

}

/*
 * radio button for use outside a group
 */

class InputRadioButton extends Element {

    var $version = "1.1.0";

    function InputRadioButton($name, $value, $id = null, $styleClass = null) {
        $this->name = $name;
        $this->id = $id;
        $this->value = $value;
        $this->styleClass = $styleClass;
    }

    function render() {
        echo "<input type=\"radio\" name=\"" . $this->name . "\" ";

        parent::render();

        if (isset($this->value)) {
            if ($this->value == '1' || $this->value == 'on' || $this->value == true || $this->value == 'CHECKED') {
                echo " checked='checked' ";
            }
        }
        $this->renderEvents();
        echo ">";
    }

}

/*
  multiple radio buttons
 */

class RadioGroup extends Element {

    var $version = "1.1.0";
    var $arrayValue;
    var $arrayLabel;

    function RadioGroup($name, $value = null, $id = null, $arrayValue, $arrayLabel, $styleClass = null) {
        $this->name = $name;
        $this->id = $id;
        $this->arrayValue = $arrayValue;
        $this->arrayLabel = $arrayLabel;
        $this->styleClass = $styleClass;
        $this->value = $value;
    }

    function render() {
        echo "<fieldset>";
        for ($i = 0; $i < count($this->arrayValue); $i++) {
            ?>
            <label><input 
                    type="radio" 
                    name="<?php echo $this->name ?>" 
            <?php if ($this->arrayValue[$i] == $this->value) echo " CHECKED "; ?>
                    value="<?php echo $this->arrayLabel[$i]; ?>" 
                    class="<?php echo $this->styleClass; ?>"   /> <?php echo $this->arrayLabel[$i]; ?></label>

            <?php
            echo "\n";
        }
        echo "</fieldset>";
        echo "\n";
    }

}

class SubmitButton extends Element {

    var $version = "1.1.0";

    function SubmitButton($name, $value, $id = null, $styleClass = null) {
        $this->name = $name;
        $this->id = $id;
        $this->styleClass = $styleClass;
        $this->value = $value;
    }

    function render() {
        echo "<input type=\"submit\" ";

        if (isset($this->value)) {
            echo " value='$this->value' ";
        }
        if (isset($this->name)) {
            echo " name='$this->name' ";
        }

        parent::render();

        $this->renderEvents();
        echo " />";
    }

}

class InputFile extends Element {

    var $version = "1.0";

    function render() {
        echo "<input type=\"file\" ";

        if (isset($this->value)) {
            echo " value='$this->value' ";
        }
        if (isset($this->name)) {
            echo " name='$this->name' ";
        }

        parent::render();

        $this->renderEvents();
        echo " />";
    }

}

class InputButton extends Element {

    var $version = "1.0";

    function InputButton($name, $value, $id = null, $styleClass = null) {
        $this->name = $name;
        $this->id = $id;
        $this->styleClass = $styleClass;
        $this->value = $value;
    }

    function render() {
        echo "<input type=\"button\" ";

        if (isset($this->value)) {
            echo " value='$this->value' ";
        }
        if (isset($this->name)) {
            echo " name='$this->name' ";
        }

        parent::render();

        $this->renderEvents();
        echo " />";
    }

}

class InputImage extends Element {

    var $version = "1.1.1";
    var $imageSource;
    var $width;
    var $height;
    var $alt;

    /**
     *  @param string $title
     * 	@param string $value
     * 	@param string $id
     *  @param string $styleClass
     *  @param string $width
     *  @param string $height
     *
     */
    function InputImage($title, $value, $id = null, $styleClass = null, $alt = null, $width = null, $height = null) {
        $this->title = $title;
        $this->id = $id;
        $this->styleClass = $styleClass;
        $this->value = $value;
        $this->width = $width;
        $this->height = $height;
        $this->alt = $alt;
    }

    function render() {

        echo "<img ";

        if (isset($this->value)) {
            echo " src=\"$this->value\" ";
        }
        if (isset($this->title)) {
            echo " title=\"$this->title\" ";
        }

        parent::render();

        if (isset($this->alt)) {
            echo " alt=\"$this->alt\" ";
        }
        if (isset($this->width)) {
            echo " width=\"$this->width\" ";
        }

        if (isset($this->height)) {
            echo " height=\"$this->height\" ";
        }
        $this->renderEvents();
        echo " />";
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function getTitle() {
        return $this->title;
    }

    function setAlt($alt) {
        $this->alt = $alt;
    }

    function getAlt() {
        return $this->alt;
    }

    function setWidth($width) {
        $this->width = $width;
    }

    function getWidth() {
        return $this->width;
    }

    function setHeight($height) {
        $this->height = $height;
    }

    function getHeight() {
        return $this->height;
    }

}

class SubmitImage extends Element {

    var $version = "1.0";
    var $imageSource;
    var $width;
    var $height;

    /**
     *  @param string $name
     * 	@param string $value
     * 	@param string $id
     *  @param string $styleClass
     *  @param string $imageSource
     *  @param string $width
     *  @param string $height
     *
     */
    function SubmitImage($name, $value, $id, $styleClass = null, $imageSource, $width = null, $height = null) {
        $this->name = $name;
        $this->id = $id;
        $this->styleClass = $styleClass;
        $this->value = $value;
        $this->imageSource = $imageSource;
        $this->width = $width;
        $this->height = $height;
    }

    function render() {
        echo "<input type=\"image\" ";

        if (isset($this->value)) {
            echo " value='$this->value' ";
        }
        if (isset($this->name)) {
            echo " name='$this->name' ";
        }

        parent::render();

        if (isset($this->imageSource)) {
            echo " src='$this->imageSource' ";
        }
        if (isset($this->width)) {
            echo " width='$this->width' ";
        }
        if (isset($this->height)) {
            echo " height='$this->height' ";
        }
        $this->renderEvents();
        echo " />";
    }

    function setAlt($alt) {
        $this->alt = $alt;
    }

    function getAlt() {
        return $this->alt;
    }

    function setWidth($width) {
        $this->width = $width;
    }

    function getWidth() {
        return $this->width;
    }

    function setHeight($height) {
        $this->height = $height;
    }

    function getHeight() {
        return $this->height;
    }

}

class Image extends Element {

    var $version = "1.1.0";
    var $imageSource;
    var $width;
    var $height;
    var $atl;

    /**
     *  @param string $title
     * 	@param string $value
     * 	@param string $id
     *  @param string $styleClass
     *  @param string $width
     *  @param string $height
     *
     */
    function Image($title, $value, $id = null, $styleClass = null, $alt = null, $width = null, $height = null) {
        $this->title = $title;
        $this->id = $id;
        $this->styleClass = $styleClass;
        $this->value = $value;
        $this->width = $width;
        $this->height = $height;
        $this->alt = $alt;
    }

    function render() {
        echo "<img ";

        if (isset($this->value)) {
            echo " src=\"$this->value\" ";
        }
        if (isset($this->title)) {
            echo " title=\"$this->title\" ";
        }

        parent::render();

        if (isset($this->alt)) {
            echo " alt=\"$this->alt\" ";
        }
        if (isset($this->width)) {
            echo " width=\"$this->width\" ";
        }

        if (isset($this->height)) {
            echo " height=\"$this->height\" ";
        }
        $this->renderEvents();
        echo " />";
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function getTitle() {
        return $this->title;
    }

    function setAlt($alt) {
        $this->alt = $alt;
    }

    function getAlt() {
        return $this->alt;
    }

    function setWidth($width) {
        $this->width = $width;
    }

    function getWidth() {
        return $this->width;
    }

    function setHeight($height) {
        $this->height = $height;
    }

    function getHeight() {
        return $this->height;
    }

}

class PWAnchor extends Element {

    var $version = "1.0";
    var $target;
    var $title;
    var $label;
    var $coords;

    /**
     * add an anchor to your form, this can be a bookmark aswell as an external link
     *
     * @param unknown_type $name
     * @param unknown_type $value
     * @param unknown_type $id
     * @param unknown_type $styleClass
     * @param unknown_type $label
     * @param unknown_type $target
     * @param unknown_type $title
     * @param unknown_type $coords
     * @param unknown_type $shape
     * @return Anchor
     */
    function PWAnchor($name, $value, $id = null, $styleClass = null, $label = null, $target = null, $title = null, $coords = null, $shape = null) {
        $this->name = $name;
        $this->value = $value;
        $this->id = $id;
        $this->styleClass = $styleClass;
        $this->target = $target;
        $this->shape = $shape;
        $this->title = $title;
        $this->label = $label;
        $this->coords = $coords;
    }

    function render() {
        echo "<a ";

        if (isset($this->value)) {
            echo " href=\"$this->value\" ";
        }
        if (isset($this->name)) {
            echo " name='$this->name' ";
        }

        if (isset($this->title)) {
            echo " title='$this->title' ";
        }

        if (isset($this->target)) {
            echo " target='$this->target' ";
        }

        parent::render();

        $this->renderEvents();
        echo " >";

        if (isset($this->label)) {
            echo $this->label;
        }
        echo "</a>";
    }

    function setLabel($label) {
        $this->label = $label;
    }

    function getLabel() {
        return $this->label;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function getTitle() {
        return $this->title;
    }

    function setTarget($target) {
        $this->target = $target;
    }

    function getTarget() {
        return $this->target;
    }

    function setCoords($coords) {
        $this->coords = $coords;
    }

    function getCoords() {
        return $this->coords;
    }

}

/**
 * This object will allow you to render the value in an array
 * at any given time just like the elements for the form
 *
 * @param unknown_type $arr
 * @param unknown_type $key
 * @return ArrayElement
 */
class ArrayElement extends Element {

    var $version = "1.0";
    var $arr = array();
    var $key;

    function ArrayElement($arr = array(), $key) {
        $this->arr = $arr;
        $this->key = $key;
    }

    function render() {
        if (is_array($this->arr)) {
            echo $this->arr[$this->key];
        } else {
            echo ("ERROR PW102: element passed is not an array");
            return -1;
        }
    }

    function getKey() {
        return $this->key;
    }

    function setKey($key) {
        $this->key = $key;
    }

}

/**
 * This object will allow you to render a specific value
 * from an array of objects	 * 
 *
 * @param the array with values
 * @param the varible(field) name to be outputed
 * @param the key value
 * @param the field thats is the key within the object
 */
class ObjectValueElement extends Element {

    var $version = "1.0";
    var $arr = array();
    var $key;
    var $varName;
    var $arrayKey;

    function ObjectValueElement(&$arr, $varName, $key, $arrayKey) {
        $this->arr = $arr;
        $this->key = $key;
        $this->varName = $varName;
        $this->arrayKey = $arrayKey;
    }

    function render() {
        if (is_array($this->arr)) {
            $newArr = $this->objectVarsToArray($this->arr, $this->varName, $this->arrayKey);
            //print_r($newArr);
            echo $newArr[$this->key];
        } else {
            echo "ERROR PW102: element passed is not an array";
            return-1;
        }
    }

    function getKey() {
        return $this->key;
    }

    function setKey($key) {
        $this->key = $key;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $arr , the actual array with objects
     * @param unknown_type $varName, the name of the variable within the object
     * @param unknown_type $arrayKey, the key identifier to be used in the array
     * @return unknown
     */
    function objectVarsToArray($arr, $varName, $arrayKey) {
        $retArr = array();
        if (is_array($arr)) {
            foreach ($arr as $obj) {
                $retArr[$obj->{$arrayKey}] = $obj->{$varName};
            }
        }
        return $retArr;
    }

}

/**
 * Renders any $value you put in here
 * most simple element ever.
 *
 */
class HTMLElement extends Element {

    var $version = "1.0";

    function HTMLElement($value) {
        $this->value = $value;
    }

    function InputHidden($value) {
        $this->value = $value;
    }

    function render() {
        echo $this->value;
        //parent::render();
    }

}

class Iframe extends Element {

    var $align;
    var $allowtransparency;
    var $frameborder;
    var $height;
    var $hspace;
    var $longdesc;
    var $marginheight;
    var $marginwidth;
    var $name;
    var $scrolling;
    var $src; //value
    var $vspace;
    var $width;
    var $style;

    function Iframe($name, $value, $id, $styleClass, $frameborder = null, $height = null, $width = null, $align = null, $allowtransparancy = null, $scrolling = null, $vspace = null, $hspace = null, $longdesc = null) {
        $this->name = $name;
        $this->id = $id;
        $this->src = $value;
        $this->styleClass = $styleClass;

        $this->align = $align;
        $this->allowtransparency = $allowtransparancy;
        $this->frameborder = $frameborder;
        $this->height = $height;
        $this->hspace = $hspace;
        $this->longdesc = $longdesc;
        $this->marginheight = $marginheight;
        $this->marginwidth = $marginwidth;
        $this->scrolling = $scrolling; //yes, no en auto
        $this->vspace = $vspace;
        $this->width = $width;
    }

    function render() {

        echo "<iframe name=\"" . $this->name . "\" src=\"" . $this->src . "\" ";

        parent::render();

        if (isset($this->width)) {
            echo " width=\"" . $this->width . "\"";
        }

        if (isset($this->height)) {
            echo " height=\"" . $this->height . "\"";
        }

        if (isset($this->frameborder)) {
            echo " frameborder=\"" . $this->frameborder . "\"";
        }

        if (isset($this->scrolling)) {
            echo " scrolling=\"" . $this->scrolling . "\"";
        }
        echo ">";
        ?>

        "Sorry your browser doesn't support iframes try FireFox for a change, its free"
        </iframe>
        <?php
    }

}

/*
  a label to be used by pwframework input elements
 */

class StandardLabel extends JavaScriptEvents {

    var $value;
    var $for;
    var $id;
    var $styleClass =  "control-label";

    function StandardLabel($value, $for = null, $id = null, $styleClass = null) {
        $this->value = $value;
        $this->id = $id;
        $this->for = $for;
        if($styleClass !=null){
            $this->styleClass = $styleClass;
        }
    }

    function render() {
        echo "<label ";

        if (isset($this->id)) {
            echo " id=\"" . $this->id . "\"";
        }

        if (isset($this->for)) {
            echo " for=\"" . $this->for . "\"";
        }

        if (isset($this->styleClass)) {
            echo " class=\"" . $this->styleClass . "\"";
        }
        $this->renderEvents();

        echo ">$this->value</label>";
    }

}

/*
  a label that can be used in the amdmin environment of Joomla. It has a feature of a mouse over help feature
 */

class JoomlaLabel extends JavaScriptEvents {

    var $value;
    var $for;
    var $id;
    var $styleClass;
    var $helpTekst;

    function JoomlaLabel($value, $helpText = null, $for = null, $id = null, $styleClass = null) {
        $this->value = $value;
        $this->id = $id;
        $this->for = $for;
        $this->styleClass = $styleClass;
        $this->helpText = $helpText;
    }

    function render() {
        if (isset($this->helpText)) {
            ?><span class="editlinktip"><span onmouseout="return nd();" onmouseover="return overlib('<?php echo $this->helpText; ?>', CAPTION, '<?php echo $this->value; ?>', BELOW, RIGHT);"><?php
        }
        echo "<label ";

        if (isset($this->id)) {
            echo " id=\"" . $this->id . "\"";
        }

        if (isset($this->for)) {
            echo " for=\"" . $this->for . "\"";
        }

        if (isset($this->styleClass)) {
            echo " class=\"" . $this->styleClass . "\"";
        }
        $this->renderEvents();

        echo ">$this->value</label></span></span>";
    }

}

class QuickForm{
	
	static function HiddenFormFields($component, $unit, $act){
//		$menu = JSite::getMenu();
//		$item = $menu->getActive();		
		
                $app = JFactory::getApplication();

                $menu = $app->getMenu();
                $item = $menu->getActive();
            
		QuickForm::InputHidden("option", $component);
		QuickForm::InputHidden("unit", $unit, "unit");
		QuickForm::InputHidden("act", $act, "act");
                if($item <> null){
                    QuickForm::InputHidden("Itemid", $item->id);                    
                }
		
	
	}
	
	/**
	 * Outputs a simple header tag contain text like the head on a view
	 */
	static function ViewHead($text){
		echo "<h1>" . JText::_($text) . "</h1>";
	}
	
	static function SelectBoxObject($name, $value=null, $id=null , $object, $valueId, $labelId, $styleClass=null,$multi=null, $disabled=null){
		$sb = new SelectBoxObject($name, $value, $id , $object, $valueId, $labelId, $styleClass,$multi, $disabled);
		$sb->render();
	}
	
	static function InputText($name, $value, $id=null,  $styleClass=null, $size=null, $maxsize=null, $readonly=null, $disabled=null){
		$it = new InputText($name, $value, $id,  $styleClass, $size, $maxsize, $readonly, $disabled);
		$it->render();
	}
	
	static function InputCheckBox($name, $value,  $id=null, $styleClass=null){
		$cb = new InputCheckBox($name, $value,  $id, $styleClass);
		$cb->render();
	}
	
	static function SubmitButton($name, $value, $id=null, $styleClass=null){
		$sb = new SubmitButton($name, $value, $id, $styleClass);
		$sb->render();
	}
	
	static function InputButton($name, $value, $id=null, $styleClass=null){
		$ib = new InputButton($name, $value, $id, $styleClass);
		$ib->render();
	}
	
	static function InputHidden($name, $value ,$id=null){
		$ih = new InputHidden($name, $value ,$id);
		$ih->render();
	}
	
	static function InputTextArea($name, $value, $id=null, $styleClass=null, $rows=null, $columns=null, $readonly=null, $disabled=null){
		$tr = new InputTextArea($name, $value, $id, $styleClass, $rows, $columns, $readonly, $disabled);
		$tr->render();
	}
	
	static function JoomlaDatePicker($name, $value, $id = null, $format = '%d-%m-%Y', $attributes=null){
		$jd = new JoomlaDatePicker($name, $value, $id, $format, $attributes=null);
		$jd->render();
	}
}


class Bootform{
    
    public static $COL_MD_4 = "col-md-4";
    public static $COL_MD_8 = "col-md-8";
    public static $COL_MD_12 = "col-md-12";
    
    public static function header($label, $subtext = null){
          /* default filter settings */
        if($subtext==null){
            printf("<h1>%s</h1><hr class='style-two'/>", $label );
        }else{
            printf("<h1>%s <small>%s</small></h1><hr class='style-two'/>", $label, $subtext );
        }
        
    }
     public static function subheader($label, $subtext = null){
         
          /* default filter settings */
        if($subtext==null){
          $html = sprintf("<h3>%s</h3><hr class='style-two'/>", $label );
        }else{
          $html = sprintf("<h3>%s <small>%s</small></h3><hr class='style-two'/>", $label, $subtext );
        }
        echo $html;
        
    }
    
    public static function modalStart($modal_id, $modal_title_attr, $modal_size_class, $modal_header_title, $minus_modal_div=null){
        
        if($minus_modal_div === null){ ?>                  
        <div id="<?php echo $modal_id;?>" class="modal" title="<?php echo $modal_title_attr;?>">
<!--  if all references to 'title' have been removed, we  can switch to: --> 
<!--  <div id="<?php //echo $modal_id;?>" class="modal" role="dialog" aria-labelledby="<?php //echo $modal_title_attr;?>" aria-hidden="true" >  -->
        <?php } ?>
                                <div class="modal-dialog <?php echo $modal_size_class;?>">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button aria-hidden="true" class="close" type="button" data-dismiss="modal" data-target="#<?php echo $modal_id; ?>">&#10006;</button>
                                            <h3 class="modal-title"><?php echo $modal_header_title;?></h3>
                                        </div>
                                        <div class="modal-body">
    <?php
    }
    
    public static function modalFinish($modal_id, $save_button_id, $save_text=null, $cancel_button=true, $cancel_text=null, $minus_modal_div=null){
        if ($save_text == null){
            $save_text = JText::_('COM_STORYBOARD_SAVE');
        }
        if ($cancel_text == null){
            $cancel_text = JText::_('COM_STORYBOARD_CANCEL');
        }
        ?>                                   
                                        </div> <!-- modal-body close -->
                                        <div class="modal-footer">
                                            <?php if($cancel_button == true){ ?>
                                            <button type="button" class="btn btn-default" data-dismiss="modal" data-target="#<?php echo $modal_id; ?>" ><?php echo $cancel_text; ?></button>
                                            <?php 
                                            }
                                            if($save_button_id != null){ ?>
                                            <button type="button" id="<?php echo $save_button_id;?>" class="btn btn-success"><?php echo $save_text; ?></button>
                                            <?php } ?>
                                        </div> <!-- modal-footer close -->
                                    </div> <!-- modal-content close -->
                                </div> <!-- modal-dialog close -->
                       <?php if($minus_modal_div === null){ ?>
                        </div> <!-- modal close -->
                       <?php } ?>
        <?php
    }
    
    public static function formStart($name, $id, $legend=null, $styleClass=null, $method=null){
        ?>
        <form 
            <?php
            if($styleClass!=null){ 
              echo 'class="'.$styleClass.'" '; 
            }?>
              name="<?php echo $name; ?>" 
              id="<?php echo $id; ?>" 
            <?php
            if($method!=null){
              echo 'method="'.$method .'" ';
            }?>
              >
            <fieldset>

                <?php 
                if($legend != null){
                // Form Name
                echo "<legend>" . $legend . "</legend><br/>";
                } 
                
    }
    
    public static function formFinish(){ 
        ?>
            </fieldset>
        </form>
        <?php
    }
    
    public static function Container($id=null, $class=null, $content=null){
        printf("<div ");
        if($id){
            echo printf(" id=\"%s\" ", $id);
        }
        
        if($class){
            echo printf(" $class=\"%s\" ", $class);
        }
         printf(" >");
         
         if($content){
            echo $content;
        }
        
        echo "</div>";
    }

    
    /**
     *  bootform text field 
     * @param type $name
     * @param type $value
     * @param type $id
     * @param type $group_width
     * @param type $label
     * @param type $label_width
     * @param type $width
     * @param type $required
     * @param type $readonly
     * @param type $disabled
     * @param type $placeholder
     * @param type $help_text
     * @param type $dateformat
     */
    public static function Text($name, $value, $id=null, $group_width=null, $label=null, $label_width=null, $width=null, $required=null, $readonly=null, $disabled=null, $placeholder=null, $help_text=null, $dateformat=null, $maxchars=null){
		?>
                <div class="form-group <?php echo $group_width; ?>">
                    <label class="<?php echo $label_width; ?> control-label" for="<?php echo $name; ?>"><?php echo $label;
                                if (isset($required)&& $required==true){
                                echo "*";
                                } ?></label>  
                          <div class="<?php echo $width; ?>">
                          <input id="<?php echo $id; ?>" 
                                 name="<?php echo $name; ?>" 
                                 type="text" 
                                 <?php 
                                if ($dateformat!=null){
                                        echo  'data-date-format="'.$dateformat.'"';
                                }
                                if (isset($value)){
                                        echo " value=\"" .$value . "\" ";
                                }                              
                                if (isset($readonly) && $readonly==true){
                                        echo " readonly=\"readonly\" ";
                                }
                                if (isset($disabled)&& $disabled==true){
                                        echo " DISABLED ";
                                } 
                                if ($placeholder!=null){
                                        echo "placeholder=\"". $placeholder. "\" ";
                                }
                                if ($maxchars!=null){
                                        echo "maxlength=\"". $maxchars. "\" ";
                                } ?>
                                class="form-control input-md <?php
                                if (isset($required)&& $required==true){
                                        echo " required ";
                                } ?> "
                          />
                          <?php
                          if ($help_text!=null){
                            echo "<span class=\"help-block\">$help_text</span>";
                          }
                          ?>
                          </div>
                        </div> 
    <?php                                
    }
    
    /**
     * 
     * @param type $name
     * @param type $value
     * @param type $id
     * @param type $group_width
     * @param type $label
     * @param type $cols
     * @param type $rows
     * @param type $label_width
     * @param type $width
     * @param type $required
     * @param type $readonly
     * @param type $disabled
     * @param type $placeholder
     * @param type $help_text
     */
    public static function TextArea($name, $value, $id=null, $group_width=null, $label=null, $cols=50, $rows=6, $label_width=null, $width=null, $required=null, $readonly=null, $disabled=null, $placeholder=null, $help_text=null){
		?>
                <div class="form-group <?php echo $group_width; ?>">
                    <label class="<?php echo $label_width; ?> control-label" for="<?php echo $name; ?>"><?php echo $label;
                                if (isset($required)&& $required==true){
                                echo "*";
                                } ?></label>  
                          <div class="<?php echo $width; ?>">
                              <textarea 
                                 id="<?php echo $id; ?>" 
                                 name="<?php echo $name; ?>" 
                                 rows="<?php echo $rows; ?>" 
                                 cols="<?php echo $cols; ?>" 
                                 <?php 
                                
                                                            
                                if (isset($readonly) && $readonly==true){
                                        echo " readonly=\"readonly\" ";
                                }
                                if (isset($disabled)&& $disabled==true){
                                        echo " DISABLED ";
                                } 
                                if ($placeholder!=null){
                                        echo "placeholder='$placeholder' ";
                                } ?>
                                class="form-control input-md <?php
                                if (isset($required)&& $required==true){
                                        echo " required ";
                                } ?> "
                          ><?php
                                if (isset($value)){
                                        echo $value;
                                }
                                ?>
                          </textarea>
                          <?php
                          if ($help_text!=null){
                            echo "<span class=\"help-block\">$help_text</span>";
                          }
                          ?>
                          </div>
                        </div> 
    <?php                                
    }
    
    
    public static function TimeIncrementer($name, $value, $id, $hidden_id, $decr, $incr, $label , $required=false, $disabled=false){
        
        if($disabled){
            $clsDisabled =  " disabled ";
        }else{
            $clsDisabled =  "";
        }
           
        if (isset($required)&& $required==true){
            $clsRequired =  " required ";
        }else{
            $clsRequired =  "";
        }
        
        if (!isset($value)){
             $value=0;
        }
        ?>
    <div class="form-group">
        <label for="<?php echo $name; ?>"><?php echo $label;
                    if (isset($required)&& $required==true){
                    echo "*";
                    } ?>
        </label>  
        <div class="btn-group">
            <input type="button" class="btn btn-default<?php echo $clsDisabled; ?>" <?php echo $clsDisabled; ?> name="decreaseHr<?php echo $id; ?>" id="decreaseHrBtn<?php echo $id; ?>" value=""
                   onclick="decreaseHrBtnOnclick('<?php echo $hidden_id; ?>', '<?php echo $id; ?>', '<?php echo $decr; ?>')"/>
            <input type="button" class="btn btn-default <?php echo $clsDisabled; ?>" <?php echo $clsDisabled; ?> name="decrease<?php echo $id; ?>" id="decreaseBtn<?php echo $id; ?>" value="-" 
                   onclick="decreaseBtnOnclick('<?php echo $hidden_id; ?>', '<?php echo $id; ?>', '<?php echo $decr; ?>')"/>                       
            <input type="button" 
                   class="btn btn-default disabled-number-input <?php echo $clsRequired . $clsDisabled; ?>" 
                   name="<?php echo $name; ?>" 
                   id="<?php echo $id; ?>" 
                   value="<?php echo $value; ?>"
                   disabled/>
            <input type="button" class="btn btn-default <?php echo $clsDisabled; ?> "  <?php echo $clsDisabled; ?> name="increase<?php echo $id; ?>" value="+" id="increaseBtn<?php echo $id; ?>"
                   onclick="increaseBtnOnclick('<?php echo $hidden_id; ?>', '<?php echo $id; ?>', '<?php echo $incr; ?>')"/>
            <input type="button" class="btn btn-default <?php echo $clsDisabled; ?>"  <?php echo $clsDisabled; ?> name="increaseHr<?php echo $id; ?>" value="" id="increaseHrBtn<?php echo $id; ?>"
                   onclick="increaseHrBtnOnclick('<?php echo $hidden_id; ?>', '<?php echo $id; ?>', '<?php echo $incr; ?>')"/>
        </div>
    </div> 
    <script> 
        document.getElementById('decreaseHrBtn<?php echo $id; ?>').value = _decr_hr;
        document.getElementById('increaseHrBtn<?php echo $id; ?>').value = _incr_hr;

        var decreaseIntervalId;
        jQuery("#decreaseBtn<?php echo $id; ?>").mousedown( function(){
            decreaseIntervalId = setInterval(decrease<?php echo $id; ?>, 300);
        }).mouseup( function(){
            clearInterval(decreaseIntervalId);
        }).mouseout( function(){
            clearInterval(decreaseIntervalId);
        });

        jQuery("#decreaseHrBtn<?php echo $id; ?>").mousedown( function(){
            decreaseIntervalId = setInterval(decreaseHr<?php echo $id; ?>, 150);
        }).mouseup( function(){
            clearInterval(decreaseIntervalId);
        }).mouseout( function(){
            clearInterval(decreaseIntervalId);
        });

        function decrease<?php echo $id; ?>(){
            decreaseBtnOnclick('<?php echo $hidden_id; ?>', '<?php echo $id; ?>', '<?php echo $decr; ?>');
        };
        function decreaseHr<?php echo $id; ?>(){
            decreaseHrBtnOnclick('<?php echo $hidden_id; ?>', '<?php echo $id; ?>', '<?php echo $decr; ?>');
        };

        var increaseIntervalId;
        jQuery("#increaseBtn<?php echo $id; ?>").mousedown( function(){
            increaseIntervalId = setInterval(increase<?php echo $id; ?>, 300);
        }).mouseup( function(){
            clearInterval(increaseIntervalId);
        }).mouseout( function(){
            clearInterval(increaseIntervalId);
        });
        jQuery("#increaseHrBtn<?php echo $id; ?>").mousedown( function(){
            increaseIntervalId = setInterval(increaseHr<?php echo $id; ?>, 150);
        }).mouseup( function(){
            clearInterval(increaseIntervalId);
        }).mouseout( function(){
            clearInterval(increaseIntervalId);
        });

        function increase<?php echo $id; ?>(){
            increaseBtnOnclick('<?php echo $hidden_id; ?>', '<?php echo $id; ?>', '<?php echo $incr; ?>');
        };
        function increaseHr<?php echo $id; ?>(){
            increaseHrBtnOnclick('<?php echo $hidden_id; ?>', '<?php echo $id; ?>', '<?php echo $incr; ?>');
        };
        </script>
    <?php                                
    }
          
    public static function TextGroup($name, $value, $arrayValue, $arrayLabel, $label_width=null, $id=null, $group_label=null, $group_label_width=null, $width=null, $readonly=null, $disabled=null, $placeholder=null, $help_text=null){
		?>
                <div class="form-group">
                    <?php
                    if($group_label!=null){
                        ?>
                    <label 
                        class="<?php echo $group_label_width . " "; ?>control-label" for="<?php echo $name; ?>"><?php echo $group_label; ?></label>  
                    <?php }
                    for($i= 0 ; $i < count($arrayValue); $i++){
                    ?>        
                    <div class="<?php echo $width; ?>">
                        <label class="<?php echo $label_width . " "; ?>control-label"><?php  echo $arrayLabel[$i];?></label>          
                          <input id="<?php  echo $id . "_" . $i; ?>" 
                                 name="<?php echo $name . "_" . $i; ?>" 
                                 type="text" 
                                 value="<?php echo $arrayValue[$i]; ?>"
                                 <?php 
                                if (isset($value)){
                                        echo " value=\"" .$value . "\" ";
                                }                              
                                if (isset($readonly) && $readonly==true){
                                        echo " readonly=\"readonly\" ";
                                }
                                if (isset($disabled)&& $disabled==true){
                                        echo " DISABLED ";
                                } 
                                if ($placeholder!=null){
                                        echo "placeholder=''$placeholder' ";
                                } ?>
                                class="form-control input-md"
                          />
                          <?php
                          if ($help_text!=null){
                            echo "<span class=\"help-block\">$help_text</span>";
                          }
                          ?>
                    </div>
                    <?php } ?>
                </div> 
    <?php                                
    }
    
    public static function CheckBox($name, $value, $id=null, $group_label=null, $label_width=null, $label=null, $width=null, $styleClass=null, $display_only=null, $group_width=null){
	?>
        <div class="form-group <?php echo  $group_width; ?>">
            <label class="<?php echo $label_width; ?> control-label" >
            <?php echo $group_label; ?>
            </label>
                <div class="<?php echo $width ." ". $styleClass; ?>">
                    <div class="checkbox">
                        <label for="<?php echo $name; ?>">
                          <input type="checkbox" 
                                 name="<?php echo $name; ?>" 
                                 id="<?php echo $id; ?>" 
                                 <?php
                                 if(isset($display_only) && $display_only !=null){
                                 echo 'class ="display_only" ';
                                 }                               
                                if (isset($value)){
                                    if(intval($value)===1 || $value==="on" || $value==="true" || $value===true || $value==="CHECKED"){
                                    echo " checked='checked' ";
                                    }
                                }
                                ?>
                          />
                          <?php echo $label; ?>
                        </label>
                    </div>           
                </div>
        </div>
        <?php
    }
    /**
     * A yes no toggle button
     * @param type $name
     * @param type $value
     * @param type $id
     */
    public static function YesNo($name, $value, $id, $label, $label_width, $width, $yesLabel="Yes", $noLabel="No",  $styleClass=null){
        printf('<input type="hidden" name="%s" value="%s" id="%s" > ', $name, $value, $id );
        ?>
       <div class="form-group">
            <label class="<?php echo $label_width; ?> control-label" >
            <?php echo $label; ?>
            </label>
            <div class="<?php echo $width ." ". $styleClass; ?>">
                <div class="btn-group btn-toggle" data-toggle="buttons">
                   <label class="btn btn-primary active">
                            <input type="radio" name="<?php echo $name;?>_options" value="on">Yes
                   </label>
                   <label class="btn btn-default">
                        <input type="radio" name="<?php echo $name;?>_options" value="off" checked="checked">No
                   </label>
                 </div>
            </div>
       </div>
                    
         <script>
            jQuery('.btn-toggle').click(function() {
                jQuery(this).find('.btn').toggleClass('active');  

                if (jQuery(this).find('.btn-primary').size()>0) {
                    jQuery(this).find('.btn').toggleClass('btn-primary');
                }
                if (jQuery(this).find('.btn-danger').size()>0) {
                    jQuery(this).find('.btn').toggleClass('btn-danger');
                }
                if (jQuery(this).find('.btn-success').size()>0) {
                    jQuery(this).find('.btn').toggleClass('btn-success');
                }
                if (jQuery(this).find('.btn-info').size()>0) {
                    jQuery(this).find('.btn').toggleClass('btn-info');
                }

                jQuery(this).find('.btn').toggleClass('btn-default');
                
               var <?php echo $name;?>_options = jQuery(this["options"]).val();
                       //jQuery("input[name=<?php// echo $name;?>_options]:checked").val()
                //console.log(<?//php echo $name;?>_options);
                jQuery("#<?php echo $id;?>").val(<?php echo $name;?>_options);
            });
        </script>
        <?php
    }


    /**
     * 
     * @param type $name
     * @param type $selected_value
     * @param type $id
     * @param type $array_objects
     * @param type $key
     * @param type $key_value
     * @param type $label
     * @param type $styleClass
     * @param type $multi
     * @param type $disabled
     * @param type $onchange
     * @param type $text_for_empty_value
     * 
     */
    public static function SelectBoxObject($name, $selected_value, $id, $array_objects, $key, $key_value, $label=null, $required=null, $group_width=null, $styleClass=null, $multi=null, $disabled=null, $onchange=null, $text_for_empty_value=null){
        $asterix = "";
        $reqClass = "";
        $onchange = ""; 
        $multiple = "";
        $multiHeight = "";
        $isreadonly = "";
        $isdisabled = "";
        
        if (isset($required)&& $required==true){
            $asterix = "*";
            $reqClass = "required";
        }        
        
        if( isset($onchange) && $onchange!=null){
            $onchange = sprintf(" onchange=\"%s\" ",$onchange);
        }
        
        if($multi){
            $multiple = 'multiple=\"true\"' ;
            if(count($array_objects) == 1){
                $multiHeight = 'style="height:34px;"';
            }
        }
        if($disabled === true){
            $isreadonly = 'readonly="readonly"';
            $isdisabled = 'disabled="disabled"';
        }
        
        printf('<div class="form-group %s">', $group_width);              
        printf('    <label class="control-label %s " for="%s">%s %s</label>',$styleClass, $name, $label, $asterix);
        printf('    <div class="%s">', $styleClass);
        printf('    <select  id="%s" name="%s" class="form-control %s " %s %s %s %s>', $id, $name, $reqClass, $multiple, $multiHeight, $isreadonly, $isdisabled);
                           
                          
        foreach ($array_objects as $object){
            if(is_array($selected_value)){ //on multi
               if(in_array($object->{$key}, $selected_value)){
                   $selected = " selected='selected' ";
               }else{
                   $selected = "";
               }
            }else{

               if(strcmp($object->{$key}, $selected_value) == 0){
                   $selected = " selected='selected' ";
               }else{
                   $selected = "";
               }
            }

            if($object->{$key} == -1){
                $object->{$key_value} = $text_for_empty_value;
            }
            printf("<option value='%s' %s>%s </option>", $object->{$key}, $selected, $object->{$key_value});                                 
        }
         ?>
                    </select>
                </div>
             </div>
        <?php       
    }
    
    
    
    public static function RadioButton($name, $value, $id=null, $label=null, $styleClass=null){
        ?>
        <div class="radio <?php echo $styleClass;?>">
            <label for="<?php  echo $name;?>">
                <input 
                    type="radio" 
                    name="<?php  echo $name ?>"
                    id="<?php  echo $id ?>" 
                    value="<?php  echo $value;?>"
                    <?php
                    if (isset($value)){
                            if($value=='1' || $value=='on' || $value==true || $value=='CHECKED'){
                                    echo " checked='checked' ";
                            }
                    }
                    ?>              
                />
                <?php  echo $label;?>
           </label>
        </div>
	}
        <?php
    }                            
            

    /*
	multiple radio buttons
    */
    public static function RadioGroup($name, $value=null, $id=null, $arrayValue=null, $arrayLabel=null, $group_width=null, $group_label=null, $group_label_width=null, $radio_width=null, $styleClass=null ){
        ?>   
                <div class="form-group <?php echo $group_width; ?>">
                <?php
                if($group_label!=null){
                    ?>
                    <label class="<?php echo $group_label_width; ?> control-label" for="<?php echo $name; ?>"><?php echo $group_label; ?></label>
                    <?php 
                } ?>
                    <div class="<?php echo $radio_width; ?>">
                        <?php 
                        for($i= 0 ; $i < count($arrayValue); $i++){
                        ?> 
                        <div class="radio <?php echo $styleClass;?>">
                            
                            <label class="control-label" for="<?php echo $name. "_" .$i ?>">
                                <input 
                                    type="radio" 
                                    name="<?php echo $name; ?>" 
                                    id="<?php  echo $id . "_" . $i; ?>"  
                                    value="<?php  echo $arrayLabel[$i]; ?>" 
                                    <?php  if($arrayValue[$i]==$value) {echo ' checked="checked" ';} ?>                                   
                                />
                                <?php  echo $arrayLabel[$i];?>
                             </label>
                        </div>
                        <?php
                        }
                    ?>
                    </div> 
                </div>
        <?php     
    }
    
    public static function Hidden($name, $value=null ,$id=null, $value_never_null= false){
        $param_id = ""; $param_value="";
        if ($id!=null){
            $param_id = "id=\"" . $id . "\" ";
        }
        if($value_never_null === false && $value!=null){
            $param_value= "value=\"" . $value . "\" ";
        }else{
            $param_value= "value=\"" . $value . "\" ";
        }
        
        printf("<input type=\"hidden\" name=\"%s\"  %s %s >", $name, $param_id, $param_value);
        	
    }

    /**
     * returns a string containing common hidden fields for a form
     */
    public static function FormControlFields($unit, $act, $unit_id="unit", $act_id="act", $option="com_storyboard") {
        global $Itemid;
        ?>
            <input type="hidden" name="option" value="<?php echo $option; ?>" />
            <input type="hidden" name="Itemid" value="<?php echo $Itemid; ?>" />
            <input type="hidden" name="unit" value="<?php echo $unit; ?>" id="<?php echo $unit_id; ?>" />
            <input type="hidden" name="act" value="<?php echo $act; ?>" id="<?php echo $act_id; ?>" />
        <?php
    }
    
    public static function SaveCancelButtons($back_url = null){
        if($back_url==null){
            $onclick  = "history.go(-1);";
        }else{
            $onclick = sprintf("window.location.href='%s'", $back_url);
        }
        ?> 

        <div class="form-group">
            <div class="col-md-8">                
                <button type="button" 
                        id="cancel_id" 
                        name="cancel" 
                        onclick="<?php echo $onclick;?>" 
                        class="btn btn-default"><?php echo JText::_('JCANCEL' ); ?></button>
                <button type="submit" 
                        id="save_id" 
                        name="save" 
                        class="btn btn-success"><?php echo JText::_('JSAVE' ); ?></button>
            </div>
        </div>
        <?php
    }
    
    /**
     * 
     * @param type $name
     * @param type $dateTime expects a mysql date formt
     * @param type $id
     * @param type $label
     */
    public static function DatePicker($name, $dateTimeObject, $id=null, $group_width=null, $label=null, $label_width=null, $width=null, $required=null, $readonly=null, $disabled=null, $placeholder=null, $help_text=null, $min_or_max= "maxDate"){
        ?>                    
                <div class="form-group <?php echo $group_width; ?>"  >
                    <label class="<?php echo $label_width; ?> control-label" for="<?php echo $name; ?>"><?php echo $label; 
                    if (isset($required)&& $required==true){
                                echo "*";
                                } ?></label>  
                          <div class="<?php echo $width; ?>"> 
                    <div class="input-group date" style="float:left;">
                            <input type="text" 
                                   name="<?php echo $name;?>" 
                                   id="<?php echo $id; ?>" 
                                   <?php if($dateTimeObject == null){ ?>
                                    value=""    
                                   <?php }else{ ?>
                                   value="<?php echo DateHelper::dateStringFromDateTimeObject($dateTimeObject); ?>" 
                                   <?php } ?>
                                   class="form-control input-md<?php
                                if (isset($required)&& $required==true){
                                        echo " required ";
                                } ?> "
                                   data-date-format="dd-mm-yyyy"
                                   <?php
                                   if (isset($readonly) && $readonly==true){
                                        echo " readonly=\"readonly\" ";
                                    }
                                    if (isset($disabled) && $disabled==true){
                                        echo " disabled=\"disabled\" ";
                                    }
                                   ?>
                                   >
                          
                            <label for="<?php echo $name; ?>" class="input-group-addon btn">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </label>
                    </div>
                </div>
                </div>
        <script>
        jQuery("#<?php echo $id;?>").datepicker({
                        format: "dd-mm-yyyy",
                        weekStart: 1,
                        calendarWeeks: true,
                        autoclose: true,
                        todayHighlight: true,
                                              
                        //"showWeek": true,                        
                        onClose: function(selectedDate) {
                            jQuery("#<?php echo $id;?>").datepicker("option", "<?php echo $min_or_max; ?>", selectedDate);
                        }
                    });
        </script>
        <?php
        
    }
    
   public static function dateRangePicker($name, $name1, $name2, $date1, $date2, $dateformat="dd-mm-yyyy", $id1=null, $id2=null, $group_width=null, $label=null, $label_width=null, $width=null, $required=null, $help_text=null){
    ?>
     <div class="form-group <?php echo $group_width; ?>"  >
            <label class="<?php echo $label_width; ?> control-label" for="<?php echo $name; ?>"><?php echo $label; 
                            if (isset($required)&& $required==true){
                                echo "*";
                            } ?>
            </label> 
            <div class="input-daterange input-group" id="datepicker" name="<?php echo $name; ?>">
            <input type="text" 
                   class="form-control" 
                   name="<?php echo $name1; ?>" 
                   id="<?php echo $id1; ?>" 
                   <?php if($date1 == null){ ?>
                       value=""    
                   <?php }else{ ?>
                       value="<?php echo $date1; ?>" 
                   <?php } ?>
                   />
            <span class="input-group-addon">to</span>
            <input type="text" 
                   class="form-control" 
                   name="<?php echo $name2; ?>" 
                   id="<?php echo $id2; ?>"
                  <?php if($date2 == null){ ?>
                       value=""    
                   <?php }else{ ?>
                       value="<?php echo $date2; ?>" 
                    <?php } ?>
                   />
            </div>
     </div>
    <script>
     jQuery('.input-daterange').datepicker({
            format: "<?php echo $dateformat; ?>",
            weekStart: 1,
            calendarWeeks: true,
            autoclose: true,
            todayHighlight: true
        });   
        
    </script>
    
    <?php
   }
}

                    
                    