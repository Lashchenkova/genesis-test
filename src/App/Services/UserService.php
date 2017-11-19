<?php

namespace App\Services;

/**
 * Class UserService
 * @package App\Services
 */
class UserService extends BaseService
{
    /**
     * Find row by id
     *
     * @param $id
     * @return array
     */
    public function getOne($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM users WHERE id = ? ', [(int)$id]);
    }

    /**
     * Find row by id
     *
     * @param string $query
     * @return array
     */
    public function search(string $query)
    {
        return $this->db->fetchAll(
            'SELECT * FROM users WHERE username LIKE ? OR firstname LIKE ? OR lastname LIKE ? OR age LIKE ?',
            ["%" . $query . "%", "%" . $query . "%", "%" . $query . "%", "%" . $query . "%"]
        );
    }

    /**
     * Find row by id
     *
     * @param string $username
     * @return array
     */
    public function checkUnique(string $username)
    {
        return $this->db->fetchAssoc(
            'SELECT * FROM users WHERE users.username = ?', [$username]
        );
    }

    /**
     * Store a new user
     *
     * @param array $user
     * @return bool
     */
    public function save($user)
    {
        $existingUser = $this->checkUnique($user['username']);

        if($existingUser){
            return false;
        }

        $this->db->insert('users', $user);

        return true;
    }

    /**
     * Get all rows
     *
     * @return array
     */
    public function getAll() : array
    {
        return $this->db->fetchAll('SELECT * FROM users ORDER BY id');
    }

}
