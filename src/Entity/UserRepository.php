<?php

namespace Pool\Entity;

use Predis\Client;
use Predis\Transaction\MultiExec;

/**
 * Class UserRepository.
 */
class UserRepository
{
    const USER_HASH_STORE = 'users';

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
        $this->redisClient->transaction(
            function (MultiExec $tx) use ($user) {
                $id = $user->getId();
                $tx->hset(self::USER_HASH_STORE, $id, serialize($user));
            }
        );
    }

    /**
     * @param User $user
     */
    public function remove(User $user)
    {
        $this->redisClient->transaction(
            function (MultiExec $tx) use ($user) {
                $id = $user->getId();
                $tx->hdel(self::USER_HASH_STORE, $id);
            }
        );
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function find($email)
    {
        $user = $this->redisClient->hget(self::USER_HASH_STORE, strtolower(trim($email)));

        return empty($user) ? null : unserialize($user);
    }

	/**
     *
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
		return null;
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
