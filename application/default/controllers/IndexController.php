<?php

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
    }

    public function indexAction()
    {
        $this->view->title = 'Главная страница';
        $this->render();
    }

    public function __call($method, $args)
    {
        $this->_forward('error404', 'errors');
    }
}
