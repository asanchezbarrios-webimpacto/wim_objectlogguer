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
            && $this->registerHook('actionObjectProductUpdateBefore');
    }
    
    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookActionObjectProductUpdateBefore($params)
    {
        Db::getInstance()->insert('objectlogguer',array(
            'affected_object' => $params['object']->id, 
            'action_type' => "update",
            'object_type' =>  "product",
            'message' => "Object Product with id " . $params['object']->id . " was update",
            'date_add' => date("Y-m-d H:i:s"),
        ));
    }
}
