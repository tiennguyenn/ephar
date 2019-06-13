<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PharmacyPoDailyLine
 *
 * @ORM\Table(name="pharmacy_po_daily_line", indexes={@ORM\Index(name="FK_pharmacy_po_daily_line", columns={"po_daily_id"}), @ORM\Index(name="FK_pharmacy_po_daily_line_rx", columns={"rx_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PharmacyPoDailyLineRepository")
 */
class PharmacyPoDailyLine
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \PharmacyPoDaily
     *
     * @ORM\ManyToOne(targetEntity="PharmacyPoDaily")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="po_daily_id", referencedColumnName="id")
     * })
     */
    private $poDaily;

    /**
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx")
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return PharmacyPoDailyLine
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
     * Set poDaily
     *
     * @param \UtilBundle\Entity\PharmacyPoDaily $poDaily
     * @return PharmacyPoDailyLine
     */
    public function setPoDaily(\UtilBundle\Entity\PharmacyPoDaily $poDaily = null)
    {
        $this->poDaily = $poDaily;

        return $this;
    }

    /**
     * Get poDaily
     *
     * @return \UtilBundle\Entity\PharmacyPoDaily 
     */
    public function getPoDaily()
    {
        return $this->poDaily;
    }

    /**
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     * @return PharmacyPoDailyLine
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
}
