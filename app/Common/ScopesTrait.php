<?php

namespace App\Common;

use App\Models\User;

trait ScopesTrait
{
    protected $userIdColumn = 'user_id';

    protected $idColumn = 'id';

    /**
     * @return string
     */
    public function getIdColumn()
    {
        return $this->idColumn;
    }

    /**
     * @return string
     */
    public function getUserIdColumn()
    {
        return $this->userIdColumn;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeMy($query)
    {
        return $query->where($this->getUserIdColumn(), User::myId());
    }

    /**
     * @param $query
     * @param $ids
     * @return mixed
     */
    public function scopeWhereInIds($query, $ids)
    {
        if (!$ids) {
            return $query;
        }

        if (is_numeric($ids)) {
            $ids = [$ids];
        }

        return $query->whereIn($this->getIdColumn(), $ids);
    }

    /**
     * @param $query
     * @param int $count
     * @return mixed
     */
    public function scopeTakeCount($query, $count = 0)
    {
        return $count > 0
            ? $query->take($count)
            : $query;
    }
}
