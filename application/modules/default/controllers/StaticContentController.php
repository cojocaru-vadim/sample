<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 5/12/11
 * Time: 3:46 PM
 */
 
class StaticContentController extends Zend_Controller_Action
{
    public function init()
    {
    }
    // display static views
    public function displayAction()
    {
        $page = $this->getRequest()->getParam('page');
        $session = new Zend_Session_Namespace('accounts');
        $page = $page . '-' . strtolower($session->locale);
        $page = str_replace('_', '-', $page);
        //exit($page);
        //echo $this->view->getScriptPath(null) . "/" . $this->getRequest()->getControllerName() . "/$page." . $this->viewSuffix; die;
        if (file_exists($this->view->getScriptPath(null) . "/" . $this->getRequest()->getControllerName() . "/$page." . $this->viewSuffix)) {
            //echo $this->view->getScriptPath(null) . "/" . $this->getRequest()->getControllerName() . "/$page." . $this->viewSuffix; die;
            if(strpos($page, 'advertise')  !==  false){
                $this->view->breadcrumb = array("advertise" => $this->view->translate('advertising'));
            }elseif(strpos($page, 'help')  !==  false){
                $this->view->breadcrumb = array("help" => $this->view->translate('menu-help'));
            }elseif(strpos($page, 'services')  !==  false){
                $this->view->breadcrumb = array("services" => $this->view->translate('menu-services'));
            }elseif(strpos($page, 'about-us')  !==  false){
                $this->view->breadcrumb = array("about-us" => $this->view->translate('about-us'));
            }elseif(strpos($page, 'terms-conditions')  !==  false){
                $this->view->breadcrumb = array("terms-conditions" => $this->view->translate('terms-conditions'));
            }
            $this->render($page);
        } else {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }
    }
}
