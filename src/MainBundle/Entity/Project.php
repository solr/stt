<?php
namespace MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="project")
 */
class Project
{
    /**
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;
    /**
     * @ORM\Column(type="float")
     */
    private $sort;

    /**
     * One Project has Many TimeLogs.
     * @ORM\OneToMany(targetEntity="TimeLog", mappedBy="project")
     */
    private $timeLogs;

    /**
     * One Project has Many Comments.
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="project")
     */
    private $comments;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->timeLogs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add timeLogs
     *
     * @param \MainBundle\Entity\TimeLog $timeLogs
     * @return Project
     */
    public function addTimeLog(\MainBundle\Entity\TimeLog $timeLogs)
    {
        $this->timeLogs[] = $timeLogs;

        return $this;
    }

    /**
     * Remove timeLogs
     *
     * @param \MainBundle\Entity\TimeLog $timeLogs
     */
    public function removeTimeLog(\MainBundle\Entity\TimeLog $timeLogs)
    {
        $this->timeLogs->removeElement($timeLogs);
    }

    /**
     * Get timeLogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTimeLogs()
    {
        return $this->timeLogs;
    }

    /**
     * Add comments
     *
     * @param \MainBundle\Entity\Comment $comments
     * @return Project
     */
    public function addComment(\MainBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \MainBundle\Entity\Comment $comments
     */
    public function removeComment(\MainBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return Project
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer 
     */
    public function getSort()
    {
        return $this->sort;
    }
}
