<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/7/17
 * Time: 15:32
 */
namespace app\test\controller;

class Tmd
{
    public function index()
    {
        $name = 'Swoft';
        $notes = [
            'New Generation of PHP Framework',
            'Hign Performance, Coroutine and Full Stack'
        ];
        $data=db('user')->select();
        return view('',['name'=>$name,'notes'=>$notes,'data'=>$data]);
    }
}