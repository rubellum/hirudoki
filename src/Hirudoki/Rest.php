<?php

namespace Hirudoki;

/**
 * Class Rest
 * @package Hirudoki
 */
class Rest
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param string $name
     * @return bool|null
     */
    function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * @return string
     */
    function getImageUrl()
    {
        $imageUrl = (isset($this->data['image_url']) && $this->data['image_url'] && is_array($this->data['image_url'])) ? reset($this->data['image_url']) : '';

        if (is_array($imageUrl)) {
            $imageUrl = reset($imageUrl);
        }

        $imageUrl = str_replace('http://', 'https://', $imageUrl);

        return $imageUrl ? $imageUrl : 'https://hirudoki.net/images/1.png';
    }

    /**
     * @return string
     */
    function getPR()
    {
        $pr = isset($this->data['pr']['pr_short']) ? $this->data['pr']['pr_short'] : '';

        $pr = mb_strimwidth($pr, 0, 50, '...');
        $pr .= ' 提供：ぐるなび';

        return $pr;
    }

    /**
     * Rest constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}