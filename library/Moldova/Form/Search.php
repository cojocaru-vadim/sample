<?php
/**
 * Created by Moldova Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 2/21/11
 * Time: 2:12 PM
 */
 
class Moldova_Form_Search extends Zend_Form{
    public $messages = array(
        Zend_Validate_Int::INVALID => '\'%value%\' is not an $girlCheckboxinteger',
        Zend_Validate_Int::NOT_INT => '\'%value%\' is not an integer'
    );

    public $searchOptions = array(
        1 => "Guy",
        2 => "Girl",
        3 => "Online",
        4 => "With photo"
    );


    public function init()
    {
        // initialize form
        $this->setAction('/search')
                ->setMethod('post')
                ->setAttrib('id', 'search_form');
/*
    $girlCheckbox_tmp = $this->createElement('checkbox', 'terms', array(
          'label'=>'Terms and Services',
          'uncheckedValue'=> '',
          'checkedValue' => 'I Agree',
          'validators' => array(
            // array($validator, $breakOnChainFailure, $options)
            array('notEmpty', true, array(
              'messages' => array(
                'isEmpty'=>'You must agree to the terms'
              )
            ))
           ),
           'required'=>true,
        ));
*/

    $girlCheckbox = $this->createElement('checkbox', 'girl_checkbox', array(
          'label'=>'Girls:',
          'uncheckedValue'=> '0',
          'checkedValue' => '2'
        ));

    $guyCheckbox = $this->createElement('checkbox', 'guy_checkbox', array(
          'label'=>'Guys:',
          'uncheckedValue'=> '0',
          'checkedValue' => '1'
        ));

    $onlineCheckbox = $this->createElement('checkbox', 'online_checkbox', array(
          'label'=>'Online:',
          'uncheckedValue'=> '0',
          'checkedValue' => '1'
        ));

    $withPhotoCheckbox = $this->createElement('checkbox', 'with_photo_checkbox', array(
          'label'=>'With photo:',
          'uncheckedValue'=> '0',
          'checkedValue' => '1'
        ));
/*
    $girlCheckbox = new Zend_Form_Element_Checkbox('girl_checkbox');
    $girlCheckbox->setCheckedValue(true) //whatever value you want to send when checked, could be a string
                 ->setValue(false) //the current value of the checkbox
                 ->setLabel('Girl:')
                 ->addValidator('Int');

    $guyCheckbox = new Zend_Form_Element_Checkbox('guy_checkbox');
    $guyCheckbox->setLabel('Guy:')->addValidator('Int');

    $onlineCheckbox = new Zend_Form_Element_Checkbox('online_checkbox');
    $onlineCheckbox->setLabel('Online:')->addValidator('Int');

    $withPhotoCheckbox = new Zend_Form_Element_Checkbox('with_photo_checkbox');
    $withPhotoCheckbox->setLabel('With photo:')->addValidator('Int');
*/

       // create select inputs for item display date
    $fromAge = new Zend_Form_Element_Select('from_age');
    $fromAge->setLabel('From:')->addValidator('Int');

    for($x=18; $x<=100; $x++) {
      $fromAge->addMultiOption($x, $x);
    }

    $toAge = new Zend_Form_Element_Select('to_age');
    $toAge->setLabel('To:')->addValidator('Int');

    for($x=18; $x<=100; $x++) {
      $toAge->addMultiOption($x, $x);
    }

    // create search button
    $search = new Zend_Form_Element_Submit('search');
    $search->setLabel('Search')
           ->setOrder(100)
           ->setOptions(array('class' => 'greenButton'));


        $this->addElement($girlCheckbox);
        $this->addElement($guyCheckbox);
        $this->addElement($onlineCheckbox);
        $this->addElement($withPhotoCheckbox);
        $this->addElement($fromAge)->addElement($toAge)->addElement($search);


/*
        // create text input for year
        $year = new Zend_Form_Element_Text('y');
        $year->setLabel('Year:')
                ->setOptions(array('size' => '6'))
                ->addValidator('Int', false,  array('messages' => $this->messages))
                ->addFilter('HtmlEntities')
                ->addFilter('StringTrim');

        // create text input for price
        $price = new Zend_Form_Element_Text('p');
        $price->setLabel('Price:')
            ->setOptions(array('size' => '8'))
            ->addValidator('Int', false, array('messages' => $this->messages))
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');

        // create select input for grade
        $grade = new Zend_Form_Element_Select('g');
        $grade->setLabel('Grade:')
            ->addValidator('Int', false, array('messages' => $this->messages))
           ->addFilter('HtmlEntities')
           ->addFilter('StringTrim')
           ->addMultiOption('', 'Any');
       foreach ($this->getGrades() as $g) {
            $grade->addMultiOption($g['GradeID'], $g['GradeName']);
       };

       // create submit button
       $submit = new Zend_Form_Element_Submit('submit');
       $submit->setLabel('Search')
            ->setOptions(array('class' => 'submit'));

       // attach elements to form
       $this->addElement($year)
           ->addElement($price)
           ->addElement($grade)
           ->addElement($submit);

       // set element decorators
       $this->setElementDecorators(array(
           array('ViewHelper'),
           array('Label', array('tag' => '<span>'))
       ));

       $submit->setDecorators(array(
            array('ViewHelper'),
       ));


        // set form decorators
        $this->setDecorators(array(
            array('FormErrors', array('markupListItemStart' => '', 'markupListItemEnd' => '')),
            array('FormElements'),
            array('Form')
        ));
*/

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
             //array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class'=> 'input-container')),
            //array(array('data' => 'HtmlTag'),  array('tag' =>'td')),
            array('Label'),
            //array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));



         $search->setDecorators(array(
            'ViewHelper',
            array(array('data' => 'HtmlTag'),  array('tag' =>'td')),
           // array(array('emptyrow' => 'HtmlTag'),  array('tag' =>'td', 'class'=> 'element', 'placement' => 'PREPEND')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
         ));



        $this->addElement($girlCheckbox);
        $this->addElement($guyCheckbox);
        $this->addElement($onlineCheckbox);
        $this->addElement($withPhotoCheckbox);

        $girlCheckbox->setDecorators(array(
                        array('ViewHelper'),
                        array('Label'),
                        array(array('data' => 'HtmlTag'),  array('tag' =>'td' , 'openOnly' => true)),
                        array('HtmlTag',
                          array(
                            'tag' => 'tr',
                            'openOnly' => true,
                            'placement' => 'prepend'
                          )
                        ),
                      ));

        $withPhotoCheckbox->setDecorators(array(
                          array('ViewHelper'),
                          array(array('data' => 'HtmlTag'),  array('tag' =>'td' , 'closeOnly' => true)),
                          array('Label'),
                          array('HtmlTag',
                            array(
                              'tag' => 'tr',
                              'closeOnly' => true,
                              'placement' => 'append'
                            )
                          ),
                       ));

      $fromAge->setDecorators(array(
                        array('ViewHelper'),
                        array('Label'),
                        array(array('data' => 'HtmlTag'),  array('tag' =>'td' , 'openOnly' => true)),
                        array('HtmlTag',
                          array(
                            'tag' => 'tr',
                            'openOnly' => true,
                            'placement' => 'prepend'
                          )
                        ),
                      ));


      $toAge->setDecorators(array(
                          array('ViewHelper'),
                          array(array('data' => 'HtmlTag'),  array('tag' =>'td' , 'closeOnly' => true)),
                          array('Label'),
                          array('HtmlTag',
                            array(
                              'tag' => 'tr',
                              'closeOnly' => true,
                              'placement' => 'append'
                            )
                          ),
                       ));


/*
      $girlCheckbox->setDecorators(array(
                        array('ViewHelper'),
                        array(array('data' => 'HtmlTag'),  array('tag' =>'td' , 'openOnly' => true)),
                        array('Label', array('tag' => 'td')),
                        array('HtmlTag',
                          array(
                            'tag' => 'tr',
                            'openOnly' => true,
                            'id' => 'divDisplayUntil',
                            'placement' => 'prepend'
                          )
                        ),
                      ));

      $guyCheckbox->setDecorators(array(
                          array('ViewHelper'),
                          array('Label', array('tag' => 'td'))
                        ));

      $onlineCheckbox->setDecorators(array(
                          array('ViewHelper'),
                          array(array('data' => 'HtmlTag'),  array('tag' =>'td' , 'closeOnly' => true)),
                          array('HtmlTag',
                            array(
                              'tag' => 'tr',
                              'closeOnly' => true,
                              'placement' => 'append'
                            )
                          ),
                       ));


*/





        
        $this->setDecorators(array(
            array('FormErrors', array('markupListItemStart' => '', 'markupListItemEnd' => '')),
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));
   }

   public function getGrades()
   {
       $q = Doctrine_Query::create()
       ->from('Moldova_Model_Grade g');
       return $q->fetchArray();
   }

}
