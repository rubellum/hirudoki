<?php

namespace Hirudoki;

/**
 * Class LargeCategory
 * @package Hirudoki
 */
class LargeCategory
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $created_at;

    /**
     * @var string
     */
    protected $updated_at;

    /**
     * @param string $name
     * @return mixed
     */
    function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    /**
     * LargeCategory constructor.
     *
     * @param string $code
     * @param string $name
     * @param string $created_at
     * @param string $updated_at
     */
    public function __construct($code, $name, $created_at, $updated_at)
    {
        $this->code = $code;
        $this->name = $name;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    /**
     * @return \ORM
     */
    protected static function getTable()
    {
        return \ORM::for_table('large_categories')->use_id_column('code');
    }

    /**
     * @param $keyword
     * @return array|mixed
     */
    public static function search($keyword)
    {
        $table = static::getTable();

        $table->where_like('name', '%' . $keyword . '%');

        $result = $table->find_array();

        $result = reset($result);

        return $result;
    }

    /**
     * @return bool
     */
    public static function updateData()
    {
        $large_categories = \Hirudoki\Client::fetchLargeCategories();

        foreach ($large_categories['category_l'] as $large_category) {
            $result = LargeCategory::getTable()->find_one($large_category['category_l_code']);

            if ($result) {
                $lc = $result;
            } else {
                $lc       = LargeCategory::getTable()->create();
                $lc->code = $large_category['category_l_code'];
            }

            $lc->name       = $large_category['category_l_name'];
            $lc->created_at = date('Y-m-d H:i:s');
            $lc->updated_at = date('Y-m-d H:i:s');

            $result = $lc->save();

            if (!$result) {
                log_output(['error' => "large category cannot create location"], __LINE__);

                return false;
            }
        }

        return true;
    }
}