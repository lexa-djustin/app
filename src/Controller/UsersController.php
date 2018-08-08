<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity;
use App\Controller\Helper;

/**
 *
 * @Route("/api/v1/users")
 */
class UsersController extends AbstractController
{
    /**
     * @Route("/login", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function login(Request $request): Response
    {
        $decoder = new Encoder\JsonDecode();
        $data = $decoder->decode($request->getContent(), Encoder\JsonEncoder::FORMAT);

        $user = $this->getDoctrine()->getRepository(Entity\User::class)->findOneBy([
            'username' => $data->username,
            'password' => $data->password,
        ]);

        if (!$user) {
            return new JsonResponse(['reason' => 'User was not found'], 400);
        }

        $user->createToken();

        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(['token' => $user->getToken()]);
    }

    /**
     * @Route("/me", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function me(Request $request): Response
    {
        try {
            $user = $this->getCurrentUser($request);

            return new JsonResponse($user);
        } catch (Exception\UserWasNotFound $e) {
            return new JsonResponse(['Reason' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @Route("/logout", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function logout(Request $request): Response
    {
        try {
            $user = $this->getCurrentUser($request);
            $user->clearToken();

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            return new Response('', 200);
        } catch (Exception\UserWasNotFound $e) {
            return new JsonResponse(['Reason' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * @param Request $request
     *
     * @return Entity\User
     * @throws Exception\UserWasNotFound
     */
    private function getCurrentUser(Request $request)
    {
        /** @var Entity\User $user */
        $user = $this->getDoctrine()->getRepository(Entity\User::class)->findOneByToken(
            $request->headers->get('x-access-token')
        );

        $this->getDoctrine()->getManager()->refresh($user);

        if (!$user) {
            throw new Exception\UserWasNotFound('Unauthorized', 401);
        }

        return $user;
    }
}