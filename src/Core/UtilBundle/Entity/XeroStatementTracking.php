<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroStatementTracking
 *
 * @ORM\Table(name="xero_statement_tracking")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroStatementTrackingRepository")
 * @ORM\HasLifecycleCallbacks
 */
class XeroStatementTracking
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
     * @ORM\Column(name="statement_id", type="integer", nullable=true)
     */
    private $statementId;

    /**
     * @var integer
     *
     * @ORM\Column(name="statement_type", type="integer", nullable=true)
     */
    private $statementType;
    /**
     * @var integer
     *
     * @ORM\Column(name="site_code", type="integer", nullable=true)
     */
    private $siteCode;


    /**
     * @var string
     *
     * @ORM\Column(name="function_name", type="string", length=250, nullable=true)
     */
    private $functionName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tracking_date", type="datetime", nullable=true)
     */
    private $trackingDate;


    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
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
     * Set statementId
     *
     * @param integer $statementId
     *
     * @return XeroStatementTracking
     */
    public function setStatementId($statementId)
    {
        $this->statementId = $statementId;

        return $this;
    }

    /**
     * Get statementId
     *
     * @return integer
     */
    public function getStatementId()
    {
        return $this->statementId;
    }

    /**
     * Set statementType
     *
     * @param boolean $statementType
     *
     * @return XeroStatementTracking
     */
    public function setStatementType($statementType)
    {
        $this->statementType = $statementType;

        return $this;
    }

    /**
     * Get statementType
     *
     * @return boolean
     */
    public function getStatementType()
    {
        return $this->statementType;
    }

    /**
     * Set functionName
     *
     * @param string $functionName
     *
     * @return XeroStatementTracking
     */
    public function setFunctionName($functionName)
    {
        $this->functionName = $functionName;

        return $this;
    }

    /**
     * Get functionName
     *
     * @return string
     */
    public function getFunctionName()
    {
        return $this->functionName;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroStatementTracking
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
     * Set siteCode
     *
     * @param integer $siteCode
     *
     * @return XeroStatementTracking
     */
    public function setSiteCode($siteCode)
    {
        $this->siteCode = $siteCode;

        return $this;
    }

    /**
     * Get siteCode
     *
     * @return integer
     */
    public function getSiteCode()
    {
        return $this->siteCode;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return XeroStatementTracking
     */
    public function setDeletedOn($deletedOn)
    {
        $this->deletedOn = $deletedOn;

        return $this;
    }

    /**
     * Get deletedOn
     *
     * @return \DateTime
     */
    public function getDeletedOn()
    {
        return $this->deletedOn;
    }

    /**
     * Set trackingDate
     *
     * @param \DateTime $trackingDate
     *
     * @return XeroStatementTracking
     */
    public function setTrackingDate($trackingDate)
    {
        $this->trackingDate = $trackingDate;

        return $this;
    }

    /**
     * Get trackingDate
     *
     * @return \DateTime
     */
    public function getTrackingDate()
    {
        return $this->trackingDate;
    }
}
