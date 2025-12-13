<?php

namespace Users\Repositories;

use Users\Models\User;
use Illuminate\Http\Request;
use App\Interfaces\RepositoryShow;
use App\Helpers\MoreImplementation;
use App\Repositories\BaseRepositoryShow;
use App\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
/**
 * Class Repository
 * @package App\Repositories
 * @version December 11, 2019, 2:33 pm UTC
 */
class UserRepositoryShow extends BaseRepositoryShow implements RepositoryShow, BaseRepositoryInterface
{

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'type',
        'status',
        'banned_until',
        'specialization',
        'hospital',
        'designation',
        'specialty',
        'languages',
        'experience',
        'mobile',
        'description',
        'approve',
        'achievements',
        'studies',
        'work_experience',
        'created_at',
        'IBAN',
        'updated_at',
        'agrement', 'teammember', 'title', 'topic', 'teamleader', 'abstract', 'national_id', 'nationality', 'religion', 'city', 'educational',
        'study', 'speciality_experience', 'speciality_experience_years', 'SCFHS_license', 'SCFHS_expiry_date', 'SCFHS_classification', 'BLS_certification', 'BLS_expiry_date',
        'ACLS_certification', 'ACLS_expiry_date', 'PHTLS_certification', 'PALS_certification', 'NRP_certification', 'EVOC_certification', 'available_to_work_in_makkah',
        'experience_working_in_the_hajj', 'experience_working_in_the_hajj_years', 'availability', 'first_name', 'second_name', 'last_name', 'speciality', 'commitments_description'
    ];

    /**
     * Use Search Criteria from request to find from model
     *
     * @param Array $request
     * @return Collection
     */

//    public function find_by(array $request)
//    {
//       $query = $this->model->newQuery();
//        if (isset($request['search']))
//        $query->whereRaw("MATCH(name, email, mobile) AGAINST(? IN BOOLEAN MODE)",array($request['search']));
//
////        if ($request)
////            $query->where($request);
//        return $query->paginate(25);
//    }
    public function find_by(array $request, $perPage = 200)
    {
        return $this->paginate($request, $perPage);
    }

    public function listUsersDetailsHasRole($search, $role, $perPage = 50, $export)
    {
        $query = $this->model->query();
        $scoper = $search['__scoped_by'] ?? null;
        unset($search['__scoped_by']);
        $query = $this->applySearch($query, $search);

        $query->role($role);
        if ($scoper) {
            $query->where(function ($q) use ($scoper) {
                $q->where('created_by', $scoper->id);
                $q->orWhere('id', $scoper->id);
            });
        }

        if ($perPage && !$export) {
            return $query->paginate($perPage);
        } else {
            return $query->get();
        }
    }


    public function applySearch($query, $search)
    {
        if (isset($search['speciality'])) {
            $query->whereIn('speciality', $search['speciality']);
        }
        if (isset($search['first_name'])) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', $search['first_name'][0], '%' . $search['first_name'][1] . '%')
                    ->orWhere('second_name', $search['second_name'][0], '%' . $search['second_name'][1] . '%')
                    ->orWhere('last_name', $search['last_name'][0], '%' . $search['last_name'][1] . '%');
            });
        }
        if (isset($search['approve'])) {
            $query->where('approve', $search['approve']);
        }
        if (isset($search['email'])) {
            $query->where('email', 'like', '%' . $search['email'] . '%');
        }
        if (isset($search['mobile'])) {
            $query->where('mobile', 'like', '%' . $search['mobile'] . '%');
        }

        if (isset($search['company_id'])) {
            $query->where('company_id', $search['company_id']);
        }

        if (isset($search['created_by'])) {
            $query->where('created_by', $search['created_by']);
        }
        if (isset($search['from']) || isset($search['to'])) {
            if (isset($search['from']) && isset($search['to'])) {
                $query->whereBetween('id', [$search['from'], $search['to']]);
            } elseif (isset($search['from'])) {
                $query->where('id', '>', $search['from']);
            } elseif (isset($search['to'])) {
                $query->whereBetween('id', [0, $search['to']]);
            }
        }
        return $query;
    }

    public function downloadAllMedia($search, $role, $perPage = 50, $export = false)
    {
        $query = $this->model->query();
//        $query = $this->allQuery($search);
        $query = $this->applySearch($query, $search);

        $query->with('company');
        $query->role($role);
        return $query;

    }

    public function listUsers($search, $role = "User", $perPage = 50, $export = false)
    {
        $query = $this->model->query();
//        $query = $this->allQuery($search);

        $query = $this->applySearch($query, $search);
        $query->whereHas('company');

        $query->role($role);

        if ($export) {
            return $query->get();
        }
        if ($perPage) {
            return $query->paginate($perPage);
        } else {
            return $query->get();
        }
    }

    public function listParamedics($search, $role, $perPage = 50, $export = false)
    {
        $query = $this->model->query();
        if (isset($search['speciality']) && $search['speciality']) {
            $query->whereIn('speciality', $search['speciality']);
        } else {
            $query->whereNotNull('speciality');
        }
        if (isset($search['first_name'])) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', $search['first_name'][0], '%' . $search['first_name'][1] . '%')
                    ->orWhere('second_name', $search['second_name'][0], '%' . $search['second_name'][1] . '%')
                    ->orWhere('last_name', $search['last_name'][0], '%' . $search['last_name'][1] . '%');
            });
        }
        if (isset($search['approve'])) {
            $query->where('approve', $search['approve']);
        }
        if (isset($search['email'])) {
            $query->where('email', 'like', '%' . $search['email'] . '%');
        }
        if (isset($search['mobile'])) {
            $query->where('mobile', 'like', '%' . $search['mobile'] . '%');
        }
        if (isset($search['company_id']) && $search['company_id']) {
            $query = $query->where('company_id', $search['company_id']);
        }
        if (isset($search['from'])  && isset($search['to'])) {
            $query->whereBetween('id', [$search['from'], $search['to']]);
        } elseif (isset($search['from'])) {
            $query->where('id', '>=', $search['from']);
        } elseif (isset($search['to'])) {
            $query->where('id', '<=', $search['to']);
        }



