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
        return $ids == null
            ? $query
            : $query->whereIn($this->getIdColumn(), $ids);
    }
}
