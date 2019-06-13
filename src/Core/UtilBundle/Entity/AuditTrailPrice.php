<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * AuditTrailPrice
 *
 * @ORM\Table(name="audit_trail_price")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\AuditTrailPriceRepository")
 * @ORM\HasLifecycleCallbacks
 */
class AuditTrailPrice
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
     * @ORM\Column(name="entity_id", type="integer", nullable=true)
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(name="table_name", type="string", length=50, nullable=true)
     */
    private $tableName;

    /**
     * @var string
     *
     * @ORM\Column(name="field_name", type="string", length=50, nullable=true)
     */
    private $fieldName;

    /**
     * @var string
     *
     * @ORM\Column(name="old_price", type="text", length=65535, nullable=true)
     */
    private $oldPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="new_price", type="text", length=65535, nullable=true)
     */
    private $newPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="effected_on", type="datetime", nullable=true)
     */
    private $effectedOn;

    public function __construct() {
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
     * Set entityId
     *
     * @param integer $entityId
     * @return Log
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set tableName
     *
     * @param string $tableName
     * @return Log
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Get tableName
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set fieldName
     *
     * @param string $fieldName
     * @return Log
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Get fieldName
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Set oldPrice
     *
     * @param string $oldPrice
     * @return Log
     */
    public function setOldPrice($oldPrice)
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    /**
     * Get oldPrice
     *
     * @return string
     */
    public function getOldPrice()
    {
        return $this->oldPrice;
    }

    /**
     * Set newPrice
     *
     * @param string $newPrice
     * @return Log
     */
    public function setNewPrice($newPrice)
    {
        $this->newPrice = $newPrice;

        return $this;
    }

    /**
     * Get newPrice
     *
     * @return string
     */
    public function getNewPrice()
    {
        return $this->newPrice;
    }

    /**
     * Set effectedOn
     *
     * @param \DateTime $effectedOn
     * @return Log
     */
    public function setEffectedOn($effectedOn)
    {
        $this->effectedOn = $effectedOn;

        return $this;
    }

    /**
     * Get effectedOn
     *
     * @return \DateTime
     */
    public function getEffectedOn()
    {
        return $this->effectedOn;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Log
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
     * @return Log
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
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
