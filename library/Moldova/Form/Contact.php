<?php
class Moldova_Form_Contact extends Zend_Form
{
    public function init()
    {
        // initialize form
        $this->setAction('/contact')
        ->setMethod('post');

        // create text input for name
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name:')
        //->setOptions(array('size' => '35'))
        ->setRequired(true)
        ->addValidator('NotEmpty', true)
        ->addValidator('Alpha', true)
        ->addFilter(new Zend_Filter_HtmlEntities())
        ->addFilter('StringTrim');

        // create text input for email address
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email:')
              //->setOptions(array('size' => '50'))
        ->setRequired(true)
        ->addValidator('NotEmpty', true)
        ->addValidator('EmailAddress', true)
        ->addFilter(new Zend_Filter_HtmlEntities())
        ->addFilter('StringToLower')
        ->addFilter('StringTrim');

        // create text input for message body
        $message = new Zend_Form_Element_Textarea('message');
        $message->setLabel('Message:')
        ->setOptions(array('rows' => '8','cols' => '27'))
        ->setRequired(true)
        ->addValidator('NotEmpty', true)
        ->addFilter(new Zend_Filter_HtmlEntities())
        ->addFilter('StringTrim');






        // create captcha
        $captcha = new Zend_Form_Element_Captcha('captcha', array(
                                                                'captcha' => array(
                                                                    'captcha' => 'Image',
                                                                    'wordLen' => 6,
                                                                    'timeout' => 300,
                                                                    'width' => 150,
                                                                    'height' => 50,
                                                                    'imgUrl' => '/captcha',
                                                                    'imgDir' => APPLICATION_PATH . '/../public/captcha',
                                                                    'font' =>     APPLICATION_PATH .'/../public/fonts/LiberationSansRegular.ttf',
                                                                )
                                                            )
        );
        $captcha->setLabel('Verification code:');

        // create submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Send Message')
        ->setOptions(array('class' => 'greenButton'));

        // attach elements to form
        $this->addElement($name)
        ->addElement($email)
        ->addElement($message)
        //->addElement($captcha)
        ->addElement($submit);


        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class'=> 'input-container')),
            array(array('data' => 'HtmlTag'),  array('tag' =>'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));


         $submit->setDecorators(array(
            'ViewHelper',
            array(array('data' => 'HtmlTag'),  array('tag' =>'td')),
            array(array('emptyrow' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element', 'placement' => 'PREPEND')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
         ));

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));
    }
}
