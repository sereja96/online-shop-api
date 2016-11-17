<?php

namespace App\Common;

trait EnableTrait
{
    protected $enableColumn = 'is_enable';

    /**
     * @return string
     */
    public function getEnableColumn()
    {
        return $this->enableColumn;
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return !!$this->{$this->getEnableColumn()};
    }

    /**
     * @return bool
     */
    public function enable()
    {
        if (!$this->isEnable()) {
            $this->{$this->getEnableColumn()} = true;
            return $this->saveOrFail();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function disable()
    {
        if ($this->isEnable()) {
            $this->{$this->getEnableColumn()} = false;
            return $this->saveOrFail();
        }

        return true;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeEnabled($query)
    {
        return $query->where($this->getEnableColumn(), true);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotEnabled($query)
    {
        return $query->where($this->getEnableColumn(), false);
    }
}
