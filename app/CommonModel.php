<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CommonModel extends Model implements CommonScopes
{

    public static function splitIds($idsString, $delimiter = ',')
    {
        $ids = explode($delimiter, $idsString);
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        return $ids;
    }

    public function updateDeleted($isDeleted)
    {
        $this->is_deleted = $isDeleted;
        return $this->saveOrFail();
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopeDeleted($query)
    {
        return $query->where('id_deleted', true);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enable', true);
    }

    public function scopeNotEnabled($query)
    {
        return $query->where('is_enable', false);
    }

    public function scopeMy($query)
    {
        return $query->where('user_id', User::myId());
    }

    public function scopeSearch($query, $search)
    {
        return $search
            ? $query->where('name', 'LIKE', $search.'%')
            : $query;
    }

    public function scopeWhereInIds($query, $ids)
    {
        return $query->whereIn('id', $ids);
    }

    public function scopeWithAll($query)
    {
        return $query;
    }
}