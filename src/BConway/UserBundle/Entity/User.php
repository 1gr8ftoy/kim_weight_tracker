<?php

namespace BConway\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="BConway\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="sign_in_count", type="integer")
     */
    protected $signInCount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="current_sign_in_at", type="datetime")
     */
    protected $currentSignInAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_sign_in_at", type="datetime")
     */
    protected $lastSignInAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=TRUE)
     */
    protected $height;

    /**
     * @ORM\OneToMany(targetEntity="BConway\TrackerBundle\Entity\Goal", mappedBy="user")
     */
    protected $goals;

    /**
     * @ORM\OneToMany(targetEntity="BConway\TrackerBundle\Entity\Entry", mappedBy="user")
     */
    protected $entries;

    /**
     * Subset of entries including weight
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $entriesWithWeight;

    /**
     * Subset of entries including deficit
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $entriesWithDeficit;

    public function __construct()
    {
        parent::__construct();

        $this->entries  = new ArrayCollection();
        $this->goals    = new ArrayCollection();
    }

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
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set signInCount
     *
     * @param integer $signInCount
     * @return User
     */
    public function setSignInCount($signInCount)
    {
        $this->signInCount = $signInCount;

        return $this;
    }

    /**
     * Get signInCount
     *
     * @return integer
     */
    public function getSignInCount()
    {
        return $this->signInCount;
    }

    /**
     * Set currentSignInAt
     *
     * @param \DateTime $currentSignInAt
     * @return User
     */
    public function setCurrentSignInAt($currentSignInAt)
    {
        $this->currentSignInAt = $currentSignInAt;

        return $this;
    }

    /**
     * Get currentSignInAt
     *
     * @return \DateTime
     */
    public function getCurrentSignInAt()
    {
        return $this->currentSignInAt;
    }

    /**
     * Set lastSignInAt
     *
     * @param \DateTime $lastSignInAt
     * @return User
     */
    public function setLastSignInAt($lastSignInAt)
    {
        $this->lastSignInAt = $lastSignInAt;

        return $this;
    }

    /**
     * Get lastSignInAt
     *
     * @return \DateTime
     */
    public function getLastSignInAt()
    {
        return $this->lastSignInAt;
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
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
     * @ORM\PreUpdate
     * @param \DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = new \DateTime();

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
     * Set height
     *
     * @param integer $height
     * @return User
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Add goals
     *
     * @param \BConway\TrackerBundle\Entity\Goal $goals
     * @return User
     */
    public function addGoal(\BConway\TrackerBundle\Entity\Goal $goals)
    {
        $this->goals[] = $goals;

        return $this;
    }

    /**
     * Remove goals
     *
     * @param \BConway\TrackerBundle\Entity\Goal $goals
     */
    public function removeGoal(\BConway\TrackerBundle\Entity\Goal $goals)
    {
        $this->goals->removeElement($goals);
    }

    /**
     * Get goals
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * Add entries
     *
     * @param \BConway\TrackerBundle\Entity\Entry $entries
     * @return User
     */
    public function addEntry(\BConway\TrackerBundle\Entity\Entry $entries)
    {
        $this->entries[] = $entries;

        return $this;
    }

    /**
     * Remove entries
     *
     * @param \BConway\TrackerBundle\Entity\Entry $entries
     */
    public function removeEntry(\BConway\TrackerBundle\Entity\Entry $entries)
    {
        $this->entries->removeElement($entries);
    }

    /**
     * Get entries
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Get only entries that include a weight
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEntriesWithWeight() {
        if (is_null($this->entriesWithWeight)) {
            $this->entriesWithWeight = new ArrayCollection();
            foreach ($this->entries as $entry) {
                $weight = $entry->getWeight();
                if (!empty($weight)) {
                    $this->entriesWithWeight->add($entry);
                }
            }
        }

        return $this->entriesWithWeight;
    }

    /**
     * Get only entries that include a deficit
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEntriesWithDeficit() {
        if (is_null($this->entriesWithDeficit)) {
            $this->entriesWithDeficit = new ArrayCollection();
            foreach ($this->entries as $entry) {
                $deficit = $entry->getDeficit();
                if (!empty($deficit)) {
                    $this->entriesWithDeficit->add($entry);
                }
            }
        }

        return $this->entriesWithDeficit;
    }

    public function getNumberOfWeighins() {
        return count($this->getEntriesWithWeight());
    }

    public function getFirstWeighinWeight() {
        if (count($this->getEntriesWithWeight()) > 0) {
            $weight = $this->getEntriesWithWeight()->first()->getWeight();

            return $weight;
        } else {
            return null;
        }
    }

    public function getMostRecentWeighinWeight() {
        if (count($this->getEntriesWithWeight()) > 0) {
            $weight = $this->getEntriesWithWeight()->last()->getWeight();

            return $weight;
        } else {
            return null;
        }
    }

    public function getFirstWeighinDate() {
        if (count($this->getEntriesWithWeight()) > 0) {
            $date = $this->getEntriesWithWeight()->first()->getEntryDate();

            return $date;
        } else {
            return null;
        }
    }

    public function getMostRecentWeighinDate() {
        if (count($this->getEntriesWithWeight()) > 0) {
            $date = $this->getEntriesWithWeight()->last()->getEntryDate();

            return $date;
        } else {
            return null;
        }
    }

    public function getNumberOfDeficitEntries() {
        return count($this->getEntriesWithDeficit());
    }

    /**
     * @return ArrayCollection
     */
    private function getDeficitEntriesSinceLastWeighin() {
        $entries = new ArrayCollection();
        foreach ($this->getEntriesWithDeficit() as $entry) {
            if ($entry->getEntryDate() > $this->getMostRecentWeighinDate()) {
                $entries->add($entry);
            }
        }

        return $entries;
    }
    /**
     * @return integer
     */
    public function getDeficitSinceLastWeighin()
    {
        $deficit_entries_since_last_weighin = $this->getDeficitEntriesSinceLastWeighin();

        if (count($deficit_entries_since_last_weighin) > 0) {
            $deficit = 0;
            foreach ($deficit_entries_since_last_weighin as $entry) {
                $deficit += $entry->getDeficit();
            }
            return $deficit;
        } else {
            return 0;
        }
    }

    /**
     * @return float|null
     */
    public function getAverageDeficit() {
        $average = 0;

        foreach($this->getEntriesWithDeficit() as $entry) {
            $average += $entry->getDeficit();
        }

        try {
            if (count($this->getEntriesWithDeficit())) {
                $average = round($average / count($this->getEntriesWithDeficit()), 1);

                return $average;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return float|string
     */
    public function getBMI() {
        if (
            $this->getMostRecentWeighinDate() &&
            !empty($this->height)
        ) {
            return round((($this->getMostRecentWeighinWeight() / ($this->height * $this->height)) * 703), 2);
        } elseif (empty($this->height)) {
            return "Insufficient data, please enter height in profile";
        } else {
            return "Insufficient data";
        }
    }

    /**
     * @return integer|string
     */
    public function getEstimatedCurrentWeight() {
        if (count($this->getEntriesWithWeight()) > 0 && $this->getMostRecentWeighinWeight()) {
            $weight = $this->getMostRecentWeighinWeight();
            $deficit_since_last_weighin = $this->getDeficitSinceLastWeighin();
            $deficit_entries_since_last_weighin = $this->getDeficitEntriesSinceLastWeighin();

            $days_since_last_weighin =
                (int)(ceil(abs(time() - $this->getMostRecentWeighinDate()->getTimestamp())/60/60/24));

            try {
                if (count($deficit_entries_since_last_weighin)) {
                    $average_deficit_since_last_weighin =
                        round(($deficit_since_last_weighin / count($deficit_entries_since_last_weighin)), 2);
                } else {
                    $average_deficit_since_last_weighin = 0;
                }
            } catch (\Exception $e) {
                $average_deficit_since_last_weighin = 0;
            }

            try {
                $weight = round((float)$weight - (((float)$average_deficit_since_last_weighin * (float)$days_since_last_weighin) / 3500.0), 1);
            } catch (\Exception $e) {
                $weight = "Unknown";
            }

            if (is_infinite($weight) || is_nan($weight)) {
                return "Unknown";
            } else {
                return $weight;
            }
        } else {
            return "Unknown";
        }
    }

    /**
     * @return array
     */
    public function getStats() {
        $stats = array();

        $stats['numberOfWeighins'] = $this->getNumberOfWeighins();
        $stats['numberOfDeficitEntries'] = $this->getNumberOfDeficitEntries();

        if ($stats['numberOfWeighins']) {
            $stats['firstWeighinDate'] = $this->getFirstWeighinDate()->format("m/d/Y");
            $stats['firstWeighinWeight'] = $this->getFirstWeighinWeight();
            $stats['mostRecentWeighinDate'] = $this->getMostRecentWeighinDate()->format("m/d/Y");
            $stats['mostRecentWeighinWeight'] = $this->getMostRecentWeighinWeight();
            $stats['averageDeficit'] = $this->getAverageDeficit();
            $stats['bmi'] = $this->getBMI();
            $stats['estimatedCurrentWeight'] = $this->getEstimatedCurrentWeight();
        }

        return $stats;
    }
}