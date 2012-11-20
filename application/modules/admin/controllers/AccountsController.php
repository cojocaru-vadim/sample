<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 4/14/11
 * Time: 11:35 PM
 */
 
class Admin_AccountsController extends Zend_Controller_Action
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
        $this->perPage = 500;
    }

    public function indexAction()
    {

    }

    public function accountPageAction()
    {
        $this->view->perPage = $this->perPage;
    }


    public function accountsListAction()
    {
        $req = $this->_request->getPost();

        $offset = (isset($req['start'])) ? $req['start'] : 0;
        $limit = (isset($req['limit'])) ? $req['limit'] : $this->perPage;

        $q = Doctrine_Query::create()
                            ->select()
                            ->from('Moldova_Model_Accounts a')
                            ->where('a.is_deleted = ?', 0);

        $q->limit($limit)
          ->offset($offset);

        $result = $q->fetchArray();

        $data = array();
        $data['items'] = $result;
        $data['total'] = count($result);
        print Zend_Json::encode($data);
    }
}
