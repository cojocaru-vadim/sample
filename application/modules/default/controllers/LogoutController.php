<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 5/20/11
 * Time: 12:41 PM
 */
 
class LogoutController extends Zend_Controller_Action{
    public function init()
    {

    }

    public function indexAction()
    {
        $session = new Zend_Session_Namespace('accounts');
        unset($session->role);
        //print_r($_SESSION); die;
        $this->_redirect('/');
    }
}
