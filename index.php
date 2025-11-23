<?php

defined('APPLICATION_ENV')
  || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

//error_reporting(E_ALL | E_STRICT);
error_reporting(E_ERROR);
date_default_timezone_set('Europe/Moscow');

require_once 'vendor/autoload.php';

// загрузка конфигурации
$config = new Zend_Config_Ini('./application/config.ini', 'general');

// настройка базы данных
$dbAdapter = Zend_Db::factory(
    $config->db->adapter,
    $config->db->config->toArray()
);
Zend_Db_Table::setDefaultAdapter($dbAdapter);

// $dbAdapter->query("SET NAMES 'utf8';");

// настройка реестра
Zend_Registry::set('config', $config);
Zend_Registry::set('dbAdapter', $dbAdapter);

Zend_Controller_Front::run(
    array(
    'default' => './application/default/controllers',
    'admin' => './application/admin/controllers',
    'tests' => './application/tests/controllers'
  )
);

//$this->_helper->viewRenderer->setNoRender();
