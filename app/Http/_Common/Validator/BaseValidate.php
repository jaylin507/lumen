<?php
/**
 * Created by PhpStorm.
 * User: songjialin
 * Date: 2018/8/24
 * Time: 下午2:20
 */

namespace Common\Validator;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BaseValidate
{

    /**
     * 验证来自request的数据
     * @param mixed ...$classList 其他验证类
     * @return array
     * @throws \Exception
     */
    public static function check(...$classList)
    {
        $data = app('request')->all();
        $classArray = [];
        self::getClassList($classList, $classArray);
        if (count($classArray) > 0) {
            foreach ($classArray as $class) {
                $class::checkData($data);
            }
        }
        self::validator($data);
        return $data;
    }

    /**
     * 验证指定数据
     * @param array $data 指定验证数据
     * @param array ...$classList 其他验证类
     * @return array
     * @throws \Exception
     */
    public static function checkData($data, ...$classList)
    {
        $classArray = [];
        self::getClassList($classList, $classArray);
        if (count($classArray) > 0) {
            foreach ($classArray as $class) {
                $class::checkData($data);
            }
        }
        self::validator($data);
        return $data;
    }

    /**
     * 执行验证
     * @param $data
     * @throws \Exception
     */
    private static function validator($data)
    {
        $validator = Validator::make($data, static::rules(), static::messages(), static::custom());
        if ($validator->fails()) {

            throw new ValidationException($validator);
        }
    }

    //递归遍历获取所有验证类
    private static function getClassList($classList, &$classArray)
    {
        foreach ($classList as $item) {
            if (is_array($item)) {
                self::getClassList($item, $classArray);
                continue;
            }
            $classArray[] = $item;
        }
    }


    /**
     * 验证规则 数组 [‘参数’=>'规则']
     * @return array
     */
    protected static function rules()
    {
        return [];
    }

    /**
     * 自定义错误信息 数组 [‘参数.规则’=>‘提示语句’]
     * 错误信息中可以使用:attribute占位符来替换实际的字段名，同时还有其他占位符可以使用，具体参考官方文档
     * @return array
     */
    protected static function messages()
    {
        return [];
    }

    /**
     * 定义字段别名用于替换错误信息中的`:attribute`
     * @return array
     */
    protected static function custom()
    {
        return [];
    }
}