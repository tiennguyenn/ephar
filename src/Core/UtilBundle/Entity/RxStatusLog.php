<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RxStatusLog
 *
 * @ORM\Table(name="rx_status_log", indexes={@ORM\Index(name="rx_id", columns={"rx_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\RxStatusLogRepository")
 * @ORM\HasLifecycleCallbacks
 */
class RxStatusLog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", length=65535, nullable=true)
     */
    private $notes;

    /**
     * @var string
     *
     * @ORM\Column(name="issue_notes", type="text", length=65535, nullable=true)
     */
    private $issueNotes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=50, nullable=false)
     */
    private $createdBy;

    /**
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx", inversedBy="rxStatusLogs",cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_id", referencedColumnName="id")
     * })
     */
    private $rx;



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
     * Set status
     *
     * @param integer $status
     *
     * @return RxStatusLog
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return RxStatusLog
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set issueNotes
     *
     * @param string $issueNotes
     *
     * @return RxStatusLog
     */
    public function setIssueNotes($issueNotes)
    {
        $this->issueNotes = $issueNotes;

        return $this;
    }

    /**
     * Get issueNotes
     *
     * @return string
     */
    public function getIssueNotes()
    {
        return $this->issueNotes;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return RxStatusLog
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     *
     * @return RxStatusLog
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     *
     * @return RxStatusLog
     */
    public function setRx(\UtilBundle\Entity\Rx $rx = null)
    {
        $this->rx = $rx;

        return $this;
    }

    /**
     * Get rx
     *
     * @return \UtilBundle\Entity\Rx
     */
    public function getRx()
    {
        return $this->rx;
    }
    
     /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
    }

    
}
