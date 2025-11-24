<?php

class Admin_TestsController extends Zend_Controller_Action
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
        $this->view->title = 'Manage Tests';
        $this->view->tests = Default_Model_Test::findAllTests();
        $this->render();
    }

    public function addAction()
    {
        $form = new Admin_Form_AddTestProperties();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $form_values = $this->_request->getPost();

                $form_values['test_is_enabled'] = (int)(bool) $form_values['test_is_enabled'];

                $dateTime = new DateTime();
                $start_time = $dateTime::createFromFormat("d.m.Y H:i", $form_values['test_start_time']);
                $form_values['test_start_time'] = $start_time->getTimestamp();

                $stop_time = $dateTime::createFromFormat("d.m.Y H:i", $form_values['test_stop_time']);
                $form_values['test_stop_time'] = $stop_time->getTimestamp();

                $form_values['test_time'] = (int) $form_values['test_time'];
                $form_values['test_time'] = $form_values['test_time'] * 60;

                $form_values['test_qst_show_cnt'] = (int) $form_values['test_qst_show_cnt'];

                $form_values['test_is_mix_qst'] = (int)(bool) $form_values['test_is_mix_qst'];

                $form_values['test_is_mix_ans'] = (int)(bool) $form_values['test_is_mix_ans'];

                $form_values['test_is_show_answers'] = (int)(bool) $form_values['test_is_show_answers'];

                $form_values['test_qst_per_page'] = (int)(bool) $form_values['test_qst_per_page'];

                $row = array(
                    'test_is_enabled' => $form_values['test_is_enabled'],
                    'test_start_time' => $form_values['test_start_time'],
                    'test_stop_time' => $form_values['test_stop_time'],
                    'test_time' => $form_values['test_time'],
                    'test_title' => $form_values['test_title'],
                    'test_desc' => $form_values['test_desc'],
                    'test_qst_show_cnt' => $form_values['test_qst_show_cnt'],
                    'test_is_mix_qst' => $form_values['test_is_mix_qst'],
                    'test_is_mix_ans' => $form_values['test_is_mix_ans'],
                    'test_is_show_answers' => $form_values['test_is_show_answers'],
                    'test_qst_per_page' => $form_values['test_qst_per_page']
                );
                Default_Model_Test::addTest($row);
                $this->redirect('/admin/tests');
                return;
            } else {
                $this->view->form = $form;
            }
        }

        $this->view->title = 'Create Test';
        $this->render();
    }

    public function editAction()
    {
        $id = (int) $this->_request->getParam('id', 0);
        $form = new Admin_Form_EditTestProperties();
        if (($this->_request->isPost()) && ($form->isValid($this->getRequest()->getPost()))) {
            if (isset($_POST['submit'])) {
                $form_values = $this->_request->getPost();

                $form_values['test_is_enabled'] = (int)(bool) $form_values['test_is_enabled'];

                $dateTime = new DateTime();
                $start_time = $dateTime::createFromFormat("d.m.Y H:i", $form_values['test_start_time']);
                $form_values['test_start_time'] = $start_time->getTimestamp();

                $stop_time = $dateTime::createFromFormat("d.m.Y H:i", $form_values['test_stop_time']);
                $form_values['test_stop_time'] = $stop_time->getTimestamp();

                $form_values['test_time'] = (int) $form_values['test_time'];
                $form_values['test_time'] = $form_values['test_time'] * 60;

                $form_values['test_qst_show_cnt'] = (int) $form_values['test_qst_show_cnt'];

                $form_values['test_is_mix_qst'] = (int)(bool) $form_values['test_is_mix_qst'];

                $form_values['test_is_mix_ans'] = (int)(bool) $form_values['test_is_mix_ans'];

                $form_values['test_is_show_answers'] = (int)(bool) $form_values['test_is_show_answers'];

                $form_values['test_qst_per_page'] = (int)(bool) $form_values['test_qst_per_page'];

                $form_values['test_id'] = (int) $form_values['test_id'];

                $row = array(
                    'test_is_enabled' => $form_values['test_is_enabled'],
                    'test_start_time' => $form_values['test_start_time'],
                    'test_stop_time' => $form_values['test_stop_time'],
                    'test_time' => $form_values['test_time'],
                    'test_title' => $form_values['test_title'],
                    'test_desc' => $form_values['test_desc'],
                    'test_qst_show_cnt' => $form_values['test_qst_show_cnt'],
                    'test_is_mix_qst' => $form_values['test_is_mix_qst'],
                    'test_is_mix_ans' => $form_values['test_is_mix_ans'],
                    'test_is_show_answers' => $form_values['test_is_show_answers'],
                    'test_qst_per_page' => $form_values['test_qst_per_page']
                );
                Default_Model_Test::updateTest($form_values['test_id'], $row);
            }
            $this->redirect('/admin/tests');
            return;
        } else {
            $this->view->form = $form;

            // If it's a POST request but validation failed, populate with posted data
            if ($this->_request->isPost()) {
                $this->view->form->populate($this->_request->getPost());
            } else {
                // Otherwise, populate with original database data
                $id = (int) $this->_request->getParam('id', 0);
                if ($id > 0) {
                    $values = Default_Model_Test::findTestById($id);
                    $values['test_start_time'] = !empty($values['test_start_time']) ? date("d.m.Y H:i", (int) $values['test_start_time']) : '';
                    $values['test_stop_time'] = !empty($values['test_stop_time']) ? date("d.m.Y H:i", (int) $values['test_stop_time']) : '';
                    $values['test_time'] = $values['test_time'] / 60;
                    $this->view->form->populate($values);
                }
            }
        }

        $this->view->title = 'Test Properties';
        $this->render();
    }

    public function deleteAction()
    {
        $test_id = (int) $this->_request->getParam('id', 0);
        Default_Model_Test::deleteTest($test_id);

        // return to test list
        $this->redirect('/admin/tests');
    }

    public function __call($method, $args)
    {
        $this->forward('error404', 'errors');
    }
}
