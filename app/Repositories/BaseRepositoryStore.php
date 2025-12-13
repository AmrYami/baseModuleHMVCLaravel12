<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BaseRepositoryStore extends BaseRepository
{

    /**
     * Create model record
     *
     * @param array $input
     *
     * @return Model
     */
    public function create(array $input): Model
    {
        $model = $this->model->newInstance($input);
        $model->save();
        return $model;
    }

    /**
     * Update model record for given id
     *
     * @param array $input
     * @param null $id
     * @param array $search
     * @return Builder|Builder[]|Collection|Model
     */
    public function update($id = null, array $input, array $search = [])
    {
        $query = $this->model->newQuery();

        if ($id){
            $model = $query->findOrFail($id);

        }

        if ($search && count($search)) {
            $query = $this->search($query, $search);
            $model = $query->firstOrFail();
        }

        $model->fill($input);

        $model->save();

        return $model;
    }

    //updateMultiRaws($request->users, ['parent_id' => $request->parent_id])
    //$id is array of ids
    //inputs what we need to update [name => $value]
    public function updateMultiRaws($id = [], array $input, array $search = [])
    {
        $query = $this->model->newQuery();
        if ($search && count($search)) {
            $query = $this->search($query, $search);
        }
        if ($id)
            $model = $query->whereIn('id', $id)->update($input);
        return $model;
    }

    //updateRawsBySpecifiecColumn(['column name', $value or $values], ['parent_id' => $value])
    //$column first take name column second value is $value or $values
    //input is array to update column :: column name => $value
    public function updateRawsBySpecifiecColumn($column = [], array $input, array $search = [])
    {
        $query = $this->model->newQuery();
        if ($search && count($search)) {
            $query = $this->search($query, $search);
        }
        if ($column && is_array($column[1]))
            $model = $query->whereIn($column[0], $column[1])->update($input);
        else
            $model = $query->where($column[0], $column[1])->update($input);
        return $model;
    }

    /**
     * @param int|null $id
     *
     * @return bool|mixed|null
     * @throws Exception
     */
    public function delete(int $id = null, $search = [])
    {
        $query = $this->model->newQuery();

        if ($id)
            $model = $query->findOrFail($id);

        if ($search && count($search)) {
            $query = $this->search($query, $search);
            $model = $query->firstOrFail();
        }
        return $model->delete();
    }

    /**
     * Update model record for given id
     *
     * @param array $input
     * @param null $id
     * @param array $search
     * @return Builder|Builder[]|Collection|Model
     */
    public function updateOrCreate($id = [], array $input, array $search = [])
    {
        $query = $this->model->newQuery();
        if ($search && count($search)) {
            $query = $this->search($query, $search);
        }
        $model = $query->updateOrCreate($id, $input);

        return $model;
    }
}
