<?php

class Default_Model_User
{
    public static function findUserById($usr_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');
        $select = $db->select();
        $select->from('users')
            ->where('usr_id = ?', $usr_id);

        return $db->fetchRow($select);
    }

    public static function findAllUsers($limit, $offset = 0)
    {
        $db = Zend_Registry::get('dbAdapter');
        $select = $db->select();
        $select->from('users')
            ->order('usr_id')
            ->limit($limit, $offset);
        return $db->fetchAll($select);
    }

    public static function addUser(array $row)
    {
        $db = Zend_Registry::get('dbAdapter');
        $rows_affected = $db->insert('users', $row);
        return $rows_affected;
    }

    public static function updateUser($usr_id, array $row)
    {
        $db = Zend_Registry::get('dbAdapter');
        $where = $db->quoteInto('usr_id = ?', $usr_id);
        $rows_affected = $db->update('users', $row, $where);
        return $rows_affected;
    }

    public static function deleteUser($usr_id = 0)
    {
        $db = Zend_Registry::get('dbAdapter');
        $where = $db->quoteInto('usr_id = ?', $usr_id);
        $db->delete('users', $where);
    }

    public static function getUsersCount()
    {
        $db = Zend_Registry::get('dbAdapter');
        $select = 'SELECT COUNT(*) FROM users';
        return $db->fetchOne($select);
    }
}
