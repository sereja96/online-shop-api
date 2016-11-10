<?php

namespace App;

interface CommonScopes
{
    public function scopeNotDeleted($query);
    public function scopeDeleted($query);
    public function scopeEnabled($query);
    public function scopeNotEnabled($query);
    public function scopeMy($query);
    public function scopeSearch($query, $search);
    public function scopeWhereInIds($query, $ids);
    public function scopeWithAll($query);

    public function updateDeleted($isDeleted);
}
