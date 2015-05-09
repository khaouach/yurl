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
// no direct access
defined('_JEXEC') or die('Restricted access');


// Import library dependencies
jimport('joomla.plugin.plugin');

class plgSystemPwframework extends JPlugin {

    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for
     * plugins because func_get_args ( void ) returns a copy of all passed arguments
     * NOT references.  This causes problems with cross-referencing necessary for the
     * observer design pattern.
     */
    function plgSystemPwframework(&$subject) {
        parent::__construct($subject);
        // load plugin parameters
        jimport('joomla.html.parameter');
        $this->_plugin = JPluginHelper::getPlugin('system', 'pwframework');
        //$jparams = new JRegistry();

        $this->_params = new JRegistry($this->_plugin->params);
    }
    
    /**
     * Initialise the framework
     */
    function onAfterInitialise() {        
        JHtml::_('jquery.framework');
        $PW_LOCATION = "/plugins/system/pwframework";
        $PW_PATH = JPATH_SITE . $PW_LOCATION;

        /* loading required classes and libraries */
        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/class.element.php");
        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/class.column.php");
        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/class.table.php");
//        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/class.dbtable.php");
        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/class.form.php");
        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/class.pager.php");
        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/class.command.php");
        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/class.form.php");
//        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/class.tablefilter.php");
        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/class.menu.php");
        $this->loadIfClassExistence($PW_PATH . "/deprecated.classes/joomla.support.php");
        
        $this->loadIfClassExistence($PW_PATH . "/lib/pw.common.php");
        $this->loadIfClassExistence($PW_PATH . "/lib/pw.command.php");
        $this->loadIfClassExistence($PW_PATH . "/lib/pw.bootworks.php");

        // force IE into higest mode available (no quirks mode or compatibility mode)
        $document = JFactory::getDocument();
        $document->setMetaData("X-UA-Compatible", "IE=edge", true);
        
        //loading javascript language files
        $lang = JFactory::getLanguage();
        $default_lang = $lang->getTag();
        $document->addScript(JURI::root(). $PW_LOCATION . '/js/' . $default_lang. '.plg_pwframework.js');
         
        $document->addScript(JURI::root(). $PW_LOCATION . '/js/jquery.pwf.js');
        $document->addScript(JURI::root(). $PW_LOCATION . '/js/jquery.contextmenu.js');
        $document->addScript(JURI::root(). $PW_LOCATION . '/js/bootbox.min.js');      
        $document->addScript(JURI::root(). $PW_LOCATION . '/js/jquery.cookie.js');
        $document->addScript(JURI::root(). $PW_LOCATION . '/js/select2.full.min.js');
        $document->addStyleSheet(JURI::root(). $PW_LOCATION . '/css/pwframework.css');
        $document->addStyleSheet(JURI::root(). $PW_LOCATION . '/css/select2.min.css');
      
    }

    function loadIfClassExistence($file) {
        if (file_exists($file)) {
            require_once($file);
        } else {
            echo "Unable to load class with in file :" . $file . " because it does not exist, continueing happily";
        }
    }
}