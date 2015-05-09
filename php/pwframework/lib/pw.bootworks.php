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
if (!(defined('_VALID_MOS') || defined('_JEXEC'))) {
    die('Restricted access');
}

class BootformElementSelect2 extends BootformElement {

    /**
     * based on code javascript Select2 found
     * https://select2.github.io/ 
     * @param type $type
     */
    public function __construct($type) {
        parent::__construct($type);
    }

    public function __toString() {
        $html = parent::__toString();
        $html .= '
                <script>jQuery(function(){                    
                    jQuery("#' . $this->id . '").select2();
                });                        
                </script>';

        // sysout($this,1);
        return $html;
    }

}

class BootformElementDatePicker extends BootformElement {

    public function __construct($type) {
        parent::__construct($type);
    }

    public function __toString() {
        //format date object to string
        $this->value = DateHelper::dateStringFromDateTimeObject($this->value);
        $html = sprintf('<div style="clear:both"></div><div class="input-group date" style="float:left;">');
        $html .= parent::__toString();
        $html .= '<label for="' . $this->name . '" class="input-group-addon btn">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </label>';
        $html .= '
                 <script>
                        jQuery("#' . $this->id . '").datepicker({
                            format: "dd-mm-yyyy",
                            weekStart: 1,
                            calendarWeeks: true,
                            autoclose: true,
                            todayHighlight: true,

                            //"showWeek": true,                        
                            onClose: function(selectedDate) {
                                jQuery("#' . $this->id . '").datepicker("option", "maxDate", selectedDate);
                            }
                        });
                </script>';
        $html .= '</div><div class="clear"></div>';
        // sysout($this,1);
        return $html;
    }

}

class BootformElementCheckbox extends BootformElement {

    public function __construct($type) {
        parent::__construct($type);
    }

    /**
     * wrap element with a horizontal formelement
     * @return string
     */
    function asHorizontalFormElement() {
        $html = '<div class="form-group"><div class="' . $this->labelWidth . '"></div>';
        $html .= '<div class="checkbox ' . $this->elementWidth . '" ><label>';

        //get the element html code
        $html .= $this->__toString();

        $html .= ' ' . JText::_($this->label);
        $html .= '</label></div></div>';
        return $html;
    }

    /**
     * wrap element with a horizontal formelement
     * @return string
     */
    function asVerticalFormElement() {
        $html = '<div class="checkbox" ><label>';

        //get the element html code
        $html .= $this->__toString();

        $html .= ' ' . JText::_($this->label);
        $html .= '</label></div>';
        return $html;
    }

}

class BootformElement {

    protected $name = null;
    protected $id = null;
    protected $value = null;
    protected $styleClass = null;
    protected $style = null;
    protected $title = null;
    protected $type = null;
    protected $label = null;
    protected $required = false;
    protected $labelWidth = null;
    protected $elementWidth = null;
    protected $readOnly = false;
    protected $disabled = false;
    protected $checked = false;
    protected $helpText = null;
    protected $placeholder = null;
    protected $dateFormat = null;
    /* start selection options */
    protected $options = null;
    protected $optionsKey = null;
    protected $optionsLabel = null;
    protected $multi = false;
    protected $maxLength = null;

    /* end selection options */

    public function __construct($type) {
        $this->type = $type;
        $this->labelWidth = BootFactory::$COL_MD_4;
        $this->elementWidth = BootFactory::$COL_MD_8;
        return $this;
    }

    function name($name) {
        $this->name = $name;
        return $this;
    }

    function label($label) {
        $this->label = $label;
        return $this;
    }

    /**
     * set options for the select <br> for select type only
     * 
     * @param type $options
     * @return \BootformElement
     */
    public function options($options) {
        $this->options = $options;
        return $this;
    }

    public function optionsKeyLabel($optionsKey, $optionsLabel) {
        $this->optionsKey = $optionsKey;
        $this->optionsLabel = $optionsLabel;
        return $this;
    }

    public function multi($multi) {
        $this->multi = $multi;
        return $this;
    }

    function id($id) {
        $this->id = $id;
        return $this;
    }

    function value($value) {
        $this->value = $value;
        return $this;
    }

    function title($title) {
        $this->title = $title;
        return $this;
    }

