<?php

require_once "classes/ObjectLogger.php";


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
            && $this->registerHook('actionObjectDeleteAfter')
            && $this->registerHook('actionObjectUpdateAfter');
    }

    public function annadirAccion($params, $event) {
        $accion = new ObjectLogger();
        $accion->affected_object = $params['object']->id;
        $accion->action_type = $event;
        $accion->object_type = get_class($params['object']);
        if($event == "update" || $event == "delete") {
            $accion->message = "Object ". get_class($params['object']) . " with id " . $params['object']->id . " was $event" ."d";
        }else {
            $accion->message = "Object ". get_class($params['object']) . " with id " . $params['object']->id . " was $event" ."ed";
        }
        $accion->date_add = date("Y-m-d H:i:s");
        if(get_class($params['object']) != "ObjectLogger"){
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
}
