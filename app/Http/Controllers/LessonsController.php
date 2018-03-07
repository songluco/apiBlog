<?php

namespace App\Http\Controllers;

use App\Lessons;
use Illuminate\Http\Request;

class LessonsController extends Controller
{
    //
    protected $initData = [
        'code' => 404,
        'msg' => 'not find model',
        'data' => []
    ];

    public function getIndex()
    {
        $data = Lessons::all();
        $data = $this->transformCollection($data->toArray());
        //转换数据
        $this->successData($data);
        return $this->initData;
    }


    public function getShow($id)
    {
        $lesson = Lessons::find($id);

        /*
        findOrFail || firstOrFail 在找不到模型的时候会抛出异常
        $lesson = Lessons::findOrFail($id);
        $lesson = Lessons::where('id', '>', 200)->firstOrFail();
        */

        if(!$lesson){
            return $this->initData;
        }

        $this->successData($this->transform($lesson));

        return $this->initData;

    }



    private function successData($data)
    {
        $this->initData['code'] = 200;
        $this->initData['msg'] = 'success';
        $this->initData['data'] = $data;
    }


    private function transform($data)
    {
        return [
            'title' => $data['title'],
            'content' => $data['body'],
            'is_free' => (boolean)$data['free']
        ];
    }

    private function transformCollection($data)
    {
        return array_map([__CLASS__, 'transform'], $data);
    }

}
