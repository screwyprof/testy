<?php

class Default_Model_Test
{
    public static function findTestById($test_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('tests')
            ->where('test_id = ?', $test_id)
            ->order('test_id');

        return $db->fetchRow($select);
    }

    public static function findAllTests()
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('tests')
            ->order('test_id');

        return $db->fetchAll($select);
    }

    public static function findAllActiveTests()
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('tests')
            ->where('test_is_enabled = ?', 1)
            ->where('test_start_time <= ?', time())
            ->where('test_stop_time > ?', time())
            ->order('test_id');

        return $db->fetchAll($select);
    }

    public static function addTest(array $row)
    {
        $db = Zend_Registry::get('dbAdapter');
        $rows_affected = $db->insert('tests', $row);
        return $rows_affected;
    }

    public static function updateTest($test_id, array $row)
    {
        $db = Zend_Registry::get('dbAdapter');
        $where = $db->quoteInto('test_id = ?', $test_id);
        $rows_affected = $db->update('tests', $row, $where);
        return $rows_affected;
    }

    public static function deleteTest($test_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');
        $where_test = $db->quoteInto('test_id = ?', $test_id);

        $questions = self::findAllQuestionsIdByTest($test_id);
        foreach ($questions as $qst_id) {
            Default_Model_Question::deleteQuestion($qst_id);
        }

        $results = Default_Model_Report::findResultsIdByTest($test_id);
        foreach ($results as $rst_id) {
            Default_Model_Report::deleteResult($rst_id);
        }

        $db->delete('tests', $where_test);
    }

    public static function findAllQuestionsIdByTest($test_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('questions', 'qst_id')
            ->where('qst_test_id = ?', $test_id)
            ->order('qst_id');

        return $db->fetchCol($select);
    }

    public static function findQuestionsIdByTest($test_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('questions', 'qst_id')
            ->where('qst_test_id = ?', $test_id)
            ->where('qst_is_enabled = ?', 1)
            ->order('qst_id');

        return $db->fetchCol($select);
    }

    public static function findAnswersIdByQuestion($question_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('answers', 'ans_id')
            ->where('ans_qst_id = ?', $question_id)
            ->order('ans_id');

        return $db->fetchCol($select);
    }

    public static function findResultAnswers($rst_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('results_answers')
            ->where('rst_id = ?', $rst_id)
            ->order('id');

        return $db->fetchAll($select);
    }

    public static function findResultById($id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('results_answers')
            ->where('id = ?', $id);

        return $db->fetchRow($select);
    }

    public static function calcResult($rst_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('results_answers', 'SUM(ans_percents)')
            ->where('rst_id = ?', $rst_id);

        return $db->fetchOne($select);
    }

    public static function generate($test_id = 0)
    {
        // Создаем окружение для данных тестирования
        $_TEST = new Zend_Session_Namespace('TEST');

        // Запрашиваем информацию о тесте
        $test = Default_Model_Test::findTestById($test_id);
        $curr_time = time();

        // Проверям наличие теста в базе данных
        if (is_null($test)) {
            throw new Exception('Тест не найден в базе данных!');
        }

        // Проверяем "доступность" теста
        if (!$test['test_is_enabled']) {
            throw new Exception('Данный тест не активен!');
        }

        if ($test['test_start_time'] > $curr_time) {
            throw new Exception('Еще не пришло время сдавать данный тест!');
        }

        if ($test['test_stop_time'] < $curr_time) {
            throw new Exception('Срок действия данного теста стек!');
        }

        // Получаем список вопросов, выбранного теста
        $questions = Default_Model_Test::findQuestionsIdByTest($test_id);
        $qst_count = sizeof($questions);
        if ($qst_count < 1) {
            throw new Exception('В тесте нет доступных вопросов!');
        }

        // Перемешиваем вопросы
        if ($test['test_is_mix_qst']) {
            shuffle($questions);
        }

        $test_showed = (int) $test['test_qst_show_cnt'];

        if ($test_showed < 0) {
            $test_showed = 0;
        }

        if ($test_showed > $qst_count) {
            $test_showed = $qst_count;
        }

        if ($test_showed > 0) {
            array_splice($questions, $test_showed);
        }

        if ($test_showed == 0) {
            $test_showed = $qst_count;
        }

        $test_time = (int) $test['test_time'];

        if ($test_time < 0) {
            $test_time = 0;
        }

        $stop_time = 0;
        if ($test_time > 0) {
            $stop_time = $test_time + $curr_time;
        }

        $usr_id = Zend_Auth::getInstance()->getIdentity()->usr_id;
        $db = Zend_Registry::get('dbAdapter');

        // массив данных для подстановки в формате 'имя столбца' => 'значение'
        $row = array(
            'rst_test_id'    => $test_id,
            'rst_usr_id'     => $usr_id,
            'rst_start_time' => $curr_time,
        );

        // вставка строки и получение ID строки
        $db->insert('results', $row);
        $rst_id = $db->lastInsertId();

        // Удаляем данные предыдущего теста
        $_TEST->unsetAll();
        $_TEST->test_id      = $test_id;
        $_TEST->rst_id       = $rst_id;
        $_TEST->start_time   = $curr_time;
        $_TEST->stop_time    = $stop_time;
        $_TEST->test_time    = $test_time;
        $_TEST->qst_count    = $test_showed;
        $_TEST->qst_curr     = 0;
        $_TEST->questions    = $questions;
        $_TEST->variants     = array();
        $_TEST->vr_order     = array();
        $_TEST->is_mix_ans   = (bool) $test['test_is_mix_ans'];
        $_TEST->is_show_ans  = (bool) $test['test_is_show_answers'];
        $_TEST->qst_per_page = (int) $test['test_qst_per_page'];
    }

    public static function getMarkByPercents($percents = 0)
    {
        $mark = 0;
        if ($percents >= 90) {
            $mark = 5;
        } elseif ($percents >= 70) {
            $mark = 4;
        } elseif ($percents >= 50) {
            $mark = 3;
        } elseif ($percents >= 40) {
            $mark = 2;
        } else {
            $mark = 1;
        }

        return $mark;
    }
}
