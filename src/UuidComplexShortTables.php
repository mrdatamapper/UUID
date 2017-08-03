<?php

namespace Akademiano\UUID;

use Carbon\Carbon;

class UuidComplexShortTables extends UuidComplexShort
{
    protected $epoch = 1451317149374;

    protected $value;
    /** @var  \DateTime */
    protected $date;
    protected $shard;
    protected $table;
    protected $id;


    /**
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param integer|string $value
     */
    public function setValue($value)
    {
        $this->date = null;
        $this->shard = null;
        $this->id = null;
        $this->table = null;
        $this->value = (integer)$value;
    }

    /**
     * @return mixed
     */
    public function getEpoch()
    {
        return $this->epoch;
    }

    /**
     * @param mixed $epoch
     */
    public function setEpoch($epoch)
    {
        $this->epoch = $epoch;
    }

    /**
     * @return Carbon
     */
    public function getDate()
    {
        if (null === $this->date) {
            $epoch = $this->getEpoch();
            $uuid = $this->getValue();
            $timestamp = $uuid >> 23;
            $timestamp = round(($timestamp + $epoch) / 1000);
            $date = Carbon::createFromTimestampUTC($timestamp);
            $this->date = $date;
        }
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getShard()
    {
        if (null === $this->shard) {
            $uuid = $this->getValue();
            $this->shard = (($uuid << 41) >> 41) >> 19;
        }
        return $this->shard;
    }

    public function getTable()
    {
        if (null === $this->table) {
            $uuid = $this->getValue();
            $this->table = (($uuid << 45) >> 45) >> 10;
        }
        return $this->table;
    }

    public function getId()
    {
        if (null === $this->id) {
            $uuid = $this->getValue();
            $this->id = ($uuid << 54) >> 54;
        }
        return $this->id;
    }

    public function getHex()
    {
        return dechex($this->getValue());
    }

    public function __toString()
    {
        return (string)$this->getValue();
    }
}
