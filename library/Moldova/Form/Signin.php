<?php
class Moldova_Form_Signin extends Zend_Form
{
    public function init()
    {
        // initialize form
        $this->setAction('/signin')
        ->setMethod('post');

        // create text input for email address
        // should contain a valid email address
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email address:')
        ->setOptions(array('size' => '16'))
        ->setRequired(true)
        ->addValidator(new Zend_Validate_EmailAddress());

        // create password input
        $pass = new Zend_Form_Element_Password('password');
        $pass->setLabel('Password:')
        ->setOptions(array('size' => '16'))
        ->setRequired(true);


        // create submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Sign in');
        // attach elements to form
        $this->addElement($email)
        ->addElement($pass)
        ->addElement($submit);
    }


}
