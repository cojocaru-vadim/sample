<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initLocale()
    {
        $locale = null;
        $session = new Zend_Session_Namespace('accounts');

        

        if(!isset($session->session_id)){
            $session_id = md5 (microtime(true));
            $session->session_id = $session_id;
        }

        if ($session->locale) {
            $locale = new Zend_Locale($session->locale);
            //Zend_Debug::dump($locale); debuh
        }
        if ($locale === null) {
//            try {
//                $locale = new Zend_Locale('browser');
//            } catch (Zend_Locale_Exception $e) {
                $locale = new Zend_Locale('en_US');
                $session->locale = 'en_US';
//            }
        }
        $this->lang = $session->locale;
        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Locale', $locale);

    }

    protected function _initTranslate()
    {
        $translate = new Zend_Translate('array', APPLICATION_PATH . '/languages/',  null,  array('scan' => Zend_Translate::LOCALE_DIRECTORY, 'disableNotices' => 1));
        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Translate', $translate);
    }
    
    protected function _initPlaceholders()
    {
        $this->bootstrap('View');
        $view = $this->getResource('View');
        $view->doctype('XHTML1_STRICT');

        // Set the initial title and separator:
        $view->headTitle($this->view->translate('page-title'))->setSeparator(' - ');

        $session = new Zend_Session_Namespace('admins');
        if($session->role == Moldova_Auth_Roles::ADMIN){
            $this->view->headLink()->appendStylesheet('/css/administration.css');
        }else{
            // Set the initial stylesheet:
            $view->headLink()->appendStylesheet('/css/styles.css');

            // Set the initial JS to load:
            $view->headScript()->appendFile('https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
            $view->headScript()->appendFile('/js/script.js');
        }

    }


/*    protected function _initDoctrine()
    {
        require_once 'Doctrine/Doctrine.php';
        $this->getApplication()->getAutoloader()->pushAutoloader(array('Doctrine', 'autoload'), 'Doctrine');
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
        $config = $this->getOption('doctrine');
        $conn = Doctrine_Manager::connection($config['dsn'], 'doctrine');
        return $conn;
    }*/



    protected function _initAdmin(){
          $session = new Zend_Session_Namespace('admins');
		  if($session->role == Moldova_Auth_Roles::ADMIN){

              $this->view->headLink()->appendStylesheet('/css/administration.css');

              /***** Init left side menu *********/
                $this->view->administrationMenu = array(
                    "Companies" => "/administration/companies",
                    "Categories" => "/administration/categories",
                );
                $this->view->administrationMenuIcons = array(
                    "Companies" => "img/icons/packs/fugue/16x16/address-book.png",
                    "Categories" => "img/icons/packs/fugue/16x16/clipboard-list.png",
                );
              /***** Init left side menu *********/

              $this->view->nickname = $session->admin['nickname'];
          }
    }


    

    /*





    protected function _initNavigation()
    {
        // read navigation XML and initialize container
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml');
        $container = new Zend_Navigation($config);

        // register navigation container
        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Navigation', $container);

        // add action helper
        Zend_Controller_Action_HelperBroker::addHelper(new Cojocaru_Controller_Action_Helper_Navigation());
    }

    protected function _initSidebar()
    {
        $this->bootstrap('View');
        $view = $this->getResource('View');

        $view->placeholder('sidebar')
             // "prefix" -> markup to emit once before all items in collection
             ->setPrefix("<div class=\"sidebar\">\n    <div class=\"block\">\n")
             // "separator" -> markup to emit between items in a collection
             ->setSeparator("</div>\n    <div class=\"block\">\n")
             // "postfix" -> markup to emit once after all items in a collection
             ->setPostfix("</div>\n</div>");
    }*/


}

