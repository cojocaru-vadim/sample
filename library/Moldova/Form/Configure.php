<?php
/**
 * Created by Moldova Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 2/24/11
 * Time: 3:11 PM
 */
 
class Moldova_Form_Configure extends Zend_Form
{
  public function init()
  {
    // initialize form
    $this->setAction('/admin/config')
         ->setMethod('post');

    // create text input for default email
    $default = new Zend_Form_Element_Text('defaultEmailAddress');
    $default->setLabel('Fallback email address for all operations:')
            ->setOptions(array('size' => '40'))
            ->setRequired(true)
            ->addValidator('EmailAddress')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

    // create text input for admin email
    $sales = new Zend_Form_Element_Text('adminEmailAddress');
    $sales->setLabel('Default email address for admin enquiries:')
          ->setOptions(array('size' => '40'))
          ->addValidator('EmailAddress')
          ->addFilter('HtmlEntities')
          ->addFilter('StringTrim');

    // create text input for domain name
    $domain = new Zend_Form_Element_Text('domainName');
    $domain->setLabel('Domain name of the main website:')
          ->setOptions(array('size' => '40'))
          ->addFilter('HtmlEntities')
          ->addFilter('StringTrim');

    // create text input for number of items per page in admin summary
    $items = new Zend_Form_Element_Text('itemsPerPage');
    $items->setLabel('Number of items per page in administrative views:')
          ->setOptions(array('size' => '4'))
          ->setRequired(true)
          ->addValidator('Int')
          ->addFilter('HtmlEntities')
          ->addFilter('StringTrim');

    // create radio button for display of seller name and address
    $seller = new Zend_Form_Element_Radio('displaySellerInfo');
    $seller->setLabel('Seller name and address visible in public catalog:')
           ->setRequired(true)
           ->setMultiOptions(array(
            '1'    => 'Yes',
            '0'    => 'No'
           ));


    // create radio button for exception logging
    $log = new Zend_Form_Element_Radio('logExceptionsToFile');
    $log->setLabel('Exceptions logged to file:')
        ->setRequired(true)
        ->setMultiOptions(array(
            '1'    => 'Yes',
            '0'    => 'No'
           ));

    // create submit button
    $submit = new Zend_Form_Element_Submit('submit');
    $submit->setLabel('Save configuration')
           ->setOptions(array('class' => 'submit'));

    // attach elements to form
    $this->addElement($sales)
         ->addElement($default)
         ->addElement($domain)
         ->addElement($items)
         ->addElement($seller)
         ->addElement($log)
         ->addElement($submit);
  }
}
