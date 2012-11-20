<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/21/11
 * Time: 3:45 PM
 */

class Administration_LoginController extends Zend_Controller_Action
{


    public function preDispatch()
    {
        $this->_helper->layout->setLayout('administrationlogin');
    }
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {

    }

    public function ajaxLoginAction()
    {
        $this->_helper->layout()->disableLayout();
		$data = $this->_request->getPost();
        $return = array();
        $adapter = new Moldova_Auth_Adapter_AdminLogin($data['adminname'], $data['password']);
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $session = new Zend_Session_Namespace('admins');
            $session->admin = $adapter->getResultArray('password');
            $session->role = Moldova_Auth_Roles::ADMIN;
            $return['message'] = "You logged in successfully.";
            $return['state'] = true;
        } else {
            $return['message'] = "You could not be logged in. Please try again.";
            $return['state'] = false;
        }

        print Zend_Json_Encoder::encode($return);
    }


}

