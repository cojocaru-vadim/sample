<?php
class Moldova_Form_Signup extends Zend_Form
{

   public $genders = array(
       "a" => "Select gender",
       1 => "Male",
       2 => "Female"
   );

  public function init()
  {
    // initialize form
    $this->setAction('/signup')
         ->setMethod('post');

    // create text input for name
    $name = new Zend_Form_Element_Text('name');
    $name->setLabel('Name:')
         //->setOptions(array('size' => '20'))
         ->setRequired(true)
         ->addValidator('Regex', false, array(
            'pattern' => '/^[a-zA-Z]+[A-Za-z\'\-\. ]{1,50}$/',
         ))
         ->addErrorMessage('Provide a valid name')
         ->addFilter('HtmlEntities')
         ->addFilter('StringTrim');

    // create text input for email address
    $email = new Zend_Form_Element_Text('email');
    $email->setLabel('Email:')
//          ->setOptions(array('size' => '20'))
          ->setRequired(true)
          ->addValidator('EmailAddress', false)
          ->addErrorMessage('Provide a valid email')
          ->addFilter('HtmlEntities')
          ->addFilter('StringTrim')
          ->addFilter('StringToLower');

        // create password input
        $pass = new Zend_Form_Element_Password('password');
        $pass->setLabel('Password:')
        //->setOptions(array('size' => '20'))
        ->setRequired(true)
        ->addValidator('NotEmpty', true)
        ->addErrorMessage('Provide a valid password');


    // create select input for item country
    $gender = new Zend_Form_Element_Select('gender');
    $gender->setLabel('Gender:')
            //->setRequired(true)
            //->addValidator('Int')
            ->addErrorMessage('Provide a valid Gender')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim');
    foreach ($this->genders as $gi => $gv ) {
      $gender->addMultiOption($gi, $gv);
    }

//------------------- DOB INFORMATION  -------------------------//

    // create hidden input for item display date
    $birthDate = new Zend_Form_Element_Hidden('birth_date');
    $birthDate->addValidator('Date')
                 ->addErrorMessage('Provide a valid date')
                 ->addFilter('HtmlEntities')
                 ->addFilter('StringTrim');      

       // create select inputs for item display date
    $birthDay = new Zend_Form_Element_Select('birth_day');
    $birthDay->setLabel('Birth Date:')
                    ->addValidator('Int')
                    ->addFilter('HtmlEntities')
                    ->addFilter('StringTrim')
                    ->addFilter('StringToUpper');

    $birthDay->addMultiOption(-1, "Day");
    for($x=1; $x<=31; $x++) {
      $birthDay->addMultiOption($x, sprintf('%02d', $x));
    }

    $birthMonth = new Zend_Form_Element_Select('birth_month');
    $birthMonth->addValidator('Int')
                      ->addFilter('HtmlEntities')
                      ->addFilter('StringTrim');
    $birthMonth->addMultiOption(-1, "Month");
    for($x=1; $x<=12; $x++) {
      $birthMonth->addMultiOption($x, date('M', mktime(1,1,1,$x,1,1)));
    }

    $birthYear = new Zend_Form_Element_Select('birth_year');
    $birthYear->addValidator('Int')
                     ->addFilter('HtmlEntities')
                     ->addFilter('StringTrim');
    $birthYear->addMultiOption(-1, "Year");
    for($x=1993; $x>=1900; $x--) {
      $birthYear->addMultiOption($x, $x);
    }

//------------------- BOD INFORMATION  -------------------------//

        // create hash
        $hash = new Zend_Form_Element_Hash('hash');
        $hash->setSalt('fgbceriufjs9porkfp');


/*
    // create radio input for item gender
    $gender = new Zend_Form_Element_Radio('gender');
    $gender->setLabel('Gender:')
         ->setRequired(true)
         ->addValidator('Int')
         ->addFilter('HtmlEntities')
         ->addFilter('StringTrim');
    foreach ($this->genders as $gi => $gv ) {
      $gender->addMultiOption($gi, $gv);
    }
    $gender->setValue(0);
*/
          // create submit button
    $submit = new Zend_Form_Element_Submit('submit');
    $submit->setLabel('Sign up')
           ->setOrder(100)
           ->setOptions(array('class' => 'greenButton'));


    // attach elements to form
    $this->addElement($name)
         ->addElement($email)
         ->addElement($pass)
         ->addElement($gender)
         ->addElement($birthDay)
         ->addElement($birthMonth)
         ->addElement($birthYear)
         ->addElement($birthDate)
         ->addElement($hash)
         ->addElement($submit);
/*
    // create display group for seller information
    $this->addDisplayGroup(array('email', 'name', 'password', 'gender'), 'account');
    $this->getDisplayGroup('account')
         ->setOrder(10)
         ->setLegend('Account information');
*/
    // attach element to form

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class'=> 'input-container')),
            array(array('data' => 'HtmlTag'),  array('tag' =>'td')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
        ));

