<?php
/**
 * Created by Moldova Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/3/11
 * Time: 4:47 PM
 */
 
class Moldova_Controller_Action_Helper_Navigation extends Zend_Controller_Action_Helper_Abstract
{
    protected $_container;

    // constructor, set navigation container
    public function __construct(Zend_Navigation $container = null)
    {
        if (null !== $container) {
            $this->_container = $container;
        }
    }

    // check current request and set active page
    public function preDispatch()
    {
        $this->getContainer()->findBy('uri', $this->getRequest()->getRequestUri())->active = true;
    }

    // retrieve navigation container
    public function getContainer()
    {
        if (null === $this->_container) {
            $this->_container = Zend_Registry::get('Zend_Navigation');
        }
        if (null === $this->_container) {
            throw new RuntimeException ('Navigation container unavailable');
        }
        return $this->_container;
    }
}