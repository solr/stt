<?php
namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="timelog")
 */
class TimeLog
{
    /**
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $year;
    /**
     * @ORM\Column(type="integer")
     */
    private $week;
    /**
     * @ORM\Column(type="integer")
     */
    private $day;

    /**
    * @ORM\Column(type="text")
    */
    private $username;
    
    /**
     * Many TimeLogs have One Project.
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="timeLogs")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\Column(type="float")
     */
    private $timelog;
	
	/**
     * @ORM\Column(type="text")
     */
    private $description;

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
     * Set year
     *
     * @param integer $year
     * @return TimeLog
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set week
     *
     * @param integer $week
     * @return TimeLog
     */
    public function setWeek($week)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * Get week
     *
     * @return integer 
     */
    public function getWeek()
    {
        return $this->week;
    }

    /**
     * Set day
     *
     * @param integer $day
     * @return TimeLog
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return integer 
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return TimeLog
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
     * Set timelog
     *
     * @param float $timelog
     * @return TimeLog
     */
    public function setTimelog($timelog)
    {
        $this->timelog = $timelog;

        return $this;
    }

    /**
     * Get timelog
     *
     * @return float 
     */
    public function getTimelog()
    {
        return $this->timelog;
    }

    /**
     * Set project
     *
     * @param \MainBundle\Entity\Project $project
     * @return TimeLog
     */
    public function setProject(\MainBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \MainBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return TimeLog
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
