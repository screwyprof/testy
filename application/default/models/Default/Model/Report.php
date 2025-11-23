<?php

class Default_Model_Report
{
    public static function findResultById($rst_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('results')
            ->from('users', array('usr_firstname', 'usr_lastname', 'usr_thirdname'))
            ->from('tests', 'test_title')
            ->where('rst_id = ?', $rst_id)
            ->where('usr_id = rst_usr_id')
            ->where('test_id = rst_test_id')
            ->order('test_id')
            ->order('usr_id')
            ->order('rst_id');
        return $db->fetchRow($select);
    }

    public static function findAllEndResults($order = array())
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();

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

        return $db->fetchAll($select);
    }

    public static function deleteResult($rst_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');
        $where = $db->quoteInto('rst_id = ?', $rst_id);
        $db->delete('results_answers', $where);
        $db->delete('results', $where);
    }

    public static function findResultsIdByTest($test_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');

        $select = $db->select();
        $select->from('results', 'rst_id')
            ->where('rst_test_id = ?', $test_id)
            ->order('rst_id');
        return $db->fetchCol($select);
    }
}
