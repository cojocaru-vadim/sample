<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/18/11
 * Time: 1:31 AM
 */
 
class Companies_UtilsController extends Zend_Controller_Action{

    var $db;
    var $_request;

    public function preDispatch()
    {
        $this->_helper->layout()->disableLayout();
    }


    public function init()
    {
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->_request = $this->getRequest();
    }

    public function indexAction(){
        
    }

    public function getLocalitiesAction()
    {
                    //Aici trebuie de trimis region_id

        
        $session = new Zend_Session_Namespace('accounts');
                    $req = $this->_request->getPost();
                    $q = Doctrine_Query::create()
                            ->select('l.locality_'.$session->locale.' AS name, l.locality_id')
                            ->from('Moldova_Model_Localities l')
                            ->where('l.region_id = ?', $req['region_id']);
                    print Zend_Json::encode($q->fetchArray());

/*        $toOut = array(
                array('locality_id' => 0, 'name' => 'Select Locality'),
                array('locality_id' => 1, 'name' => 'Кишинёв'),
                array('locality_id' => 2, 'name' => 'Бэлць'),
                array('locality_id' => 3, 'name' => 'Бричень'),
                array('locality_id' => 4, 'name' => 'Глодень'),
                array('locality_id' => 5, 'name' => 'Дондушень'),
                array('locality_id' => 6, 'name' => 'Дрокия'),
                array('locality_id' => 7, 'name' => 'Единец'),
                array('locality_id' => 8, 'name' => 'Кантемир'),
                array('locality_id' => 9, 'name' => 'Кэушень'),
                array('locality_id' => 10, 'name' => 'Криулень'),
                array('locality_id' => 11, 'name' => 'Кэинарь'),
                array('locality_id' => 12, 'name' => 'Кэлэрашь'),
                array('locality_id' => 13, 'name' => 'Леова'),
                array('locality_id' => 14, 'name' => 'Ниспорень'),
                array('locality_id' => 15, 'name' => 'Новые Анены'),
                array('locality_id' => 16, 'name' => 'Окница'),
                array('locality_id' => 17, 'name' => 'Орхей'),
                array('locality_id' => 18, 'name' => 'Резина'),
                array('locality_id' => 19, 'name' => 'Рышкань'),
                array('locality_id' => 20, 'name' => 'Сорока'),
                array('locality_id' => 21, 'name' => 'Стрэшень'),
                array('locality_id' => 22, 'name' => 'Сынжерей'),
                array('locality_id' => 23, 'name' => 'Теленешть'),
                array('locality_id' => 24, 'name' => 'Унгень'),
                array('locality_id' => 25, 'name' => 'Фэлешть'),
                array('locality_id' => 26, 'name' => 'Флорешть'),
                array('locality_id' => 27, 'name' => 'Чимишлия'),
                array('locality_id' => 28, 'name' => 'Шолдэнешть'),
                array('locality_id' => 29, 'name' => 'Штефан Водэ'),
                array('locality_id' => 30, 'name' => 'Яловень'),
                array('locality_id' => 31, 'name' => 'Басарабяска'),
                array('locality_id' => 32, 'name' => 'Бендер'),
                array('locality_id' => 33, 'name' => 'Григориопол'),
                array('locality_id' => 34, 'name' => 'Дубэсарь'),
                array('locality_id' => 35, 'name' => 'Каменка'),
                array('locality_id' => 36, 'name' => 'Кахул'),
                array('locality_id' => 37, 'name' => 'Комрат'),
                array('locality_id' => 38, 'name' => 'Рыбница'),
                array('locality_id' => 39, 'name' => 'Слобозия'),
                array('locality_id' => 40, 'name' => 'Тираспол'),
                array('locality_id' => 41, 'name' => 'Хынчешть'),
                array('locality_id' => 42, 'name' => 'Вулкэнешть'),
                array('locality_id' => 43, 'name' => 'Тараклия'),
                array('locality_id' => 44, 'name' => 'Чадыр-Лунга'),
                array('locality_id' => 55, 'name' => 'Бендеры'),
                array('locality_id' => 56, 'name' => 'Григориополь'),
                array('locality_id' => 57, 'name' => 'Дубоссары'),
                array('locality_id' => 58, 'name' => 'Слободзея'),
                array('locality_id' => 59, 'name' => 'Днестровск'),
                array('locality_id' => 60, 'name' => 'Рыбница'),
                array('locality_id' => 61, 'name' => 'Каменка')
               );
        print Zend_Json::encode($toOut);*/
    }

