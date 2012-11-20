<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/13/11
 * Time: 11:36 PM
 */

class LocaleController extends Zend_Controller_Action
{
    // action to manually override locale
    public function indexAction()
    {
        // if supported locale, add to session
        if (Zend_Validate::is( $this->getRequest()->getParam('locale'),  'InArray',  array('haystack' => array('en_US', 'ru_RU', 'ro_MD')))) {
            $session = new Zend_Session_Namespace('accounts');
            $session->locale = $this->getRequest()->getParam('locale');
        }
        // redirect to requesting URL
        $url = $this->getRequest()->getServer('HTTP_REFERER');
        $this->_redirect($url);
    }
}