    function styleClass($styleClass) {
        $this->styleClass = $styleClass;
        return $this;
    }

    function style($style) {
        $this->style = $style;
        return $this;
    }

    function dateFormat($dateFormat) {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    function readOnly($readOnly) {
        $this->readOnly = $readOnly;
        return $this;
    }

    function required($required) {
        $this->required = $required;
        return $this;
    }

    function disabled($disabled) {
        $this->disabled = $disabled;
        return $this;
    }

    function checked($checked) {
        $this->checked = $checked;
        return $this;
    }

    function helpText($helpText) {
        $this->helpText = $helpText;
        return $this;
    }

    function placeholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    function elementWidth($elementWidth) {
        $this->elementWidth = $elementWidth;
        return $this;
    }

    function labelWidth($labelWidth) {
        $this->labelWidth = $labelWidth;
        return $this;
    }

    /**
     * Set the maximum allowed lenght of charactest
     * Applies to text field only
     * @param type $maxLength
     * @return \BootformElement
     */
    function maxLength($maxLength) {
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * wrap element with a horizontal formelement
     * @return string
     */
    function asHorizontalFormElement() {
        $html = '<div class="form-group">';
        $html .= '<label class="' . $this->labelWidth . ' control-label" for="' . $this->name . '">' . JText::_($this->label);

        if ($this->required == true) {
            $html .= "<em>*</em>";
        }
        $html .= "</label>";
        $html .= '<div class="' . $this->elementWidth . '">';

        //get the element html code
        $html .= $this->__toString();

        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * wrap element with a horizontal formelement
     * @return string
     */
    function asVerticalFormElement() {
        $html = '<div class="form-group">';
        $html .= '<label class="control-label" for="' . $this->name . '">' . JText::_($this->label);

        if ($this->required == true) {
            $html .= "<em>*</em>";
        }
        $html .= "</label>";

        //get the element html code
        $html .= $this->__toString();
        $html .= '</div>';
        return $html;
    }

    function __toString() {

        if ($this->type == "hidden" || $this->type == "text" || $this->type == "datePicker") {
            if ($this->type == "datePicker") {
                $html = '<input name="' . $this->name . '" ';
            } else {
                $html = '<input name="' . $this->name . '" type="' . $this->type . '" ';
            }

            if (isset($this->value)) {
                $html .= ' value="' . $this->value . '" ';
            }

            if ($this->placeholder != null) {
                $html .= 'placeholder="' . $this->placeholder . '" ';
            }
        } else if ($this->type == "checkbox") {

            $html = '<input type="' . $this->type . '" name="' . $this->name . '" ';

            if (isset($this->value)) {
                $html .= ' value="' . $this->value . '" ';
            }

            if (boolval($this->checked) === true) {
                $html .= ' checked="checked" ';
            }
        } else if ($this->type == "select") {
            $html = '<select name="' . $this->name;

            //boost the name to support arrays
            if ($this->multi) {
                $html .= '[]" ';
            }
            $html .= '" ';
        }

        /* generic attributes */

        if ($this->id != null) {
            $html .= 'id="' . $this->id . '" ';
        }

        if ($this->dateFormat != null) {
            $html .= 'data-date-format="' . $this->dateFormat . '" ';
        }
        if ($this->readOnly == true) {
            $html .= ' readonly="readonly" ';
        }

        if ($this->disabled == true) {
            $html .= ' DISABLED ';
        }

        /* form control class */
        if ($this->type !== "checkbox") {
            $html .= 'class="form-control ';
            /** specific stuff */
            if ($this->required == true) {
                $html .= ' required ';
            }
            $html .= '"';
        }


        if ($this->type == "text") {
            if ($this->maxLength !== null) {
                $html .= ' maxlength="' . intval($this->maxLength) . '" ';
            }
        }

        /* closing first tag elements */
        if ($this->type == "hidden" || $this->type == "text" || $this->type == "datePicker" || $this->type == "checkbox") {
            /* elements without body */
            $html .= '/>';
        } else if ($this->type == "select") {

            if ($this->multi == true) {
                $html .= ' multiple="multiple" ';
            }

            $html .='>';
            if (count($this->options)) {
                foreach ($this->options as $option) {
                    $selected = '';

                    if (is_array($this->value)) {
                        if (count($this->value)) {
                            foreach ($this->value as $key => $value) {
                                if (strcmp($value, $option->{$this->optionsKey}) == 0) {
                                    $selected = ' selected="SELECTED" ';
                                }
                            }
                        }
                    } else {
                        if (strcmp($this->value, $option->{$this->optionsKey}) == 0) {
                            $selected = ' selected="SELECTED" ';
                        }
                    }
                    $html .= sprintf('<option %s value="%s">%s</option>', $selected, $option->{$this->optionsKey}, ($option->{$this->optionsLabel}));
                }
            }
            $html .='</select>';
        }

        if ($this->helpText != null) {
            $html .= '<span class="help-block">$help_text</span>';
        }

        return $html;
    }

}

class BootFactory {

    public static $COL_MD_1 = "col-md-1";
    public static $COL_MD_2 = "col-md-2";
    public static $COL_MD_3 = "col-md-3";
    public static $COL_MD_4 = "col-md-4";
    public static $COL_MD_5 = "col-md-5";
    public static $COL_MD_6 = "col-md-6";
    public static $COL_MD_7 = "col-md-7";
    public static $COL_MD_8 = "col-md-8";
    public static $COL_MD_9 = "col-md-9";
    public static $COL_MD_10 = "col-md-10";
    public static $COL_MD_11 = "col-md-11";
    public static $COL_MD_12 = "col-md-12";
    public $option = null;
    public $Itemid = null;
    private static $factory = null;

    public function __construct() {
        $jinput = JFactory::getApplication()->input;
        $Itemid = $jinput->get('Itemid');
        $menu = JFactory::getApplication()->getMenu();
        if (intval($Itemid) > 0) {
            $menuId = $Itemid;
        } else {
            // in case of ajax events.. they don't have an active menu            
            if ($menu->getActive()) {
                //get current menu option
                $menuId = JFactory::getApplication()->getMenu()->getActive()->id;
            }
        }
        $params = null;
        if ($menu->getActive()) {
            $item = $menu->getItem($menuId);
            $params = $item->query;
            $this->Itemid = $menuId;
        }

        if ($params != null && array_key_exists("option", $params)) {
            $this->option = $params['option'];
        } else {
            $this->option = $jinput->get('option');
        }
    }

    /**
     * get an instance of the factory
     * @return BootFactory
     */
    public static function inst() {
        if (self::$factory == null) {
            self::$factory = new BootFactory();
        }
        return self::$factory;
    }

    public function getText() {
        return new BootformElement("text");
    }

    public function getCheckBox() {
        return new BootformElementCheckbox("checkbox");
    }

    public function getSelect() {
        return new BootformElement("select");
    }

    public function getSelect2() {
        return new BootformElementSelect2("select");
    }

    public function getDatePicker() {
        return new BootformElementDatePicker("datePicker");
    }

    public function getHidden() {
        return new BootformElement("hidden");
    }

    public function getCrudBar() {
        return new BootformCrudBar();        
    }
    
    public function getButtonBar() {
        return new BootformButtonBar();        
    }

    public function getButton() {
        return new BootformButton();
    }

    public function getUrl($unit, $act) {
        return new BootformUrl($this->option, $this->Itemid, $unit, $act);
    }

    public function getFormControlFields() {
        return new BootformControlFields($this->option, $this->Itemid);
    }

}

class BootformControlFields {

    private $id = null;
    private $id_id = null;
    private $option = null;
    private $Itemid = null;
    private $action = null;
    private $action_id = null;
    private $unit = null;
    private $unit_id = null;
    private $token = false;

    public function __construct($option, $Itemid) {
        $this->option = $option;
        $this->Itemid = $Itemid;
    }

    public function id($id, $id_id = "id") {
        $this->id = $id;
        $this->id_id = $id_id;
        return $this;
    }

    function option($option) {
        $this->option = $option;
        return $this;
    }

    function Itemid($Itemid) {
        $this->Itemid = $Itemid;
        return $this;
    }

    function unit($unit, $unit_id = "unit") {
        $this->unit = $unit;
        $this->unit_id = $unit_id;
        return $this;
    }

    function action($action, $action_id = "act") {
        $this->action = $action;
        $this->action_id = $action_id;
        return $this;
    }

    /**
     * render html joomla security token
     * @param type $token
     * @return \BootformControlFields
     */
    function token($token = false) {
        $this->token = $token;
        return $this;
    }

    function __toString() {
        $html = BootFactory::inst()->getHidden()->name("option")->value($this->option);
        $html .= BootFactory::inst()->getHidden()->name("Itemid")->value($this->Itemid);
        if ($this->token) {
            $html .= JHtml::_('form.token');
        }
        $html .= BootFactory::inst()->getHidden()->name("unit")->id($this->unit_id)->value($this->unit);
        $html .= BootFactory::inst()->getHidden()->name("act")->id($this->action_id)->value($this->action);
        if ($this->id != null) {
            $html .= BootFactory::inst()->getHidden()->name("id")->id($this->id_id)->value($this->id);
        }
        return $html;
    }

}

class BootformUrl {

    private $option, $unit, $act;
    private $Itemid = null;
    private $token;
    private $params = array();

    public function __construct($option, $Itemid, $unit, $act) {
        $this->option = $option;
        $this->Itemid = $Itemid;
        $this->unit = $unit;
        $this->act = $act;
    }

    public function param($attribute, $value) {
        $this->params[] = sprintf("&%s=%s", $attribute, $value);
        return $this;
    }

    public function token($token = false) {
        $this->token = $token;
        return $this;
    }

    public function __toString() {
        $url = sprintf("index.php?option=%s&unit=%s&act=%s&Itemid=%s", $this->option, $this->unit, $this->act, $this->Itemid);
        //add security token
        if ($this->token) {
            $url .= "&" . JSession::getFormToken() . "=-1";
        }
        if (count($this->params)) {
            foreach ($this->params as $param) {
                $url .= $param;
            }
        }
        return $url;
    }

}

class BootformButton {

    private $unit = null;
    private $unit_id = null;
    private $action = null;
    private $action_id = null;
    private $form = null;
    private $name = null;
    private $title = null;
    private $styleClass = "btn-default";
    private $href = null;
    private $iconClass = null;
    private $label = null;
    private $confirmSelectedId = null;

    public function __construct() {
        
    }

    public function name($name) {
        $this->name = $name;
        return $this;
    }

    public function title($title) {
        $this->title = $title;
        return $this;
    }

    public function styleClass($styleClass) {
        $this->styleClass = $styleClass;
        return $this;
    }

    public function href($href) {
        $this->href = $href;
        return $this;
    }

    public function iconClass($iconClass) {
        $this->iconClass = $iconClass;
        return $this;
    }

    public function label($label) {
        $this->label = $label;
        return $this;
    }

    public function unit($unit, $unit_id = "unit") {
        $this->unit = $unit;
        $this->unit_id = $unit_id;
        return $this;
    }

    public function action($action, $action_id = "act") {
        $this->action = $action;
        $this->action_id = $action_id;
        return $this;
    }

    public function form($form) {
        $this->form = $form;
        return $this;
    }

    /**
     * Pass a selector like in jquery. The value of the element will be checked for existense and value
     * If the value is not found an alert will appear stating that there is no selection
     * @param type $selector
     */
    public function confirmSelectedId($selector) {
        $this->confirmSelectedId = $selector;
        return $this;
    }

    public function __toString() {
        if ($this->href != null) {
            $href = $this->href;
        } else {
            if ($this->confirmSelectedId == null) {
                $href = "javascript:bootbuttonSubmit('" . $this->form . "', '" . $this->unit . "', '" . $this->action . "', '" . $this->unit_id . "', '" . $this->action_id . "');";
            } else {
                $href = "javascript:bootbuttonSubmitConfirm('" . $this->form . "', '" . $this->confirmSelectedId . "', '" . $this->unit . "', '" . $this->action . "', '" . $this->unit_id . "', '" . $this->action_id . "');";
            }
        }

        $html = '<a href="' . $href . '"';
        $html .= ' class="btn ' . $this->styleClass . '">';
        if ($this->iconClass != null) {
            $html .='<i class="' . $this->iconClass . '"></i> ';
        }
        $html .= JText::_($this->label) . '</a>';
        return $html;
    }

}

class BootFormCrudBar extends BootformButtonBar {

    private $crudbuttons = array();
    private $create = false;
    private $edit = false;
    private $delete = false;

    public function __construct() {
        parent::__construct();
    }

    public function create($create) {
        $this->create = $create;
        return $this;
    }

    public function edit($edit) {
        $this->edit = $edit;
        return $this;
    }

    public function delete($delete) {
        $this->delete = $delete;
        return $this;
    }

    private function addCrudButtons() {
        $this->crudbuttons = array();
        /* add a delete button */
        if ($this->delete) {
            $bcd = BootFactory::inst()->getButton()
                    ->label(JText:: _('JACTION_DELETE'))
                    ->unit($this->unit, $this->unit_id)
                    ->action("delete", $this->action_id)
                    ->confirmSelectedId("#the_id")
                    ->form($this->form)
                    ->iconClass("glyphicon glyphicon-trash");

            array_unshift($this->buttons, $bcd);
        }

        /* add a edit button */
        if ($this->edit) {
            $bce = BootFactory::inst()->getButton()
                    ->label(JText:: _('JACTION_EDIT'))
                    ->unit($this->unit, $this->unit_id)
                    ->action("edit", $this->action_id)
                    ->form($this->form)
                    ->iconClass("glyphicon glyphicon-pencil");
            array_unshift($this->buttons, $bce);
        }

        /* add a create button */
        if ($this->create) {
            $bcc = BootFactory::inst()->getButton()
                    ->label(JText:: _('JACTION_CREATE'))
                    ->unit($this->unit, $this->unit_id)
                    ->action("create", $this->action_id)
                    ->form($this->form)
                    ->iconClass("glyphicon glyphicon-plus")
                    ->styleClass("btn-success");
            array_unshift($this->buttons, $bcc);
        }
    }
    
    public function __toString() {
        $this->addCrudButtons();
        return parent::__toString();
    }

}

class BootformButtonBar {

    protected $unit = null;
    protected $unit_id = "unit";
    protected $action_id = "act";
    protected $buttons = array();
    protected $form = null;
    protected $size = null;
    protected $justified = false;
    public static $SIZE_LARGE = "btn-group-lg";
    public static $SIZE_SMALL = "btn-group-sm";
    public static $SIZE_XSMALL = "btn-group-xs";

    public function __construct() {
        
    }

    public function button($button) {
        $this->buttons[] = $button;
        return $this;
    }

    public function form($form) {
        $this->form = $form;
        return $this;
    }

    public function unit($unit, $unit_id = "unit") {
        $this->unit = $unit;
        $this->unit_id = $unit_id;
        return $this;
    }

    public function size($size) {
        $this->size = $size;
        return $this;
    }

    public function justified($justified) {
        $this->justified = $justified;
        return $this;
    }

    public function action_id($action_id) {
        $this->action_id = $action_id;
        return $this;
    }

    public function __toString() {

        $language = JFactory::getLanguage();
        $language->load('plg_system_pwframework');

        if ($this->size != null) {
            $size = $this->size;
        } else {
            $size = "";
        }

        if ($this->justified == true) {
            $justified = "btn-group-justified";
        } else {
            $justified = "";
        }

        $html = '<div class="btn-group ' . $size . ' ' . $justified . '" role="group" aria-label="...">';
      

        /* render the buttons */
        if (count($this->buttons)) {
            foreach ($this->buttons as $button) {
                //wrapping each button in a btn-group to support IE8
                $html .= '<div class="btn-group">' . $button->__toString() . '</div>';
            }
        }

        $html .= '</div>';
        return $html;
    }

    public function asContextMenu($selector) {
        $html = '<ul id="contextMenu" class="dropdown-menu" role="menu" style="display:none" >';
  
        /* render the additional buttons */
        if (count($this->buttons)) {
            foreach ($this->buttons as $button) {
                $html .= '<li>' . $button->__toString() . '</li>';
            }
        }

        $html .= '</ul>';

        $html .= '<script>';
        $html .= 'jQuery(function(){jQuery("' . $selector . '").contextMenu({ menuSelector: "#contextMenu", menuSelected: function (invokedOn, selectedMenu) {    } });});  </script>';

        return $html;
    }

}
