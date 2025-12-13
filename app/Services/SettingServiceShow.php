<?php

namespace App\Services;

use App\Interfaces\ServiceShow;
use App\Repositories\SettingRepositoryShow;
use Illuminate\Http\Request;

class SettingServiceShow implements ServiceShow
{
    public $repo;

    public function __construct(SettingRepositoryShow $repository)
    {
        $this->repo = $repository;
    }

    public function find_by(Request $request): object
    {
        $users = $this->repo->find_by($request->all());
        return $users;
    }

    public function find($id, Request $request): object
    {
            $user = $this->repo->find($id);
            return $user;
    }

    

}