    public function getRegionsAction()
    {
        // post: locality_id

        $session = new Zend_Session_Namespace('accounts');

           $q = Doctrine_Query::create()
                            ->select('r.region_'.$session->locale.' AS name, r.region_id')
                            ->from('Moldova_Model_Regions r');
            print Zend_Json::encode($q->fetchArray());
/*
        $toOut = array(
                array('region_id' => 0, 'name' => 'Select Region'),
                array('region_id' => 2, 'name' => 'Бэчой'),
                array('region_id' => 3, 'name' => 'Бык'),
                array('region_id' => 4, 'name' => 'Брэила'),
                array('region_id' => 5, 'name' => 'Бубуечь'),
                array('region_id' => 6, 'name' => 'Будешть'),
                array('region_id' => 7, 'name' => 'Бунець'),
                array('region_id' => 8, 'name' => 'Чероборта'),
                array('region_id' => 9, 'name' => 'Келтуиторь'),
                array('region_id' => 10, 'name' => 'Чореску'),
                array('region_id' => 11, 'name' => 'Кодру'),
                array('region_id' => 12, 'name' => 'Колоница'),
                array('region_id' => 13, 'name' => 'Кодрица'),
                array('region_id' => 14, 'name' => 'Крикова'),
                array('region_id' => 15, 'name' => 'Крузешть'),
                array('region_id' => 16, 'name' => 'Доброжа'),
                array('region_id' => 17, 'name' => 'Думбрава'),
                array('region_id' => 18, 'name' => 'Дурлешть'),
                array('region_id' => 19, 'name' => 'Фэурешть'),
                array('region_id' => 20, 'name' => 'Фрумушика'),
                array('region_id' => 1162, 'name' => 'Гидигичь'),
                array('region_id' => 1163, 'name' => 'Гоян'),
                array('region_id' => 1164, 'name' => 'Гоянул Ноу'),
                array('region_id' => 1165, 'name' => 'Грэтиешть'),
                array('region_id' => 1166, 'name' => 'Хулбоака'),
                array('region_id' => 1167, 'name' => 'Хумулешть'),
                array('region_id' => 1168, 'name' => 'Ревака'),
                array('region_id' => 1169, 'name' => 'Стэучень'),
                array('region_id' => 1170, 'name' => 'Стрэистень'),
                array('region_id' => 1171, 'name' => 'Сынжера'),
                array('region_id' => 1172, 'name' => 'Тогатин'),
                array('region_id' => 1173, 'name' => 'Трушень'),
                array('region_id' => 1175, 'name' => 'Вадул луй Водэ'),
                array('region_id' => 1176, 'name' => 'Ватра'),
                array('region_id' => 1177, 'name' => 'Вэдулень')
        );
        print Zend_Json::encode($toOut);*/
    }

    public function getCategoriesAction()
    {

        $session = new Zend_Session_Namespace('accounts');
        $q = Doctrine_Query::create()
                             ->select('c.name_'.$session->locale.' AS name, c.category_id')
                             ->from('Moldova_Model_CompaniesCategories c')
                             ->where('c.is_deleted = ?', 0);
        print Zend_Json::encode($q->fetchArray());

/*
        $sql = "SELECT category_id, name_en_US AS name FROM companies_categories WHERE is_deleted = 0"; //deja o sa faci normal cu traduceri
        $result = $this->db->query($sql)->fetchAll();
        print Zend_Json::encode($result);*/
        
    }

    public function getSubcategoriesAction()
    {
        $req = $this->_request->getPost();
        $session = new Zend_Session_Namespace('accounts');

        $q = Doctrine_Query::create()
                            ->select("sc.subcategory_id, sc.name_".$session->locale." AS name")
                           ->from('Moldova_Model_CompaniesSubcategories sc')
                           ->where('sc.is_deleted = ?', 0)
                           ->andWhere('sc.category_id = ?', $req['category_id']);

        print Zend_Json::encode($q->fetchArray());
/*
        $sql = "SELECT subcategory_id, name_en_US AS name FROM companies_subcategories WHERE is_deleted = 0 AND category_id = '".$req['category_id']."'"; //deja o sa faci normal cu traduceri
        $result = $this->db->query($sql)->fetchAll();
        print Zend_Json::encode($result);*/
    }
}
