<?php
namespace FinanceApp\Controller;

use Doctrine\DBAL\DBALException;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{

    public function register(Request $request)
    {
        try {
				$user = $this->userService->createUser(
                $request->get('email'),
                $request->get('password'),
                $request->get('name')
            );
        } catch (DBALException $e) {
            return $this->createErrorResponse('email already exists');
        }

        return new JsonResponse(
            [
                'email' => $user->email,
                'name' => $user->name
            ],
            Response::HTTP_CREATED
        );
    }

    public function getUser(Request $request)
    {
        $user = $this->getUserByAuthorization($request);
        if ($user === false) {
            return $this->createUnathorizedResponse();
        }

        return new JsonResponse([
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    public function updateUser(Request $request)
    {
        $user = $this->getUserByAuthorization($request);
        if ($user === false) {
            return $this->createUnathorizedResponse();
        }

        try {
            $user = $this->userService->updateUser($user, $request->get('email'), $request->get('name'));
        } catch (DBALException $e) {
            return $this->createErrorResponse('email already exists');
        }

        return new JsonResponse([
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

}
