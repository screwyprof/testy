<?php

class ErrorController extends Zend_Controller_Action
{
    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
    }

    public function errorAction()
    {
        $this->view->title = 'Ошибка 404';
        Zend_Debug::dump($this->getResponse());
        die();
    }

    public function error404Action()
    {
        $response = $this->getResponse();
        $response->setHttpResponseCode(404);

        $this->view->title = 'Ошибка 404';
        $this->render();
    }

}
