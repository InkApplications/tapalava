<?php

namespace Tapalava\AuthenticationBundle\Controller;

use InkApplications\Knock\Login;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\DiExtraBundle\Annotation\InjectParams;
use JMS\DiExtraBundle\Annotation\Inject;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Handles actions for Logging the user into the system.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 * @Route("/login")
 */
class LoginController extends Controller
{
    /** @var Login */
    private $login;

    /**
     * @InjectParams({
     *     "login" = @Inject("knock.login"),
     *     "encoder" = @Inject("security.password_encoder")
     * })
     */
    public function __construct(Login $login)
    {
        $this->login = $login;
    }

    /**
     * Starting login form where the user provides their email address for
     * logging in.
     *
     * @Route("/", name="login-start")
     * @Method("GET")
     * @Template
     */
    public function startLoginAction()
    {
        return [];
    }

    /**
     * Invoked when the user submits their email to login.
     *
     * @Route("/send-code", name="login-send-code")
     * @Method("POST")
     * @Template
     */
    public function sendCodeAction(Request $request)
    {
        $email = $request->get('email');
        $this->login->start($email);

        return [];
    }

    /**
     * Route for when the user clicks the email login link.
     *
     * This route will never be invoked, as it is intercepted by the Security
     * Handlers. It just exists for routing.
     *
     * @Route("/authorize", name="login-authorize")
     * @Method("GET")
     */
    public function authorizeLoginAction() {}
}
