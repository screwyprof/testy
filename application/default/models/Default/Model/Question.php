<?php

class Default_Model_Question
{
    public static function findQuestionsByTest($test_id = 0, array $order = array())
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        /*
        $rst_fields = array(
        'rst_id',
        'rst_start_time',
        'rst_time_spent',
        'rst_points' => 'round(rst_points)',
        'rst_mark',
        'rst_is_time_exceeded'
        );

        $select->from('results', $rst_fields)
        ->from('users', array('usr_firstname', 'usr_lastname', 'usr_thirdname'))
        ->from('tests', 'test_title')
        ->where('usr_id = rst_usr_id')
        ->where('test_id = rst_test_id')
        ->where('rst_stop_time > 0')
        ->order($order);
        */

        $select->from('questions')
            ->where('qst_test_id = ?', $test_id);
        return $db->fetchAll($select);
    }


    public static function findQuestionById($qst_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('questions')
            ->where('qst_id = ?', $qst_id);

        return $db->fetchRow($select);
    }

    public static function findAnswersByQuestionId($qst_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('answers')
            ->where('ans_qst_id = ?', $qst_id)
            ->order('ans_id');

        return $db->fetchAll($select);
    }

    public static function addQuestion($test_id = 0, array $question, array $variants)
    {
        $db = Zend_Registry::get('dbAdapter');

        $db->insert('questions', $question);
        $qst_id = $db->lastInsertId();

        foreach ($variants as $variant) {
            $variant['ans_qst_id'] = $qst_id;
            $db->insert('answers', $variant);
        }
    }

    public static function deleteQuestion($qst_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');
        $where_ans = $db->quoteInto('ans_qst_id = ?', $qst_id);
        $where_qst = $db->quoteInto('qst_id = ?', $qst_id);

        $db->delete('answers', $where_ans);
        $db->delete('questions', $where_qst);
    }

    public static function checkAnswers($qst_id, $qst_curr, $useranswers)
    {
        // Получаем данные теста
        $_TEST = new Zend_Session_Namespace('TEST');

        if (is_null($useranswers)) {
            $useranswers = array();
        }

        if (!is_array($useranswers)) {
            $useranswers = array($useranswers);
        }

        $question = Default_Model_Question::findQuestionById($qst_id);
        if (is_null($question)) {
            throw new Exception('Вопрос не найден в базе данных!');
        }

        $variants = Default_Model_Question::findAnswersByQuestionId($qst_id);
        if (sizeof($variants) < 1) {
            throw new Exception('Вопрос не содержит вариантов ответа!');
        }

        $vr_order = $_TEST->vr_order[$qst_curr];
        $tmp = array();
        foreach ($vr_order as $vo) {
            $tmp[] = $variants[$vo - 1];
        }
        $variants = $tmp;

        $answers_correct = array();
        $answers_percent = 0;
        switch ((int) $question['qst_type']) {
            case 1:
                foreach ($variants as $variant) {
                    if ($variant['ans_is_correct']) {
                        $answers_correct[] = $variant['ans_text'];
                    }
                }

                foreach ($answers_correct as $ac) {
                    if (trim($ac) == @trim($useranswers[0])) {
                        $answers_percent = 100;
                    }
                }
                break;
            case 2:
                foreach ($variants as $variant) {
                    $answers_correct[] = $variant['ans_is_correct'];
                }

                foreach ($useranswers as $i => $var) {
                    if ((int) $var == (int) $answers_correct[$i]) {
                        $answers_percent = 100;
                    } else {
                        $answers_percent = 0;
                        break;
                    }
                }
                break;
            case 3:
                foreach ($variants as $vc => $variant) {
                    if ($variant['ans_is_correct']) {
                        $answers_correct[] = $vc + 1;
                    }
                }

                foreach ($answers_correct as $ac) {
                    if ($ac == (int) @$useranswers[0]) {
                        $answers_percent = 100;
                    }
                }
                break;
            case 4:
                foreach ($variants as $vc => $variant) {
                    if ($variant['ans_is_correct']) {
                        $answers_correct[] = $vc + 1;
                    }
                }

                if (sizeof($useranswers) <= sizeof($answers_correct)) {
                    foreach ($useranswers as $var) {
                        if (in_array((int) $var, $answers_correct)) {
                            $answers_percent += 1 / sizeof($answers_correct) * 100;
                        } else {
                            $answers_percent = 0;
                            break;
                        }
                    }
                }
                break;
            case 5:

                for ($i = 1; $i <= sizeof($variants); $i++) {
                    foreach ($variants as $j => $variant) {
                        if ($variant['ans_is_correct'] == $i) {
                            $answers_correct[] =  $j + 1;
                        }
                    }
                }

                foreach ($useranswers as $i => $ua) {
                    if ($answers_correct[$i] == (int) $ua) {
                        $answers_percent = 100;
                    } else {
                        $answers_percent = 0;
                        break;
                    }
                }

                // Zend_Debug::dump($variants);
                // Zend_Debug::dump($answers_correct);
                // Zend_Debug::dump($useranswers);
                // Zend_Debug::dump($answers_percent);

                break;
        }

        if ($answers_percent > 100) {
            $answers_percent = 100;
        }

        if ($answers_percent < 0) {
            $answers_percent = 0;
        }

        $answers_percent = round($answers_percent);
        $timespent = ($_TEST->qst_start > 0) ? time() - $_TEST->qst_start : 0;

        $ans_is_correct = 0;
        if ($answers_percent == 100) {
            $ans_is_correct = 1;
        } elseif ($answers_percent > 0 && $answers_percent < 100) {
            $ans_is_correct = 2;
        }

        $retval = array();
        $retval['rst_id']         = $_TEST->rst_id;
        $retval['qst_id']         = $qst_id;
        $retval['ans_vr_order']   = implode(',', $vr_order);
        $retval['ans_correct']    = implode(',', $answers_correct);
        $retval['ans_answer']     = implode(',', $useranswers);
        $retval['ans_percents']   = $answers_percent;
        $retval['ans_is_correct'] = $ans_is_correct;
        $retval['ans_timespent']  = $timespent;
        $retval['ans_is_time_exceeded'] = 0;
        return $retval;
    }

    public static function statGetAdvPercents($qst_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('results_answers', 'SUM(ans_percents)')
            ->where('qst_id = ?', $qst_id);

        return $db->fetchOne($select);
    }

    public static function statGetReqCount($qst_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('results_answers', 'COUNT(*)')
            ->where('qst_id = ?', $qst_id);

        return $db->fetchOne($select);
    }
}
