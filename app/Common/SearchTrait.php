<?php

namespace App\Common;


trait SearchTrait
{

    protected $defaultSearchField = 'name';

    /**
     * @return string
     */
    public function getSearchField()
    {
        return isset($this->searchField)
            ? $this->searchField
            : $this->defaultSearchField;
    }

    /**
     * @param $query
     * @param null $search
     * @param int $take
     * @return mixed
     */
    public function scopeSearch($query, $search = null, $take = 15)
    {
        return $search
            ? $query->where($this->getSearchField(), 'LIKE', $search.'%')
                ->take($take)
            : $query->take($take);
    }

}
