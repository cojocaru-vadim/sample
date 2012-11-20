<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/13/11
 * Time: 12:47 PM
 */
 
class Companies_CategoriesController extends Zend_Controller_Action{

        public function preDispatch()
    {

    }


    public function init()
    { 

    }

    public function indexAction()
    {
        $req = $this->getRequest()->getParams();

        if($this->validateCategAndOrSubcateg($req)){
            $session = new Zend_Session_Namespace('accounts');
            $categ = $req['categ'];
            $subcateg = $req['sub_categ'];
            $page = $req['page'];
            $perPage = 10;
            $numPageLinks = 5;

            if($categ != 'n' && $subcateg != 'n'){

               $q = Doctrine_Query::create()
                   ->select("c.company_id, c.logo_img, c.company_url, c.name, sc.name_".$session->locale." AS subcat_name")
                   ->from('Moldova_Model_Companies c')
                   ->leftJoin('c.Moldova_Model_Accounts a')
                   ->leftJoin('c.Moldova_Model_CompaniesSubcategories sc')
                   ->where('c.is_deleted = ?', 0)
                   ->andWhere('c.status = ?', 1)
                   ->andWhere('sc.subcat_url = ?', $req['sub_categ'])
                   ->orderBy('c.creation_date DESC');




            //initilaze pager
            $pager = new Doctrine_Pager($q, $page, $perPage);

            //execute paged query
            $result = $pager->execute(array(), Doctrine::HYDRATE_ARRAY);

            //initialize pager layout
            $pagerRange = new Doctrine_Pager_Range_Sliding(array('chunk' => $numPageLinks), $pager);

            //$pagerUrlBase = $this->view->url(array('module' => 'admin' , 'controller' => 'people'), 'default') . "/{%page}";
            $pagerUrlBase = $this->view->url(array(), 'companies-categories', 1) . "/{$categ}/{$subcateg}/{%page}";

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
                    ->select("c.name_".$session->locale." AS cat_name, c.cat_url")
                    ->from('Moldova_Model_CompaniesCategories c')
                    ->where('c.cat_url = ?' , $req['categ']);
            $breadResult = $breadQuery->fetchArray();

            $breadQuery2 = Doctrine_Query::create()
                    ->select("sc.name_".$session->locale." AS subcat_name, sc.subcat_url")
                    ->from('Moldova_Model_CompaniesSubcategories sc')
                    ->where('sc.subcat_url = ?' , $req['sub_categ']);
            $breadResult2 = $breadQuery2->fetchArray();
            $this->view->subcat_name = $breadResult2[0]['subcat_name'];
            $this->view->breadcrumb = array("companies" => $this->view->translate('companies'), "categories" => $this->view->translate('categories'), $breadResult[0]['cat_url'] => $breadResult[0]['cat_name'], $breadResult2[0]['subcat_url'] => $breadResult2[0]['subcat_name']);


            }else if($categ != 'n'){
               $q = Doctrine_Query::create()
                                    ->select("sc.category_id, c.cat_url AS cat_url, c.name_".$session->locale." AS cat_name, sc.name_".$session->locale." AS subcat_name, sc.subcat_url AS subcat_url")
                                   //->select("sc.category_id, c.cat_url, c.name_".$session->locale." AS cat_name, sc.name_".$session->locale." AS subcat_name, CONCAT_WS('/', c.cat_url, sc.subcat_url) AS subcat_url")
                                   //->select("sc.category_id, c.name_".$session->locale." AS cat_name, sc.name_".$session->locale." AS subcat_name, sc.subcat_url")
                                   ->from('Moldova_Model_CompaniesSubcategories sc')
                                   ->leftJoin('sc.Moldova_Model_CompaniesCategories c')
                                   ->where('sc.is_deleted = ?', 0)
                                   ->andWhere('c.cat_url = ?', $req['categ']);

               $this->view->subcategories = $q->fetchArray();

               $q = Doctrine_Query::create()
                                   ->select("c.*")
                                   ->from('Moldova_Model_Companies c')
                                   ->leftJoin('c.Moldova_Model_Accounts a')
                                   ->leftJoin('c.Moldova_Model_CompaniesCategories cat')
                                   ->where('c.is_deleted = ?', 0)
                                   ->andWhere('c.status = ?', 1)
                                   ->andWhere('cat.cat_url = ?', $req['categ'])
                                   ->orderBy('c.creation_date DESC');


            //initilaze pager
            $pager = new Doctrine_Pager($q, $page, $perPage);

            //execute paged query
            $result = $pager->execute(array(), Doctrine::HYDRATE_ARRAY);

            //initialize pager layout
            $pagerRange = new Doctrine_Pager_Range_Sliding(array('chunk' => $numPageLinks), $pager);

            //$pagerUrlBase = $this->view->url(array('module' => 'admin' , 'controller' => 'people'), 'default') . "/{%page}";
            $pagerUrlBase = $this->view->url(array(), 'companies-categories', 1) . "/{$categ}/n/{%page}";

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
                    ->select("c.name_".$session->locale." AS cat_name, c.cat_url")
                    ->from('Moldova_Model_CompaniesCategories c')
                    ->where('c.cat_url = ?' , $req['categ']);
            $breadResult = $breadQuery->fetchArray();
            $this->view->breadcrumb = array("companies" => $this->view->translate('companies'), "categories" => $this->view->translate('categories'), $breadResult[0]['cat_url'] => $breadResult[0]['cat_name']);



            } else {
                $q = Doctrine_Query::create()
                     ->select('c.name_'.$session->locale.' AS cat_name, c.cat_url, c.category_id')
                     ->from('Moldova_Model_CompaniesCategories c')
                     ->where('c.is_deleted = ?', 0);
                $this->view->categories = $q->fetchArray();
                $this->view->breadcrumb = array("companies" => $this->view->translate('companies'), "categories" => $this->view->translate('categories'));

            }


        }else{
           throw new Zend_Controller_Action_Exception("Invalid input");
        }
    }

