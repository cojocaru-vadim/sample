<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 6/17/11
 * Time: 12:32 PM
 */
 
class MeteoController extends Zend_Controller_Action
{

    public function preDispatch()
    {
        $this->_helper->layout->setLayout('meteo_layout');
    }

    public function init()
    {
        /* Initialize action controller here */
        $this->session = new Zend_Session_Namespace('accounts');

    }

    public function indexAction()
    {

        //Moldova_Utils::getCoord();
        //$this->view->breadcrumb = array("meteo" => $this->view->translate('meteo'));

        //Default meteo
        $lat = '47.026859';
        $lng = '28.841551';

        $req = $this->getRequest()->getParams();
        $region = $req['region'];
        $locality = $req['locality'];
        
        if($this->validateRegionOrLocality($req))
        {
            if($region != 'n' && $locality != 'n')
            {
               $q = Doctrine_Query::create()
                       ->select('l.lat, l.lng, l.region_id, l.locality_url, r.region_url, r.region_'. $this->session->locale.' AS region_name, l.locality_'. $this->session->locale.' AS locality_name')
                       ->from('Moldova_Model_Localities l')
                       ->leftJoin('l.Moldova_Model_Regions r')
                       ->where('l.locality_url = ?', $locality)
                       ->andWhere('r.region_url = ?' , $region);

               $locality = $q->fetchArray();
               $this->view->locality = $locality;
               $lat = $locality[0]['lat'];
               $lng = $locality[0]['lng'];

               //dump($locality);
               //die;
               $this->view->pageTitle = $this->view->translate('meteo') . ' ' . $this->view->translate('in') . ' ' . $locality[0]['locality_name'] . ', ' . $locality[0]['Moldova_Model_Regions']['region_name'] . ', Moldova' ;
               $this->view->boxTitle = $this->view->translate('meteo').'<br />'.$this->view->translate('choose-locality');
               $this->view->localities = $this->getLocalities($locality[0]['region_id']);
               $this->view->breadcrumb = array("meteo" => $this->view->translate('meteo'), $locality[0]['Moldova_Model_Regions']['region_url'] => $locality[0]['Moldova_Model_Regions']['region_name'], $locality[0]['locality_url'] => $locality[0]['locality_name']);

            }
            else if($region != 'n')
            {
               $q = Doctrine_Query::create()
                       ->select('r.region_id, r.lat, r.lng, r.region_'. $this->session->locale.' AS region_name')
                       ->from('Moldova_Model_Regions r')
                       ->where('r.region_url = ?', $region);

               $region = $q->fetchArray();
               //dump($region); die;
               $this->view->region = $region;
               //echo $region[0]['lat']; die;
               $lat = $region[0]['lat'];
               $lng = $region[0]['lng'];
               $this->view->pageTitle = $this->view->translate('meteo') . ' ' . $this->view->translate('in') . ' ' . $region[0]['region_name'] . ', Moldova' ;
               $this->view->boxTitle = $this->view->translate('meteo').'<br />'.$this->view->translate('choose-locality');
               $this->view->localities = $this->getLocalities($region[0]['region_id']);
               $this->view->breadcrumb = array("meteo" => $this->view->translate('meteo'), $region[0]['region_url'] => $region[0]['region_name']);

            }
            else
            {
               $this->view->pageTitle = $this->view->translate('meteo') . ' ' . $this->view->translate('in') . ' ' . 'Moldova, Chișinău';
               $this->view->boxTitle = $this->view->translate('meteo').'<br />'.$this->view->translate('choose-region');
               $this->view->regions = $this->getRegions();
               $this->view->breadcrumb = array("meteo" => $this->view->translate('meteo'));
            }
        }
        else
        {
           throw new Zend_Controller_Action_Exception("Invalid input");
        }

        $doc = new DOMDocument();
        $doc->load("http://api.yr.no/weatherapi/locationforecastlts/1.1/?lat={$lat};lon={$lng}");
        $times = $doc->getElementsByTagName( "time" );

        $meteoData = array();

        foreach( $times as $time )
        {
            $temperature = $time->getElementsByTagName( "temperature" );
            $precipitation = $time->getElementsByTagName( "precipitation" );

            if ($temperature->length > 0)
            {
                $fromTime = date('Y-m-d H:i:s', strtotime($time->getAttribute('from')));
                $toTime = date('Y-m-d H:i:s', strtotime($time->getAttribute('to')));
                $meteoData[$toTime]['temperatureValue'] =  round($time->getElementsByTagName( "temperature" )->item(0)->getAttribute('value'));
                $meteoData[$toTime]['pressureValue'] =  round($time->getElementsByTagName( "pressure" )->item(0)->getAttribute('value') * 0.75006);
                $meteoData[$toTime]['windDirection'] =  $time->getElementsByTagName( "windDirection" )->item(0)->getAttribute('name');
                $meteoData[$toTime]['windDirectionDEG'] =  $time->getElementsByTagName( "windDirection" )->item(0)->getAttribute('deg');
                $meteoData[$toTime]['windSpeed'] =  $time->getElementsByTagName( "windSpeed" )->item(0)->getAttribute('mps');
                $meteoData[$toTime]['humidity'] =  round($time->getElementsByTagName( "humidity" )->item(0)->getAttribute('value'));

                //echo "From: " . $fromTime . " -> To: " . $toTime . "<br />";
            }

            if ($precipitation->length > 0)
            {
                $fromTime = date('Y-m-d H:i:s', strtotime($time->getAttribute('from')));
                $onlyTime = date('H', strtotime($time->getAttribute('to')));
                $night = ($onlyTime > 18 || $onlyTime < 6) ? 1 : 0;
                $toTime = date('Y-m-d H:i:s', strtotime($time->getAttribute('to')));
                $meteoData[$toTime]['precipitationValue'] =  $time->getElementsByTagName( "precipitation" )->item(0)->getAttribute('value');
                $meteoData[$toTime]['image'] =  "http://api.yr.no/weatherapi/weathericon/1.0/?symbol=".$time->getElementsByTagName( "symbol" )->item(0)->getAttribute('number').";is_night=".$night.";content_type=image/png";
                $meteoData[$toTime]['condition'] =  $this->view->translate($time->getElementsByTagName( "symbol" )->item(0)->getAttribute('number').$time->getElementsByTagName( "symbol" )->item(0)->getAttribute('id'));
            }
        }
        //dump($meteoData); die;
        $this->view->meteoData = $meteoData;
    }

