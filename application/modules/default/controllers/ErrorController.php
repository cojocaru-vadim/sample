<?php

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->title = $this->view->translate('page-not-found');
                $this->view->message = $this->view->translate('error-404');
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->title = 'Internal Server Error';
                $this->view->message = 'Due to an application error, the requested page could not be displayed.';
            break;
        }
        $this->view->breadcrumb = array("error" => $this->view->translate('error'));
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;

        // initialize logging engine
        $logger = new Zend_Log();

        /*    // add XML writer
        $config = $this->getInvokeArg('bootstrap')->getOption('logs');
        $xmlWriter = new Zend_Log_Writer_Stream($config['logPath'] . '/error.log.xml');
        $logger->addWriter($xmlWriter);
        $formatter = new Zend_Log_Formatter_Xml();
        $xmlWriter->setFormatter($formatter);
        */

        // add Doctrine writer
        $columnMap = array(
            'message' => 'LogMessage',
            'priorityName' => 'LogLevel',
            'timestamp' => 'LogTime',
            'stacktrace' => 'Stack',
            'request' => 'Request',
        );
        $dbWriter = new Moldova_Log_Writer_Doctrine('Moldova_Model_Log', $columnMap);
        $logger->addWriter($dbWriter);
        
        // add Firebug writer
        //$fbWriter = new Zend_Log_Writer_Firebug();
        //$logger->addWriter($fbWriter);

        // add additional data to log message - stack trace and request parameters
        $logger->setEventItem('stacktrace', $errors->exception->getTraceAsString());
        $logger->setEventItem('request', Zend_Debug::dump($errors->request->getParams(), null, false));
        // log exception to writer
        $logger->log($errors->exception->getMessage(), Zend_Log::ERR);

    }

/*
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if (!$errors) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $request->getParams());
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }
*/

}

