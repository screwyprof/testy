<?php

class Admin_ReportsController extends Zend_Controller_Action
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
        $order = array(
            'test_title',
            'usr_lastname',
            'usr_firstname',
            'rst_points DESC',
            'rst_mark DESC',
            'rst_time_spent',
            'rst_is_time_exceeded',
            'rst_id'
        );
        $results = Default_Model_Report::findAllEndResults($order);

        $this->view->title   = 'Reports';
        $this->view->results =  $results;
        $this->render();
    }

    public function deleteAction()
    {
        if ($this->_request->isPost()) {
            $filter = new Zend_Filter_Alpha();
            $id  = (int)$this->_request->getPost('id', 0);
            $del = $filter->filter($this->_request->getPost('del'));
            if ($del == 'Yes') {
                Default_Model_Report::deleteResult($id);
            }
        } else {
            $id = (int)$this->_request->getParam('id', 0);
            $this->view->result = Default_Model_Report::findResultById($id);
            $this->view->title = 'Delete Result';
            $this->render();
            return;
        }

        // return to results list
        $this->redirect('/admin/reports');
    }

    public function resultsAction()
    {
        // Get test result identifier
        $rst_id = (int) $this->_request->getParam('id', 0);

        // Get test result information
        $result_info = Default_Model_Report::findResultById($rst_id);
        if (is_null($result_info)) {
            throw new Exception('Test result not found in database!');
        }

        // Get all answers for this test result
        $results = Default_Model_Test::findResultAnswers($rst_id);
        for ($i = 0; $i < sizeof($results); $i++) {
            $question = Default_Model_Question::findQuestionById($results[$i]['qst_id']);
            $results[$i]['question'] = $question['qst_text'];
        }

        $this->view->title      = 'Test Results: ' . $result_info['test_title'];
        $this->view->result     = $result_info['rst_points'];
        $this->view->mark       = $result_info['rst_mark'];
        $this->view->results    = $results;
        $this->view->test_title = $result_info['test_title'];
        $this->view->start_time = date('d.m.Y, H:i:s', $result_info['rst_start_time']);
        $this->view->time_spent = date('H:i:s', $result_info['rst_time_spent']);
        $this->view->qst_per_page = 0; // Show time for each question
        $this->view->rst_id = $rst_id; // For detail links
        $this->render();
    }

    public function detailsAction()
    {
        // Get answer identifier
        $id = (int) $this->_request->getParam('id', 0);

        $result = Default_Model_Test::findResultById($id);
        if (is_null($result)) {
            throw new Exception('Result not found in database!');
        }

        $question = Default_Model_Question::findQuestionById($result['qst_id']);
        if (is_null($question)) {
            throw new Exception('Question not found in database!');
        }

        $variants = Default_Model_Question::findAnswersByQuestionId($result['qst_id']);
        if (sizeof($variants) < 1) {
            throw new Exception('Question has no answer options!');
        }

        $vr_order = array();
        $vr_order = explode(',', $result['ans_vr_order']);

        $tmp = array();
        foreach ($vr_order as $vo) {
            $tmp[] = $variants[$vo - 1];
        }
        $variants = $tmp;

        $ans_correct = array();
        $ans_correct = @explode(',', $result['ans_correct']);

        $ans_user = array();
        $ans_user = @explode(',', $result['ans_answer']);

        $answers = array();
        $usransw = array();

        switch ($question['qst_type']) {
            case 1:
                $answers = $ans_correct;
                $usransw = $ans_user;
                break;
            case 2:
            case 5:

                foreach ($ans_correct as $ac) {
                    foreach ($variants as $variant) {
                        if ($ac == $variant['ans_is_correct']) {
                            $answers[] = $variant;
                        }
                    }
                }

                foreach ($answers as $i => $variant) {
                    foreach ($ans_user as $j => $au) {
                        if ($i == $j) {
                            $usransw[$i] = $variant;
                            $usransw[$i]['ans_is_correct'] = $au;
                        }
                    }
                }
                break;
            case 3:
            case 4:
            default:
                $answers = $variants;

                foreach ($ans_user as $au) {
                    foreach ($variants as $vo => $variant) {
                        if ($au == $vo + 1) {
                            $usransw[] = $variant;
                        }
                    }
                }
                break;
        }

        $this->view->title     = 'View Question Answers';
        $this->view->question  = $question;
        $this->view->variants  = $answers;
        $this->view->usransw   = $usransw;
        $this->view->percents  = $result['ans_percents'];
        $this->view->timespent = $result['ans_timespent'];
        $this->view->result    = $result; // Pass the full result to get rst_id for back navigation
        $this->render();
    }

    public function __call($method, $args)
    {
        $this->forward('error404', 'errors');
    }
}
