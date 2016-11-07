<?php

namespace Hirudoki;

/**
 * Class SearchParameter
 * @package Hirudoki
 */
class SearchParameter
{
    /**
     * @var string|null
     */
    protected $latitude = null;

    /**
     * @var string|null
     */
    protected $longitude = null;

    /**
     * @var string
     */
    protected $range = '2';

    /**
     * @var string
     */
    protected $lunch = '1';

    /**
     * @var string
     */
    protected $late_lunch = null;

    /**
     * @var string
     */
    protected $freeword = null;

    /**
     * @var string
     */
    protected $largeCategoryCode = null;

    /**
     * @var string
     */
    protected $smallCategoryCode = null;

    /**
     * @var integer
     */
    protected $hit_per_page = 3;

    /**
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @param string $range
     */
    public function setRange($range)
    {
        if ($range !== '1' && $range !== '2' && $range !== '3') {
            throw new \InvalidArgumentException('range is invalid range');
        }

        $this->range = $range;
    }

    /**
     * @param string $lunch
     */
    public function setLunch($lunch)
    {
        $this->lunch = $lunch ? '1' : '0';
    }

    /**
     * @param string $late_lunch
     */
    public function setLateLunch($late_lunch)
    {
        $this->late_lunch = $late_lunch ? '1' : '0';
    }

    /**
     * @param string $freeword
     */
    public function setFreeword($freeword)
    {
        $this->freeword = $freeword;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit = 3)
    {
        $this->hit_per_page = $limit;
    }

    /**
     * @param string $lCode
     */
    public function setLargeCategoryCode($lCode)
    {
        $this->largeCategoryCode = $lCode;
    }

    /**
     * @param string $sCode
     */
    public function setSmallCategoryCode($sCode)
    {
        $this->smallCategoryCode = $sCode;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        $params = [];

        if (isset($this->latitude)) {
            $params['latitude'] = $this->latitude;
        }
        if (isset($this->longitude)) {
            $params['longitude'] = $this->longitude;
        }
        if (isset($this->range)) {
            $params['range'] = $this->range;
        }
        if (isset($this->lunch)) {
            $params['lunch'] = $this->lunch;
        }
        if (isset($this->largeCategoryCode)) {
            $params['category_l'] = $this->largeCategoryCode;
        }
        if (isset($this->smallCategoryCode)) {
            $params['category_s'] = $this->smallCategoryCode;
        }
        if (isset($this->late_lunch)) {
            $params['late_lunch'] = $this->late_lunch;
        }
        if (isset($this->freeword)) {
            $params['freeword'] = $this->freeword;
        }
        if (isset($this->hit_per_page)) {
            $params['hit_per_page'] = $this->hit_per_page;
        }

        return $params;
    }
}