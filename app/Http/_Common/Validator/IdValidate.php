<?php
/**
 * Created by PhpStorm.
<<<<<<< HEAD
 * User: admin
 * Date: 2018/4/3
 * Time: 12:01
=======
 * User: songjialin
 * Date: 2018/8/24
 * Time: 下午2:24
>>>>>>> jaylin_youzan_1.0_20180822
 */

namespace Common\Validator;


class IdValidate extends BaseValidate
{

    protected static function rules()
    {
        $rules = [
            'id' => 'required'
        ];
        return $rules;
    }

    protected static function messages()
    {
        $messages = [
            'id.required' => ':attribute参数必填',
        ];
        return $messages;
    }

    protected static function custom()
    {
        return parent::custom();
    }

}