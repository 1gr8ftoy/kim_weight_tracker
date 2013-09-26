<?php

namespace BConway\TrackerBundle\Features\Context;

use BConway\TrackerBundle\Entity\Entry;
use BConway\TrackerBundle\Entity\Goal;
use BConway\UserBundle\Entity\User;
use Behat\Behat\Context\ContextInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Doctrine\Common\Collections\ArrayCollection;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\When;
use Behat\Behat\Context\Step\Then;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Feature context.
 */
class FeatureContext extends MinkContext //MinkContext if you want to test web
    implements KernelAwareInterface
{
    private $kernel;
    private $parameters;
    private $_entries;
    private $_goals;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Wait until something is true before proceeding
     * @param $lambda
     * @param array $options
     * @param int $wait
     * @throws \Exception
     * @return bool
     */
    public function spin ($lambda, $options = array(), $wait = 60)
    {
        for ($i = 0; $i < $wait; $i++)
        {
            try {
                if ($lambda($this, $options)) {
                    return true;
                }
            } catch (Exception $e) {
                // do nothing
            }

            sleep(1);
        }

        $backtrace = debug_backtrace();

        throw new \Exception(
            "Timeout thrown by " . $backtrace[1]['class'] . "::" . $backtrace[1]['function'] . "()\n" .
            $backtrace[1]['file'] . ", line " . $backtrace[1]['line']
        );
    }

    /**
     * @When /^(?:|I )wait until "(?P<element>(?:([^"]*)))" is visible$/
     */
    public function iWaitUntilIsVisible($element)
    {
        $options = array('field' => $element);

        $this->spin(function($context, $options) {
            $el = $context->getSession()->getPage()->findField($options['field']);

            if (!is_object($el)) {
                $el = $context->getSession()->getPage()->find('css', $options['field']);
            }

            if (is_object($el)) {
                return $el->isVisible();
            } else {
                return false;
            }
        }, $options);
    }

    /**
     * Returns the Doctrine entity manager.
     *
     * @return Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function getRepository($repository)
    {
        return $this->getEntityManager()->getRepository($repository);
    }

    protected function findUserByName($name)
    {
        // Return user from the database
        return $this->getRepository('BConwayUserBundle:User')->findOneByUsername($name);
    }

    /**
     * @Given /^The database is empty$/
     */
    public function theDatabaseIsEmpty()
    {
        return array(
            new Given ("There are no goals in the database"),
            new Given ("There are no entries in the database"),
            new Given ("There are no users in the database")
        );
    }

    /**
     * @Given /^I have ([^"]*) entr(y|ies)$/
     */
    public function iHaveEntries($arg1)
    {
        for ($i = 1; $i <= $arg1; $i++) {
            $entry = new Entry();

            // Assign needed parameters
            $entry->setDeficit(rand(500, 1500));

            // Only assign weights to every other entry
            if ($i % 2) {
                $entry->setWeight(rand(150, 250));
            }

            $entry->setEntryDate((new \DateTime())->add(new \DateInterval('P' . $i . 'D')));

            if (!is_array($this->_entries))
                $this->_entries = array();

            $this->_entries[] = $entry;
        }

    }

    /**
     * @When /^I assign the entries to user "([^"]*)"$/
     */
    public function iAssignTheEntriesToUser($arg1)
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        $user = $this->findUserByName($arg1);

        foreach ($this->_entries as $entry) {
            $entry->setUser($user);

            // Required otherwise the $user object returns cached object without entry
            $user->addEntry($entry);
        }
    }

    /**
     * @Given /^I save the entries to the database$/
     */
    public function iSaveTheEntriesToTheDatabase()
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        foreach ($this->_entries as $entry) {
            $manager->persist($entry);
        }

        $manager->flush();

        unset($this->_entries);
    }

    /**
     * @Given /^I have an entry with a deficit of "([^"]*)" a weight of "([^"]*)"$/
     */
    public function iHaveAnEntryWithADeficitOfAWeightOf($arg1, $arg2)
    {
        // Create new entry
        $entry = new Entry();

        // Assign needed parameters
        $entry->setDeficit($arg1);
        $entry->setWeight($arg2);
        $entry->setEntryDate(new \DateTime());


        if (!is_array($this->_entries))
            $this->_entries = array();

        // Add entry to the entries array
        $this->_entries[] = $entry;
    }

    /**
     * @Given /^I have an entry with a deficit of "([^"]*)" a weight of "([^"]*)" with a negative day offset "(\d+)"$/
     */
    public function iHaveAnEntryWithADeficitOfAWeightOfWithDayOffset($arg1, $arg2, $arg3)
    {
        // Create new entry
        $entry = new Entry();

        // Assign needed parameters
        $entry->setDeficit($arg1);
        $entry->setWeight($arg2);

        // Create entry date
        if (is_numeric($arg3) && $arg3 > 0) {
            $entry->setEntryDate((new \DateTime())->sub(new \DateInterval('P' . $arg3 . 'D')));
        } else {
            $entry->setEntryDate(new \DateTime());
        }

        if (!is_array($this->_entries))
            $this->_entries = array();

        // Add entry to the entries array
        $this->_entries[] = $entry;
    }

    /**
     * @When /^I assign the entry to user "([^"]*)"$/
     */
    public function iAssignTheEntryToUser($arg1)
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        // Find user in database
        $user = $this->findUserByName($arg1);

        // Assign entry to given user
        $this->_entries[0]->setUser($user);

        // Required otherwise the $user object returns cached object without entry
        $user->addEntry($this->_entries[0]);
    }

    /**
     * @Given /^I save the entry to the database$/
     */
    public function iSaveTheEntryToTheDatabase()
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        $entry = array_pop($this->_entries);

        $manager->persist($entry);
        $manager->flush();
    }

    /**
     * @Given /^There are no entries in the database$/
     */
    public function thereAreNoEntriesInTheDatabase()
    {
        $entities = $this->getEntityManager()->getRepository('BConwayTrackerBundle:Entry')->findAll();

        foreach ($entities as $eachEntity) {
            $this->getEntityManager()->remove($eachEntity);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I have a goal "([^"]*)" lbs in "([^"]*)" days$/
     */
    public function iHaveAGoalLbsInDays($arg1, $arg2)
    {
        $goal = new Goal();
        $startDate = (new \DateTime())->sub(new \DateInterval('P' . $arg2 . 'D'));
        $goal->setStartDate(\DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d') . ' 00:00:00'));

        $goal->setEndDate(\DateTime::createFromFormat('Y-m-d H:i:s', (new \DateTime())->format('Y-m-d') . ' 23:59:59'));

        $goal->setStartWeight($arg1 * 1.5);
        $goal->setEndWeight($arg1);
        if (!is_array($this->_goals))
            $this->_goals = array();

        $this->_goals[] = $goal;
    }

    /**
     * @Given /^I have "([^"]*)" goals$/
     */
    public function iHaveGoals($arg1)
    {
        for ($i = 1; $i <= $arg1; $i++) {
            $goal = new Goal();

            // Assign needed parameters
            $startDate = (new \DateTime())->add(new \DateInterval('P' . ($i * 2) . 'D'));
            $goal->setEndDate(\DateTime::createFromFormat('Y-m-d H:i:s', $startDate->format('Y-m-d') . ' 00:00:00'));

            $goal->setStartDate(\DateTime::createFromFormat('Y-m-d H:i:s', (new \DateTime())->format('Y-m-d') . ' 23:59:59'));

            $goal->setStartWeight(200 - ($arg1 * $i));
            $goal->setEndWeight(200 - ($arg1 * $i) - ($arg1 * ($arg1 + 1 - $i) * 0.5));

            if (!is_array($this->_goals))
                $this->_goals = array();

            $this->_goals[] = $goal;
        }
    }

    /**
     * @When /^I assign the goal to user "([^"]*)"$/
     */
    public function iAssignTheGoalToUser($arg1)
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        $user = $this->findUserByName($arg1);

        $this->_goals[0]->setUser($user);

        // Required otherwise the $user object returns cached object without goal
        $user->addGoal($this->_goals[0]);
    }

    /**
     * @When /^I assign the goals to user "([^"]*)"$/
     */
    public function iAssignTheGoalsToUser($arg1)
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        $user = $this->findUserByName($arg1);

        foreach ($this->_goals as $goal) {
            $goal->setUser($user);

            // Required otherwise the $user object returns cached object without goal
            $user->addGoal($goal);
        }
    }

    /**
     * @Then /^I save the goal to the database$/
     */
    public function iSaveTheGoalToTheDatabase()
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        $goal = array_pop($this->_goals);

        $manager->persist($goal);
        $manager->flush();
    }

    /**
     * @Then /^I save the goals to the database$/
     */
    public function iSaveTheGoalsToTheDatabase()
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        foreach ($this->_goals as $goal) {
            $manager->persist($goal);
        }

        $manager->flush();

        unset($this->_goals);
    }

    /**
     * @Then /^I should have valid goal stats for user "([^"]*)"$/
     */
    public function iShouldHaveValidGoalStatsForUserUsingAGoalOfLbsInDaysAndAnEntryWithADeficitOfAndAWeightOf($arg1)
    {
        $user = $this->findUserByName($arg1);

        // Get goals fom the database
        $goal = $this->getRepository('BConwayTrackerBundle:Goal')->createQueryBuilder('g')
            ->where('g.user = :user')
            ->orderBy('g.id', 'ASC')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();

        // Use only last found goal
        $goal = end($goal);

        // Get goal stats
        $stats = $goal->getStats();

        assertEquals($stats['currentAverageDeficit'], 87.1);
        assertEquals($stats['currentAverageDeficitNeeded'], 34100);
        assertEquals($stats['currentCalorieDeficit'], 2700);
        assertEquals($stats['deficitProgress'], 7.34);
        assertEquals($stats['deficitSinceLastWeighin'], 900);
        assertEquals($stats['endDate'], (new \DateTime())->format('m/d/Y'));
        assertEquals($stats['estimatedCurrentWeight'], 164.5);
        assertEquals($stats['goalDaysRemaining'], 1);
        assertEquals($stats['goalLength'], 31);
        assertEquals($stats['goalWeight'], 155);
        assertEquals($stats['lastWeighInDate'], (new \DateTime())->sub(new \DateInterval("P2D"))->format('m/d/Y'));
        assertEquals($stats['lastWeighInWeight'], 165);
        assertEquals($stats['relativeWeighinsCount'], 3);
        assertEquals($stats['startDate'], (new \DateTime())->sub(new \DateInterval("P30D"))->format('m/d/Y'));
        assertEquals($stats['startWeight'], 232.5);
        assertEquals($stats['totalAverageDeficitNeeded'], 8750);
        assertEquals($stats['totalCaloriesToLoseRemaining'], 34100);
    }

    /**
     * @Given /^There are no goals in the database$/
     */
    public function thereAreNoGoalsInTheDatabase()
    {
        $entities = $this->getEntityManager()->getRepository('BConwayTrackerBundle:Goal')->findAll();

        foreach ($entities as $eachEntity) {
            $this->getEntityManager()->remove($eachEntity);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I have a user "([^"]*)" with password "([^"]*)" and a height of "([^"]*)"$/
     */
    public function iHaveAUserWithPasswordAndAHeightOf($arg1, $arg2, $arg3)
    {
        // Create new user object
        $user = new User();

        // Set username and email
        $user->setUsername($arg1);
        $user->setEmail('test' . md5(uniqid('', true) . uniqid('', true)) . '@email.com');
        $user->setHeight($arg3);

        // Set user as admin
        $user->addRole('ROLE_ADMIN');

        // Get configured encoder, so we can encode the password
        $encoder = $this
            ->kernel
            ->getContainer()
            ->get('security.encoder_factory')
            ->getEncoder($user)
        ;

        // Set user password
        $user->setPassword($encoder->encodePassword($arg2, $user->getSalt()));

        $user->setEnabled(true);

        // Set user height to six feet
        //
        // Note: Used in BMI calculation
        $user->setHeight($arg3);

        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        // Save user to database
        $manager->persist($user);

        $manager->flush();
    }

    /**
     * @Then /^I should find that user "([^"]*)" has "([^"]*)" goals$/
     */
    public function iShouldFindThatUserHasGoals($arg1, $arg2)
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        // Get user from the database
        $user = $this->findUserByName($arg1);

        // Make sure that we got a valid user
        assertInstanceOf('\BConway\UserBundle\Entity\User', $user);

        // Build query to find goal
        $query =
            $manager->getRepository('BConwayTrackerBundle:Goal')->createQueryBuilder('g')
                ->where('g.user = :user')
                ->setParameter('user', $user)
                ->getQuery();

        // Get query result from database
        $goals = $query->getResult();

        // Make sure that we got a valid goal
        assertCount((int)$arg2, $goals);
    }

    /**
     * @Then /^I should find that the user "([^"]*)" has "([^"]*)" entries$/
     */
    public function iShouldFindThatTheUserHasEntries($arg1, $arg2)
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        // Get user from the database
        $user = $this->findUserByName($arg1);

        // Make sure that we got a valid user
        assertInstanceOf('\BConway\UserBundle\Entity\User', $user);

        // Build query to find goal
        $query =
            $manager->getRepository('BConwayTrackerBundle:Entry')->createQueryBuilder('e')
                ->where('e.user = :user')
                ->setParameter('user', $user)
                ->getQuery();

        // Get query result from database
        $entries = $query->getResult();

        // Make sure that we got a valid goal
        assertCount((int)$arg2, $entries);
    }

    /**
     * @Then /^I should find that the user "([^"]*)" has an entry with a deficit of "([^"]*)" a weight of "([^"]*)"$/
     */
    public function iShouldFindThatTheUserHasAnEntryWithADeficitOfAWeightOf($arg1, $arg2, $arg3)
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        // Find user in database
        $user = $this->findUserByName($arg1);

        // Make sure that we got a valid user
        assertInstanceOf('\BConway\UserBundle\Entity\User', $user);

        // Build query to find goal
        $query =
            $manager->getRepository('BConwayTrackerBundle:Entry')->createQueryBuilder('e')
                ->where('e.user = :user')
                ->andWhere('e.weight = :weight')
                ->andWhere('e.deficit = :deficit')
                ->setParameter('user', $user)
                ->setParameter('weight', $arg3)
                ->setParameter('deficit', $arg2)
                ->setMaxResults(1)
                ->getQuery();

        // Get query result from database
        $entry = $query->getSingleResult();

        // Make sure that we got a valid goal
        assertInstanceOf('\BConway\TrackerBundle\Entity\Entry', $entry);
    }

    /**
     * @Then /^I should find goal "([^"]*)" lbs in "([^"]*)" days in user "([^"]*)" goals$/
     */
    public function iShouldFindGoalLbsInDaysInUserGoals($arg1, $arg2, $arg3)
    {
        // Get Doctrine entity manager
        $manager = $this->getEntityManager();

        // Find user in database
        $user = $this->findUserByName($arg3);

        // Make sure that we got a valid user
        assertInstanceOf('\BConway\UserBundle\Entity\User', $user);

        // Generate start/end date to search for
        $date_string = (new \DateTime())->sub(new \DateInterval('P' . $arg2 . 'D'))->format('Y-m-d');

        $start_date = \DateTime::createFromFormat('Y-m-d H:i:s', $date_string . ' 00:00:00');
        $end_date = \DateTime::createFromFormat('Y-m-d H:i:s', $date_string . ' 23:59:59');

        // Build query to find goal
        $query =
            $manager->getRepository('BConwayTrackerBundle:Goal')->createQueryBuilder('g')
                ->where('g.user = :user')
                ->andWhere('g.endWeight = :weight')
                ->andWhere('g.startDate BETWEEN :startDate AND :endDate')
                ->setParameter('user', $user)
                ->setParameter('weight', $arg1)
                ->setParameter('startDate', $start_date)
                ->setParameter('endDate', $end_date)
                ->setMaxResults(1)
                ->getQuery();

        // Get query result from database
        $goal = $query->getSingleResult();

        // Make sure that we got a valid goal
        assertInstanceOf('\BConway\TrackerBundle\Entity\Goal', $goal);
    }

    /**
     * @Then /^I should have valid user stats for user "([^"]*)" using a goal of "([^"]*)" lbs in "([^"]*)" days and an entry with a deficit of "([^"]*)" and a weight of "([^"]*)"$/
     */
    public function iShouldHaveValidUserStatsForUserUsingAGoalOfLbsInDaysAndAnEntryWithADeficitOfAndAWeightOf($arg1, $arg2, $arg3, $arg4, $arg5)
    {
        // Process provided data
        $deficits = explode(',', $arg4);
        $weights = array_filter(explode(',', $arg5));

        $user = $this->findUserByName($arg1);

        // Get user stats
        $stats = $user->getStats();

        // Verify that an array is returned
        assertEquals(is_array($stats), true);

        // Verify that the array has data
        assertGreaterThan(0, count($stats));

        // Calculate total deficit from deficits provided in $arg4
        $total_deficit = 0;

        foreach ($deficits as $deficit) {
            $total_deficit += $deficit;
        }

        // Verify calculated stats
        assertEquals($stats['averageDeficit'], $total_deficit / count($deficits));
        assertEquals($stats['bmi'], round(((end($weights) / ($user->getHeight() * $user->getHeight())) * 703), 2));
        assertEquals($stats['estimatedCurrentWeight'], "164.5");
        assertEquals($stats['firstWeighinDate'], (new \DateTime())->sub(new \DateInterval('P3D'))->format("m/d/Y"));
        assertEquals($stats['firstWeighinWeight'], reset($weights));
        assertEquals($stats['mostRecentWeighinDate'], (new \DateTime())->sub(new \DateInterval('P2D'))->format("m/d/Y"));
        assertEquals($stats['mostRecentWeighinWeight'], end($weights));
        assertEquals($stats['numberOfDeficitEntries'], count($deficits));
        assertEquals($stats['numberOfWeighins'], count($weights));
    }

    /**
     * @Given /^There are no users in the database$/
     */
    public function thereAreNoUsersInTheDatabase()
    {
        $entities = $this->getEntityManager()->getRepository('BConwayUserBundle:User')->findAll();

        foreach ($entities as $eachEntity) {
            $this->getEntityManager()->remove($eachEntity);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I am logged in with password "([^"]*)"$/
     */
    public function iAmLoggedInWithPassword($arg1)
    {
        return array(
            new Given('I am on "/login"'),
            new Then('the "h2" element should contain "Login"'),
            new When('I fill in "username" with "TestUser"'),
            new When('I fill in "password" with "' . $arg1 . '"'),
            new When('I press "Sign in"')
        );
    }

    /**
     * @Given /^I navigate to edit profile$/
     */
    public function iNavigateToEditProfile()
    {
        return array(
            new Given('I follow "Edit profile"'),
            new When('I wait until "h2" is visible'),
            new Then('the "h2" element should contain "Edit profile"')
        );
    }

    /**
     * @When /^(?:|I )confirm the popup$/
     */
    public function confirmPopup()
    {
        $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
    }

    /**
     * @When /^(?:|I )cancel the popup$/
     */
    public function cancelPopup()
    {
        $this->getSession()->getDriver()->getWebDriverSession()->dismiss_alert();
    }

    /**
     * Wait either $duration milliseconds or until jQuery: is undefined or has no active ajax calls AND has no active animations
     *
     * @param int $duration
     */
    protected function jqueryWait($duration = 1000)
    {
        $this->getSession()->wait($duration, '(typeof jQuery === \'undefined\' || (0 === jQuery.active && 0 === jQuery(\':animated\').length))');
    }

    /**
     * @When /^I wait for the response$/
     */
    public function iWaitForTheResponse()
    {
        $this->jqueryWait(20000);
    }

    /**
     * @When /^I filter entries from the next (\d+) days$/
     */
    public function iFilterEntriesFromTheNextDays($arg1)
    {
        $startDate = new \DateTime();
        $endDate = (new \DateTime())->add(new \DateInterval('P' . $arg1 . 'D'));

        return array(
            new When('I fill in "start_date" with "' . $startDate->format('Y-m-d') . '"'),
            new When('I fill in "end_date" with "' . $endDate->format('Y-m-d') . '"'),
        );
    }

    /** Click on the element with the provided xpath query
     *
     * @When /^(?:|I )click on the "([^"]*)" element$/
     */
    public function iClickOnTheElement($locator)
    {
        $session = $this->getSession(); // get the mink session
        $element = $session->getPage()->find('css', $locator); // runs the actual query and returns the element

        // errors must not pass silently
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
        }

        // ok, let's click on it
        $element->click();
    }
}
