<?php

class IndexController extends Zend_Controller_Action
{

    public function preDispatch(){
        $this->_helper->layout->setLayout('home_layout');
    }
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        //dump($_SESSION); die;
        $q = Doctrine_Query::create()
               ->select("c.company_id, c.logo_img, c.company_url, c.name")
               ->from('Moldova_Model_Companies c')
               ->where('c.is_deleted = ?', 0)
               ->andWhere('c.status = ?', 1)
               ->orderBy('c.creation_date DESC')
               ->limit(12);


        $result = $q->fetchArray();

        for($i=0; $i<count($result); $i++){
            if($result[$i]['logo_img'] == null){
                $result[$i]['logo_img'] = "uploads/companies/no_logo.png";
            } else {
                $result[$i]['logo_img'] = "uploads/companies/{$result[$i]['company_id']}/{$result[$i]['logo_img']}";
            }

        }

        $this->view->companies = $result;
        $session = new Zend_Session_Namespace('accounts');
        $q = Doctrine_Query::create()
                ->select('f.*, f.message_'.$session->locale.' as message, f.duty_'.$session->locale.' as duty')
                ->from('Moldova_Model_Feedback f');
        
        $feedbaks = $q->fetchArray();
        $this->view->feedbacks = $feedbaks;


        //Moldova_Utils::generateXML();
        //Moldova_Utils::saveFeedback();
        //die;

        //dump($feedbaks);
        //die;
        





/*        $sql = Doctrine::getTable('Moldova_Model_CompaniesCategories')->findAll();
        $data = array();
        $data['items'] = $sql->toArray();
        echo $sql->count(); die;
        print_r($data);
        $data['items'] = array(array('id'=>1, 'name_en'=>'Computers and office equipment', 'name_ro'=>'Calculatoare si birotica', 'name_ru'=>'Компьютеры и Оргтехника'),array('id'=>2, 'name_en'=>'Transfer', 'name_ro'=>'Transport', 'name_ru'=>'Транспорт'),array('id'=>3, 'name_en'=>'Communication', 'name_ro'=>'Aparate telefonice', 'name_ru'=>'Телефоны и Связь'));
        print_r($data);*/

/*        $q = Doctrine_Query::create()->from('Moldova_Model_CompaniesCategories cat')->where('cat.is_deleted = ?', 0);
        $result = $q->fetchArray();

        $data = array();
        //$data['items'] = $sql->toArray();
        //$data['items'] = array(array('id'=>1, 'name_en'=>'Computers and office equipment', 'name_ro'=>'Calculatoare si birotica', 'name_ru'=>'Компьютеры и Оргтехника'),array('id'=>2, 'name_en'=>'Transfer', 'name_ro'=>'Transport', 'name_ru'=>'Транспорт'),array('id'=>3, 'name_en'=>'Communication', 'name_ro'=>'Aparate telefonice', 'name_ru'=>'Телефоны и Связь'));
        $data['items'] = $result;

        print_r($data);
        $data['items'] = array(array('id'=>1, 'name_en'=>'Computers and office equipment', 'name_ro'=>'Calculatoare si birotica', 'name_ru'=>'Компьютеры и Оргтехника'),array('id'=>2, 'name_en'=>'Transfer', 'name_ro'=>'Transport', 'name_ru'=>'Транспорт'),array('id'=>3, 'name_en'=>'Communication', 'name_ro'=>'Aparate telefonice', 'name_ru'=>'Телефоны и Связь'));
        print_r($data);*/


/*            $q = Doctrine_Query::create()
                ->update('Moldova_Model_CompaniesCategories cat')
                ->set('cat.is_deleted', '?', 1)
                ->whereIn('cat.category_id', array(1, 2));
            var_dump($q->execute());*/

/*        $q2 = Doctrine_Query::create()
                            ->select('sc.category_id, sc.name_en_US, sc.name_ro_MD, sc.name_ru_RU, c.name_en_US AS cat_name')
                            ->from('Moldova_Model_CompaniesSubcategories sc')
                            ->leftJoin('sc.Moldova_Model_CompaniesCategories c')
                            ->where('sc.is_deleted = ?', 0);

        $result2 = $q2->fetchArray();
        Moldova_Utils::customPrint($result2);*/
/*        echo Moldova_Utils::Transliterate("Это небольшой пример использования функции транслитерации...
Эта строка в кодировке cp-1251, а результат мы получим в utf-8.", "utf-8", "utf-8");*/
        //echo Moldova_Utils::cleanUrl("Chișinău");
        //var_dump(iconv_get_encoding('all'));
        //Moldova_Utils::insertLoc();
        //$sql = Doctrine::getTable('Moldova_Model_Localities')->findAll();
        //dump($sql->toArray());

        //dump(Moldova_Utils::insertLocTwo());
        //Moldova_Utils::insertLocTwo();
        //echo Moldova_Utils::cleanUrl("ȚȘĂÎÂ");
        //$this->view->breadcrumb = array("admin" => "Admin", "categ" => "Categories" );companies-categories/transport
        //$this->view->breadcrumb = array("companies-categories" => "Companies categories", "transport" => "Transport" );

        //Moldova_Utils::insertlocurl();
        //Moldova_Utils::DFL2();
        //echo Moldova_Utils::cleanUrl("țșăî"); die;
    }


}

