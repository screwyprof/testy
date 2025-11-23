<?php

class Admin_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->initView();
        $this->view->setScriptPath('./application/default/views/scripts')
            ->addScriptPath('./application/admin/views/scripts');

        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
    }

    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            // Store the current URL for redirect after login
            $requestUri = $this->_request->getRequestUri();
            $baseUrl = $this->_request->getBaseUrl();
            $relativeUri = str_replace($baseUrl, '', $requestUri);

            // Use the redirector helper for clean URL construction
            $this->_helper->redirector->gotoUrlAndExit('auth/login?redirect=' . urlencode($relativeUri));
        }
    }

    public function indexAction()
    {
        $this->view->title = 'Администрирование';
        $this->render();
    }

    public function __call($method, $args)
    {
        $this->forward('error404', 'errors');
    }
}
