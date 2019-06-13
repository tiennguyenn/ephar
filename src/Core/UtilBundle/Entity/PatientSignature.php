<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PatientSignature
 *
 * @ORM\Table(name="patient_signature", indexes={@ORM\Index(name="FK_patient_signature", columns={"patient_id"})})
 * @ORM\Entity
 */
class PatientSignature
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
     * @ORM\Column(name="signature", type="blob", nullable=true)
     */
    private $signature;

    /**
     * @var string
     *
     * @ORM\Column(name="signature_url", type="string", length=250, nullable=true)
     */
    private $signatureUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="address_in_ktp", type="string", nullable=true)
     */
    private $addressInKtp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Patient
     *
     * @ORM\ManyToOne(targetEntity="Patient")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     * })
     */
    private $patient;



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
     * Set signature
     *
     * @param string $signature
     * @return string
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    /**
     * Get signature
     *
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * Set signatureUrl
     *
     * @param string $signatureUrl
     *
     * @return PatientSignature
     */
    public function setSignatureUrl($signatureUrl)
    {
        $this->signatureUrl = $signatureUrl;

        return $this;
    }

    /**
     * Get signatureUrl
     *
     * @return string
     */
    public function getSignatureUrl()
    {
        return $this->signatureUrl;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return PatientSignature
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
     * Set patient
     *
     * @param \UtilBundle\Entity\Patient $patient
     *
     * @return PatientSignature
     */
    public function setPatient(\UtilBundle\Entity\Patient $patient = null)
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * Get patient
     *
     * @return \UtilBundle\Entity\Patient
     */
    public function getPatient()
    {
        return $this->patient;
    }

    /**
     * Set addressInKtp
     *
     * @param string $addressInKtp
     * @return string
     */
    public function setAddressInKtp($addressInKtp)
    {
        $this->addressInKtp = $addressInKtp;

        return $this;
    }

    /**
     * Get addressInKtp
     *
     * @return string
     */
    public function getAddressInKtp()
    {
        return $this->addressInKtp;
    }
}
