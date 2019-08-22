<?php
/**
 * Created by PhpStorm.
 * User: songjialin
 * Date: 2018/8/30
 * Time: 下午5:02
 */

namespace Api\Controllers;


use Common\Controllers\BaseController;
use Common\Validator\IdValidate;
use Illuminate\Support\Facades\Log;

class IndexController extends BaseController
{
    /**
     * @return array
     * @throws \Exception
     */
    public function index()
    {
        IdValidate::check();

        return $this->success();
    }
}