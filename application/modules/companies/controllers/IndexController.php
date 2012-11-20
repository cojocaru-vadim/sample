<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/13/11
 * Time: 1:44 PM
 */
 
class Companies_IndexController extends Zend_Controller_Action{

            public function preDispatch()
    {

    }


    public function init()
    {

    }

    public function indexAction()
    {
        $session = new Zend_Session_Namespace('accounts');
        $q = Doctrine_Query::create()
             ->select('c.name_'.$session->locale.' AS cat_name, c.cat_url, c.category_id')
             ->from('Moldova_Model_CompaniesCategories c')
             ->where('c.is_deleted = ?', 0);
        $this->view->categories = $q->fetchArray();

        $q = Doctrine_Query::create()
             ->select('r.region_'.$session->locale.' AS region_name, r.region_url, r.region_id')
             ->from('Moldova_Model_Regions r');
        $this->view->regions = $q->fetchArray();

        $q = Doctrine_Query::create()
             ->select()
             ->from('Moldova_Model_Countries c');
        $this->view->countries = $q->fetchArray();

        $this->view->breadcrumb = array("companies" => $this->view->translate('companies'));

    }

}
