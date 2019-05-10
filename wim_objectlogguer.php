<?php
/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Aitor_San
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once "classes/ObjectLogger.php";


if (!defined('_PS_VERSION_')) {
    exit;
}

class Wim_ObjectLogguer extends Module
{
    public function __construct()
    {
        $this->name = 'wim_objectlogguer';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Aitor_San';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('wim_objectlogguer');
        $this->description = $this->l('Modulo CRUD.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('backOfficeHeader')
            && $this->registerHook('actionObjectAddAfter')
            && $this->registerHook('actionObjectDeleteAfter')
            && $this->registerHook('actionObjectUpdateAfter');
    }

    public function annadirAccion($params, $event)
    {
        $accion = new ObjectLogger();
        $accion->affected_object = $params['object']->id;
        $accion->action_type = $event;
        $accion->object_type = get_class($params['object']);
        if ($event == "update" || $event == "delete") {
            $accion->message = "Object ". get_class($params['object'])
            . " with id " . $params['object']->id . " was $event" ."d";
        } else {
            $accion->message = "Object ". get_class($params['object'])
            . " with id " . $params['object']->id . " was $event" ."ed";
        }
        $accion->date_add = date("Y-m-d H:i:s");
        if (get_class($params['object']) != "ObjectLogger") {
            $accion->add();
        }
    }

    public function hookActionObjectAddAfter($params)
    {
        $this->annadirAccion($params, "add");
    }

    public function hookActionObjectUpdateAfter($params)
    {
        $this->annadirAccion($params, "update");
    }

    public function hookActionObjectDeleteAfter($params)
    {
        $this->annadirAccion($params, "delete");
    }


    public function renderList()
    {
        $fields_list = array(

            'id_objectlogguer' => array(
                'title' => $this->trans('id_objectlogguer', array(), 'Admin.Global'),
                'search' => true,
            ),
            'affected_object' => array(
                'title' => $this->trans('affected_object', array(), 'Admin.Global'),
                'search' => true,
            ),
            'action_type' => array(
                'title' => $this->trans('action_type', array(), 'Admin.Global'),
                'search' => true,
            ),
            'object_type' => array(
                'title' => $this->trans('object_type', array(), 'Admin.Global'),
                'search' => true,
            ),
            'message' => array(
                'title' => $this->trans('message', array(), 'Admin.Global'),
                'search' => true,
            ),
            'date_add' => array(
                'title' => $this->trans('date_add', array(), 'Admin.Global'),
                'search' => true,
            ),
        );

        if (!Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            unset($fields_list['shop_name']);
        }

        $helper_list = new HelperList();
        $helper_list->module = $this;
        $helper_list->shopLinkType = '';
        $helper_list->no_link = true;
        $helper_list->show_toolbar = true;
        $helper_list->simple_header = false;
        $helper_list->identifier = 'id';
        $helper_list->table = 'merged';
        $helper_list->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name;
        $helper_list->token = Tools::getAdminTokenLite('AdminModules');

        // This is needed for displayEnableLink to avoid code duplication
        $this->_helperlist = $helper_list;

        /* Retrieve list data */
        $actions = $this->getActions();

        return $helper_list->generateList($actions, $fields_list);
    }

    function getActions() {
        if(Tools::getValue('mergedFilter_id_objectlogguer') != null) {
            return Db::getInstance()->ExecuteS("SELECT * FROM ps_objectlogguer WHERE id_objectlogguer = ".Tools::getValue('mergedFilter_id_objectlogguer'));
        } else if (Tools::getValue('mergedFilter_affected_object') != null) {
            return Db::getInstance()->ExecuteS("SELECT * FROM ps_objectlogguer WHERE affected_object = ". "'".Tools::getValue('mergedFilter_affected_object'). "'");
        } else if (Tools::getValue('mergedFilter_action_type') != null) {
            return Db::getInstance()->ExecuteS("SELECT * FROM ps_objectlogguer WHERE action_type = ". "'".Tools::getValue('mergedFilter_action_type'). "'");
        } else if (Tools::getValue('mergedFilter_object_type') != null) {
            return Db::getInstance()->ExecuteS("SELECT * FROM ps_objectlogguer WHERE object_type = ". "'".Tools::getValue('mergedFilter_object_type'). "'");
        } else if (Tools::getValue('mergedFilter_message') != null) {
            return Db::getInstance()->ExecuteS("SELECT * FROM ps_objectlogguer WHERE ps_objectlogguer.message = ". "'".Tools::getValue('mergedFilter_message'). "'");
        } else if (Tools::getValue('mergedFilter_date_add') != null) {
            return Db::getInstance()->ExecuteS("SELECT * FROM ps_objectlogguer WHERE date_add = ".Tools::getValue('mergedFilter_date_add'));
        } else {
            return Db::getInstance()->ExecuteS("SELECT * FROM ps_objectlogguer");
        }
    }

    function getContent() {
        $this->_html .= $this->renderList();
        return $this->_html;
    }
    
}
