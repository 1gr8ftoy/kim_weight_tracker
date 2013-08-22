<?php

namespace BConway\TrackerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entry
 *
 * @ORM\Table(name="entries")
 * @ORM\Entity(repositoryClass="BConway\TrackerBundle\Entity\EntryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Entry
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
     * @ORM\ManyToOne(targetEntity="BConway\UserBundle\Entity\User", inversedBy="entries")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer")
     */
    private $weight;

    /**
     * @var integer
     *
     * @ORM\Column(name="deficit", type="integer")
     */
    private $deficit;

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
     * @var \DateTime
     *
     * @ORM\Column(name="entryDate", type="date")
     */
    private $entryDate;


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
     * Set weight
     *
     * @param integer $weight
     * @return Entry
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set deficit
     *
     * @param integer $deficit
     * @return Entry
     */
    public function setDeficit($deficit)
    {
        $this->deficit = $deficit;
    
        return $this;
    }

    /**
     * Get deficit
     *
     * @return integer 
     */
    public function getDeficit()
    {
        return $this->deficit;
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     * @return Entry
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
     * @ORM\PreUpdate
     * @return Entry
     */
    public function setUpdatedAt()
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
     * Set entryDate
     *
     * @param \DateTime $entryDate
     * @return Entry
     */
    public function setEntryDate($entryDate = null)
    {
        $updatedAt = ($entryDate == null)
            ? new DateTime()
            : $entryDate;

        $this->entryDate = $entryDate;
    
        return $this;
    }

    /**
     * Get entryDate
     *
     * @return \DateTime 
     */
    public function getEntryDate()
    {
        return $this->entryDate;
    }

    /**
     * Set user
     *
     * @param \BConway\UserBundle\Entity\User $user
     * @return Entry
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
}