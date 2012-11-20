<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 29/11/11
 * Time: 11:40 AM
 */

class Administration_IndexController extends Zend_Controller_Action{

    public function preDispatch()
    {
        $this->_helper->layout->setLayout('administration');
        $url = $this->getRequest()->getRequestUri();
        if(Moldova_Utils::checkAdmin($url)){
            
            //$this->_helper->layout()->disableLayout();
        } else {
            $this->_redirect('administration/login');
        }
    }


    public function init()
    {

    }

    public function indexAction()
    {
      //dump($_SESSION); die;
    }
}
