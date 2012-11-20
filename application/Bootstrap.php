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
                $locale = new Zend_Locale('ro_MD');
                $session->locale = 'ro_MD';
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
            $view->headLink()->appendStylesheet('/css/components.css');
            $view->headLink()->appendStylesheet('/css/reset.css');
            $view->headLink()->appendStylesheet('/css/slider.css');
            //$view->headLink()->appendStylesheet('/css/tipsy.css');
            //$view->headLink()->appendStylesheet('/css/forms.css');
            //$view->headLink()->appendStylesheet('/css/nivo-slider.css');

            // Set the initial JS to load:


            $view->headScript()->appendFile('https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js');
            //$view->headScript()->appendFile('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js');
            //$view->headScript()->appendFile('/js/jquery.tipsy.js');
            //$view->headScript()->appendFile('/js/jquery.nivo.slider.pack.js');
            $view->headScript()->appendFile('/js/script.js');
            $view->headScript()->appendFile('/js/jquery.slider.js');
            //$view->headScript()->appendFile('http://www.openlayers.org/api/OpenLayers.js');
        }

    }


    protected function _initDoctrine()
    {
        require_once 'Doctrine/Doctrine.php';
        $this->getApplication()->getAutoloader()->pushAutoloader(array('Doctrine', 'autoload'), 'Doctrine');
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
        $config = $this->getOption('doctrine');
        $conn = Doctrine_Manager::connection($config['dsn'], 'doctrine');
        return $conn;
    }


    protected function _initSide()
    {
            $this->view->lang = $this->lang;

        /************************************************** METEO *********************************************/

        
                $location = 'Chisinau';
                $weather_xml = simplexml_load_file('http://www.google.com/ig/api?weather=' . $location);
                $this->view->information = $weather_xml->xpath("/xml_api_reply/weather/forecast_information");
                $this->view->current = $weather_xml->xpath("/xml_api_reply/weather/current_conditions");
                $this->view->forecast_list = $weather_xml->xpath("/xml_api_reply/weather/forecast_conditions");

                $lat = '47.026859';
                $lng = '28.841551';
                $doc = new DOMDocument();
                $doc->load("http://api.yr.no/weatherapi/locationforecastlts/1.1/?lat={$lat};lon={$lng}");
                $times = $doc->getElementsByTagName( "time" );

                $currentMeteo = array();
                $i = 0;
                foreach( $times as $time )
                {
                    if($i==2) break;
                    $temperature = $time->getElementsByTagName( "temperature" );
                    $precipitation = $time->getElementsByTagName( "precipitation" );

                    if ($temperature->length > 0)
                    {
                        $fromTime = date('Y-m-d H:i:s', strtotime($time->getAttribute('from')));
                        $toTime = date('Y-m-d H:i:s', strtotime($time->getAttribute('to')));
                        $currentMeteo[0]['temperatureValue'] =  round($time->getElementsByTagName( "temperature" )->item(0)->getAttribute('value'));
                        $currentMeteo[0]['pressureValue'] =  round($time->getElementsByTagName( "pressure" )->item(0)->getAttribute('value') * 0.75006);
                        $currentMeteo[0]['windDirection'] =  $time->getElementsByTagName( "windDirection" )->item(0)->getAttribute('name');
                        $currentMeteo[0]['windDirectionDEG'] =  $time->getElementsByTagName( "windDirection" )->item(0)->getAttribute('deg');
                        $currentMeteo[0]['windSpeed'] =  $time->getElementsByTagName( "windSpeed" )->item(0)->getAttribute('mps');
                        $currentMeteo[0]['humidity'] =  round($time->getElementsByTagName( "humidity" )->item(0)->getAttribute('value'));

                        //echo "From: " . $fromTime . " -> To: " . $toTime . "<br />";
                    }

                    if ($precipitation->length > 0)
                    {
                        $fromTime = date('Y-m-d H:i:s', strtotime($time->getAttribute('from')));
                        $onlyTime = date('H', strtotime($time->getAttribute('to')));
                        $night = ($onlyTime > 18 || $onlyTime < 6) ? 1 : 0;
                        $toTime = date('Y-m-d H:i:s', strtotime($time->getAttribute('to')));
                        $currentMeteo[0]['precipitationValue'] =  $time->getElementsByTagName( "precipitation" )->item(0)->getAttribute('value');
                        $currentMeteo[0]['image'] =  "http://api.yr.no/weatherapi/weathericon/1.0/?symbol=".$time->getElementsByTagName( "symbol" )->item(0)->getAttribute('number').";is_night=".$night.";content_type=image/png";
                        $currentMeteo[0]['condition'] =  $this->view->translate($time->getElementsByTagName( "symbol" )->item(0)->getAttribute('number').$time->getElementsByTagName( "symbol" )->item(0)->getAttribute('id'));
                    }
                    $i++;
                }
                $this->view->currentMeteo = $currentMeteo;

        //dump($this->view->current); die;

        /************************************************** METEO *********************************************/


        /************************************************** Currency *********************************************/
                date_default_timezone_set('Europe/Chisinau');
                $today= date("d.m.Y");
                $yesterday= date("d.m.Y", strtotime('-1 day'));
                $q_today = Doctrine_Query::create()
                        ->select()
                        ->from('Moldova_Model_Currency')
                        ->where('date = ?', $today);
        
                if(count($q_today->fetchArray())==0){
                    $currency_xml = simplexml_load_file('http://bnm.md/md/official_exchange_rates?get_xml=1&date=' . $today);
                    $currency_information = $currency_xml->xpath("/ValCurs/Valute");
                    //dump($currency_information[0]->NumCode); die;
                    for($i=0; $i<count($currency_information); $i++){
                        $currency_entry = new Moldova_Model_Currency();
                        $currency_entry->date = $today;
                        $currency_entry->valute_id = $currency_information[$i]['ID'];
                        $currency_entry->num_code = $currency_information[$i]->NumCode;
                        $currency_entry->char_code = $currency_information[$i]->CharCode;
                        $currency_entry->nominal = $currency_information[$i]->Nominal;
                        $currency_entry->name = $currency_information[$i]->Name;
                        $currency_entry->value = $currency_information[$i]->Value;
                        $currency_entry->save();
                    }
                    //currency_information[0]->CharCode
                }

                $currency_information = $q_today->fetchArray();
                //dump($currency_information); die;

                $q_yesterday = Doctrine_Query::create()
                        ->select()
                        ->from('Moldova_Model_Currency')
                        ->where('date = ?', $yesterday);

                $currency_information_yesterday = $q_yesterday->fetchArray();

                for($i=0; $i<count($currency_information); $i++){
                    $differenceImg = '';
                    if($currency_information[$i]['value'] > $currency_information_yesterday[$i]['value']){
                        $differenceImg = 'increase';
                    } else {
                        $differenceImg = 'decrease';
                    }
                    $difference = $currency_information[$i]['value'] - $currency_information_yesterday[$i]['value'];

                    $currency_information[$currency_information[$i]['char_code']] = $currency_information[$i];
                    $currency_information[$currency_information[$i]['char_code']]['differenceImg'] = $differenceImg;
                    $currency_information[$currency_information[$i]['char_code']]['difference'] = $difference;
                    unset($currency_information[$i]);
                }
                //dump($currency_information); die;

                $this->view->currency_information = $currency_information;
                //dump($this->view->currency_information[0]->CharCode); die;

        /************************************************** Currency *********************************************/


        /************************************************** News *********************************************/

                $session = new Zend_Session_Namespace('accounts');
                $email_array = explode('@', $session->account['email']);
                $this->view->username = $email_array[0];
                //$q = 'moldova';
                if($session->locale == 'ro_MD'){
                    $newsFeed = "http://www.24h.md/ro/rss/9929/news.xml";
                    $newsSource = '<a target="_blank" href="http://www.24h.md/ro/start/">www.24h.md</a>';
                    $this->view->news = array();
                    $newsFeed = Zend_Feed_Reader::import($newsFeed);
                    $count = 0;
                    foreach($newsFeed as $entry){
                        //echo $entry->getDescription();
                        $xml = simplexml_load_string($entry->getDescription());
                        //dump($xml->tr->td[1]);
                        $this->view->news[$count]['title'] = $entry->getTitle();
                        $this->view->news[$count]['description'] = $xml->tr->td[1];
                        $count++;
                        //dump($xml->tr->td[1]);
                        //dump($xml);
                    }
                }elseif($session->locale == 'ru_RU'){
                    $newsFeed = "http://www.24h.md/ru/rss/9929/news.xml";
                    $newsSource = '<a target="_blank" href="http://www.24h.md/ro/start/">www.24h.md</a>';
                    $this->view->news = array();
                    $newsFeed = Zend_Feed_Reader::import($newsFeed);
                    $count = 0;
                    foreach($newsFeed as $entry){
                        //echo $entry->getDescription();
                        $xml = simplexml_load_string($entry->getDescription());
                        //dump($xml->tr->td[1]);
                        $this->view->news[$count]['title'] = $entry->getTitle();
                        $this->view->news[$count]['description'] = $xml->tr->td[1];
                        $count++;
                        //dump($xml->tr->td[1]);
                        //dump($xml);
                    }
                }elseif($session->locale == 'en_US'){
                    //$newsFeed = "http://news.google.com/news?hl=en&q=$q&output=rss";
                    $newsFeed = "http://feeds.reuters.com/reuters/worldNews";
                    $newsSource = '<a target="_blank" href="http://www.reuters.com/">www.reuters.com</a>';
                    $this->view->news = array();
                    $newsFeed = Zend_Feed_Reader::import($newsFeed);
                    $count = 0;
                    //dump($newsFeed);
                    foreach($newsFeed as $entry){
                        //echo $entry->getDescription();
                        //$xml = simplexml_load_string($entry->getDescription());
                        //dump($xml);
                        $this->view->news[$count]['title'] = $entry->getTitle();
                        $this->view->news[$count]['description'] = $entry->getDescription();
                        $count++;
                        //dump($xml->tr->td[1]);
                        //dump($xml);
                        //echo "<hr />";
                    }
                    //die;
                }else{
                    $newsFeed = "http://www.24h.md/ro/rss/9929/news.xml";
                    $newsSource = '<a target="_blank" href="http://www.24h.md/ro/start/">www.24h.md</a>';
                    $this->view->news = array();
                    $newsFeed = Zend_Feed_Reader::import($newsFeed);
                    $count = 0;
                    foreach($newsFeed as $entry){
                        //echo $entry->getDescription();
                        $xml = simplexml_load_string($entry->getDescription());
                        //dump($xml->tr->td[1]);
                        $this->view->news[$count]['title'] = $entry->getTitle();
                        $this->view->news[$count]['description'] = $xml->tr->td[1];
                        $count++;
                        //dump($xml->tr->td[1]);
                        //dump($xml);
                    }
                }

                $this->view->newsSource = $newsSource;
                //dump($this->view->news); die;


        /************************************************** News *********************************************/


    }

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

