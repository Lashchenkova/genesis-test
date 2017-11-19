<?php

namespace App\Controller;

use App\Services\TrackerService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TrackerController
 * @package App\Controllers
 */
class TrackerController
{
    /**
     * @var TrackerService
     */
    protected $trackerService;

    /**
     * TrackerController constructor.
     * @param TrackerService $service
     */
    public function __construct(TrackerService $service)
    {
        $this->trackerService = $service;
    }

    /**
     * Store a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        $event = $this->getDataFromRequest($request);
        $this->trackerService->publish($event);

        return new JsonResponse(true);
    }

    /**
     * Uniform all data
     *
     * @param Request $request
     * @return array
     */
    public function getDataFromRequest(Request $request)
    {
        return $request->request->all();
    }
}
