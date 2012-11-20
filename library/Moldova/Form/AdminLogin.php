<?php
/**
 * Created by Moldova Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 2/21/11
 * Time: 10:06 AM
 */
 
class Moldova_Form_AdminLogin extends Zend_Form{
    public function init()
    {
    // initialize form
    $this->setAction('/admin/login')
    ->setMethod('post');
    // create text input for name
    $adminname = new Zend_Form_Element_Text('adminname');
    $adminname->setLabel('Admin name:')
            ->setOptions(array('size' => '30'))
            ->setRequired(true)
            ->addValidator('Alnum')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');
    // create text input for password
    $password = new Zend_Form_Element_Password('password');
    $password->setLabel('Password:')
            ->setOptions(array('size' => '30'))
            ->setRequired(true)
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');
    // create submit button
    $submit = new Zend_Form_Element_Submit('submit');
    $submit->setLabel('Log In')
            ->setOptions(array('class' => 'submit'));
            // attach elements to form
    $this->addElement($adminname)
            ->addElement($password)
            ->addElement($submit);
            }

}
