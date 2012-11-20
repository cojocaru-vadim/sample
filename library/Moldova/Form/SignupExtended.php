<?php
/**
 * Created by Moldova Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 2/20/11
 * Time: 11:56 PM
 */
 
class Moldova_Form_SignupExtended extends Moldova_Form_Signup{
    public function init()
  {
 // get parent form
    parent::init();

    // set form action (set to false for current URL)
    $this->setAction('/admin/people/edit');


    // create hidden input for item ID
    $id = new Zend_Form_Element_Hidden('acc_id');
    $id->addValidator('Int')
       ->addFilter('HtmlEntities')
       ->addFilter('StringTrim');

      $this->addElement($id);
  }


}

