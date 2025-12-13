<?php

namespace App\Services;

use App\Factories\ModulesFactory;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use Auth;

class NotificationService
{
    private $repo;
    private $realTimeService;

    /**
     * Create a new Repository instance.
     *
     * @param NotificationRepository $repository
     * @return void
     */
    public function __construct(NotificationRepository $repository, RealTimeService $realTimeService)
    {
        $this->repo = $repository;
        $this->realTimeService = $realTimeService;
    }

    /**
     * Use Search Criteria from request to find from Repository
     *
     * @param Request $request
     * @return Collection
     */

    public function find_by(Request $request)
    {
        $notification = $this->repo->find_by($request);
        return $notification;
    }

    public function updateMultiRaws(Request $request)
    {
        $data = ['seen' => TRUE];
        $ids = [Auth::user()->id];
        $column = 'user_id';
        $notification = $this->repo->updateMultiRawsData($data, $ids, $column);
        return $notification;
    }

    /**
     * Use id to find from Repository
     *
     * @param Int $id
     * @return notification
     */
    public function find($id)
    {
        $notification = $this->repo->find($id);
        return $notification;
    }

    /**
     * Use save data into Repository
     *
     * @param Request $request
     * @param Int $id
     * @return Boolean
     */
    public function save(Request $request, $id = null, $relatedTypeId = null)
    {
//        try {
            if ($relatedTypeId) {
//            $request->merge(['created_by' => Auth::user()->id]);
                $request->merge($relatedTypeId);
            }
            $notification = $this->repo->save($request, $id);
//        } catch (\Exception $e) {
//            return false;
//        }
        return $notification;
    }

    public function notificationAction($userId, $request, $url, $model, $data = []): void
    {
        $reqNotification = new Request();
//            $myClass = ModulesFactory::build('\App\Http\Controllers\PushController');
        $reqNotification->merge([
            'title' => $data['title'] ?? 'there\'s new item for you',
            'body' => $data['body'] ?? 'there\'s new item for you',
            'user_id' => $userId,
            'type' => $data['type'] ?? 'new item',
            'icon' => $data['icon'] ?? 'flaticon2-line-chart kt-font-success',
            'route' => url($url)
        ]);
//            $myClass->push($reqNotification, $userId);
        $this->realTimeService->publishData($userId, $reqNotification->all(), 'notifications', 'message');
        $this->save($reqNotification, null, ['related_type' => get_class($model), 'related_id' => $request->related_id]);
    }
}
