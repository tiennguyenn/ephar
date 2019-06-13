<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Patient
 *
 * @ORM\Table(name="patient", uniqueConstraints={@ORM\UniqueConstraint(name="patient_code", columns={"patient_code"})}, indexes={@ORM\Index(name="doctor_id", columns={"doctor_id"}), @ORM\Index(name="primary_residence_country_id", columns={"primary_residence_country_id"}), @ORM\Index(name="personal_information_id", columns={"personal_information_id"}), @ORM\Index(name="issue_country_id", columns={"issue_country_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PatientRepository")
 */
class Patient
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
     * @ORM\Column(name="patient_code", type="string", length=20, nullable=false)
     */
    private $patientCode;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_id", type="string", length=20, nullable=false)
     */
    private $taxId;

    /**
     * @var string
     *
     * @ORM\Column(name="global_id", type="string", length=36, nullable=false)
     */
    private $globalId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_assessed", type="boolean", nullable=true)
     */
    private $isAssessed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_enrolled", type="boolean", nullable=true)
     */
    private $isEnrolled;

    /**
     * @var boolean
     *
     * @ORM\Column(name="use_caregiver", type="boolean", nullable=false)
     */
    private $useCaregiver;

    /**
     * @var string
     *
     * @ORM\Column(name="signature", type="blob", nullable=true)
     */
    private $signature;

    /**
     * @var string
     *
     * @ORM\Column(name="address_in_ktp", type="string", nullable=true)
     */
    private $addressInKtp;

    /**
     * @var boolean
     *
     * @ORM\Column(name="has_future_rx", type="boolean", nullable=true)
     */
    private $hasFutureRx;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_send_mail_to_caregiver", type="boolean", nullable=true)
     */
    private $isSendMailToCaregiver = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_prescription_date", type="datetime", nullable=true)
     */
    private $lastPrescriptionDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="first_refill_reminder_date", type="datetime", nullable=true)
     */
    private $firstRefillReminderDate;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issue_country_id", referencedColumnName="id")
     * })
     */
    private $issueCountry;

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
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="primary_residence_country_id", referencedColumnName="id")
     * })
     */
    private $primaryResidenceCountry;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nationality", referencedColumnName="id")
     * })
     */
    private $nationality;

    /**
     * @var \PersonalInformation
     *
     * @ORM\ManyToOne(targetEntity="PersonalInformation", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personal_information_id", referencedColumnName="id")
     * })
     */
    private $personalInformation;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Diagnosis", inversedBy="patient")
     * @ORM\JoinTable(name="patient_diagnosis",
     *   joinColumns={
     *     @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="diagnosis_id", referencedColumnName="id")
     *   }
     * )
     */
    private $diagnosis;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="PatientMedicationAllergy", mappedBy="patient", cascade={"persist"}, orphanRemoval=true)
     */
    private $allergies;

    /**
     * @var CareGiver
     *
     * @ORM\OneToMany(targetEntity="CareGiver", mappedBy="patient", cascade={"persist"})
     */
    private $caregivers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Phone", inversedBy="patient", cascade={"persist"})
     * @ORM\JoinTable(name="patient_phone")
     */
    private $phones;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Address", inversedBy="patients", cascade={"persist"})
     * @ORM\JoinTable(name="patient_address")
     */
    private $addresses;
    /**
     * @var CareGiver
     *
     * @ORM\OneToMany(targetEntity="PatientNote", mappedBy="patient", cascade={"persist"})
     */

    private $notes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->diagnosis = new ArrayCollection();
        $this->allergies = new ArrayCollection();
        $this->phones = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->caregivers = new ArrayCollection();
        $this->notes = new ArrayCollection();
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
     * Set taxId
     *
     * @param string $taxId
     * @return Patient
     */
    public function setTaxId($taxId)
    {
        $this->taxId = $taxId;

        return $this;
    }

    /**
     * Get taxId
     *
     * @return string
     */
    public function getTaxId()
    {
        return $this->taxId;
    }


    /**
     * Set patientCode
     *
     * @param string $patientCode
     * @return Patient
     */
    public function setPatientCode($patientCode)
    {
        $this->patientCode = $patientCode;

        return $this;
    }

    /**
     * Get patientCode
     *
     * @return string
     */
    public function getPatientCode()
    {
        return $this->patientCode;
    }

    /**
     * Set globalId
     *
     * @param string $globalId
     * @return Patient
     */
    public function setGlobalId($globalId)
    {
        $this->globalId = $globalId;

        return $this;
    }

    /**
     * Get globalId
     *
     * @return string
     */
    public function getGlobalId()
    {
        return $this->globalId;
    }

    /**
     * Set isAssessed
     *
     * @param boolean $isAssessed
     * @return Patient
     */
    public function setIsAssessed($isAssessed)
    {
        $this->isAssessed = $isAssessed;

        return $this;
    }

    /**
     * Get isAssessed
     *
     * @return boolean
     */
    public function getIsAssessed()
    {
        return $this->isAssessed;
    }

    /**
     * Set useCaregiver
     *
     * @param boolean $useCaregiver
     * @return Patient
     */
    public function setUseCaregiver($useCaregiver)
    {
        $this->useCaregiver = $useCaregiver;

        return $this;
    }

    /**
     * Get useCaregiver
     *
     * @return boolean
     */
    public function getUseCaregiver()
    {
        return $this->useCaregiver;
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

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Patient
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
     * @return Patient
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return Patient
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
     * Set lastPrescriptionDate
     *
     * @param \DateTime $lastPrescriptionDate
     * @return Patient
     */
    public function setLastPrescriptionDate($lastPrescriptionDate)
    {
        $this->lastPrescriptionDate = $lastPrescriptionDate;

        return $this;
    }

    /**
     * Get lastPrescriptionDate
     *
     * @return \DateTime
     */
    public function getLastPrescriptionDate()
    {
        return $this->lastPrescriptionDate;
    }

    /**
     * Set firstRefillReminderDate
     *
     * @param \DateTime $firstRefillReminderDate
     * @return Patient
     */
    public function setFirstRefillReminderDate($firstRefillReminderDate)
    {
        $this->firstRefillReminderDate = $firstRefillReminderDate;

        return $this;
    }

    /**
     * Get firstRefillReminderDate
     *
     * @return \DateTime
     */
    public function getFirstRefillReminderDate()
    {
        return $this->firstRefillReminderDate;
    }

    /**
     * Set isSendMailToCaregiver
     *
     * @param boolean $isSendMailToCaregiver
     *
     * @return Patient
     */
    public function setIsSendMailToCaregiver($isSendMailToCaregiver)
    {
        $this->isSendMailToCaregiver = $isSendMailToCaregiver;

        return $this;
    }

    /**
     * Get isSendMailToCaregiver
     *
     * @return boolean
     */
    public function getIsSendMailToCaregiver()
    {
        return $this->isSendMailToCaregiver;
    }

    /**
     * Set issueCountry
     *
     * @param \UtilBundle\Entity\Country $issueCountry
     * @return Patient
     */
    public function setIssueCountry(\UtilBundle\Entity\Country $issueCountry = null)
    {
        $this->issueCountry = $issueCountry;

        return $this;
    }

    /**
     * Get issueCountry
     *
     * @return \UtilBundle\Entity\Country
     */
    public function getIssueCountry()
    {
        return $this->issueCountry;
    }

    /**
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return Patient
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


    /**
     * Set nationality
     *
     * @param \UtilBundle\Entity\Country $nationality
     * @return Patient
     */
    public function setNationality(\UtilBundle\Entity\Country $nationality = null)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return \UtilBundle\Entity\Country
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set primaryResidenceCountry
     *
     * @param \UtilBundle\Entity\Country $primaryResidenceCountry
     * @return Patient
     */
    public function setPrimaryResidenceCountry(\UtilBundle\Entity\Country $primaryResidenceCountry = null)
    {
        $this->primaryResidenceCountry = $primaryResidenceCountry;

        return $this;
    }

    /**
     * Get primaryResidenceCountry
     *
     * @return \UtilBundle\Entity\Country
     */
    public function getPrimaryResidenceCountry()
    {
        return $this->primaryResidenceCountry;
    }

    /**
     * Set personalInformation
     *
     * @param \UtilBundle\Entity\PersonalInformation $personalInformation
     * @return Patient
     */
    public function setPersonalInformation(\UtilBundle\Entity\PersonalInformation $personalInformation = null)
    {
        $this->personalInformation = $personalInformation;

        return $this;
    }

    /**
     * Get personalInformation
     *
     * @return \UtilBundle\Entity\PersonalInformation
     */
    public function getPersonalInformation()
    {
        return $this->personalInformation;
    }

    /**
     * Add diagnosis
     *
     * @param \UtilBundle\Entity\Diagnosis $diagnosis
     * @return Patient
     */
    public function addDiagnosi(\UtilBundle\Entity\Diagnosis $diagnosis)
    {
        $this->diagnosis[] = $diagnosis;

        return $this;
    }

    /**
     * Remove diagnosis
     *
     * @param \UtilBundle\Entity\Diagnosis $diagnosis
     */
    public function removeDiagnosi(\UtilBundle\Entity\Diagnosis $diagnosis)
    {
        $this->diagnosis->removeElement($diagnosis);
    }

    /**
     * Get diagnosis
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDiagnosis()
    {
        return $this->diagnosis;
    }

    /**
     * Add allergy
     *
     * @param PatientMedicationAllergy $allergy
     * @return Patient
     */
    public function addAllergy(PatientMedicationAllergy $allergy)
    {
        $allergy->setPatient($this);
        $this->allergies[] = $allergy;

        return $this;
    }

    /**
     * Remove allergy
     *
     * @param PatientMedicationAllergy $allergy
     */
    public function removeAllergy(PatientMedicationAllergy $allergy)
    {
        $this->allergies->removeElement($allergy);
    }

    /**
     * Get allergies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAllergies()
    {
        return $this->allergies;
    }

    /**
     * Add caregiver
     *
     * @param CareGiver $caregiver
     * @return Patient
     */
    public function addCaregiver(CareGiver $caregiver)
    {
        $caregiver->setPatient($this);
        $caregiver->setCreatedOn(new \DateTime());
        $this->caregivers[] = $caregiver;

        return $this;
    }

    /**
     * Get caregiver
     *
     * @return CareGiver
     */
    public function removeCaregiver(CareGiver $caregiver)
    {
        return $this->caregivers->removeElement($caregiver);
    }

    /**
     * Get caregivers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCaregivers()
    {
        return $this->caregivers;
    }

    /**
     * Add phone
     *
     * @param Phone $phone
     * @return Patient
     */
    public function addPhone(Phone $phone)
    {
        $this->phones[] = $phone;

        return $this;
    }

    /**
     * Remove phone
     *
     * @param Phone $phone
     */
    public function removePhone(Phone $phone)
    {
        $this->phones->removeElement($phone);
    }

    /**
     * Get phones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Add address
     *
     * @param \UtilBundle\Entity\Address $address
     *
     * @return Patient
     */
    public function addAddress(\UtilBundle\Entity\Address $address)
    {
        $this->addresses[] = $address;

        return $this;
    }

    /**
     * Remove address
     *
     * @param \UtilBundle\Entity\Address $address
     */
    public function removeAddress(\UtilBundle\Entity\Address $address)
    {
        $this->addresses->removeElement($address);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Set isEnrolled
     *
     * @param boolean $isEnrolled
     *
     * @return Patient
     */
    public function setIsEnrolled($isEnrolled)
    {
        $this->isEnrolled = $isEnrolled;

        return $this;
    }

    /**
     * Get isEnrolled
     *
     * @return boolean
     */
    public function getIsEnrolled()
    {
        return $this->isEnrolled;
    }

    /**
     * Add note
     *
     * @param \UtilBundle\Entity\PatientNote $note
     *
     * @return Patient
     */
    public function addNote(\UtilBundle\Entity\PatientNote $note)
    {
        $note->setPatient($this);
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param \UtilBundle\Entity\PatientNote $note
     */
    public function removeNote(\UtilBundle\Entity\PatientNote $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set setHasFutureRx
     *
     * @param boolean $hasFutureRx
     *
     * @return Patient
     */
    public function setHasFutureRx($hasFutureRx)
    {
        $this->hasFutureRx = $hasFutureRx;

        return $this;
    }

    /**
     * Get hasFutureRx
     *
     * @return boolean
     */
    public function getHasFutureRx()
    {
        return $this->hasFutureRx;
    }
}
