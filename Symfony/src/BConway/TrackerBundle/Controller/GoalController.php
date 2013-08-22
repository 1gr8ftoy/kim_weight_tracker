<?php

namespace BConway\TrackerBundle\Controller;

use BConway\TrackerBundle\Entity\Entry;
use BConway\TrackerBundle\Entity\Goal;
use BConway\TrackerBundle\Form\Type\GoalType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GoalController extends Controller
{
    public function viewStatsAction($id)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $goal = $em
            ->getRepository('BConwayTrackerBundle:Goal')
            ->find($id)
        ;

        if ($goal) {
            return $this->render('BConwayTrackerBundle:Goal:_stats.html.twig', array(
                'stats' => $goal->getStats(),
            ));
//                new Response(json_encode($goal->getStats()));
        } else {
            return $this->createNotFoundException('Goal not found for id #' . $id);
        }
    }

    public function listAction()
    {
        $goals = $this->getUser()->getGoals();
        return $this->render('BConwayTrackerBundle:Goal:list.html.twig', array(
            'goals' => $goals,
        ));
    }

    public function editAction($id, Request $request)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $goal = $em
            ->getRepository('BConwayTrackerBundle:Goal')
            ->find($id);

        if ($goal && $goal->getUser() != $this->getUser()) {
            $goal = new Goal();
        }

        $form = $this
            ->createFormBuilder($goal)
            ->setAction($this->generateUrl('b_conway_tracker_goal_edit', array('id' => $id)))
            ->setMethod('POST')
            ->add('startDate', 'date', array('widget' => 'single_text'))
            ->add('endDate', 'date', array('widget' => 'single_text'))
            ->add('startWeight', 'integer')
            ->add('endWeight', 'integer')
            ->add('updateGoal', 'submit')
            ->add('deleteGoal', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            // Get user's PHP session
            $session = $this
                ->getRequest()
                ->getSession();

            $buttonClicked = $form->get('updateGoal')->isClicked()
                ? 'update'
                : 'delete';

            if ($buttonClicked == 'update') {
                // Set message to user
                $session
                    ->getFlashBag()
                    ->add(  'notice',
                            'Goal updated successfully'
                    );

                // Save changes to the DB
                $em->flush();
            } elseif ($buttonClicked == 'delete') {
                // Set message to user
                $session
                    ->getFlashBag()
                    ->add(  'notice',
                            'Goal deleted successfully'
                    );

                // Delete the object from the DB
                $em->remove($goal);
                $em->flush();
            } else {
                // Set message to user
                $session
                    ->getFlashBag()
                    ->add(  'error',
                            'Unknown error occurred, changes not saved'
                    );
            }

            return $this->redirect($this->generateUrl('b_conway_tracker_goal_list'));
        } else {
            if ($goal && $goal->getId()) {
                return $this->render('BConwayTrackerBundle:Goal:_settings.html.twig', array(
                    'form'  => $form->createView(),
                ));
            } else {
                return $this->createNotFoundException('No goal found with id #' . $id);
            }
        }
    }

    public function createAction(Request $request)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $goal = new Goal();

        $form = $this
            ->createFormBuilder($goal)
            ->setAction($this->generateUrl('b_conway_tracker_goal_create'))
            ->setMethod('POST')
            ->add('startDate', 'date', array('widget' => 'single_text'))
            ->add('endDate', 'date', array('widget' => 'single_text'))
            ->add('startWeight', 'integer')
            ->add('endWeight', 'integer')
            ->add('saveGoal', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            // Save goal to the DB
            $goal->setUser($this->getUser());
            $em->persist($goal);
            $em->flush();

            $session = $this
                ->getRequest()
                ->getSession();

            // Set message to user
            $session
                ->getFlashBag()
                ->add(  'notice',
                        'Goal created successfully'
                );

            return $this->redirect($this->generateUrl('b_conway_tracker_goal_list'));
        } else {
            return $this->render('BConwayTrackerBundle:Goal:_create.html.twig', array(
                'form'  => $form->createView(),
            ));
        }
    }
}
