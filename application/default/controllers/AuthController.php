<?php

class AuthController extends Zend_Controller_Action
{
    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {
        $this->_redirect('/');
    }

    public function loginAction()
    {
        $this->view->message = '';
        $this->view->username = '';

        // Store the original requested URL for redirect after login
        $redirectUrl = $this->_request->getParam('redirect', null);
        if ($redirectUrl) {
            // Store in session for use after successful login
            $session = new Zend_Session_Namespace('login_redirect');
            $session->redirectUrl = $redirectUrl;
        }

        if ($this->_request->isPost()) {
            // collect the data from the user
            //Zend_Loader::loadClass('Zend_Filter_StripTags');
            $filter = new Zend_Filter_StripTags();
            $username = $filter->filter($this->_request->getPost('username'));
            $password = $filter->filter($this->_request->getPost('password'));

            if (empty($username)) {
                $this->view->message = 'Пожайлуста заполните все поля!';
            } else {
                // setup Zend_Auth adapter for a database table
                //Zend_Loader::loadClass('Zend_Auth_Adapter_DbTable');
                $dbAdapter = Zend_Registry::get('dbAdapter');
                $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
                $authAdapter->setTableName('users')
                ->setIdentityColumn('usr_login')
                ->setCredentialColumn('usr_passwd')
                ->setCredentialTreatment('MD5(?)');

                // Set the input credential values to authenticate against
                $authAdapter->setIdentity($username)
                ->setCredential($password);

                // do the authentication
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    // success : store database row to auth's storage system
                    // (not the password though!)
                    $data = $authAdapter->getResultRowObject(array('usr_id', 'usr_login', 'usr_role'), null);
                    $auth->getStorage()->write($data);

                    // Check if there's a stored redirect URL
                    $session = new Zend_Session_Namespace('login_redirect');
                    $redirectUrl = isset($session->redirectUrl) ? $session->redirectUrl : null;

                    // Clear the stored redirect URL
                    unset($session->redirectUrl);

                    // Simple redirect logic - use stored redirect URL or default based on role
                    if ($redirectUrl) {
                        $this->_helper->redirector->gotoUrlAndExit($redirectUrl);
                    } else {
                        // Default redirects if no specific URL was stored
                        if ($data->usr_role === 'a' || $data->usr_role === 'e') {
                            $this->_helper->redirector->gotoUrlAndExit('/admin');
                        } else {
                            $this->_helper->redirector->gotoUrlAndExit('/');
                        }
                    }
                } else {
                    $message = '';
                    switch ($result->getCode()) {
                        case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                            /** do stuff for nonexistent identity **/
                            $message = 'Неверный пользователь!';
                            break;

                        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                            /** do stuff for invalid credential **/
                            $message = 'Неверный пароль!';
                            break;

                        default:
                            /** do stuff for other failure **/
                            $message = 'Сбой при входе в систему!';
                            break;
                    }
                    // failure: clear database row from session
                    $this->view->message  = $message;
                    $this->view->username = $username;
                }
            }
        }

        $this->view->title = "Вход в систему";
        $this->render();
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $_SESSION = array();
        $this->_redirect('/');
    }

    public function __call($method, $args)
    {
        $this->_forward('errors', 'error404');
    }
}
