<?php

class Tests_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->user = Zend_Auth::getInstance()->getIdentity();

        set_include_path(PATH_SEPARATOR . './application/tests/models/'
            . PATH_SEPARATOR . get_include_path());

        $this->view->setScriptPath('./application/default/views/scripts')
            ->addScriptPath('./application/tests/views/scripts');
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
        $this->view->title = 'Select Test';
        $this->view->tests = Default_Model_Test::findAllActiveTests();
        $this->render();
    }

    public function test1Action()
    {
        // Get test data
        $_TEST = new Zend_Session_Namespace('TEST');

        if ($this->_request->isPost()) {

            for ($qst_curr = 0; $qst_curr < $_TEST->qst_count; $qst_curr++) {
                $answers = null;
                if (isset($_POST['v'][$qst_curr])) {
                    $answers =  $_POST['v'][$qst_curr];
                }

                $qst_id = $_TEST->questions[$qst_curr];
                $row = Default_Model_Question::checkAnswers($qst_id, $qst_curr, $answers);


                // Zend_Debug::dump($row);
                $db = Zend_Registry::get('dbAdapter');
                // insert row
                $db->insert('results_answers', $row);
            }
            // Go to results page
            $this->_redirect('/tests/index/results');
            return;
        }

        /*
        // If test time is limited
        if ($_TEST->test_time > 0) {
        // If test time has expired
        if($_TEST->stop_time < time()) {
        // go to results page
        $this->_forward('results');
        return;
        }
        }
        */

        $questions = array();
        $variants = array();

        for ($qst_curr = 0; $qst_curr < $_TEST->qst_count; $qst_curr++) {

            $_TEST->qst_id = $_TEST->questions[$qst_curr];

            $question = Default_Model_Question::findQuestionById($_TEST->qst_id);
            if (is_null($question)) {
                throw new Exception('Question not found in database!');
            }

            $answers = Default_Model_Question::findAnswersByQuestionId($_TEST->qst_id);
            if (sizeof($answers) < 1) {
                throw new Exception('Question has no answer options!');
            }

            $vr_order = array();
            for ($i = 0; $i < sizeof($answers); $i++) {
                $vr_order[$i] = $i + 1;
            }

            if ($_TEST->is_mix_ans) {
                shuffle($vr_order);
            }

            $tmp = array();
            foreach ($vr_order as $vo) {
                $tmp[] = $answers[$vo - 1];
            }

            $_TEST->vr_order[$qst_curr] = $vr_order;
            $questions[$qst_curr] = $question;
            $variants[$qst_curr] = $tmp;
        }

        $this->view->title       = 'Taking Test';
        $this->view->test_time   = $_TEST->test_time;
        $this->view->qst_curr    = $_TEST->qst_curr;
        $this->view->qst_count   = $_TEST->qst_count;
        $this->view->question    = $questions;
        $this->view->variants    = $variants;
        $this->view->is_show_ans = $_TEST->is_show_ans;
    }

    public function testAction()
    {
        // Get test data
        $_TEST = new Zend_Session_Namespace('TEST');

        if (isset($_TEST->qst_id)) {

            $answers = null;
            if (isset($_POST['v'][$_TEST->qst_curr])) {
                $answers = $_POST['v'][$_TEST->qst_curr];
            }

            //if (! is_null($answers)) {
            $db = Zend_Registry::get('dbAdapter');
            $row = Default_Model_Question::checkAnswers($_TEST->qst_id, $_TEST->qst_curr, $answers);

            // save answer to database
            $db->insert('results_answers', $row);

            unset($_TEST->qst_id);
            unset($_TEST->qst_start);
            ++$_TEST->qst_curr;

            $this->_redirect('/tests/index/test');
            return;
            //}
        }

        /*
        // If test time is limited
        if ($_TEST->test_time > 0) {
        // If test time has expired
        if($_TEST->stop_time < time()) {
        // go to results page
        $this->_redirect('/tests/index/results');
        return;
        }
        }
        */

        // If no more questions left
        if ($_TEST->qst_curr >= $_TEST->qst_count) {
            // then go to results page
            $this->_redirect('/tests/index/results');
            return;
        }

        if (!isset($_TEST->qst_id)) {
            $_TEST->qst_id    = $_TEST->questions[$_TEST->qst_curr];
            $_TEST->qst_start = time();
        }

        $question = Default_Model_Question::findQuestionById($_TEST->qst_id);
        if (is_null($question)) {
            throw new Exception('Question not found in database!');
        }

        $variants = Default_Model_Question::findAnswersByQuestionId($_TEST->qst_id);
        if (sizeof($variants) < 1) {
            throw new Exception('Question has no answer options!');
        }

        $vr_order = array();
        for ($i = 0; $i < sizeof($variants); $i++) {
            $vr_order[$i] = $i + 1;
        }

        if ($_TEST->is_mix_ans) {
            shuffle($vr_order);
        }

        $tmp = array();
        foreach ($vr_order as $vo) {
            $tmp[] = $variants[$vo - 1];
        }

        $variants = $tmp;

        $_TEST->vr_order[$_TEST->qst_curr] = $vr_order;

        $this->view->title       = 'Taking Test';
        $this->view->qst_curr    = $_TEST->qst_curr;
        $this->view->question    = $question;
        $this->view->variants    = $variants;
        $this->view->is_show_ans = $_TEST->is_show_ans;

        $this->render();
    }

    public function resultsAction()
    {
        // Get test data
        $_TEST = new Zend_Session_Namespace('TEST');

        if (!isset($_TEST->test_id)) {
            $this->_redirect('/tests');
            return;
        }

        $result = Default_Model_Test::calcResult($_TEST->rst_id);
        @$result /= $_TEST->qst_count;

        $result = round($result);
        if ($result < 0) {
            $result = 0;
        }

        if ($result > 100) {
            $result = 100;
        }

        $mark          = Default_Model_Test::getMarkByPercents($result);
        $curr_time     = time();
        $time_spent    = $curr_time - $_TEST->start_time;
        $time_ecxeeded = 0;

        if ($_TEST->stop_time > 0 && $_TEST->stop_time < $curr_time) {
            $time_ecxeeded = 1;
        }

        $rows = array(
            'rst_stop_time' => $curr_time,
            'rst_time_spent' => $time_spent,
            'rst_is_time_exceeded' => $time_ecxeeded,
            'rst_points' => $result,
            'rst_mark' => $mark,
        );

        $db = Zend_Registry::get('dbAdapter');

        $where = $db->quoteInto('rst_id = ?', $_TEST->rst_id);
        $db->update('results', $rows, $where);

        $results = Default_Model_Test::findResultAnswers($_TEST->rst_id);
        for ($i = 0; $i < sizeof($results); $i++) {
            $question = Default_Model_Question::findQuestionById($results[$i]['qst_id']);
            $results[$i]['question'] = $question['qst_text'];
        }

        $test = Default_Model_Test::findTestById($_TEST->test_id);

        $start_time = $_TEST->start_time;
        $test_title = $test['test_title'];

        $qst_per_page = $_TEST->qst_per_page;

        // Delete test data
        $_TEST->unsetAll();

        $this->view->title      = 'Test Results';
        $this->view->result     = $result;
        $this->view->mark       = $mark;
        $this->view->results    = $results;
        $this->view->test_title = $test_title;
        $this->view->start_time = date('d.m.Y, H:i:s', $start_time);
        $this->view->time_spent = date('H:i:s', $time_spent);
        $this->view->qst_per_page = $qst_per_page;
        $this->render();
    }

    public function showAction()
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
        $this->render();
    }

    public function startAction()
    {
        // Get test data
        $_TEST = new Zend_Session_Namespace('TEST');

        // If session doesn't exist
        if (!isset($_TEST->test_id)) {

            // Get test identifier
            $test_id = (int) $this->_request->getParam('id', 0);
            if ($test_id < 1) {
                throw new Exception('You have not selected any test!');
            }

            // Generate session data
            Default_Model_Test::generate($test_id);
        }

        if ($_TEST->qst_per_page > 0) {
            $this->_redirect('/tests/index/test1');
        } else {
            $this->_redirect('/tests/index/test');
        }
    }

    public function __call($method, $args)
    {
        $this->_forward('error404', 'errors');
    }
}
