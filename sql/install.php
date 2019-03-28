<?php

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'objectlogguer` 
(
    `id_objectlogguer` int(11) NOT NULL AUTO_INCREMENT,
    `affected_object` int(11),
    `action_type` varchar(255),
    `object_type` varchar(255),
    `message` text,
    `date_add` datetime,
    PRIMARY KEY  (`id_objectlogguer`)
) 
ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
