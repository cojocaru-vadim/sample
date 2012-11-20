<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/31/11
 * Time: 11:34 PM
 */
 
class SignupController extends Zend_Controller_Action{

    public function preDispatch()
    {

    }

    public function init()
    {

    }

    public function indexAction()
    {
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        }

        if ($this->getRequest()->isPost()) {
            if($this->validateSignUpForm($this->getRequest()->getPost())){

               // print_r($req->getUnknown()); die;
                //print_r($company_id); die ;
                $req = $this->getRequest()->getPost();
                $q = Doctrine_Query::create()
                        ->select('a.email')
                        ->from('Moldova_Model_Accounts a')
                        ->where('a.email = ?' , $req['email']);
                if(count($q->fetchArray()) > 0){
                    $this->_helper->FlashMessenger->addMessage($this->view->translate('form-message-signup-account-exists'));
                    $this->_redirect('/signup');
                } else {
                    $acc = new Moldova_Model_Accounts();
                    $acc->email = $req['email'];
                    $acc->password = Moldova_Utils::encryptpass($req['password']);
                    $acc->creation_date = date('Y-m-d H:i:s', mktime());
                    $acc->is_deleted = 0;
                    $acc->save();
                    $this->_helper->FlashMessenger->addMessage($this->view->translate('form-message-signup-success'));
                    $this->_redirect('/signup/success');
                }

            } else {
                $this->_redirect('/signup');
            }
            //$req = $this->getRequest()->getPost();
            //dump($req);
            //dump($_FILES);
        }
        $this->view->breadcrumb = array("signup" => $this->view->translate('signup'));

        //print_r($req);
    }

    public function successAction()
    {
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        } else {
            $this->_redirect('/');
        }
    }

    private function validateSignUpForm($post){
        $return = false;
        $validators = array(
/*            'password' => array(
                'StringEquals',
                'fields' => array($post['password'], $post['repassword'])
            ),*/
            'email'=> array(
                new Zend_Validate_Regex("/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/"),
                'messages' => array('A valid email is required',
                    Zend_Validate_Regex::NOT_MATCH=>'The email is not valid',)
            ) ,
        );

        $filters = array(
            '*' => array('HtmlEntities', 'StripTags', 'StringTrim'),
        );

        $options = array(
            'notEmptyMessage' => "'%field%' " . $this->view->translate('form-message-field-notempty'),
            //'missingMessage' => "Field '%field%' is required"
        );

        // Now we chain the validators, the filters and pass the post params on
        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($post);
        $input->setOptions($options);

        if ($input->isValid())
        {
            $return = true;
        } else {
            $messages = $input->getMessages();
            foreach ($messages as $key => $value)
            {
                //print_r($value); die;
                // Store all messages in FlashMessenger
                foreach ($value as $msg)
                {
                    $this->_helper->FlashMessenger->addMessage($msg);
                }
            }
        }
        return $return;
    }
}
