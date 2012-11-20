<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 12/6/11
 * Time: 10:23 AM
 */
 
class Administration_LogoutController extends Zend_Controller_Action{


    public function preDispatch()
    {
        $this->_helper->layout->disableLayout();
    }
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
          //print "logout message";
		  $session = new Zend_Session_Namespace('admins');
		  $session->role = Moldova_Auth_Roles::GUEST;
		  $this->_redirect('/administration');
    }
}
