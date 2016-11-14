<?php

namespace App;


class Functions
{
    public static function splitIds($idsString, $delimiter = ',')
    {
        $ids = explode($delimiter, $idsString);
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        return $ids;
    }
}