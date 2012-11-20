<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/14/11
 * Time: 1:40 AM
 */
 
class Admin_IndexController extends Zend_Controller_Action{

    public function preDispatch()
    {
        $url = $this->getRequest()->getRequestUri();
        if(Moldova_Utils::checkAdmin($url)){
            $this->_helper->layout()->disableLayout();
        } else {
            $this->_redirect('admin/login');
        }
    }


    public function init()
    {

    }

    public function indexAction()
    {
        $tree = array();
        $tree[] = array('text' => 'Companies',
                        'cls' => 'folder',
                        'singleClickExpand' => true,
                        'children' => array(
                            array(
                                'text' => 'Categories',
                                'leaf' => true,
                                'href' => 'admin/companies/category-page'
                                ),
                            array(
                                'text' => 'Subcategories',
                                'leaf' => true,
                                'href' => 'admin/companies/subcategory-page'
                                ),
                            array(
                                'text' => 'Companies',
                                'leaf' => true,
                                'href' => 'admin/companies/company-page'
                                )
                        ));
        $tree[] = array('text' => 'Accounts',
                        'cls' => 'folder',
                        'singleClickExpand' => true,
                        'children' => array(
                            array(
                                'text' => 'Accounts',
                                'leaf' => true,
                                'href' => 'admin/accounts/account-page'
                                ),
                        ));
        $tree[] = array('text' => 'General',
                        'cls' => 'folder',
                        'singleClickExpand' => true,
                        'children' => array(
                            array(
                                'text' => 'Emails PF',
                                'leaf' => true,
                                'href' => 'admin/general/emails-pf-page'
                                ),
                            array(
                                'text' => 'Emails PJ',
                                'leaf' => true,
                                'href' => 'admin/general/emails-pj-page'
                                ),
                            array(
                                'text' => 'Contact form',
                                'leaf' => true,
                                'href' => 'admin/general/contact-form-page'
                                ),
                        ));
        $this->view->menu = Zend_Json::encode($tree);
    }
}
