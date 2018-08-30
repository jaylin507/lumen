<?php

namespace Common\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseModel extends Model
{
    /**
     * 默认分页大小
     * @var int
     */
    protected $pageSize = 20;

    /**
     * @param $query
     * @return \stdClass
     * 普通分页
     */
    public function scopePages($query)
    {
        $page = app('request')->input('page', 1);
        $pageSize = app('request')->input('pageSize', $this->pageSize);
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $pageSize;

        $data = new \stdClass();
        $data->count = $query->count();
        $data->page = ceil($data['count'] / $pageSize);
        $data->data = $query->offset($offset)->limit($pageSize)->get();
        return $data;
    }

    /**
     * @param $query
     * @param $select
     * @return mixed
     * 带group分页
     */
    public function scopeGroupPages($query, $select = null)
    {
        $page = app('request')->input('page', 1);
        $pageSize = app('request')->input('pageSize', $this->pageSize);
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $pageSize;
        $pages = clone $query;

        $data = new \stdClass();
        $data->data = $query->offset($offset)->limit($pageSize)->get();
        if ($select) {
            $count = $pages->select(DB::raw('count(*) as count'), $select)->get();
        } else {
            $count = $pages->select(DB::raw('count(*) as count'))->get();
        }

        $data->count = count($count);
        $data->page = ceil($data->count / $pageSize);
        return $data;
    }
}
