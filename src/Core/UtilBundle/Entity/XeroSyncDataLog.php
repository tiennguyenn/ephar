<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * XeroSyncDataLog
 *
 * @ORM\Table(name="xero_sync_data_log", indexes={@ORM\Index(name="FK_xero_sync_data_log", columns={"xero_sync_data_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class XeroSyncDataLog
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
     * @var string
     *
     * @ORM\Column(name="xero_post_xml_doc", type="blob", length=16777215, nullable=false)
     */
    private $xeroPostXmlDoc;

    /**
     * @var string
     *
     * @ORM\Column(name="xero_response_xml_doc", type="blob", length=16777215, nullable=false)
     */
    private $xeroResponseXmlDoc;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="error_code", type="string", length=5, nullable=true)
     */
    private $errorCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \XeroSyncData
     *
     * @ORM\ManyToOne(targetEntity="XeroSyncData")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_sync_data_id", referencedColumnName="id")
     * })
     */
    private $xeroSyncData;



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
     * Set xeroPostXmlDoc
     *
     * @param string $xeroPostXmlDoc
     *
     * @return XeroSyncDataLog
     */
    public function setXeroPostXmlDoc($xeroPostXmlDoc)
    {
        $this->xeroPostXmlDoc = $xeroPostXmlDoc;

        return $this;
    }

    /**
     * Get xeroPostXmlDoc
     *
     * @return string
     */
    public function getXeroPostXmlDoc()
    {
        return $this->xeroPostXmlDoc;
    }

    /**
     * Set xeroResponseXmlDoc
     *
     * @param string $xeroResponseXmlDoc
     *
     * @return XeroSyncDataLog
     */
    public function setXeroResponseXmlDoc($xeroResponseXmlDoc)
    {
        $this->xeroResponseXmlDoc = $xeroResponseXmlDoc;

        return $this;
    }

    /**
     * Get xeroResponseXmlDoc
     *
     * @return string
     */
    public function getXeroResponseXmlDoc()
    {
        return $this->xeroResponseXmlDoc;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroSyncDataLog
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
     * Set xeroSyncData
     *
     * @param \UtilBundle\Entity\XeroSyncData $xeroSyncData
     *
     * @return XeroSyncDataLog
     */
    public function setXeroSyncData(\UtilBundle\Entity\XeroSyncData $xeroSyncData = null)
    {
        $this->xeroSyncData = $xeroSyncData;

        return $this;
    }

    /**
     * Get xeroSyncData
     *
     * @return \UtilBundle\Entity\XeroSyncData
     */
    public function getXeroSyncData()
    {
        return $this->xeroSyncData;
    }

    /**
     * Set errorCode
     *
     * @param string $errorCode
     *
     * @return XeroSyncDataLog
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * Get errorCode
     *
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
