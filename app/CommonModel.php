<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CommonModel extends Model
{

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

    /**
     * @param string $idsString
     * @param string $delimiter
     * @return array
     */
    public static function splitIds(string $idsString, string $delimiter = ',')
    {
        $ids = explode($delimiter, $idsString);
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        return $ids;
    }
}