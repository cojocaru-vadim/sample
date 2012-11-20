<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/21/11
 * Time: 3:45 PM
 */

class Admin_LoginController extends Zend_Controller_Action
{


    public function preDispatch()
    {
        $this->_helper->layout->setLayout('adminlogin');
    }
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $form = new Moldova_Form_AdminLogin();
        $this->view->form = $form;

        // check for valid input
        // authenticate using adapter
        // persist user record to session
        // redirect to original request URL if present
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                $adapter = new Moldova_Auth_Adapter_AdminLogin($values['adminname'], $values['password']);
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($adapter);
                if ($result->isValid()) {
                    $session = new Zend_Session_Namespace('admins');
                    $session->admin = $adapter->getResultArray('password');
                    $session->role = Moldova_Auth_Roles::ADMIN;
                    if (isset($session->requestURL)) {
                        $url = $session->requestURL;
                        unset($session->requestURL);
                        $this->_redirect($url);
                    } else {
                        $this->_helper->getHelper('FlashMessenger')->addMessage('You were successfully logged in.');
                        $this->_redirect('/admin/login/success');
                    }
                } else {
                    $this->view->message = 'You could not be logged in. Please try again.';
                }
            }
        }
    }

    public function successAction()
    {
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        } else {
            $this->_redirect('/admin');
        }
    }


}

