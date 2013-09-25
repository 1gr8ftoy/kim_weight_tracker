<?php

namespace BConway\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function ajaxGoalListAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        if ($user) {
            $goals = $user->getGoals();

            return new Response(json_encode($goals));
        }

        return $this->createNotFoundException('Invalid user, no goals found');
    }
}
