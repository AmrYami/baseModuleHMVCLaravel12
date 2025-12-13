<?php

namespace App\Repositories;


use App\Models\Exam\Exam;
use App\Repositories\BaseRepository;
use App\Interfaces\BaseRepositoryInterface;

/**
 * Class Repository
 * @package App\Repositories
 * @version December 11, 2019, 2:33 pm UTC
 */
class UserRepository extends BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * Configure the Model
     **/
    public function model() : string
    {
        return Exam::class;
    }

    public function getFieldsSearchable():array
    {
        return $this->fieldSearchable;
    }
}
