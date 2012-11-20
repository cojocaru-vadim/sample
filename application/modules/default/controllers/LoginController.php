<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/31/11
 * Time: 11:32 PM
 */
 
class LoginController extends Zend_Controller_Action{
    public function preDispatch()
    {

    }


    public function init()
    {

    }

    public function indexAction()
    {
        //Moldova_Utils::initiateMail();
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        }

        if ($this->getRequest()->isPost()) {
            $adapter = new Moldova_Auth_Adapter_AccLogin($_POST['email'], $_POST['password']);
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($adapter);
            if ($result->isValid()) {
                $session = new Zend_Session_Namespace('accounts');
                $session->account = $adapter->getResultArray('password');
                $session->role = Moldova_Auth_Roles::USER;
                $this->_helper->FlashMessenger->addMessage($this->view->translate('form-message-login-success'));
                $this->_redirect('/');
            } else {
                $this->_helper->FlashMessenger->addMessage($this->view->translate('form-message-login-error'));
                $this->_redirect('/login');
            }
        }

        $this->view->breadcrumb = array("login" => $this->view->translate('login'));
    }

    public function ajaxLoginAction()
    {
        $this->_helper->layout()->disableLayout();
        /*$form = new Moldova_Form_AdminLogin();
        $this->view->form = $form;*/
        $return = array();
        $adapter = new Moldova_Auth_Adapter_AccLogin($_POST['email'], $_POST['password']);
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $session = new Zend_Session_Namespace('accounts');
            $session->account = $adapter->getResultArray('password');
            $session->role = Moldova_Auth_Roles::USER;
            $return['success'] = 1;
            $return['message'] = $this->view->translate('login-successfully');
        } else {
            $return['success'] = 0;
            $return['message'] = $this->view->translate('login-unsuccessfully');
        }
        //echo Zend_Json::encode($return);
        echo '<script language="javascript" type="text/javascript">
                window.top.window.stopLogin('.$return['success'].', "'.$return['message'].'");
            </script>';

    }

}
