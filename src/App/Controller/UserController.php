<?php

namespace App\Controller;

use App\Services\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 * @package App\Controllers
 */
class UserController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserController constructor.
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->userService = $service;
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getOne($id)
    {
        return new JsonResponse($this->userService->getOne($id));
    }

    /**
     * @param string $query
     * @return JsonResponse
     */
    public function search(string $query)
    {
        return new JsonResponse($this->userService->search($query));
    }

    /**
     * Store a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        $user = $this->getDataFromRequest($request);
        return new JsonResponse($this->userService->save($user));
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

    /**
     * Get all users
     *
     * @return JsonResponse
     */
    public function getAll() : JsonResponse
    {
        return new JsonResponse($this->userService->getAll());
    }

}
