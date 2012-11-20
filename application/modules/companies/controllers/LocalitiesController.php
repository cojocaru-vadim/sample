<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/16/11
 * Time: 10:11 PM
 */
 
class Companies_LocalitiesController extends Zend_Controller_Action{

    public function preDispatch()
    {

    }


    public function init()
    {

    }

    public function indexAction()
    {
        $req = $this->getRequest()->getParams();

        if($this->validateRegionOrLocality($req)){
            $session = new Zend_Session_Namespace('accounts');
            $region = $req['region'];
            $locality = $req['locality'];
            $page = $req['page'];
            $perPage = 10;
            $numPageLinks = 5;

            if($region != 'n' && $locality != 'n'){

               $q = Doctrine_Query::create()
                   ->select("c.company_id, c.logo_img, c.company_url, c.name, l.locality_".$session->locale." AS locality_name")
                   ->from('Moldova_Model_Companies c')
                   ->leftJoin('c.Moldova_Model_Accounts a')
                   ->leftJoin('c.Moldova_Model_Localities l')
                   ->where('c.is_deleted = ?', 0)
                   ->andWhere('c.status = ?', 1)
                   ->andWhere('l.locality_url = ?', $req['locality'])
                   ->orderBy('c.creation_date DESC');




            //initilaze pager
            $pager = new Doctrine_Pager($q, $page, $perPage);

            //execute paged query
            $result = $pager->execute(array(), Doctrine::HYDRATE_ARRAY);

            //initialize pager layout
            $pagerRange = new Doctrine_Pager_Range_Sliding(array('chunk' => $numPageLinks), $pager);

            //$pagerUrlBase = $this->view->url(array('module' => 'admin' , 'controller' => 'people'), 'default') . "/{%page}";
            $pagerUrlBase = $this->view->url(array(), 'companies-localities', 1) . "/{$region}/{$locality}/{%page}";

            $pagerLayout = new Doctrine_Pager_Layout($pager, $pagerRange, $pagerUrlBase);

            //set page link display template
            $pagerLayout->setTemplate('<li><a href="{%url}">{%page}</a></li>');
            $pagerLayout->setSelectedTemplate('<li class="active">{%page}</li>');
            $pagerLayout->setSeparatorTemplate('');

            $this->view->pages = $pagerLayout->display(null, true);

            for($i=0; $i<count($result); $i++){
                if($result[$i]['logo_img'] == null){
                    $result[$i]['logo_img'] = "uploads/companies/no_logo.png";
                } else {
                    $result[$i]['logo_img'] = "uploads/companies/{$result[$i]['company_id']}/{$result[$i]['logo_img']}";
                }

            }
                
                $this->view->companies = $result;
                //print_r($this->view->companies);


            $breadQuery = Doctrine_Query::create()
                    ->select("r.region_".$session->locale." AS region_name, r.region_url")
                    ->from('Moldova_Model_Regions r')
                    ->where('r.region_url = ?' , $req['region']);
            $breadResult = $breadQuery->fetchArray();

            $breadQuery2 = Doctrine_Query::create()
                    ->select("l.locality_".$session->locale." AS locality_name, l.locality_url")
                    ->from('Moldova_Model_Localities l')
                    ->where('l.locality_url = ?' , $req['locality']);
            $breadResult2 = $breadQuery2->fetchArray();

            $this->view->locality_name = $breadResult2[0]['locality_name'];
            $this->view->breadcrumb = array("companies" => $this->view->translate('companies'), "localities" => $this->view->translate('localities'), $breadResult[0]['region_url'] => $breadResult[0]['region_name'], $breadResult2[0]['locality_url'] => $breadResult2[0]['locality_name']);


            }else if($region != 'n'){
               $q = Doctrine_Query::create()
                                    ->select("l.locality_id, r.region_url AS region_url, r.region_".$session->locale." AS region_name, l.locality_".$session->locale." AS locality_name, l.locality_url AS locality_url")
                                   ->from('Moldova_Model_Localities l')
                                   ->leftJoin('l.Moldova_Model_Regions r')
                                   ->andWhere('r.region_url = ?', $req['region']);

               $this->view->localities = $q->fetchArray();

               $q = Doctrine_Query::create()
                                   ->select("c.*")
                                   ->from('Moldova_Model_Companies c')
                                   ->leftJoin('c.Moldova_Model_Accounts a')
                                   ->leftJoin('c.Moldova_Model_Regions r')
                                   ->where('c.is_deleted = ?', 0)
                                   ->andWhere('c.status = ?', 1)
                                   ->andWhere('r.region_url = ?', $req['region'])
                                   ->orderBy('c.creation_date DESC');


            //initilaze pager
            $pager = new Doctrine_Pager($q, $page, $perPage);

            //execute paged query
            $result = $pager->execute(array(), Doctrine::HYDRATE_ARRAY);

            //initialize pager layout
            $pagerRange = new Doctrine_Pager_Range_Sliding(array('chunk' => $numPageLinks), $pager);

            //$pagerUrlBase = $this->view->url(array('module' => 'admin' , 'controller' => 'people'), 'default') . "/{%page}";
            $pagerUrlBase = $this->view->url(array(), 'companies-localities', 1) . "/{$region}/n/{%page}";

            $pagerLayout = new Doctrine_Pager_Layout($pager, $pagerRange, $pagerUrlBase);

            //set page link display template
            $pagerLayout->setTemplate('<li><a href="{%url}">{%page}</a></li>');
            $pagerLayout->setSelectedTemplate('<li class="active">{%page}</li>');
            $pagerLayout->setSeparatorTemplate('');


            $this->view->pages = $pagerLayout->display(null, true);

            for($i=0; $i<count($result); $i++){
                if($result[$i]['logo_img'] == null){
                    $result[$i]['logo_img'] = "uploads/companies/no_logo.png";
                } else {
                    $result[$i]['logo_img'] = "uploads/companies/{$result[$i]['company_id']}/{$result[$i]['logo_img']}";
                }

            }

            $this->view->companies = $result;

            $breadQuery = Doctrine_Query::create()
                    ->select("r.region_".$session->locale." AS region_name, r.region_url")
                    ->from('Moldova_Model_Regions r')
                    ->where('r.region_url = ?' , $req['region']);
            $breadResult = $breadQuery->fetchArray();
            $this->view->breadcrumb = array("companies" => $this->view->translate('companies'), "localities" => $this->view->translate('localities'), $breadResult[0]['region_url'] => $breadResult[0]['region_name']);



            } else {
                $q = Doctrine_Query::create()
                     ->select('r.region_'.$session->locale.' AS region_name, r.region_url, r.region_id')
                     ->from('Moldova_Model_Regions r');
                $this->view->regions = $q->fetchArray();
                $this->view->breadcrumb = array("companies" => $this->view->translate('companies'), "localities" => $this->view->translate('localities'));

            }


        }else{
           throw new Zend_Controller_Action_Exception("Invalid input");
        }
    }

    /**
     * Validates region AND/OR locality which come from $_GET
     * @param  $req
     * @return bool
     */
    private function validateRegionOrLocality($req){

        $valid = false;
        $region = $req['region'];
        $locality = $req['locality'];

        if($region != 'n' && $locality != 'n'){

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

            if($input->isValid()){
                $valid = true;
            }

        }else if($region != 'n'){
            $filters = array(
                    'region' => array('HtmlEntities', 'StripTags', 'StringTrim'),
            );

            $q=Doctrine_Query::create()
                    ->select('r.region_url')
                    ->from('Moldova_Model_Regions r');
            $urls = $q->execute();

            foreach($urls as $url) {
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

            if($input->isValid()){
                $valid = true;
            }
        }else if($locality != 'n'){
            $filters = array(
                    'locality'  => array('HtmlEntities', 'StripTags', 'StringTrim')
            );

            $q=Doctrine_Query::create()
                    ->select('l.locality_url')
                    ->from('Moldova_Model_Localities l');
            $urls = $q->execute();

            foreach($urls as $url) {
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

            if($input->isValid()){
                $valid = true;
            }
        }elseif($region == 'n' && $locality == 'n'){
            $valid = true;
        }


        return $valid;
    }
}
