<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/8/4
 * Time: 15:14
 */
namespace tests;

class IndexTest extends TestCase
{
    public function testSomethingsTrue()
    {
//        $this->assertTrue(true);
        $this->visit('/api/index')->seeAction('index');
    }
}