      //$birthDate->setDecorators(array(array('ViewHelper')));

      $birthDay->setDecorators(array(
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

      $birthMonth->setDecorators(array(
                          array('ViewHelper')
                        ));

      $birthYear->setDecorators(array(
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

      $hash->setDecorators(array(
                          array('ViewHelper')
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
/*
    // create text input for name
    $name = new Zend_Form_Element_Text('SellerName');
    $name->setLabel('Name:')
         ->setOptions(array('size' => '35'))
         ->setRequired(true)
         ->addValidator('Regex', false, array(
            'pattern' => '/^[a-zA-Z]+[A-Za-z\'\-\. ]{1,50}$/'
           ))
         ->addFilter('HtmlEntities')
         ->addFilter('StringTrim');

    // create text input for email address
    $email = new Zend_Form_Element_Text('SellerEmail');
    $email->setLabel('Email address:');
    $email->setOptions(array('size' => '50'))
          ->setRequired(true)
          ->addValidator('EmailAddress', false)
          ->addFilter('HtmlEntities')
          ->addFilter('StringTrim')
          ->addFilter('StringToLower');

    // create text input for tel number
    $tel = new Zend_Form_Element_Text('SellerTel');
    $tel->setLabel('Telephone number:');
    $tel->setOptions(array('size' => '50'))
        ->addValidator('StringLength', false, array('min' => 8))
        ->addValidator('Regex', false, array(
            'pattern'   => '/^\+[1-9][0-9]{6,30}$/',
            'messages'  => array(
              Zend_Validate_Regex::INVALID    =>
                '\'%value%\' does not match international number format +XXYYZZZZ',
              Zend_Validate_Regex::NOT_MATCH  =>
                '\'%value%\' does not match international number format +XXYYZZZZ'
            )
          ))
        ->addFilter('HtmlEntities')
        ->addFilter('StringTrim');

    // create text input for address
    $address = new Zend_Form_Element_Textarea('SellerAddress');
    $address->setLabel('Postal address:')
          ->setOptions(array('rows' => '6','cols' => '36'))
          ->addFilter('HtmlEntities')
          ->addFilter('StringTrim');

    // create text input for item title
    $title = new Zend_Form_Element_Text('Title');
    $title->setLabel('Title:')
          ->setOptions(array('size' => '60'))
          ->setRequired(true)
          ->addFilter('HtmlEntities')
          ->addFilter('StringTrim');

    // create text input for item year
    $year = new Zend_Form_Element_Text('Year');
    $year->setLabel('Year:')
         ->setOptions(array('size' => '8', 'length' => '4'))
         ->setRequired(true)
         ->addValidator('Between', false, array('min' => 1700, 'max' => 2015))
         ->addFilter('HtmlEntities')
         ->addFilter('StringTrim');

    // create select input for item country
    $country = new Zend_Form_Element_Select('CountryID');
    $country->setLabel('Country:')
            ->setRequired(true)
            ->addValidator('Int')
            ->addFilter('HtmlEntities')
            ->addFilter('StringTrim')
            ->addFilter('StringToUpper');
    foreach ($this->getCountries() as $c) {
      $country->addMultiOption($c['CountryID'], $c['CountryName']);
    }

    // create text input for item denomination
    $denomination = new Zend_Form_Element_Text('Denomination');
    $denomination->setLabel('Denomination:')
                 ->setOptions(array('size' => '8'))
                 ->setRequired(true)
                 ->addValidator('Float')
                 ->addFilter('HtmlEntities')
                 ->addFilter('StringTrim');

    // create radio input for item type
    $type = new Zend_Form_Element_Radio('TypeID');
    $type->setLabel('Type:')
         ->setRequired(true)
         ->addValidator('Int')
         ->addFilter('HtmlEntities')
         ->addFilter('StringTrim');
    foreach ($this->getTypes() as $t) {
      $type->addMultiOption($t['TypeID'], $t['TypeName']);
    }
    $type->setValue(1);

    // create select input for item grade
    $grade = new Zend_Form_Element_Select('GradeID');
    $grade->setLabel('Grade:')
          ->setRequired(true)
          ->addValidator('Int')
          ->addFilter('HtmlEntities')
          ->addFilter('StringTrim');
    foreach ($this->getGrades() as $g) {
      $grade->addMultiOption($g['GradeID'], $g['GradeName']);
    };

    // create text input for sale price (min)
    $priceMin = new Zend_Form_Element_Text('SalePriceMin');
    $priceMin->setLabel('Sale price (min):')
                 ->setOptions(array('size' => '8'))
                 ->setRequired(true)
                 ->addValidator('Float')
                 ->addFilter('HtmlEntities')
                 ->addFilter('StringTrim');

    // create text input for sale price (max)
    $priceMax = new Zend_Form_Element_Text('SalePriceMax');
    $priceMax->setLabel('Sale price (max):')
                 ->setOptions(array('size' => '8'))
                 ->setRequired(true)
                 ->addValidator('Float')
                 ->addFilter('HtmlEntities')
                 ->addFilter('StringTrim');

    // create text input for item description
    $notes = new Zend_Form_Element_Textarea('Description');
    $notes->setLabel('Description:')
          ->setOptions(array('rows' => '15','cols' => '60'))
          ->setRequired(true)
          ->addFilter('HTMLEntities')
          ->addFilter('StripTags')
          ->addFilter('StringTrim');

    // create CAPTCHA for verification
    $captcha = new Zend_Form_Element_Captcha('Captcha', array(
      'captcha' => array(
        'captcha' => 'Image',
        'wordLen' => 6,
        'timeout' => 300,
        'width'   => 300,
        'height'  => 100,
        'imgUrl'  => '/captcha',
        'imgDir'  => APPLICATION_PATH . '/../public/captcha',
        'font'    => APPLICATION_PATH . '/../public/fonts/LiberationSansRegular.ttf',
        )
    ));

    // create submit button
    $submit = new Zend_Form_Element_Submit('submit');
    $submit->setLabel('Submit Entry')
           ->setOrder(100)
           ->setOptions(array('class' => 'submit'));

    // attach elements to form
    $this->addElement($name)
         ->addElement($email)
         ->addElement($tel)
         ->addElement($address);

    // create display group for seller information
    $this->addDisplayGroup(array('SellerName', 'SellerEmail', 'SellerTel', 'SellerAddress'), 'contact');
    $this->getDisplayGroup('contact')
         ->setOrder(10)
         ->setLegend('Seller Information');

    // attach elements to form
    $this->addElement($title)
         ->addElement($year)
         ->addElement($country)
         ->addElement($denomination)
         ->addElement($type)
         ->addElement($grade)
         ->addElement($priceMin)
         ->addElement($priceMax)
         ->addElement($notes);

    // create display group for item information
    $this->addDisplayGroup(array('Title', 'Year', 'CountryID', 'Denomination', 'TypeID', 'GradeID', 'SalePriceMin', 'SalePriceMax', 'Description'), 'item');
    $this->getDisplayGroup('item')
         ->setOrder(20)
         ->setLegend('Item Information');

    // attach element to form
    $this->addElement($captcha);

    // create display group for CAPTCHA
    $this->addDisplayGroup(array('Captcha'), 'verification');
    $this->getDisplayGroup('verification')
         ->setOrder(30)
         ->setLegend('Verification Code');

    // attach element to form
    $this->addElement($submit);
 
 */
  }
/*
  public function getCountries() {
    $q = Doctrine_Query::create()
         ->from('Square_Model_Country c');
    return $q->fetchArray();
  }

  public function getGrades() {
    $q = Doctrine_Query::create()
         ->from('Square_Model_Grade g');
    return $q->fetchArray();
  }

  public function getTypes() {
    $q = Doctrine_Query::create()
         ->from('Square_Model_type t');
    return $q->fetchArray();
  }*/
}
