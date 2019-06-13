<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorDrug
 *
 * @ORM\Table(name="doctor_drug", indexes={@ORM\Index(name="FK_doctor_drug", columns={"doctor_id"}), @ORM\Index(name="FK_doctor_drug_1", columns={"drug_id"})})
 * @ORM\Entity
 */
class DoctorDrug
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
     * @ORM\Column(name="list_price_domestic", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $listPriceDomestic;

    /**
     * @var string
     *
     * @ORM\Column(name="list_price_domestic_new", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $listPriceDomesticNew;

    /**
     * @var string
     *
     * @ORM\Column(name="list_price_international", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $listPriceInternational;

    /**
     * @var string
     *
     * @ORM\Column(name="list_price_international_new", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $listPriceInternationalNew;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="effective_date", type="datetime", nullable=true)
     */
    private $effectiveDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var \Drug
     *
     * @ORM\ManyToOne(targetEntity="Drug")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="drug_id", referencedColumnName="id")
     * })
     */
    private $drug;

    /**
     * @var \Doctor
     *
     * @ORM\ManyToOne(targetEntity="Doctor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_id", referencedColumnName="id")
     * })
     */
    private $doctor;



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
     * Set listPriceDomestic
     *
     * @param string $listPriceDomestic
     *
     * @return DoctorDrug
     */
    public function setListPriceDomestic($listPriceDomestic)
    {
        $this->listPriceDomestic = $listPriceDomestic;

        return $this;
    }

    /**
     * Get listPriceDomestic
     *
     * @return string
     */
    public function getListPriceDomestic()
    {
        return $this->listPriceDomestic;
    }

    /**
     * Set listPriceDomesticNew
     *
     * @param string $listPriceDomesticNew
     *
     * @return DoctorDrug
     */
    public function setListPriceDomesticNew($listPriceDomesticNew)
    {
        $this->listPriceDomesticNew = $listPriceDomesticNew;

        return $this;
    }

    /**
     * Get listPriceDomesticNew
     *
     * @return string
     */
    public function getListPriceDomesticNew()
    {
        return $this->listPriceDomesticNew;
    }

    /**
     * Set listPriceInternational
     *
     * @param string $listPriceInternational
     *
     * @return DoctorDrug
     */
    public function setListPriceInternational($listPriceInternational)
    {
        $this->listPriceInternational = $listPriceInternational;

        return $this;
    }

    /**
     * Get listPriceInternational
     *
     * @return string
     */
    public function getListPriceInternational()
    {
        return $this->listPriceInternational;
    }

    /**
     * Set listPriceInternationalNew
     *
     * @param string $listPriceInternationalNew
     *
     * @return DoctorDrug
     */
    public function setListPriceInternationalNew($listPriceInternationalNew)
    {
        $this->listPriceInternationalNew = $listPriceInternationalNew;

        return $this;
    }

    /**
     * Get listPriceInternational
     *
     * @return string
     */
    public function getListPriceInternationalNew()
    {
        return $this->listPriceInternationalNew;
    }

    /**
     * Set effectiveDate
     *
     * @param \DateTime $effectiveDate
     *
     * @return DoctorDrug
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    /**
     * Get effectiveDate
     *
     * @return \DateTime
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return DoctorDrug
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
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return DoctorDrug
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return \DateTime
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * Set drug
     *
     * @param \UtilBundle\Entity\Drug $drug
     *
     * @return DoctorDrug
     */
    public function setDrug(\UtilBundle\Entity\Drug $drug = null)
    {
        $this->drug = $drug;

        return $this;
    }

    /**
     * Get drug
     *
     * @return \UtilBundle\Entity\Drug
     */
    public function getDrug()
    {
        return $this->drug;
    }

    /**
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     *
     * @return DoctorDrug
     */
    public function setDoctor(\UtilBundle\Entity\Doctor $doctor = null)
    {
        $this->doctor = $doctor;

        return $this;
    }

    /**
     * Get doctor
     *
     * @return \UtilBundle\Entity\Doctor
     */
    public function getDoctor()
    {
        return $this->doctor;
    }
}
