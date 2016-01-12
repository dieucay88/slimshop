<?php
/**
 * Created by PhpStorm.
 * User: vuhoanggiang
 * Date: 1/12/16
 * Time: 10:06 AM
 */

namespace App\Controller;

use App\Model\Users;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Libs\Validate as Validate;

final class UserController extends BaseController
{

    public function loginAction(Request $request, Response $response, $args)
    {
        $flash_success = $this->flash->getMessage('success');
        $flash_error = $this->flash->getMessage('error');
        $this->view->render($response, 'user/login.html', [
            'flash_success' => $flash_success,
            'flash_error' => $flash_error
        ]);
        return $response;
    }

    public function registerAction(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $username = $data['name'];
        $email = $data['email'];
        $pass = $data['pass'];

        $res = true;

        if (!Validate::isValidUsername($username)) {
            $res = false;
            $this->flash->addMessage('error', 'ERROR-NAME !');
        }
        if (!Validate::isValidEmail($email)) {
            $res = false;
            $this->flash->addMessage('error', 'ERROR-EMAIL !');
        }
        if (!Validate::isValidPass($pass)) {
            $res = false;
            $this->flash->addMessage('error', 'ERROR-PASSWORD !');
        }

        $listUser = $this->em->getRepository('App\Model\Users')->findOneBy(['username' => $data['name']]);
        $listEmail= $this->em->getRepository('App\Model\Users')->findOneBy(['email' => $data['email']]);

        if($listUser) {
            $res = false;
            $this->flash->addMessage('error', 'NAME DONE !');
        }
        if($listEmail) {
            $res = false;
            $this->flash->addMessage('error', 'EMAIL HAVE !');
        }

        if ($res) {
            $user = new Users();
            $user->setUsername($data['name']);
            $user->setEmail($data['email']);
            $user->setPassword($data['pass']);
            $user->getCreatedAt(new \DateTime());
            try {
                $this->em->persist($user);
                $this->em->flush();
            } catch (\Exception $e) {
                $this->flash->addMessage('error', $e->getMessage());
                return $response->withStatus(301)->withHeader('Location', '/login');
            }
            $this->flash->addMessage('success', "SUCCESS !");
        }

        return $response->withStatus(301)->withHeader('Location', '/login');
    }

}


