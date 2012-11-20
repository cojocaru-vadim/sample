<?php
// ---------file: doctrine-models-generate.php
// include main Doctrine class file
include_once 'Doctrine.php';
spl_autoload_register(array('Doctrine', 'autoload'));
// create Doctrine manager
$manager = Doctrine_Manager::getInstance();
// create database connection
$conn = Doctrine_Manager::connection('mysql://root:asdf@localhost/moldova', 'doctrine');
// auto-generate models
Doctrine::generateModelsFromDb('/tmp/models', array('doctrine'), array('classPrefix' => 'Moldova_Model_', 'classPrefixFiles' => false));
?>
