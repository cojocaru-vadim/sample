<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 5/15/11
 * Time: 10:36 PM
 */
 
class Admin_GeneralController extends Zend_Controller_Action
{
    var $_request;
    var $perPage;

    public function preDispatch()
    {
        $this->_helper->layout()->disableLayout();
    }

    public function init()
    {
        $this->_request = $this->getRequest();
        $this->perPage = 50;
    }

    public function indexAction()
    {

    }

    public function emailsPfPageAction()
    {
        $this->view->perPage = $this->perPage;
    }

    public function emailsPjPageAction()
    {
        $this->view->perPage = $this->perPage;
    }
    public function contactFormPageAction()
    {
        $this->view->perPage = $this->perPage;
    }
    public function getPfEmailsAction()
    {
        $req = $this->_request->getPost();

        $offset = (isset($req['start'])) ? $req['start'] : 0;
        $limit = (isset($req['limit'])) ? $req['limit'] : $this->perPage;

        // asta de mai jos am copiat-o din accounts, tu vezi deja ce si cum o sa faci
        $q = Doctrine_Query::create()
                            ->from('Moldova_Model_EmailsPf epf');

        $q->limit($limit)
          ->offset($offset);

        $result = $q->fetchArray();

        $data = array();
        $data['items'] = $result;
        $data['total'] = count($result);
        print Zend_Json::encode($data);
    }

    public function addPfEmailAction()
    {
        $data = array();
        $data['success'] = false;
        $data['message'] = 'Request is not post';
        if($this->_request->isPost())
        {
            $req = $this->_request->getPost();
            try{

                $q = Doctrine_Query::create()
                        ->select('epf.email')
                        ->from('Moldova_Model_EmailsPf epf')
                        ->where('epf.email = ?' , $req['email']);

                if(count($q->fetchArray()) > 0){
                    $data['message'] = 'Email already exists';
                }else{
                    $email = new Moldova_Model_EmailsPf();
                    $email->email = $req['email'];
                    $email->save();

                    $data['success'] = true;
                    $data['message'] = 'Email added';
                }


            }catch(Exception $e){
                $data['message'] = "Operation failed: " . $e->getMessage();
            }
        }

        print Zend_Json::encode($data);
    }

    public function getPjEmailsAction()
    {
        $req = $this->_request->getPost();

        $offset = (isset($req['start'])) ? $req['start'] : 0;
        $limit = (isset($req['limit'])) ? $req['limit'] : $this->perPage;

        // asta de mai jos am copiat-o din accounts, tu vezi deja ce si cum o sa faci
        $q = Doctrine_Query::create()
                            ->select()
                            ->from('Moldova_Model_EmailsPj epj');

        $q->limit($limit)
          ->offset($offset);

        $result = $q->fetchArray();

        $data = array();
        $data['items'] = $result;
        $data['total'] = count($result);
        print Zend_Json::encode($data);
    }

    public function addPjEmailAction()
    {
        $data = array();
        $data['success'] = false;
        $data['message'] = 'Request is not post';
        if($this->_request->isPost())
        {
            $req = $this->_request->getPost();
            try{

                $q = Doctrine_Query::create()
                        ->select('epj.email')
                        ->from('Moldova_Model_EmailsPj epj')
                        ->where('epj.email = ?' , $req['email']);

                if(count($q->fetchArray()) > 0){
                    $data['message'] = 'Email already exists';
                }else{
                    $email = new Moldova_Model_EmailsPj();
                    $email->email = $req['email'];
                    $email->save();

                    $data['success'] = true;
                    $data['message'] = 'Email added';
                }


            }catch(Exception $e){
                $data['message'] = "Operation failed: " . $e->getMessage();
            }
        }

        print Zend_Json::encode($data);
    }

    public function getContactFormAction()
    {
        $req = $this->_request->getPost();

        $offset = (isset($req['start'])) ? $req['start'] : 0;
        $limit = (isset($req['limit'])) ? $req['limit'] : $this->perPage;

        // asta de mai jos am copiat-o din accounts, tu vezi deja ce si cum o sa faci
        $q = Doctrine_Query::create()
                            ->select()
                            ->from('Moldova_Model_Contact c');

        $q->limit($limit)
          ->offset($offset);

        $result = $q->fetchArray();

        $data = array();
        $data['items'] = $result;
        $data['total'] = count($result);
        print Zend_Json::encode($data);
    }

    public function contactFormViewPageAction(){
        $contact_id = $this->_request->getParam("contact_id");
        $this->view->contact_id = $contact_id;
        $q = Doctrine_Query::create()
                    ->select()
                    ->from('Moldova_Model_Contact')
                    ->where('contact_id = ?', $contact_id);

        $result = $q->fetchArray();
        $this->view->contact = $result;
    }
}
