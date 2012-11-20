<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 5/12/11
 * Time: 4:10 PM
 */
 
class ContactController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {


        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        }

        if ($this->getRequest()->isPost()) {

            if($this->validateContactForm($this->getRequest()->getPost())){

                $req = $this->getRequest()->getPost();
                $contact = new Moldova_Model_Contact();
                $contact->name = $req['name'];
                $contact->message = $req['message'];
                $contact->email = $req['email'];

                $contact->save();
                $this->_redirect('/contact/success');
            } else {
                $this->_redirect('/contact');
            }
            //$req = $this->getRequest()->getPost();
            //dump($req);
            //dump($_FILES);
        }
        $this->view->breadcrumb = array("contact" => $this->view->translate('contact-us'));
    }

    public function successAction()
    {
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        } else {
            $this->_redirect('/');
        }
    }    

    private function validateContactForm($post){
        $return = false;
        $validators = array(
            'name'  => array('NotEmpty',
                          //  'messages' => array('A valid name is required')
            ),
            'message'  => array('NotEmpty',
                          //  'messages' => array('A valid name is required')
            ),
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
        //$input = new Zend_Filter_Input(null, $validators);
        $input->setData($post);
        $input->setOptions($options);

        if ($input->isValid())
        {
            $this->_helper->FlashMessenger->addMessage($this->view->translate('contact-us-success'));
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
