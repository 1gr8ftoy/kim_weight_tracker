<?php

namespace BConway\TrackerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Goal
 *
 * @ORM\Table(name="goals")
 * @ORM\Entity(repositoryClass="BConway\TrackerBundle\Entity\GoalRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Goal
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="BConway\UserBundle\Entity\User", inversedBy="goals")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     */
    private $endDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="start_weight", type="integer")
     */
    private $startWeight;

    /**
     * @var integer
     *
     * @ORM\Column(name="end_weight", type="integer")
     */
    private $endWeight;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Goal
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Goal
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    
        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set startWeight
     *
     * @param integer $startWeight
     * @return Goal
     */
    public function setStartWeight($startWeight)
    {
        $this->startWeight = $startWeight;
    
        return $this;
    }

    /**
     * Get startWeight
     *
     * @return integer 
     */
    public function getStartWeight()
    {
        return $this->startWeight;
    }

    /**
     * Set endWeight
     *
     * @param integer $endWeight
     * @return Goal
     */
    public function setEndWeight($endWeight)
    {
        $this->endWeight = $endWeight;
    
        return $this;
    }

    /**
     * Get endWeight
     *
     * @return integer 
     */
    public function getEndWeight()
    {
        return $this->endWeight;
    }

    /**
     * set createdAt
     *
     * @return Goal
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedTime
     * @return Goal
     */
    public function setUpdatedAt($updatedTime = null)
    {
        if ($updatedTime == null) {
            $this->updatedAt = new \DateTime();
        } else {
            $this->updatedAt = $updatedTime;
        }

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param \BConway\UserBundle\Entity\User $user
     * @return Goal
     */
    public function setUser(\BConway\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \BConway\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get all entries that occurred between the start and end date for the this goal (or the given dates)
     *
     * @return array
     */
    private function getRelativeEntries($start_date = null, $end_date = null) {
        /**
         * All entries for the logged in user
         */
        $all_entries = $this->user->getEntries();
        $relative_entries = new ArrayCollection();

        /**
         * If no dates provided, use goal start_date and end_date
         */
        if (is_null($start_date)) {
            $start_date = $this->getStartDate()->getTimestamp();
        }
        if (is_null($end_date)) {
            $end_date = $this->getEndDate()->getTimestamp();
        }

        foreach ($all_entries as $entry) {
            if (
                $entry->getEntryDate()->getTimestamp() >= $start_date &&
                $entry->getEntryDate()->getTimestamp() <= $end_date
            ) {
                $relative_entries->add($entry);
            }
        }

        return $relative_entries;
    }

    private function mostRecentWeighinCalorieDeficit() {
        $deficit = 0;
        $entries = $this->getRelativeEntries(
            strtotime($this->user->getMostRecentWeighinDate()->format('Y-m-d H:i:s') . " +1 day")
        );
        foreach ($entries as $entry) {
            $deficit += $entry ->getDeficit();
        }

        return $deficit;
    }

    /**
     * Get compiled goal stats
     *
     * @return array
     */
    public function getStats()
    {
        /**
         * All entries for the logged in user
         */
        $entries = $this->getRelativeEntries();

        /**
         * Array of stats for the given goal
         */
        $stats = array();

        /**
         * Date for the most recent weigh-in
         */
        $stats['lastWeighInDate'] = $this->user->getMostRecentWeighinDate()->format("m/d/Y");
        $stats['lastWeighInWeight'] = $this->user->getMostRecentWeighinWeight();

        /**
         * Count of all entries that occurred between the start and end date for this goal
         *
         * @param $entries
         * @return int
         */
        $stats['relativeWeighinsCount'] = count($entries);

        /**
         * Length of goal in days
         */
        $stats['goalLength'] =
            (int)(
                ceil(
                    abs(
                            $this
                                ->getStartDate()
                                ->getTimestamp()
                        -
                            $this
                                ->getEndDate()
                                ->getTimestamp()
                        )
                    /60/60/24
                )
            );

        /**
         * Number of days remaining to complete goal
         */
        $stats['goalDaysRemaining'] =
            (int)max(0, (int)(ceil(($this->getEndDate()->getTimestamp() - time())/60/60/24)));

        $deficit = 0;

        foreach ($entries as $entry) {
            $deficit +=  $entry->getDeficit();
        }

        /**
         * Total sum of all deficits for all related
         *
         * @param $entries
         * @return int
         */
        $stats['currentCalorieDeficit'] = $deficit;

        if (count($this->user->getEntriesWithWeight()) > 0) {
            $stats['totalCaloriesToLoseRemaining'] =
                (($this->user->getMostRecentWeighinWeight() - $this->getEndWeight()) * 3500) - $this->mostRecentWeighinCalorieDeficit();
        } else {
            $stats['totalCaloriesToLoseRemaining'] =
                (($this->startWeight - $this->endWeight) * 3500) - $stats['currentCalorieDeficit'];
        }

        try {
            if ($stats['goalLength']) {
                $stats['totalAverageDeficitNeeded'] = round((($this->startWeight - $this->endWeight) * 3500) / $stats['goalLength'], 2);
            } else {
                $stats['totalAverageDeficitNeeded'] = 0;
            }
        } catch (\Exception $e) {
            $stats['totalAverageDeficitNeeded'] = 0;
        }

        try {
            if ($stats['goalDaysRemaining']) {
                $stats['currentAverageDeficitNeeded'] =
                    round(($stats['totalCaloriesToLoseRemaining'] / $stats['goalDaysRemaining']), 2);
            } else {
                $stats['currentAverageDeficitNeeded'] = 0;
            }
        } catch (\Exception $e) {
            $stats['currentAverageDeficitNeeded'] = 0;
        }

        try {
            $stats['currentAverageDeficit'] =
                round($stats['currentCalorieDeficit'] / (((time() - $this->startDate->getTimestamp())/60/60/24) + 1), 2);
        } catch (\Exception $e) {
            $stats['currentAverageDeficit'] = 0;
        }

        try {
            if ((
                $stats['totalCaloriesToLoseRemaining'] +
                $stats['currentCalorieDeficit']
            )) {
                $progress =
                    (($stats['currentCalorieDeficit'] /
                            (
                                $stats['totalCaloriesToLoseRemaining'] +
                                $stats['currentCalorieDeficit']
                            )
                        ) * 100
                    );
                if (!is_nan($progress) && !is_infinite($progress) &&
                    (is_float($progress) || is_int($progress))) {
                    $stats['deficitProgress'] = round($progress, 2);
                } else {
                    $stats['deficitProgress'] = 0;
                }
            } else {
                $stats['deficitProgress'] = 0;
            }
        } catch (\Exception $e) {
            $stats['deficitProgress'] = 0;
        }

        $stats['startWeight'] = $this->getStartWeight();
        $stats['goalWeight'] = $this->getEndWeight();
        $stats['deficitSinceLastWeighin'] = $this->user->getDeficitSinceLastWeighin();
        $stats['startDate'] = $this->getStartDate()->format("m/d/Y");
        $stats['endDate'] = $this->getEndDate()->format("m/d/Y");

        $stats['estimatedCurrentWeight'] = $this->user->getEstimatedCurrentWeight();

        return $stats;

    }
}