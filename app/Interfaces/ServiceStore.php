<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ServiceStore
{
    public function save(Request $request);

    public function update(int $id, Request $request);
}
