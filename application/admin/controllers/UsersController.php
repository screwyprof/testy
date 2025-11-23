<?php

class Admin_UsersController extends Zend_Controller_Action
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
        $this->view->title = 'Управление пользователями';

        $items_per_page = 5;

        ////

        $page_no = (int) $this->_request->getParam('pageno', 1);
        if ($page_no < 1) {
            $page_no = 1;
        }

        $offset = 0;
        $page_count = 0;

        if ($items_per_page > 0) {
            $rows_count = (int) Default_Model_User::getUsersCount();
            ($rows_count < 0) ? 0 : $rows_count;

            $offset = ($page_no - 1) * $items_per_page;
            $page_count = floor(($rows_count - 1) / $items_per_page) + 1;

            if ($offset > $rows_count) {
                $page_no = $page_count;
                $offset = ($page_no - 1) * $items_per_page;
            }
        }

        $this->view->page_no    = $page_no;
        $this->view->page_prev  = max($page_no - 1, 1);
        $this->view->page_next  = min($page_no + 1, $page_count);
        $this->view->page_count = $page_count;

        $this->view->users = Default_Model_User::findAllUsers($items_per_page, $offset);
        //$this->render();
    }

    public function addAction()
    {
        if ($this->_request->isPost()) {
            if (isset($_POST['submit'])) {
                $filter  = new Zend_Filter_StripTags();

                $usr_is_enabled = $this->_request->getPost('usr_is_enabled');
                $usr_is_enabled = (int)(bool) $usr_is_enabled;

                $usr_login = $this->_request->getPost('usr_login');
                $usr_login = $filter->filter($usr_login);
                $usr_login = trim($usr_login);

                $usr_lastname = $this->_request->getPost('usr_lastname');
                $usr_lastname = $filter->filter($usr_lastname);
                $usr_lastname = trim($usr_lastname);

                $usr_firstname = $this->_request->getPost('usr_firstname');
                $usr_firstname = $filter->filter($usr_firstname);
                $usr_firstname = trim($usr_firstname);

                $usr_thirdname = $this->_request->getPost('usr_thirdname');
                $usr_thirdname = $filter->filter($usr_thirdname);
                $usr_thirdname = trim($usr_thirdname);

                $usr_email = $this->_request->getPost('usr_email');
                $usr_email = $filter->filter($usr_email);
                $usr_email = trim($usr_email);

                $usr_passwd = $this->_request->getPost('usr_passwd');
                $usr_passwd = $filter->filter($usr_passwd);
                $usr_passwd = trim($usr_passwd);
                $usr_passwd = md5($usr_passwd);

                $usr_role = $this->_request->getPost('usr_role');
                $usr_role = $filter->filter($usr_role);
                $usr_role = trim($usr_role);


                $row = array(
                    'usr_is_enabled' => $usr_is_enabled,
                    'usr_login' => $usr_login,
                    'usr_passwd' => $usr_passwd,
                    'usr_firstname' => $usr_firstname,
                    'usr_lastname' => $usr_lastname,
                    'usr_thirdname' => $usr_thirdname,
                    'usr_email' => $usr_email,
                    'usr_role' => $usr_role,
                );
                Default_Model_User::addUser($row);
            }
            $this->redirect('/admin/users');
            return;
        }

        $this->view->title = 'Создание пользователя';
        $this->render();
    }

    public function editAction()
    {
        if ($this->_request->isPost()) {
            if (isset($_POST['submit'])) {
                $filter  = new Zend_Filter_StripTags();
                $usr_id = (int) $this->_request->getPost('usr_id', 0);

                $usr_is_enabled = $this->_request->getPost('usr_is_enabled');
                $usr_is_enabled = (int)(bool) $usr_is_enabled;

                $usr_login = $this->_request->getPost('usr_login');
                $usr_login = $filter->filter($usr_login);
                $usr_login = trim($usr_login);

                $usr_lastname = $this->_request->getPost('usr_lastname');
                $usr_lastname = $filter->filter($usr_lastname);
                $usr_lastname = trim($usr_lastname);

                $usr_firstname = $this->_request->getPost('usr_firstname');
                $usr_firstname = $filter->filter($usr_firstname);
                $usr_firstname = trim($usr_firstname);

                $usr_thirdname = $this->_request->getPost('usr_thirdname');
                $usr_thirdname = $filter->filter($usr_thirdname);
                $usr_thirdname = trim($usr_thirdname);

                $usr_email = $this->_request->getPost('usr_email');
                $usr_email = $filter->filter($usr_email);
                $usr_email = trim($usr_email);

                $usr_role = $this->_request->getPost('usr_role');
                $usr_role = $filter->filter($usr_role);
                $usr_role = trim($usr_role);


                $row = array(
                    'usr_is_enabled' => $usr_is_enabled,
                    'usr_login' => $usr_login,
                    'usr_firstname' => $usr_firstname,
                    'usr_lastname' => $usr_lastname,
                    'usr_thirdname' => $usr_thirdname,
                    'usr_email' => $usr_email,
                    'usr_role' => $usr_role,
                );
                Default_Model_User::updateUser($usr_id, $row);
            }
            $this->redirect('/admin/users');
            return;
        } else {
            // Получаем идентификатор пользователя
            $id = (int) $this->_request->getParam('id', 0);
            if ($id > 0) {
                $this->view->usr = Default_Model_User::findUserById($id);
            }
        }

        $this->view->title = 'Параметры пользователя';
        $this->render();
    }


    public function deleteAction()
    {
        if ($this->_request->isPost()) {
            $filter = new Zend_Filter_Alpha();
            $id  = (int)$this->_request->getPost('id', 0);
            $del = $filter->filter($this->_request->getPost('del'));
            if ($del == 'Yes') {
                Default_Model_User::deleteUser($id);
            }
        } else {
            $id = (int)$this->_request->getParam('id', 0);
            $this->view->usr   = Default_Model_User::findUserById($id);
            $this->view->title = 'Удаление пользователя';
            $this->render();
            return;
        }

        // возвращаемся к списку пользователей
        $this->redirect('/admin/users');
    }


    public function __call($method, $args)
    {
        $this->forward('error404', 'errors');
    }
}
