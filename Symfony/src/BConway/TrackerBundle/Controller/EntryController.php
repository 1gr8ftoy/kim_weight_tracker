<?php

namespace BConway\TrackerBundle\Controller;

use BConway\TrackerBundle\Entity\Entry;
use BConway\TrackerBundle\Form\Type\EntryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EntryController extends Controller
{
    public function createAction(Request $request)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        $entry = new Entry();

        // Necessary to show user stats on new entry page
        $userStats = array();

        // Necessary to provide goals list
        $goals = array();

        $form = $this->createForm(new EntryType(), $entry);
        $form->handleRequest($request);


        if ($form->isValid()) {
            // Save entry to the DB
            $entry->setUser($this->getUser());
            $em->persist($entry);
            $em->flush();

            $session = $this
                ->getRequest()
                ->getSession();

            // Set message to user
            $session
                ->getFlashBag()
                ->add(  'notice',
                    'Entry created successfully'
                );

            return $this->redirect($this->generateUrl('b_conway_tracker_entry_create'));
        } else {
            $user = $this->getUser();

            if ($user) {
                $goals = $user->getGoals();
                $userStats = $user->getStats();
            }
        }

        return $this->render('BConwayTrackerBundle:Entry:create.html.twig', array(
            'form'      => $form->createView(),
            'userStats' => $userStats,
            'goals'     => $goals,
        ));
    }

    public function viewAction()
    {
        $start_date = $this->getRequest()->query->get('start_date');
        $end_date = $this->getRequest()->query->get('end_date');
        $all = $this->getRequest()->query->get('all');
        $all = filter_var($all, FILTER_VALIDATE_BOOLEAN);

        $em = $this
            ->getDoctrine()
            ->getManager();

        $entries = $em
            ->getRepository('BConwayTrackerBundle:Entry')
            ->findEntries(
                $this->getUser()->getId(),
                $all,
                $start_date,
                $end_date
            );

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render('BConwayTrackerBundle:Entry:_entriesTable.html.twig', array(
                'entries' => $entries,
            ));
        } else {
            return $this->render('BConwayTrackerBundle:Entry:view.html.twig', array(
                'entries' => $entries,
            ));
        }
    }

    public function editAction($id, Request $request)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        // Get entry from DB
        $entry = $em
            ->getRepository('BConwayTrackerBundle:Entry')
            ->findOneBy(array(
                'id' => $id,
            ));

        $form = $this->createForm(new EntryType(), $entry);
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Save updated entity to DB
            $em->flush();

            // Get user PHP session
            $session = $this
                ->getRequest()
                ->getSession();

            // Set message to user
            $session
                ->getFlashBag()
                ->add(  'notice',
                        'Entry updated successfully'
                );

            $entries = $em
                ->getRepository('BConwayTrackerBundle:Entry')
                ->findEntries(
                    $this->getUser()->getId(),
                    null,
                    null,
                    null
                );


            // Render view
            return $this->redirect($this->generateUrl('b_conway_tracker_entry_view'));
//            return $this->render('BConwayTrackerBundle:Entry:view.html.twig', array(
//                'entries' => $entries,
//            ));
        } else {
            // Render view
            return $this->render('BConwayTrackerBundle:Entry:edit.html.twig', array(
                'form'  => $form->createView(),
                'entry' => $entry,
            ));
        }
    }

    public function deleteAction($id)
    {
        $em = $this
            ->getDoctrine()
            ->getManager();

        // Get entry from DB
        $entry = $em
            ->getRepository('BConwayTrackerBundle:Entry')
            ->findOneBy(array(
                'id' => $id,
            ));

        if ($entry) {
            // Remove entity
            $em->remove($entry);

            // Save changes to DB
            $em->flush();

            // Get user PHP session
            $session = $this
                ->getRequest()
                ->getSession();

            // Set message to user
            $session
                ->getFlashBag()
                ->add(  'notice',
                    'Entry deleted successfully'
                );

            // Get all entries from the DB
            $entries = $em
                ->getRepository('BConwayTrackerBundle:Entry')
                ->findEntries(
                    $this->getUser()->getId(),
                    null,
                    null,
                    null
                );

            // Render view
            return $this->redirect($this->generateUrl('b_conway_tracker_entry_view'));
//                render('BConwayTrackerBundle:Entry:view.html.twig', array(
//                'entries' => $entries,
//            ));
        } else {
            return $this->createNotFoundException('No entry found with id #' . $id);
        }
    }
}
