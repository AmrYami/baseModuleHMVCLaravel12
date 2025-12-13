<?php

namespace Users\Services;

use Users\Models\User;
use ZipStream\ZipStream;
use Illuminate\Http\Request;
use App\Interfaces\ServiceShow;
use App\Helpers\MoreImplementation;
use Illuminate\Support\Facades\Storage;
use Users\Repositories\UserRepositoryShow;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\SpecialityRepositoryShow;

class UserServiceShow implements ServiceShow
{
    /**
     * @var UserRepositoryShow
     */
    public $repo;

    /**
     * Create a new Repository instance.
     *
     * @param UserRepository $repository
     * @return void
     */
    public function __construct(UserRepositoryShow $repository, SpecialityRepositoryShow $specialityRepositoryShow)
    {
        $this->repo = $repository;
        $this->specialityRepositoryShow = $specialityRepositoryShow;
    }

    

    /**
     * [dataTable make search using datatable search and returns result]
     */
    public function dataTable(Request $request, ?string $resourceNameSpace = null): array
    {
        $result = $this->repo->dataTableSearch($request);
        $data = $result['data'] ?? collect();
        $recordsTotal = $result['recordsTotal'] ?? $data->count();
        $recordsFiltered = $result['recordsFiltered'] ?? $data->count();

        if($resourceNameSpace){
            $data = $resourceNameSpace::collection($data);
        }

        return [
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];
    }

    /**
     * Use Search Criteria from request to find from Repository
     *
     * @param Request $request
     * @return Collection
     */

    public function find_by(Request $request, int $perPage = 50, $export = null): object
    {
        $filter = array_filter($request->except('approve'));

        if (isset($request->name) && $request->name) {
            $name = $request->name;
            $request->merge([
                'first_name' => ['like', '%' . $request->name . '%'],
                'second_name' => ['like', '%' . $request->name . '%'],
                'last_name' => ['like', '%' . $request->name . '%'],
            ]);
            $request->offsetUnset('name');
            $filter = array_filter($request->except('approve'));
            if ($request->approve)
                $filter['approve'] = $request->approve;
        }
        $authed = auth()->user();
        if (isset($authed) && auth()->user()->hasRole('CRM Admin')) {
            $users = $this->repo->find_by($filter);
        } else {
            if (!$request->routeIs('dashboard.users.index'))
                $filter['approve'] = $request->approve;
            // Non-CRM users: only see users they created or within their hospital (plus themselves)
            $filter['__scoped_by'] = $authed;
            $users = $this->repo->listUsersDetailsHasRole($filter, "User", $perPage, $export);
        }
        $request->merge(['name' => $name ?? null]);
        return $users;
    }

    public function downloadAll(Request $request, int $perPage = 50, $export = false)
    {

        $filter = array_filter($request->except('approve'));

        $zipFileName = 'all_users_media_'.now()->format('Y_m_d_H_i_s').'.zip';
        return response()->streamDownload(function () use ($zipFileName, $filter, $export, $perPage) {

        // named args on the constructor replace the old ZipOptions API
        $zip = new ZipStream(
            defaultDeflateLevel:     6,
            defaultEnableZeroHeader: true,
            sendHttpHeaders: false,
            outputName:      $zipFileName,
        );

        $disk = Storage::disk('s3');

        $usersQuery = $this->repo->downloadAllMedia($filter, "User", $perPage, $export);
        $usersQuery->has('media')
            ->with('media')
            ->chunkById(100, function ($users) use ($zip, $disk) {

                foreach ($users as $user) {
                    foreach ($user->media as $media) {
                        // get the S3 key (path) that Spatie stores
                        $key = $media->getPathRelativeToRoot();

                        if ($stream = $disk->readStream($key)) {
                            $folder = $user->hashid ?? \App\Support\IdHasher::encode($user->id);
                            $zip->addFileFromStream("{$folder}/{$media->file_name}", $stream);
                            fclose($stream);
                        }
                    }
                }
            });

        $zip->finish();

        }, $zipFileName, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.$zipFileName.'"',
        ]);
    }

    public function contractApproved(Request $request, int $perPage = 50, $export = false, $doesntHaveId = false): object
    {
        $filter = array_filter($request->except('approve'));
        if (isset($request->name) && $request->name) {
            $name = $request->name;
            $request->merge([
                'first_name' => ['like', '%' . $request->name . '%'],
                'second_name' => ['like', '%' . $request->name . '%'],
                'last_name' => ['like', '%' . $request->name . '%'],
            ]);
            $request->offsetUnset('name');
            $filter = array_filter($request->except('approve'));
            if ($request->approve)
                $filter['approve'] = $request->approve;
        }
        $auth = auth()->user();
        if (isset($auth) && auth()->user()->hasRole('CRM Admin')) {
            $users = $this->repo->find_by($filter);
        } else {
            if (!$request->routeIs('dashboard.users.index'))
                $filter['approve'] = $request->approve;
            $users = $this->repo->contractApproved($filter, "User", $perPage, $export, $doesntHaveId);
        }
        $request->merge(['name' => $name ?? null]);
        return $users;
    }


    public function listUsers(Request $request, int $perPage = 50, $export = false): object
    {
        $filter = array_filter($request->except('approve'));
        if (isset($request->name) && $request->name) {
            $name = $request->name;
            $request->merge([
                'first_name' => ['like', '%' . $request->name . '%'],
                'second_name' => ['like', '%' . $request->name . '%'],
                'last_name' => ['like', '%' . $request->name . '%'],
            ]);
            $request->offsetUnset('name');
            $filter = array_filter($request->except('approve'));
            if ($request->approve)
                $filter['approve'] = $request->approve;
        }


        $users = $this->repo->listUsers($filter, "User", $perPage, $export);
        return $users;
    }

    public function listSpecialities()
    {
        return $this->specialityRepositoryShow->all();
    }

    public function listParamedics(Request $request, int $perPage = 50, $export = false): object
    {
        $filter = array_filter($request->except('approve'));
        if (isset($request->name) && $request->name) {
            $name = $request->name;
            $request->merge([
                'first_name' => ['like', '%' . $request->name . '%'],
                'second_name' => ['like', '%' . $request->name . '%'],
                'last_name' => ['like', '%' . $request->name . '%'],
            ]);
            $request->offsetUnset('name');
            $filter = array_filter($request->except('approve'));

        }
        if ($request->approve)
            $filter['approve'] = $request->approve;

        $users = $this->repo->listParamedics($filter, "User", $perPage, $export);

        $request->merge(['name' => $name ?? null]);
        return $users;
    }

    /**
     * Use Search Criteria from request to find from Repository
     *
     * @param Request $request
     * @return Collection
     */

    public function listDoctors(Request $request): object
    {
        MoreImplementation::setWhereHas([
            'roles' => [
                'where' => [
                    'name' => 'doctor',
                ]
            ]
        ]);
        $users = $this->repo->find_by($request->all());
        return $users;
    }

    /**
     * Use id to find from Repository
     *
     * @param Int $id
     */
    public function find($id, Request $request = null): object
    {
        return $this->repo->findWithRelations($id);
    }

    /**
     * @param array $criteria
     * @param array|string[] $columns
     * @return mixed
     */
    public function findByOperator(array $criteria, array $columns = ['*'])
    {
        return $this->repo->findByOperator($criteria, $columns);
    }

    public function listUsersHasRole($role)
    {
        return $this->repo->listUsersHasRole($role);
    }

    public function listUsersHasPemission($permission)
    {
        return $this->repo->listUsersHasPemission($permission);
    }

}
