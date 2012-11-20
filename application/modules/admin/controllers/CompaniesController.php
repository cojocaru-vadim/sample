<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/14/11
 * Time: 1:40 AM
 */
 
class Admin_CompaniesController extends Zend_Controller_Action
{
    var $_request;
    var $perPage;

    public function preDispatch()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function init()
    {
        $this->_request = $this->getRequest();
        $this->perPage = 500;
    }

    public function indexAction()
    {
        
    }

    public function categoryPageAction()
    {
        $this->view->perPage = $this->perPage;
    }

    public function getCategoriesAction()
    {
        $req = $this->_request->getPost();

        $offset = (isset($req['start'])) ? $req['start'] : 0;
        $limit = (isset($req['limit'])) ? $req['limit'] : $this->perPage;
        //$sql = Doctrine::getTable('Moldova_Model_CompaniesCategories')->findAll();

        $q = Doctrine_Query::create()
             ->from('Moldova_Model_CompaniesCategories cat')
             ->where('cat.is_deleted = ?', 0)
             ->offset($offset)
             ->limit($limit);
        $result = $q->fetchArray();
        $q = Doctrine_Query::create()
             ->from('Moldova_Model_CompaniesCategories cat')
             ->where('cat.is_deleted = ?', 0);
        $count = $q->fetchArray();
        //Moldova_Utils::customPrint($result);
        $data = array();
        $data['items'] = $result;
        $data['total'] = count($count);
        print Zend_Json::encode($data);
    }

    public function addEditCategoryAction()
    {
        $data = array();
        $data['success'] = false;
        $data['message'] = 'Request is not post';
        if($this->_request->isPost()) 
        {
            $req = $this->_request->getPost();
            if($req['category_id'] == 0){
                //means we add new
                try{
                    $cat = new Moldova_Model_CompaniesCategories();
                    $cat->name_en_US = $req['name_en_US'];
                    $cat->name_ro_MD = $req['name_ro_MD'];
                    $cat->name_ru_RU = $req['name_ru_RU'];
                    $cat->cat_url = Moldova_Utils::cleanUrl($req['name_ro_MD']);
                    $cat->save();

                    $data['success'] = true;
                    $data['message'] = 'Category added';

                }catch(Exception $e){
                    $data['message'] = "Operation failed: " . $e->getMessage();
                }


            }else{
                //means we edit
                try{
                    $cat = Doctrine::getTable('Moldova_Model_CompaniesCategories')->find($req['category_id']);
                    $cat->name_en_US = $req['name_en_US'];
                    $cat->name_ro_MD = $req['name_ro_MD'];
                    $cat->name_ru_RU = $req['name_ru_RU'];
                    $cat->cat_url = Moldova_Utils::cleanUrl($req['name_ro_MD']);
                    $cat->save();

                    $data['success'] = true;
                    $data['message'] = 'Category saved';
                }catch(Exception $e){
                    $data['message'] = "Operation failed: " . $e->getMessage();
                }


            }
        }

        print Zend_Json::encode($data);
    }

    public function deleteCategoryAction()
    {
        $data = array();
        $data['success'] = false;
        $data['message'] = 'Request is not post';
        if($this->_request->isPost())
        {
            $req = $this->_request->getPost();
            
            $ids = explode(',', $req['ids']);
            $q = Doctrine_Query::create()
                ->update('Moldova_Model_CompaniesCategories cat')
                ->set('cat.is_deleted', '?', 1)
                ->whereIn('cat.category_id', $ids);
            if($q->execute()){
                $data['success'] = true;
                $data['message'] = 'Category deleted';
            }else{
                $data['message'] = 'Could not delete category';
            }
        }
        print Zend_Json::encode($data);
    }

    public function subcategoryPageAction()
    {
        $this->view->perPage = $this->perPage;
    }

    public function getSubcategoriesAction()
    {
        $req = $this->_request->getPost();

        $offset = (isset($req['start'])) ? $req['start'] : 0;
        $limit = (isset($req['limit'])) ? $req['limit'] : $this->perPage;

        $q = Doctrine_Query::create()
                            ->select('sc.category_id, sc.name_en_US, sc.name_ro_MD, sc.name_ru_RU, c.name_en_US AS cat_name')
                            ->from('Moldova_Model_CompaniesSubcategories sc')
                            ->leftJoin('sc.Moldova_Model_CompaniesCategories c')
                            ->where('sc.is_deleted = ?', 0)
                            ->offset($offset)
                            ->limit($limit);
        $result = $q->fetchArray();
        $q = Doctrine_Query::create()
                            ->select('sc.category_id, sc.name_en_US, sc.name_ro_MD, sc.name_ru_RU, c.name_en_US AS cat_name')
                            ->from('Moldova_Model_CompaniesSubcategories sc')
                            ->leftJoin('sc.Moldova_Model_CompaniesCategories c')
                            ->where('sc.is_deleted = ?', 0);
        $count = $q->fetchArray();

        $data = array();
        $data['items'] = $result;
        $data['total'] = count($count);
        print Zend_Json::encode($data);
    }

    public function getCategoriesForSubcatAction()
    {
        $q = Doctrine_Query::create()
                            ->select('cat.name_en_US AS cat_name, cat.category_id')
                            ->from('Moldova_Model_CompaniesCategories cat')->where('cat.is_deleted = ?', 0);
        $result = $q->fetchArray();

        print Zend_Json::encode($result);
    }

