<?php

namespace Pool\Entity;

use Predis\Client;
use Predis\Transaction\MultiExec;

/**
 * Class TeamRepository.
 */
class TeamRepository
{
    const TEAM_HASH_STORE = 'teams';

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
     * @param Team
     */
    public function persist(Team $team)
    {
        $this->redisClient->transaction(
            function (MultiExec $tx) use ($team) {
                $id = $team->getId();
                $tx->hset(self::TEAM_HASH_STORE, $id, serialize($team));
            }
        );
    }

    /**
     * @param Team
     */
    public function remove(Team $team)
    {
		$id = $team->getId();
		$tx->hdel(self::TEAM_HASH_STORE, $id);

    }

    /**
     * @param string $email
     *
     * @return Team|null
     */
    public function find($teamId)
    {
        $team = $this->redisClient->hget(self::TEAM_HASH_STORE, strtolower(trim($teamId)));

        return empty($team) ? null : unserialize($team);
    }

	/**
     *
     * @return array|null
     */
    public function getAll()
    {
        $teams = $this->redisClient->hgetall(self::TEAM_HASH_STORE);
        
        if ($teams && is_array($teams)) {
			$result = [];
			foreach ($teams as $team) {
				$result[] = unserialize($team);
			}
			return $result;
		}
		return null;
    }
}

/* vim: set tabstop=4 shiftwidth=4 expandtab: fdm=marker */