    /**
     * Validates category AND/OR subcategory which come from $_GET
     * @param  $req
     * @return bool
     */
    private function validateCategAndOrSubcateg($req){

        $valid = false;
        $categ = $req['categ'];
        $subcateg = $req['sub_categ'];

        if($categ != 'n' && $subcateg != 'n'){

            $filters = array(
                    'categ' => array('HtmlEntities', 'StripTags', 'StringTrim'),
                    'sub_categ'  => array('HtmlEntities', 'StripTags', 'StringTrim')
            );

            $q=Doctrine_Query::create()
                    ->select('c.cat_url')
                    ->from('Moldova_Model_CompaniesCategories c')
                    ->where('c.is_deleted = ?', 0);
            $urls = $q->execute();

            foreach($urls as $url) {
                $cat_haystack[] = $url->cat_url;
            }

            $q=Doctrine_Query::create()
                    ->select('sc.subcat_url')
                    ->from('Moldova_Model_CompaniesSubcategories sc')
                    ->where('sc.is_deleted = ?', 0);
            $urls = $q->execute();

            foreach($urls as $url) {
                $subcat_haystack[] = $url->subcat_url;
            }

            $validators = array(
                    'categ' => array(
                        array('InArray', 'haystack' => $cat_haystack)
                     ),
                    'sub_categ'  => array(
                        array('InArray', 'haystack' => $subcat_haystack)
                    )
                );

            $input = new Zend_Filter_Input($filters, $validators);
            $input->setData($req);

            if($input->isValid()){
                $valid = true;
            }

        }else if($categ != 'n'){
            $filters = array(
                    'categ' => array('HtmlEntities', 'StripTags', 'StringTrim'),
            );

            $q=Doctrine_Query::create()
                    ->select('c.cat_url')
                    ->from('Moldova_Model_CompaniesCategories c')
                    ->where('c.is_deleted = ?', 0);
            $urls = $q->execute();

            foreach($urls as $url) {
                $cat_haystack[] = $url->cat_url;
            }

            $validators = array(
                    'categ' => array(
                        array('InArray', 'haystack' => $cat_haystack)
                     )
                );

            $input = new Zend_Filter_Input($filters, $validators);
            $input->setData($req);

            if($input->isValid()){
                $valid = true;
            }
        }else if($subcateg != 'n'){
            $filters = array(
                    'sub_categ'  => array('HtmlEntities', 'StripTags', 'StringTrim')
            );

            $q=Doctrine_Query::create()
                    ->select('sc.subcat_url')
                    ->from('Moldova_Model_CompaniesSubcategories sc')
                    ->where('sc.is_deleted = ?', 0);
            $urls = $q->execute();

            foreach($urls as $url) {
                $subcat_haystack[] = $url->subcat_url;
            }

            $validators = array(
                    'sub_categ'  => array(
                        array('InArray', 'haystack' => $subcat_haystack)
                    )
                );
            $input = new Zend_Filter_Input($filters, $validators);
            $input->setData($req);

            if($input->isValid()){
                $valid = true;
            }
        }elseif($categ == 'n' && $subcateg == 'n'){
            $valid = true;
        }


        return $valid;
    }

}
