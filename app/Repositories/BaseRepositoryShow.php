<?php

namespace App\Repositories;

use App\Helpers\MoreImplementation;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BaseRepositoryShow extends BaseRepository
{

    /**
     * Paginate records for scaffold.
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(array $search = [], int $perPage, array $columns = ['*'], $skip = null, $limit = null, $orderBy = null): LengthAwarePaginator
    {
        $query = $this->allQuery($search, $skip, $limit, $orderBy);
        return $query->paginate($perPage, $columns);
    }

    /**
     * Build a query for retrieving all records.
     *
     * @param array $search
     * @param int|null $skip
     * @param int|null $limit
     * @return Builder
     */
    public function allQuery(array $search = [], int $skip = null, int $limit = null, $orderBy = null): Builder
    {
        $query = $this->model->newQuery();

        $query = $this->search($query, $search);

        $query = $this->moreImplementation($query);

        if (!is_null($skip)) {
            $query->skip($skip);
        }

        if (!is_null($limit)) {
            $query->limit($limit);
        }
        if (!empty($orderBy)) {
            $query = $this->orderByToQuery($query, $orderBy);

        }

        MoreImplementation::reset();
        return $query;
    }

    //$orderBy = [['seen', 'asc'],['seen', 'asc']];
    public function orderByToQuery($query, $orderBy)
    {
        if (is_array($orderBy[0])) {

            foreach ($orderBy as $key => $value) {
                $query = $query->orderBy($value[0], $value[1]);
            }
        } else {
            $query = $query->orderBy($orderBy[0], $orderBy[1]);
        }
        return $query;
    }

    /**
     * Retrieve all records with given filter criteria
     *
     * @param array $search
     * @param int|null $skip
     * @param int|null $limit
     * @param array $columns
     *
     * @return Builder[]|Collection
     */
    public function all(array $search = [], int $skip = null, int $limit = null, array $columns = ['*'])
    {
        $query = $this->allQuery($search, $skip, $limit);

        return $query->get($columns);
    }

    /**
     * Find model record for given id
     *
     * @param int $id
     * @param array $columns
     *
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function find(int $id, array $columns = ['*'])
    {
        $query = $this->model->newQuery();
        $query = $this->moreImplementation($query);
        return $query->find($id, $columns);
    }

    /**
     * @param array $criteria
     * @param array|string[] $columns
     * @return Builder|Model|object|null
     */
    public function findOneBy(array $criteria, array $columns = ['*'])
    {
        $builder = $this->model->query();
        foreach ($criteria as $key => $value) {
            $operator = '=';
            if (is_array($value)) {
                $operator = $value['operator'];
                $value = $value['value'];
            }
            $builder->where($key, $operator, $value);
        }
        return $builder->first($columns);
    }

    public function findWithRelations($id)
    {
        $query = $this->model->newQuery();
        return $query->find($id);
    }

    /**
     * @param array $criteria
     * @param array|string[] $columns
     * @return Builder
     */
    public function findByOperator(array $criteria, array $columns = ['*'])
    {
        $builder = $this->model->query();
        foreach ($criteria as $key => $value) {
            $operator = '=';
            if (is_array($value) && in_array($key, $this->getFieldsSearchable())) {
                $builder->where($key, $value['operator'], $value['value']);
            } else {
                if (!is_array($value)) {
                    $builder->error = "$value is Not array";
                } elseif (!in_array($key, $this->getFieldsSearchable())) {
                    $builder->error = "$key is Not searchable";
                }
            }
        }
        return $builder;
    }

    public function moreImplementation($query)
    {
        if ($orWhere = MoreImplementation::getOrWhere())
            $query = $this->addOrWhereToQuery($query, $orWhere);

        if ($with = MoreImplementation::getWith())
            $query = $this->addWithToQuery($query, $with);

        if ($whereHas = MoreImplementation::getWhereHas())
            $query = $this->addWhereHasToQuery($query, $whereHas);


        if ($whereDoesntHave = MoreImplementation::getWhereDoesntHave())
            $query = $this->addWhereDoesntHaveToQuery($query, $whereDoesntHave);

        if ($withQuery = MoreImplementation::getWithQuery())
            $query = $this->addWithQueryToQuery($query, $withQuery);

        if ($moreConditionsInFirstLevel = MoreImplementation::getMoreConditionsInFirstLevel())
            $query = $this->addMoreConditionsInFirstLevelToQuery($query, $moreConditionsInFirstLevel);

        return $query;
    }

//        MoreImplementation::setOrWhere(['status', 1]);
    public function addOrWhereToQuery($query, $orWhere)
    {
        foreach ($orWhere as $val) {
            if (in_array($val[0], $this->getFieldsSearchable())) {
                if (is_array($val[1]))
                    $query->where($val[0], $val[1][0], $val[1][1]);
                else
                    $query->orWhere($val[0], $val[1]);
            }
        }
        return $query;
    }

//MoreImplementation::setWith(['parent', 'shift', 'delegation', 'jobTitle', 'administrationTitle']);
    public function addWithToQuery($query, $with)
    {
        foreach ($with as $val) {
            $query->with($val);
        }
        return $query;
    }

//    add wherehas to query take array
//['shift' => [//name relation
//          'deep' => add more relation in the last level
//                  ['users' => [
//                      'parent_id' => Auth::user()->id
//                              ]
//                  ]
//           'add more conditions' =>
//             ]
//];


//MoreImplementation::setWhereHas([
    //              'roles' => [
    //              'where' => ['name' => "owner"]
    //          ]
    //      ]);
    public function addWhereHasToQuery($query, $whereHas)
    {
        foreach ($whereHas as $val) {
            foreach ($val as $key => $value) {
                $query = $query->whereHas($key, function ($q) use ($key, $value) {
                    $q = self::proccessQuery($q, $value);
                    if (isset($value['deep']) && count($value['deep']) > 0)
                        $this->addWhereHasToQuery($q, $value['deep']);
                });
            }
        }
        return $query;
    }

//MoreImplementation::setWhereDoesntHaveToQuery([
    //              'roles' => [
    //              'where' => ['name' => "owner"]
    //          ]
    //      ]);
    public function addWhereDoesntHaveToQuery($query, $whereHas)
    {
        foreach ($whereHas as $val) {
            foreach ($val as $key => $value) {
                $query = $query->whereDoesntHave($key, function ($q) use ($key, $value) {
                    $q = self::proccessQuery($q, $value);
                    if (isset($value['deep']) && count($value['deep']) > 0)
                        $this->addWhereHasToQuery($q, $value['deep']);
                });
            }
        }
        return $query;
    }
//$with = [
//        'attendances' => [
//            'where' => [
//            'company_id' => Auth::user()->company_id
//        ],
//            'whereDate' => [
//            'date' => $today
//        ],
//    ]
//];
//MoreImplementation::setWithQuery($with);
//use with function to add conditions in with relation
    public function addWithQueryToQuery($query, $whereHas)
    {
        foreach ($whereHas as $val) {
            foreach ($val as $key => $value) {
                $query = $query->with([$key => function ($q) use ($key, $value) {
                    $q = self::proccessQuery($q, $value);
                    if (isset($value['deep']) && count($value['deep']) > 0)
                        $this->addWithQueryToQuery($q, $value['deep']);
                }]);
            }
        }
        return $query;
    }

//MoreImplementation::setMoreConditionsInFirstLevel(['whereMonth' => ['date' => $month],
// add more conditions in first level
    public function addMoreConditionsInFirstLevelToQuery($query, $conditions)
    {
        foreach ($conditions as $val) {
            $query = self::proccessQuery($query, $val);
//            }
        }
        return $query;
    }

    public function searchInRel($query, $search)
    {
        if (count($search)) {
//            foreach ($search as $val) {
            foreach ($search as $key => $value) {
                $query->where($key, $value);
//                }
            }
        }
        return $query;
    }


    public function whereDate($query, $search)
    {
        if (count($search)) {
            foreach ($search as $key => $value) {
                if (is_array($value))
                    $query->whereDate($key, $value[0], $value[1]);
                else
                    $query->whereDate($key, $value);
            }
        }
        return $query;
    }

    public function whereYear($query, $search)
    {
        if (count($search)) {
            foreach ($search as $key => $value) {
                if (is_array($value))
                    $query->whereYear($key, $value[0], $value[1]);
                else
                    $query->whereYear($key, $value);
            }
        }
        return $query;
    }

    public function whereMonth($query, $search)
    {
        if (count($search)) {
            foreach ($search as $key => $value) {
                if (is_array($value))
                    $query->whereMonth($key, $value[0], $value[1]);
                else
                    $query->whereMonth($key, $value);
            }
        }
        return $query;
    }

    public function whereIn($q, $values)
    {
        if (isset($values['whereIn']) && count($values['whereIn']) > 0) {
            foreach ($values['whereIn'] as $where => $value) {
                $q->whereIn($where, $value);
            }
        }
        return $q;
    }

    public function whereBetween($q, $values)
    {
        foreach ($values as $key => $value) {
            $q->whereBetween($key, [$value[0], $value[1]]);
        }
    }


    public function proccessQuery($q, $values)
    {
        if (isset($values['where']) && count($values['where']) > 0) {
            $q = $this->searchInRel($q, $values['where']);
        }
        if (isset($values['whereDate']) && count($values['whereDate']) > 0) {
            $q = $this->whereDate($q, $values['whereDate']);
        }
        if (isset($values['whereYear']) && count($values['whereYear']) > 0) {
            $q = $this->whereYear($q, $values['whereYear']);
        }
        if (isset($values['whereMonth']) && count($values['whereMonth']) > 0) {
            $q = $this->whereMonth($q, $values['whereMonth']);
        }
        if (isset($values['whereBetween']) && count($values['whereBetween']) > 0) {
            $q = $this->whereBetween($q, $values['whereBetween']);
        }
        if (isset($values['whereIn']) && count($values['whereIn']) > 0) {
            $q = $this->whereIn($q, $values['whereIn']);
        };
        if (isset($values['orWhereNotNull']) && count($values['orWhereNotNull']) > 0) {
            $q = $this->whereNotNull($q, $values['orWhereNotNull']);
        }
        if (isset($values['orWhereNull']) && count($values['orWhereNull']) > 0) {
            $num = 0;
            foreach ($values['orWhereNull'] as $column) {
                if ($num == 0)
                    $q->whereNull($column);
                else
                    $q->orWhereNull($column);
                $num++;
            }
        }
        if (isset($values['orWherePivot']) && count($values['orWherePivot']) > 0) {
            foreach ($values['orWherePivot'] as $where => $value) {
                $q->orWhere($where, $value);
            }
        }
        if (isset($values['whereNotIn']) && count($values['whereNotIn']) > 0) {
            foreach ($values['whereNotIn'] as $where => $value) {
                $q->whereNotIn($where, $value);
            }
        }
        if (isset($values['doesntHave']) && count($values['doesntHave']) > 0) {
            foreach ($values['doesntHave'] as $val) {
                $q->doesntHave($val);
            }
        }
        return $q;
    }

}