    public function addEditSubcategoryAction()
    {
        $data = array();
        $data['success'] = false;
        $data['message'] = 'Request is not post';
        if($this->_request->isPost())
        {
            $req = $this->_request->getPost();
            if($req['subcategory_id'] == 0){
                //means we add new
                try{
                    $cat = new Moldova_Model_CompaniesSubcategories();
                    $cat->name_en_US = $req['name_en_US'];
                    $cat->name_ro_MD = $req['name_ro_MD'];
                    $cat->name_ru_RU = $req['name_ru_RU'];
                    $cat->subcat_url = Moldova_Utils::cleanUrl($req['name_ro_MD']);
                    $cat->category_id = $req['category_id'];
                    $cat->save();

                    $data['success'] = true;
                    $data['message'] = 'Subcategory added';

                }catch(Exception $e){
                    $data['message'] = "Operation failed: " . $e->getMessage();
                }


            }else{
                //means we edit
                try{
                    $cat = Doctrine::getTable('Moldova_Model_CompaniesSubcategories')->find($req['subcategory_id']);
                    $cat->name_en_US = $req['name_en_US'];
                    $cat->name_ro_MD = $req['name_ro_MD'];
                    $cat->name_ru_RU = $req['name_ru_RU'];
                    //iconv('UTF-8', 'ASCII//TRANSLIT', $str);
                    //print Zend_Json::encode(Moldova_Utils::cleanUrl($req['name_ro_MD'])); die;
                    $cat->subcat_url = Moldova_Utils::cleanUrl($req['name_ro_MD']);
                    $cat->category_id = $req['category_id'];
                    $cat->save();

                    $data['success'] = true;
                    $data['message'] = 'Subcategory saved';
                }catch(Exception $e){
                    $data['message'] = "Operation failed: " . $e->getMessage();
                }


            }
        }
        print Zend_Json::encode($data);
    }

    public function deleteSubcategoryAction()
    {
        $data = array();
        $data['success'] = false;
        $data['message'] = 'Request is not post';
        if($this->_request->isPost())
        {
            $req = $this->_request->getPost();
            
            $ids = explode(',', $req['ids']);
            $q = Doctrine_Query::create()
                ->update('Moldova_Model_CompaniesSubcategories subcat')
                ->set('subcat.is_deleted', '?', 1)
                ->whereIn('subcat.subcategory_id', $ids);
            if($q->execute()){
                $data['success'] = true;
                $data['message'] = 'Subcategory deleted';
            }else{
                $data['message'] = 'Could not delete subcategory';
            }
        }
        print Zend_Json::encode($data);
    }

    public function companyPageAction()
    {
        $this->view->perPage = $this->perPage;
    }

    public function getSubcatForComboAction()
    {
        $req = $this->_request->getPost();

        $q = Doctrine_Query::create()
                            ->select('subcat.name_en_US AS subcat_name, subcat.subcategory_id')
                            ->from('Moldova_Model_CompaniesSubcategories subcat')
                            ->where('subcat.is_deleted = ?', 0)
                            ->andWhere('subcat.category_id = ?', $req['cat_id']);
        $result = $q->fetchArray();

        print Zend_Json::encode($result);
    }

    public function getCompanyStatusesAction()
    {
        print Zend_Json::encode(Moldova_Utils::$companies_status);
    }

    public function companyListAction()
    {
        $req = $this->_request->getPost();

        $offset = (isset($req['start'])) ? $req['start'] : 0;
        $limit = (isset($req['limit'])) ? $req['limit'] : $this->perPage;

        $q = Doctrine_Query::create()
                            ->select('c.*, c.email AS company_email, a.email AS account_email, r.region_ro_MD AS region_name, cat.name_ro_MD AS cat_name')
                            ->from('Moldova_Model_Companies c')
                            ->leftJoin('c.Moldova_Model_Accounts a')
                            ->leftJoin('c.Moldova_Model_Regions r')
                            ->leftJoin('c.Moldova_Model_CompaniesCategories cat')
                            ->where('c.is_deleted = ?', 0);

        if($req['cat_id'] != 0){
            $q->andWhere('c.category_id = ?', $req['cat_id']);
        }
        if($req['subcat_id'] != 0){
            $q->andWhere('c.subcategory_id = ?', $req['subcat_id']);
        }
        if(isset($req['status'])){
            $q->andWhere('c.status = ?', $req['status']);
        }
        $q->limit($limit)
          ->offset($offset);

        $result = $q->fetchArray();

        $data = array();
        $data['items'] = $result;
        $data['total'] = count($result);
        print Zend_Json::encode($data);
    }

    public function companyViewPageAction()
    {
        $company_id = $this->_request->getParam("company_id");
        $this->view->company_id = $company_id;
        $q = Doctrine_Query::create()
                    ->select("c.*, sc.name_ro_MD AS subcat, r.region_ro_MD AS region, l.locality_ro_MD AS locality")
                    ->from('Moldova_Model_Companies c')
                    ->leftJoin('c.Moldova_Model_CompaniesSubcategories sc')
                    ->leftJoin('c.Moldova_Model_Regions r')
                    ->leftJoin('c.Moldova_Model_Localities l')
                    ->where('c.company_id = ?', $company_id);

        $result = $q->fetchArray();
        $result[0]['logo_img'] =  "uploads/companies/{$result[0]['company_id']}/{$result[0]['logo_img']}";
        $this->view->company = $result;
    }

    public function publishAction()
    {
        $data = array();
        $data['success'] = false;
        $data['message'] = 'Request is not post';
        if($this->_request->isPost())
        {
            $req = $this->_request->getPost();
            $ids = explode(',', $req['ids']);
            try{
                foreach($ids as $id){
                    $company = Doctrine::getTable('Moldova_Model_Companies')->find($id);
                    $company->status = 1;
                    $company->save();
                }
                $data['success'] = true;
                $data['message'] = 'Company published';
            }catch(Exception $e){
                $data['message'] = 'Could not publish company';
            }
        }
        print Zend_Json::encode($data);
    }
}

?>