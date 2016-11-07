<?php

namespace Hirudoki;

/**
 * Class SmallCategory
 * @package Hirudoki
 */
class SmallCategory
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
    protected $large_category_id;

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
     * @param string $large_category_id
     * @param string $code
     * @param string $name
     * @param string $created_at
     * @param string $updated_at
     */
    public function __construct($large_category_id, $code, $name, $created_at, $updated_at)
    {
        $this->large_category_id = $large_category_id;
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
        return \ORM::for_table('small_categories')->use_id_column('code');
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
        $small_categories = \Hirudoki\Client::fetchSmallCategories();

        foreach ($small_categories['category_s'] as $small_category) {
            $result = SmallCategory::getTable()->find_one($small_category['category_s_code']);

            if ($result) {
                $sc = $result;
            } else {
                $sc       = SmallCategory::getTable()->create();
                $sc->code = $small_category['category_s_code'];
            }

            $sc->large_code = $small_category['category_l_code'];
            $sc->name       = $small_category['category_s_name'];
            $sc->created_at = date('Y-m-d H:i:s');
            $sc->updated_at = date('Y-m-d H:i:s');

            $result = $sc->save();

            if (!$result) {
                log_output(['error' => "small category cannot create location"], __LINE__);

                return false;
            }
        }

        return true;
    }

    public static function createInstance(array $data)
    {

    }
}