//        $query = $this->allQuery($search);
        $query->with('statuses');
        $query->with('company');
        $query->role($role);

        if ($export) {
            return $query->get();
        } else {
            return $query->paginate($perPage);
        }
    }


    public function listUsersHasRole($role)
    {
        $query = $this->model->query();
        return $query->role($role)->pluck("name", 'id');
    }

    public function listUsersHasPemission($permission)
    {
        $query = $this->model->query();
        return $query->permission($permission)->pluck("name", 'id');
    }

    /**
     * [dataTableSearch Makes search using DT schema]
     * 
     * @param Request $request
     * 
     * @return Collection
     */
    public function dataTableSearch(Request $request): array
    {
        $query = $this->makeModel();
        $viewer = auth()->user();
        if ($viewer && !$viewer->hasRole('CRM Admin')) {
            $query = $query->where(function ($q) use ($viewer) {
                $q->where('created_by', $viewer->id)
                    ->orWhere('id', $viewer->id);
            });
        }
        $columns = $request->columns ?? [];
        $length = (int) ($request->length ?? 10);
        $offset = (int) ($request->start ?? 0);
        $orderColumn =  'id';
        $orderDir = 'desc';
        if(isset($request->order) && isset($request->order[0])){
            if( isset($request->order[0]['column']) && isset($columns[$request->order[0]['column']]['name'])){
                $orderColumn = $columns[$request->order[0]['column']]['name'];
            }

            if( isset($request->order[0]['dir'])){
                $orderDir = $request->order[0]['dir'];
            }
        }

        $totalRecords = $query->count();

        if(isset($request->search) && isset($request->search['value']) && $request->search['value']!==''){
            $searchValue = "%".$request->search['value']."%";
            $query = $query->where(function($q) use ($request,$searchValue){
                if(is_numeric($request->search['value'])){
                    $q->where('id', 'like', $searchValue)
                      ->orWhere('mobile', 'like', $searchValue);
                }elseif(filter_var($request->search['value'], FILTER_VALIDATE_EMAIL)){
                    $q->where('email', 'like', $searchValue);
                }else{
                    $q->where('name', 'like', $searchValue)
                      ->orWhere('email', 'like', $searchValue);
                }
            });
        }

        $filteredRecords = $query->count();

        $data = $query->orderBy($orderColumn, $orderDir)
            ->skip($offset)
            ->take($length)
            ->get();

        return [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
        ];
    }

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return User::class;
    }

}
