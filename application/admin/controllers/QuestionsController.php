<?php

class Admin_QuestionsController extends Zend_Controller_Action
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
        // Получаем идентификатор теста
        $test_id = (int) $this->_request->getParam('test', 0);

        $this->view->title = 'Вопросы теста';
        $this->view->test_id   = $test_id;
        $this->view->questions = Default_Model_Question::findQuestionsByTest($test_id);
        $this->render();
    }

    public function addAction()
    {
        if ($this->_request->isPost()) {
            $test_id = (int) $this->_request->getPost('test_id', 0);
            if (isset($_POST['submit'])) {
                $filter  = new Zend_Filter_StripTags();

                $qst_is_enabled = $this->_request->getPost('qst_is_enabled');
                $qst_is_enabled = (int)(bool) $qst_is_enabled;

                $qst_type = $this->_request->getPost('qst_type');
                $qst_type = (int) $qst_type;

                $qst_text = $this->_request->getPost('qst_text');
                $qst_text = $filter->filter($qst_text);
                $qst_text = trim($qst_text);

                $variants = $this->_request->getPost('ans_text');
                $correct  = $this->_request->getPost('ans_correct');

                $qst_row = array(
                    'qst_test_id' => $test_id,
                    'qst_is_enabled' => $qst_is_enabled,
                    'qst_type' => $qst_type,
                    'qst_text' => $qst_text,
                );


                $answers = array();
                $tmp = '';
                $key = 0;
                foreach ($variants as $i => $variant) {
                    $tmp = $filter->filter($variant);
                    $tmp = trim($tmp);

                    if (empty($tmp)) {
                        continue;
                    }

                    $answers[$key]['ans_text'] = $tmp;
                    $answers[$key]['ans_is_correct'] = 0;

                    switch ($qst_type) {
                        case 1:
                            $answers[$key]['ans_is_correct'] = 1;
                            break;

                        case 2:
                            $answers[$key]['ans_is_correct'] = $key + 1;
                            break;

                        case 3:
                        case 4:
                            if (isset($correct[$key])) {
                                $answers[$key]['ans_is_correct'] = 1;
                            }
                            break;
                    }
                    $key++;
                }
                Default_Model_Question::addQuestion($test_id, $qst_row, $answers);
            }
            $this->redirect('/admin/questions/index/test/' . $test_id);
            return;
        }
        $test_id = (int) $this->_request->getParam('test', 0);

        $this->view->test_id = $test_id;
        $this->view->title = 'Создание вопроса';
        $this->render();
    }


    public function deleteAction()
    {
        $test_id = (int) $this->_request->getParam('test', 0);
        $qst_id  = (int) $this->_request->getParam('qst', 0);

        // Удаляем вопрос
        Default_Model_Question::deleteQuestion($qst_id);

        // возвращаемся к списку тестов
        $this->redirect('/admin/questions/index/test/' . $test_id);
    }

    public function statAction()
    {
        $test_id = (int) $this->_request->getParam('test', 0);
        $qst_id  = (int) $this->_request->getParam('qst', 0);

        $question = Default_Model_Question::findQuestionById($qst_id);

        $qst_percents  = (int) Default_Model_Question::statGetAdvPercents($qst_id);
        $qst_req_count = (int) Default_Model_Question::statGetReqCount($qst_id);

        if ($qst_req_count < 0) {
            $qst_req_count = 0;
        }

        $qst_adv_percents = 0;
        if ($qst_req_count != 0) {
            $qst_adv_percents = floor($qst_percents / $qst_req_count);
        }

        $question['qst_req_count']    = $qst_req_count;
        $question['qst_adv_percents'] = $qst_adv_percents;


        $answers = Default_Model_Question::findAnswersByQuestionId($qst_id);
        if (sizeof($answers) < 1) {
            throw new Exception('Вопрос не содержит вариантов ответа!');
        }


        $i_answercount = 0;
        //        $i_answers_correct = array();
        $answers_clicks = array();
        $answers_clicks_total = 0;
        $question_views_total = 0;
        $question_correct = 0;
        $question_incorrect = 0;
        $question_partially = 0;

        foreach ($answers as $i => $answer) {
            $answers_clicks[$i] = 0;
        }


        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('results_answers')
            ->where('qst_id = ?', $qst_id)
            ->order('id');

        $rst_ans = $db->fetchAll($select);
        $qst_type = (int) $question['qst_type'];

        switch ($qst_type) {
            case 1:
                foreach ($rst_ans as $ra) {
                    foreach ($answers as $ak => $answer) {
                        if ($answer['ans_is_correct']) {
                            if (trim($answer['ans_text']) == trim($ra['ans_answer'])) {
                                $answers_clicks[$ak]++;
                            }
                        }
                    }
                    switch ((int) $ra['ans_is_correct']) {
                        case 0:
                            $question_incorrect++;
                            break;
                        case 1:
                            $question_correct++;
                            break;
                        case 2:
                            $question_partially++;
                            break;
                    }
                    $answers_clicks_total++;
                    $question_views_total++;
                }
                break;
            case 2:
            case 3:
            case 4:
            case 5:
                foreach ($rst_ans as $ra) {
                    if ($ra['ans_answer']) {
                        $useranswers = array();
                        $useranswers = @explode(',', $ra['ans_answer']);

                        $vr_order = array();
                        $vr_order = explode(',', $ra['ans_vr_order']);

                        $tmp = array();
                        foreach ($useranswers as $useranswer) {
                            if ($useranswer > 0) {
                                $tmp[] = $vr_order[$useranswer - 1];
                            }
                        }
                        $useranswers = $tmp;


                        foreach ($useranswers as $useranswer) {
                            $answers_clicks[(int)$useranswer - 1]++;
                            $answers_clicks_total++;
                        }
                    }

                    switch ((int) $ra['ans_is_correct']) {
                        case 0:
                            $question_incorrect++;
                            break;
                        case 1:
                            $question_correct++;
                            break;
                        case 2:
                            $question_partially++;
                            break;
                    }
                    $question_views_total++;
                }
                break;
        }

        $this->view->title       = 'Статистика по вопросу';
        $this->view->test_id     = $test_id;
        $this->view->question    = $question;
        $this->view->variants    = $answers;
        $this->view->clicks      = $answers_clicks;
        $this->view->total       = $answers_clicks_total;
        $this->view->ans_correct = $question_correct;
        $this->view->ans_incorrect = $question_incorrect;
        $this->view->ans_partially = $question_partially;

        $this->render();
    }


    public function __call($method, $args)
    {
        $this->forward('error404', 'errors');
    }
}