    private function getRegions()
    {
       $q_r = Doctrine_Query::create()
                ->select('*, r.region_'. $this->session->locale.' AS region_name')
                ->from('Moldova_Model_Regions r');
        //die("ss");

        return $q_r->fetchArray();
    }

    private function getLocalities($region_id)
    {
       $q_r = Doctrine_Query::create()
                ->select('l.*, r.*, l.locality_'. $this->session->locale.' AS locality_name')
                ->from('Moldova_Model_Localities l')
                ->leftJoin('l.Moldova_Model_Regions r')
                ->where('l.region_id = ?' , $region_id);
                ;
        //dump($q_r->fetchArray());
        //die("ss");

        return $q_r->fetchArray();
    }

    private function validateRegionOrLocality($req)
    {

        $valid = false;
        $region = $req['region'];
        $locality = $req['locality'];

        if($region != 'n' && $locality != 'n')
        {

            $filters = array(
                    'region' => array('HtmlEntities', 'StripTags', 'StringTrim'),
                    'locality'  => array('HtmlEntities', 'StripTags', 'StringTrim')
            );

            $q=Doctrine_Query::create()
                    ->select('r.region_url')
                    ->from('Moldova_Model_Regions r');
            $urls = $q->execute();

            foreach($urls as $url) {
                $region_haystack[] = $url->region_url;
            }

            $q=Doctrine_Query::create()
                    ->select('l.locality_url')
                    ->from('Moldova_Model_Localities l');
            $urls = $q->execute();

            foreach($urls as $url) {
                $locality_haystack[] = $url->locality_url;
            }

            $validators = array(
                    'region' => array(
                        array('InArray', 'haystack' => $region_haystack)
                     ),
                    'locality'  => array(
                        array('InArray', 'haystack' => $locality_haystack)
                    )
                );

            //$input = new Zend_Filter_Input($filters, $validators);
            $input = new Zend_Filter_Input(null, $validators);
            $input->setData($req);

            if($input->isValid())
            {
                $valid = true;
            }

        }else if($region != 'n')
        {
            $filters = array(
                    'region' => array('HtmlEntities', 'StripTags', 'StringTrim'),
            );

            $q=Doctrine_Query::create()
                    ->select('r.region_url')
                    ->from('Moldova_Model_Regions r');
            $urls = $q->execute();

            foreach($urls as $url)
            {
                $region_haystack[] = $url->region_url;
            }

            $validators = array(
                    'region' => array(
                        array('InArray', 'haystack' => $region_haystack)
                     )
                );

            //$input = new Zend_Filter_Input($filters, $validators);
            $input = new Zend_Filter_Input(null, $validators);
            $input->setData($req);
            //dump($input->getUnescaped()); die;
            //dump($input->getInvalid()); die;

            if($input->isValid())
            {
                $valid = true;
            }
        }
        else if($locality != 'n')
        {
            $filters = array(
                    'locality'  => array('HtmlEntities', 'StripTags', 'StringTrim')
            );

            $q=Doctrine_Query::create()
                    ->select('l.locality_url')
                    ->from('Moldova_Model_Localities l');
            $urls = $q->execute();

            foreach($urls as $url)
            {
                $locality_haystack[] = $url->locality_url;
            }

            $validators = array(
                    'locality'  => array(
                        array('InArray', 'haystack' => $locality_haystack)
                    )
                );
            //$input = new Zend_Filter_Input($filters, $validators);
            $input = new Zend_Filter_Input(null, $validators);
            $input->setData($req);

            if($input->isValid())
            {
                $valid = true;
            }
        }
        elseif($region == 'n' && $locality == 'n')
        {
            $valid = true;
        }


        return $valid;
    }

}