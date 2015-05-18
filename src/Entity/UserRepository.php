<?php

namespace Pool\Entity;

use Predis\Client;

/**
 * Class UserRepository.
 */
class UserRepository
{
    const USER_HASH_STORE = 'users';
    const EMAIL2USER_HASH_STORE = 'email2user';

    /**
     * @var Client
     */
    protected $redisClient;

    /**
     * @param Client $redisClient
     */
    public function __construct(Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     * @param User $user
     */
    public function persist(User $user)
    {
        $id = $user->getId();
        if (!$id) {
            $id = $this->redisClient->incr(self::USER_HASH_STORE.'_id');
            $user->setId($id);
        }
        $this->redisClient->hset(self::EMAIL2USER_HASH_STORE, $user->getEmail(), $id);
        $this->redisClient->hset(self::USER_HASH_STORE, $id, serialize($user));
    }

    /**
     * @param User $user
     */
    public function remove(User $user)
    {
        $id = $user->getId();
        $this->redisClient->hdel(self::USER_HASH_STORE, $id);
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function find($id)
    {
        $user = $this->redisClient->hget(self::USER_HASH_STORE, $id);

        return empty($user) ? null : unserialize($user);
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail($email)
    {
        $id = $this->redisClient->hget(self::EMAIL2USER_HASH_STORE, strtolower(trim($email)));
        if (!$id) {
            return null;
        }
        return $this->find($id);
    }

    /**
     * @return array|null
     */
    public function getAll()
    {
        $users = $this->redisClient->hgetall(self::USER_HASH_STORE);

        if ($users && is_array($users)) {
            $result = [];
            foreach ($users as $user) {
                $result[] = unserialize($user);
            }

            return $result;
        }

        return;
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
