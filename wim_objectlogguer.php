<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Wim_objectlogguer extends Module
{
    public function __construct()
    {
        $this->name = 'wim_objectlogguer';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Aitor Sánchez';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('wim_objectlogguer');
        $this->description = $this->l('Almacena los cambios que suceden al añadir, modificar o eliminar un producto, cliente o cualquier cosa.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('backOfficeHeader') 
            && $this->registerHook('actionObjectAddAfter')
            && $this->registerHook('actionObjectAddBefore')
            && $this->registerHook('actionObjectDeleteAfter')
            && $this->registerHook('actionObjectDeleteBefore')
            && $this->registerHook('actionObjectUpdateAfter')
            && $this->registerHook('actionObjectUpdateBefore');
    }
    
    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookActionObjectAddAfter($params)
    {
        Db::getInstance()->insert('objectlogguer',array(
            'affected_object' => $params['object']->id, 
            'action_type' =>  "add" ,
            'object_type' =>  get_class($params['object']),
            'message' => "Object ". get_class($params['object']) . " with id " . $params['object']->id . " add",
            'date_add' => date("Y-m-d H:i:s"),
        ));
    }
    /*
    public function hookActionObjectAddBefore($params)
    {
        Db::getInstance()->insert('objectlogguer',array(
            'affected_object' => $params['object']->id, 
            'action_type' =>  "add" ,
            'object_type' =>  get_class($params['object']),
            'message' => "Object ". get_class($params['object']) . " with id " . $params['object']->id . " add",
            'date_add' => date("Y-m-d H:i:s"),
        ));
    }*/

    public function hookActionObjectDeleteAfter($params)
    {
        Db::getInstance()->insert('objectlogguer',array(
            'affected_object' => $params['object']->id, 
            'action_type' => "delete",
            'object_type' =>  get_class($params['object']),
            'message' => "Object ". get_class($params['object']) . " with id " . $params['object']->id . " delete",
            'date_add' => date("Y-m-d H:i:s"),
        ));
    }
    /*
    public function hookActionObjectDeleteBefore($params)
    {
        Db::getInstance()->insert('objectlogguer',array(
            'affected_object' => $params['object']->id, 
            'action_type' => "delete",
            'object_type' =>  get_class($params['object']),
            'message' => "Object ". get_class($params['object']) . " with id " . $params['object']->id . " delete",
            'date_add' => date("Y-m-d H:i:s"),
        ));
    }*/


    public function hookActionObjectUpdateAfter($params)
    {
        Db::getInstance()->insert('objectlogguer',array(
            'affected_object' => $params['object']->id, 
            'action_type' =>  "update" ,
            'object_type' =>  get_class($params['object']),
            'message' => "Object ". get_class($params['object']) . " with id " . $params['object']->id . " update",
            'date_add' => date("Y-m-d H:i:s"),
        ));
    }
    /*
    public function hookActionObjectUpdateBefore($params)
    {
        Db::getInstance()->insert('objectlogguer',array(
            'affected_object' => $params['object']->id, 
            'action_type' =>  "update" ,
            'object_type' =>  get_class($params['object']),
            'message' => "Object ". get_class($params['object']) . " with id " . $params['object']->id . " update",
            'date_add' => date("Y-m-d H:i:s"),
        ));
    }*/

    
}
