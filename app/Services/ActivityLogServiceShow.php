<?php

namespace App\Services;

use App\Interfaces\ServiceShow;
use App\Repositories\ActivityLogRepositoryShow;
use Illuminate\Http\Request;

class ActivityLogServiceShow implements ServiceShow
{
    public $repo;

    public function __construct(ActivityLogRepositoryShow $repository)
    {
        $this->repo = $repository;
    }

    /**
     * @param Request $request
     * @return object
     */
    public function find_by(Request $request): object
    {
        $activities = $this->repo->find_by($request->all());
        $s = 0;
        return $activities->map(function ($log) use (&$s) {
            $oldValues = $this->removeKeys($log->properties['old'] ?? []);
            $newValues = $this->removeKeys($log->properties['attributes'] ?? []);

            //convert array to string to make array_diff_assoc($newValues, $oldValues) work correctly because its work fine when array has one level not multi dimension array
            if (isset($newValues['languages']) && is_array($newValues['languages']))
                $newValues['languages'] = json_encode($newValues['languages']);

            if (isset($oldValues['languages']) && is_array($oldValues['languages']))
                $oldValues['languages'] = json_encode($oldValues['languages']);

            if (isset($newValues['nationality']) && is_array($newValues['nationality']))
                $newValues['nationality'] = json_encode($newValues['nationality']);

            if (isset($oldValues['nationality']) && is_array($oldValues['nationality']))
                $oldValues['nationality'] = json_encode($oldValues['nationality']);
//convert array to string to make array_diff_assoc($newValues, $oldValues) work correctly because its work fine when array has one level not multi dimension array

            $changes = array_diff_assoc($newValues, $oldValues);
            $s++;
            return [
                'date' => $log->created_at->format('Y-m-d H:i:s'), // Format as needed
                'old' => array_intersect_key($oldValues, $changes), // Only the changed old values
                'new' => $changes, // Only the changed new values
            ];
        });
    }

    /**
     * @param array $array
     * @return array
     */
    function removeKeys(array $array): array
    {

        $keysToRemove = ['password', 'two_factor_confirmed_at', 'two_factor_recovery_codes', 'two_factor_secret', 'code', 'type', 'remember_token'];
        foreach ($keysToRemove as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
            }
        }
        return $array;
    }


    /**
     * @param $id
     * @param Request $request
     * @return object|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function find($id, Request $request): object
    {
        $activity = $this->repo->find($id);
        return $activity;
    }

}
