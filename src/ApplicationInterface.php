<?php

namespace App;

interface ApplicationInterface
{
    public function get($path, $func);
    public function post($path, $func);
    public function run();
}