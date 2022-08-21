<?php

namespace src\controllers;

class Test
{
    public function test()
    {
        require dirname(__DIR__, 2) . "/views/test.php";
    }
}