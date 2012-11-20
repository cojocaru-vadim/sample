<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/17/11
 * Time: 11:27 PM
 */
 
class Companies_AddController extends Zend_Controller_Action{

    var $_request;

    public function preDispatch()
    {
/*        $this->_request = $this->getRequest();
        $url = $this->getRequest()->getRequestUri();
        
        if(Moldova_Utils::checkAccount($url)){
            //allowed
        } else {
            $this->_redirect('login');
        }*/
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

            if($this->validateAddCompanyForm($this->getRequest()->getPost())){

                $req = $this->getRequest()->getPost();
                $company_url = Moldova_Utils::cleanUrl($req['name']);
                $company_id = $this->getCompanyId();
                 $q = Doctrine_Query::create()
                ->select('c.company_id')
                ->from('Moldova_Model_Companies c')
                ->where('c.is_deleted = ?' , 0)
                ->andWhere('c.company_url = ?', $company_url);

                if(count($q->fetchArray()) > 0){
                    $company_url = $company_url.$company_id;
                }


               // print_r($req->getUnknown()); die;

                //print_r($company_id); die ;
                $company = Doctrine::getTable('Moldova_Model_Companies')->find($company_id);
                $company->name = $req['name'];
                $company->company_url = $company_url;
                $company->desc_en_US = $req['desc_en_US'];
                $company->desc_ro_MD = $req['desc_ro_MD'];
                $company->desc_ru_RU = $req['desc_ru_RU'];
                $company->locality_id = $req['locality_id'];
                $company->region_id = $req['region_id'];
                $company->postcode = $req['postcode'];
                $company->street = $req['street'];
                $company->house = $req['house'];
                $company->tel = $req['tel'];
                $company->mob = $req['mob'];
                $company->fax = $req['fax'];
                $company->web = $req['web'];
                $company->email = $req['email'];
                $company->category_id = $req['category_id'];
                $company->subcategory_id = $req['subcategory_id'];
                $company->is_deleted = 0;
                $company->status = 0;

                //print_r($company->toArray()); die;

                $company->save();
                $this->_redirect('/companies/add/success');
            } else {
                $this->_redirect('/companies/add');
            }
            //$req = $this->getRequest()->getPost();
            //dump($req);
            //dump($_FILES);
        }
        $this->view->breadcrumb = array("companies" => $this->view->translate('companies'), 'add' =>  $this->view->translate('add'));
    }

    public function successAction()
    {
        if ($this->_helper->getHelper('FlashMessenger')->getMessages()) {
            $this->view->messages = $this->_helper->getHelper('FlashMessenger')->getMessages();
        } else {
            $this->_redirect('/companies');
        }
    }
    
    public function saveImagesAction()
    {
        $this->_helper->layout()->disableLayout();
        $result = 0;
        $company_id = $this->getCompanyId();
        $config = $this->getInvokeArg('bootstrap')->getOption('uploads');
        $uploadpath = $config['uploadPath'];
        $currentUploadPath = $uploadpath . "/companies/{$company_id}";

        $upload = new Zend_File_Transfer_Adapter_Http();


        if(!is_dir($currentUploadPath)){
            mkdir($currentUploadPath);

        }
         $upload->addValidator('Extension', false, 'png,jpg,gif')
          ->setDestination($currentUploadPath);

        if (!$upload->isValid()) {
            //print_r($upload->getMessages());
        }else{
            try {
                $upload->receive();
                $imageName = $upload->getFileInfo();
                $imageName = $imageName['company_img']['name'];
                $file = $currentUploadPath . "/" .$imageName;

                $resized = new Moldova_ImageManager();
                $resized->load($file);
                $info = $resized->getsize();
                if($info[0]>500 || $info[1]>500){
                    $resized->resize(500, 500, true);
                }
                $resizedUrl = $company_id.'_'.time().'.jpg';
                $file1 = $resized->save("{$currentUploadPath}/{$resizedUrl}", "jpg", 100);


                $thumb = new Moldova_ImageManager();
                $thumb->load($file);
                $thumb->resize(75, 75, true);
                $thumbdUrl = $company_id.'_thumb_'. time() .'.jpg';
                $file2 = $thumb->save("{$currentUploadPath}/{$thumbdUrl}", "jpg", 100);


                $companyImg = new Moldova_Model_CompaniesImages();
                $companyImg->company_id = $company_id;
                $companyImg->image_url = $resizedUrl;
                $companyImg->thumb_url = $thumbdUrl;
                $companyImg->is_deleted = 0;
                $companyImg->save();

                //dump($info);
                //die;
                $result = 1;
            } catch (Zend_File_Transfer_Exception $e) {
                echo $e->getMessage();
            }
        }
        $image = "uploads/companies/{$company_id}/{$resizedUrl}"; // imaginea care trebuie de afishat dupa upload
        echo '<script language="javascript" type="text/javascript">
                window.top.window.stopUpload('.$result.', "'.$image.'");
            </script>';
    }

    public function removeImagesAction()
    {
        $this->_helper->layout()->disableLayout();
        $req = $this->_request->getPost();
        $pathArray = explode('/', $req['path']);
        $lastkey = array_pop(array_keys($pathArray));
        $imgUrl = $pathArray[$lastkey];
        $company_id = $this->getCompanyId();
        $q = Doctrine_Query::create()
                ->update('Moldova_Model_CompaniesImages ci')
                ->set('ci.is_deleted ', '?' , 1)
                ->where('ci.image_url = ?' , $imgUrl)
                ->andWhere('ci.company_id = ?' , $company_id);
        $q->execute();
    }

    public function saveLogoImagesAction()
    {
        $this->_helper->layout()->disableLayout();
        $result = 0;
        $company_id = $this->getCompanyId();
        $config = $this->getInvokeArg('bootstrap')->getOption('uploads');
        $uploadpath = $config['uploadPath'];
        $currentUploadPath = $uploadpath . "/companies/{$company_id}";

        $upload = new Zend_File_Transfer_Adapter_Http();


        if(!is_dir($currentUploadPath)){
            mkdir($currentUploadPath);

        }
         $upload->addValidator('Extension', false, 'png,jpg,gif')
          ->setDestination($currentUploadPath);

        if (!$upload->isValid()) {
            //print_r($upload->getMessages());
        }else{
            try {
                $upload->receive();
                $imageName = $upload->getFileInfo();
                $imageName = $imageName['logo_img']['name'];
                $file = $currentUploadPath . "/" .$imageName;
                $thumb = new Moldova_ImageManager();
                $thumb->load($file);
                $info = $thumb->getsize();
                if($info[0]>130 || $info[1]>130){
                    if($info[0] < $info[1]){
                        $thumb->resize(0, 130, true);
                    }else{
                        $thumb->resize(130, 130, true);
                    }

                }
                $addon = time();
                $logoUrl = $company_id.'_'.$addon.'_logo.jpg';
                $file = $thumb->save("{$currentUploadPath}/{$logoUrl}", "jpg", 100);

                $company = Doctrine::getTable('Moldova_Model_Companies')->find($company_id);
                $company->logo_img = $logoUrl;
                $company->save();

                //dump($info);
                //die;
                $result = 1;
            } catch (Zend_File_Transfer_Exception $e) {
                echo $e->getMessage();
            }
        }
        $image = "uploads/companies/{$company_id}/{$logoUrl}"; // imaginea care trebuie de afishat dupa upload
        echo '<script language="javascript" type="text/javascript">
                window.top.window.stopLogoUpload('.$result.', "'.$image.'");
            </script>';
    }

    public function removeLogoImagesAction()
    {
        $this->_helper->layout()->disableLayout();
        $company_id = $this->getCompanyId();
        $company = Doctrine::getTable('Moldova_Model_Companies')->find($company_id);
        $company->logo_img = '';
        $company->save();
        //$req = $this->_request->getPost();
        //print $req['path'];
    }

    private function getCompanyId(){
        $session = new Zend_Session_Namespace('accounts');
        //echo $session->session_id; die;

        $q = Doctrine_Query::create()
                ->select('c.company_id')
                ->from('Moldova_Model_Companies c')
                ->where('c.is_deleted = ?' , 1)
                ->andWhere('c.session_id = ?', $session->session_id);
        if(count($q->fetchArray()) > 0){
            $company_id = $q->fetchArray();
            $company_id = $company_id[0]['company_id'];
        } else {
            $company = new Moldova_Model_Companies();
            $company->session_id = $session->session_id;
            $company->is_deleted = 1;
            $company->status = 0;
            $company->creation_date = date('Y-m-d H:i:s', mktime());
            $company->save();
            $company_id = $company->getIncremented();
        }
        return $company_id;
    }
    
    private function validateAddCompanyForm($post){
        $return = false;
        $validators = array(
            'name'  => array('NotEmpty',
                          //  'messages' => array('A valid name is required')
            ),
            
            'category_id' => array( new Zend_Validate_Between(1, 100000),
                            'messages' => $this->view->translate('form-message-category-required')

            ),
            'subcategory_id' => array( new Zend_Validate_Between(1, 100000),
                            'messages' => $this->view->translate('form-message-subcategory-required')
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
            $this->_helper->FlashMessenger->addMessage($this->view->translate('form-message-add-success'));
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

