<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroSiteBatch
 *
 * @ORM\Table(name="xero_site_batch", indexes={@ORM\Index(name="FK_xero_site_batch", columns={"site_id"})})
 * @ORM\Entity
 */
class XeroSiteBatch
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
     * @ORM\Column(name="batch_site_label", type="string", length=50, nullable=true)
     */
    private $batchSiteLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="gmedes_code", type="string", length=50, nullable=true)
     */
    private $gmedesCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \Site
     *
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * })
     */
    private $site;



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
     * Set batchSiteLabel
     *
     * @param string $batchSiteLabel
     *
     * @return XeroSiteBatch
     */
    public function setBatchSiteLabel($batchSiteLabel)
    {
        $this->batchSiteLabel = $batchSiteLabel;

        return $this;
    }

    /**
     * Get batchSiteLabel
     *
     * @return string
     */
    public function getBatchSiteLabel()
    {
        return $this->batchSiteLabel;
    }

    /**
     * Set gmedesCode
     *
     * @param string $gmedesCode
     *
     * @return XeroSiteBatch
     */
    public function setGmedesCode($gmedesCode)
    {
        $this->gmedesCode = $gmedesCode;

        return $this;
    }

    /**
     * Get gmedesCode
     *
     * @return string
     */
    public function getGmedesCode()
    {
        return $this->gmedesCode;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return XeroSiteBatch
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
     * Set site
     *
     * @param \UtilBundle\Entity\Site $site
     *
     * @return XeroSiteBatch
     */
    public function setSite(\UtilBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \UtilBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }
}
