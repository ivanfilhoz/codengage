<?php

namespace App\Tool;

interface IFormatterRepository
{
    public function create();
    public function list();
    public function read($id);
    public function search($term);
}
