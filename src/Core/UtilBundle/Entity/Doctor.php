<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Doctor
 *
 * @ORM\Table(name="doctor", uniqueConstraints={@ORM\UniqueConstraint(name="doctor_code", columns={"doctor_code"})}, indexes={@ORM\Index(name="personal_information_id", columns={"personal_information_id"}), @ORM\Index(name="medical_license_id", columns={"medical_license_id"}), @ORM\Index(name="bank_account_id", columns={"bank_account_id"}), @ORM\Index(name="sequence_numbers_id", columns={"sequence_numbers_id"}), @ORM\Index(name="FK_doctor_user", columns={"user_id"}), @ORM\Index(name="FK_doctor_code_medicine", columns={"gst_medicine_code_id"}), @ORM\Index(name="FK_doctor_code_service", columns={"gst_service_code_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\DoctorRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Doctor
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
     * @var boolean
     *
     * @ORM\Column(name="is_gst", type="boolean", nullable=true)
     */
    private $isGst;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="gst_effect_date", type="datetime", nullable=true)
     */
    private $gstEffectDate;

    /**
     * @var string
     *
     * @ORM\Column(name="gst_no", type="string", length=50, nullable=true)
     */
    private $gstNo;

    /**
     * @var string
     *
     * @ORM\Column(name="doctor_code", type="string", length=20, nullable=false)
     */
    private $doctorCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="global_id", type="integer", nullable=true)
     */
    private $globalId;

    /**
     * @var string
     *
     * @ORM\Column(name="signature_url", type="string", length=255, nullable=true)
     */
    private $signatureUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_photo_url", type="string", length=255, nullable=true)
     */
    private $profilePhotoUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="rx_review_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $rxReviewFee;

    /**
     * @var string
     *
     * @ORM\Column(name="rx_review_fee_local", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $rxReviewFeeLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="rx_review_fee_international", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $rxReviewFeeInternational;

    /**
     * @var string
     *
     * @ORM\Column(name="rx_fee_live_consult_local", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $rxFeeLiveConsultLocal;

    /**
     * @var string
     *
     * @ORM\Column(name="rx_fee_live_consult_international", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $rxFeeLiveConsultInternational;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_confirmed", type="integer", nullable=true)
     */
    private $isConfirmed;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_apply_3rd_agent", type="boolean", nullable=true)
     */
    private $isApply3rdAgent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_customize_medicine_enabled", type="boolean", nullable=true)
     */
    private $isCustomizeMedicineEnabled;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_term_condition", type="datetime", nullable=true)
     */
    private $updatedTermCondition;
    /**
     * @var string
     *
     * @ORM\Column(name="payment_gate", type="string", length=50, nullable=true)
     */
    private $paymentGate;

    /**
     * @var string
     *
     * @ORM\Column(name="new_payment_gate", type="string", length=50, nullable=true)
     */
    private $newPaymentGate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="payment_gate_effective", type="datetime", nullable=true)
     */
    private $paymentGateEffective;


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
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255, nullable=true)
     */
    private $displayName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \PersonalInformation
     *
     * @ORM\ManyToOne(targetEntity="PersonalInformation", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personal_information_id", referencedColumnName="id")
     * })
     */
    private $personalInformation;

    /**
     * @var \MedicalLicense
     *
     * @ORM\ManyToOne(targetEntity="MedicalLicense", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="medical_license_id", referencedColumnName="id")
     * })
     */
    private $medicalLicense;

    /**
     * @var \BankAccount
     *
     * @ORM\ManyToOne(targetEntity="BankAccount", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bank_account_id", referencedColumnName="id")
     * })
     */
    private $bankAccount;

    /**
     * @var \SequenceNumber
     *
     * @ORM\ManyToOne(targetEntity="SequenceNumber", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sequence_numbers_id", referencedColumnName="id")
     * })
     */
    private $sequenceNumbers;

    /**
     * @var \SequenceNumber
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \GstCode
     *
     * @ORM\ManyToOne(targetEntity="GstCode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gst_medicine_code_id", referencedColumnName="id")
     * })
     */
    private $gstMedicineCode;

    /**
     * @var \GstCode
     *
     * @ORM\ManyToOne(targetEntity="GstCode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="gst_service_code_id", referencedColumnName="id")
     * })
     */
    private $gstServiceCode;

    /**
     * @ORM\OneToMany(targetEntity="DoctorPhone", mappedBy="doctor", cascade={"persist", "remove" })
     */
    private $doctorPhones;

    /**
     * @ORM\OneToMany(targetEntity="Clinic", mappedBy="doctor", cascade={"persist", "remove" })
     */
    private $clinics;

    /**
     * @ORM\OneToMany(targetEntity="DoctorGstSetting", mappedBy="doctor", cascade={"persist", "remove" })
     */
    private $gstSettings;

    /**
     * @ORM\OneToMany(targetEntity="AgentDoctor", mappedBy="doctor", cascade={"persist", "remove" })
     */
    private $agentDoctors;

    /**
     * @ORM\ManyToMany(targetEntity="MedicalSpecialty", inversedBy="doctor")
     * @ORM\JoinTable(name="doctor_medical_specialty")
     */
    private $medicalSpecialty;

    /**
     * @ORM\ManyToMany(targetEntity="Identification", inversedBy="doctor", cascade={"persist", "remove" })
     * @ORM\JoinTable(name="doctor_identification")
     */
    private $identification;
    /**
     * @ORM\ManyToMany(targetEntity="Agent", inversedBy="doctors",cascade={"persist", "remove" })
     * @ORM\JoinTable(name="agent_doctor")
     */
    private $agents;

    /**
     * @ORM\ManyToMany(targetEntity="Address", inversedBy="doctors",cascade={"persist", "remove" })
     *
     * @ORM\JoinTable(name="doctor_address")
     */
    private $addresses;

    /**
     * @ORM\ManyToMany(targetEntity="MasterProxyAccount", inversedBy="doctors",cascade={"persist", "remove" })
     *
     * @ORM\JoinTable(name="master_proxy_account_doctor")
     */
    private $masterProxyAccounts;

    /**
     * @ORM\OneToMany(targetEntity="Rx", mappedBy="doctor", cascade={"persist", "remove" })
     *
     *
     */
    private $rxes;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="partner_client_id", type="integer", nullable=true)
     */
    private $partnerClientId;

    public function __construct()
    {
        $this->doctorPhones = new ArrayCollection();
        $this->clinics = new ArrayCollection();
        $this->gstSettings = new ArrayCollection();
        $this->agentDoctors = new ArrayCollection();
        $this->medicalSpecialty = new ArrayCollection();
        $this->identification = new ArrayCollection();
        $this->agents = new ArrayCollection();
        $this->rxes = new ArrayCollection();
        $this->masterProxyAccounts = new ArrayCollection();
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
     * Set isGst
     *
     * @param boolean $isGst
     * @return Doctor
     */
    public function setIsGst($isGst)
    {
        $this->isGst = $isGst;

        return $this;
    }

    /**
     * Get isGst
     *
     * @return boolean
     */
    public function getIsGst()
    {
        return $this->isGst;
    }

    /**
     * Set gstEffectDate
     *
     * @param \DateTime $gstEffectDate
     * @return Doctor
     */
    public function setGstEffectDate($gstEffectDate)
    {
        $this->gstEffectDate = $gstEffectDate;

        return $this;
    }

    /**
     * Get gstEffectDate
     *
     * @return \DateTime
     */
    public function getGstEffectDate()
    {
        return $this->gstEffectDate;
    }

    /**
     * Set gstNo
     *
     * @param string $gstNo
     * @return Doctor
     */
    public function setGstNo($gstNo)
    {
        $this->gstNo = $gstNo;

        return $this;
    }

    /**
     * Get gstNo
     *
     * @return string
     */
    public function getGstNo()
    {
        return $this->gstNo;
    }

    /**
     * Set doctorCode
     *
     * @param string $doctorCode
     * @return Doctor
     */
    public function setDoctorCode($doctorCode)
    {
        $this->doctorCode = $doctorCode;

        return $this;
    }

    /**
     * Get doctorCode
     *
     * @return string
     */
    public function getDoctorCode()
    {
        return $this->doctorCode;
    }

    /**
     * Set globalId
     *
     * @param integer $globalId
     * @return Doctor
     */
    public function setGlobalId($globalId)
    {
        $this->globalId = $globalId;

        return $this;
    }

    /**
     * Get globalId
     *
     * @return integer
     */
    public function getGlobalId()
    {
        return $this->globalId;
    }

    /**
     * Set signatureUrl
     *
     * @param string $signatureUrl
     * @return Doctor
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
     * Set profilePhotoUrl
     *
     * @param string $profilePhotoUrl
     * @return Doctor
     */
    public function setProfilePhotoUrl($profilePhotoUrl)
    {
        $this->profilePhotoUrl = $profilePhotoUrl;

        return $this;
    }

    /**
     * Get profilePhotoUrl
     *
     * @return string
     */
    public function getProfilePhotoUrl()
    {
        return $this->profilePhotoUrl;
    }

    /**
     * Set rxReviewFee
     *
     * @param string $rxReviewFee
     * @return Doctor
     */
    public function setRxReviewFee($rxReviewFee)
    {
        $this->rxReviewFee = $rxReviewFee;

        return $this;
    }

    /**
     * Get rxReviewFee
     *
     * @return string
     */
    public function getRxReviewFee()
    {
        return $this->rxReviewFee;
    }

    /**
     * Set rxReviewFeeLocal
     *
     * @param string $rxReviewFeeLocal
     * @return Doctor
     */
    public function setRxReviewFeeLocal($rxReviewFeeLocal)
    {
        $this->rxReviewFeeLocal = $rxReviewFeeLocal;

        return $this;
    }

    /**
     * Get rxReviewFeeLocal
     *
     * @return string
     */
    public function getRxReviewFeeLocal()
    {
        return $this->rxReviewFeeLocal;
    }

    /**
     * Set rxReviewFeeInternational
     *
     * @param string $rxReviewFeeInternational
     * @return Doctor
     */
    public function setRxReviewFeeInternational($rxReviewFeeInternational)
    {
        $this->rxReviewFeeInternational = $rxReviewFeeInternational;

        return $this;
    }

    /**
     * Get rxReviewFeeInternational
     *
     * @return string
     */
    public function getRxReviewFeeInternational()
    {
        return $this->rxReviewFeeInternational;
    }

    /**
     * Set rxFeeLiveConsultLocal
     *
     * @param string $rxFeeLiveConsultLocal
     * @return Doctor
     */
    public function setRxFeeLiveConsultLocal($rxFeeLiveConsultLocal)
    {
        $this->rxFeeLiveConsultLocal = $rxFeeLiveConsultLocal;

        return $this;
    }

    /**
     * Get rxFeeLiveConsultLocal
     *
     * @return string
     */
    public function getRxFeeLiveConsultLocal()
    {
        return $this->rxFeeLiveConsultLocal;
    }

    /**
     * Set rxFeeLiveConsultInternational
     *
     * @param string $rxFeeLiveConsultInternational
     * @return Doctor
     */
    public function setRxFeeLiveConsultInternational($rxFeeLiveConsultInternational)
    {
        $this->rxFeeLiveConsultInternational = $rxFeeLiveConsultInternational;

        return $this;
    }

    /**
     * Get rxFeeLiveConsultInternational
     *
     * @return string
     */
    public function getRxFeeLiveConsultInternational()
    {
        return $this->rxFeeLiveConsultInternational;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Doctor
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isConfirmed
     *
     * @param boolean $isConfirmed
     * @return Doctor
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    /**
     * Get isConfirmed
     *
     * @return boolean
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

    /**
     * Set updatedTermCondition
     *
     * @param \DateTime $updatedTermCondition
     * @return Doctor
     */
    public function setUpdatedTermCondition($updatedTermCondition)
    {
        $this->updatedTermCondition = $updatedTermCondition;

        return $this;
    }

    /**
     * Get updatedTermCondition
     *
     * @return \DateTime
     */
    public function getUpdatedTermCondition()
    {
        return $this->updatedTermCondition;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Doctor
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
     * @return Doctor
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
     * @return Doctor
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
     * Set personalInformation
     *
     * @param \UtilBundle\Entity\PersonalInformation $personalInformation
     * @return Doctor
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
     * Set medicalLicense
     *
     * @param \UtilBundle\Entity\MedicalLicense $medicalLicense
     * @return Doctor
     */
    public function setMedicalLicense(\UtilBundle\Entity\MedicalLicense $medicalLicense = null)
    {
        $this->medicalLicense = $medicalLicense;

        return $this;
    }

    /**
     * Get medicalLicense
     *
     * @return \UtilBundle\Entity\MedicalLicense
     */
    public function getMedicalLicense()
    {
        return $this->medicalLicense;
    }

    /**
     * Set bankAccount
     *
     * @param \UtilBundle\Entity\BankAccount $bankAccount
     * @return Doctor
     */
    public function setBankAccount(\UtilBundle\Entity\BankAccount $bankAccount = null)
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    /**
     * Get bankAccount
     *
     * @return \UtilBundle\Entity\BankAccount
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * Set sequenceNumbers
     *
     * @param \UtilBundle\Entity\SequenceNumber $sequenceNumbers
     * @return Doctor
     */
    public function setSequenceNumbers(\UtilBundle\Entity\SequenceNumber $sequenceNumbers = null)
    {
        $this->sequenceNumbers = $sequenceNumbers;
        return $this;
    }

    /**
     * Get sequenceNumbers
     *
     * @return \UtilBundle\Entity\SequenceNumber
     */
    public function getSequenceNumbers()
    {
        return $this->sequenceNumbers;
    }

    /**
     * Set gstMedicineCode
     *
     * @param \UtilBundle\Entity\GstCode $gstMedicineCode
     * @return Doctor
     */
    public function setGstMedicineCode(\UtilBundle\Entity\GstCode $gstMedicineCode = null)
    {
        $this->gstMedicineCode = $gstMedicineCode;

        return $this;
    }

    /**
     * Get gstMedicineCode
     *
     * @return \UtilBundle\Entity\GstCode
     */
    public function getGstMedicineCode()
    {
        return $this->gstMedicineCode;
    }

    /**
     * Set gstServiceCode
     *
     * @param \UtilBundle\Entity\GstCode $gstServiceCode
     * @return Doctor
     */
    public function setGstServiceCode(\UtilBundle\Entity\GstCode $gstServiceCode = null)
    {
        $this->gstServiceCode = $gstServiceCode;

        return $this;
    }

    /**
     * Get gstServiceCode
     *
     * @return \UtilBundle\Entity\GstCode
     */
    public function getGstServiceCode()
    {
        return $this->gstServiceCode;
    }

    /**
     * Add doctorPhones
     *
     * @param \UtilBundle\Entity\DoctorPhone $doctorPhones
     * @return Doctor
     */
    public function addDoctorPhone(\UtilBundle\Entity\DoctorPhone $doctorPhones)
    {
        $this->doctorPhones[] = $doctorPhones;
        $doctorPhones->setDoctor($this);
        return $this;
    }

    /**
     * Remove doctorPhones
     *
     * @param \UtilBundle\Entity\DoctorPhone $doctorPhones
     */
    public function removeDoctorPhone(\UtilBundle\Entity\DoctorPhone $doctorPhones)
    {
        $this->doctorPhones->removeElement($doctorPhones);
    }

    /**
     * Get doctorPhones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDoctorPhones()
    {
        return $this->doctorPhones;
    }

    /**
     * Add clinics
     *
     * @param \UtilBundle\Entity\Clinic $clinics
     * @return Doctor
     */
    public function addClinic(\UtilBundle\Entity\Clinic $clinics)
    {
        $this->clinics[] = $clinics;
        $clinics->setDoctor($this);
        return $this;
    }

    /**
     * Remove clinics
     *
     * @param \UtilBundle\Entity\Clinic $clinics
     */
    public function removeClinic(\UtilBundle\Entity\Clinic $clinics)
    {
        $this->clinics->removeElement($clinics);
    }

    /**
     * Get clinics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClinics()
    {
        return $this->clinics;
    }

    /**
     * Add gstSettings
     *
     * @param \UtilBundle\Entity\Clinic $gstSettings
     * @return Doctor
     */
    public function addGstSetting(\UtilBundle\Entity\DoctorGstSetting $gstSetting)
    {
        $this->gstSettings[] = $gstSetting;
        $gstSetting->setDoctor($this);
        return $this;
    }

    /**
     * Remove gstSettings
     *
     * @param \UtilBundle\Entity\Clinic $gstSettings
     */
    public function removeGstSetting(\UtilBundle\Entity\DoctorGstSetting $gstSetting)
    {
        $this->gstSettings->removeElement($gstSetting);
    }

    /**
     * Get gstSettings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGstSettings()
    {
        return $this->gstSettings;
    }

    /**
     * Add agentDoctors
     *
     * @param \UtilBundle\Entity\AgentDoctor $agentDoctors
     * @return Doctor
     */
    public function addAgentDoctor(\UtilBundle\Entity\AgentDoctor $agentDoctor)
    {
        $this->agentDoctors[] = $agentDoctor;
        $agentDoctor->setDoctor($this);
        return $this;
    }

    /**
     * Remove agentDoctors
     *
     * @param \UtilBundle\Entity\AgentDoctor $agentDoctors
     */
    public function removeAgentDoctor(\UtilBundle\Entity\AgentDoctor $agentDoctors)
    {
        $this->agentDoctors->removeElement($agentDoctors);
    }

    /**
     * Get agentDoctors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgentDoctors()
    {
        return $this->agentDoctors;
    }

    /**
     * Add medicalSpecialty
     *
     * @param \UtilBundle\Entity\MedicalSpecialty $medicalSpecialty
     * @return Doctor
     */
    public function addMedicalSpecialty(\UtilBundle\Entity\MedicalSpecialty $medicalSpecialty)
    {
        $this->medicalSpecialty[] = $medicalSpecialty;

        return $this;
    }

    /**
     * Remove medicalSpecialty
     *
     * @param \UtilBundle\Entity\MedicalSpecialty $medicalSpecialty
     */
    public function removeMedicalSpecialty(\UtilBundle\Entity\MedicalSpecialty $medicalSpecialty)
    {
        $this->medicalSpecialty->removeElement($medicalSpecialty);
    }

    /**
     * Get medicalSpecialty
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMedicalSpecialty()
    {
        return $this->medicalSpecialty;
    }

    /**
     * Add identification
     *
     * @param \UtilBundle\Entity\Identification $identification
     * @return Doctor
     */
    public function addIdentification(\UtilBundle\Entity\Identification $identification)
    {
        $this->identification[] = $identification;

        return $this;
    }

    /**
     * Remove identification
     *
     * @param \UtilBundle\Entity\Identification $identification
     */
    public function removeIdentification(\UtilBundle\Entity\Identification $identification)
    {
        $this->identification->removeElement($identification);
    }

    /**
     * Get identification
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIdentification()
    {
        return $this->identification;
    }

    /**
     * Add agents
     *
     * @param \UtilBundle\Entity\Agent $agents
     * @return Doctor
     */
    public function addAgent(\UtilBundle\Entity\Agent $agents)
    {
        $this->agents[] = $agents;

        return $this;
    }

    /**
     * Remove agents
     *
     * @param \UtilBundle\Entity\Agent $agents
     */
    public function removeAgent(\UtilBundle\Entity\Agent $agents)
    {
        $this->agents->removeElement($agents);
    }

    /**
     * Get agents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAgents()
    {
        return $this->agents;
    }
    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
        $this->updatedOn = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedOn = new \DateTime("now");
    }

    /**
     * Add address
     *
     * @param \UtilBundle\Entity\Address $address
     *
     * @return Doctor
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
     * Set user
     *
     * @param \UtilBundle\Entity\User $user
     *
     * @return Doctor
     */
    public function setUser(\UtilBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UtilBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set paymentGate
     *
     * @param string $paymentGate
     *
     * @return Doctor
     */
    public function setPaymentGate($paymentGate)
    {
        $this->paymentGate = $paymentGate;

        return $this;
    }

    /**
     * Get paymentGate
     *
     * @return string
     */
    public function getPaymentGate()
    {
        return $this->paymentGate;
    }

    /**
     * Set newPaymentGate
     *
     * @param string $newPaymentGate
     *
     * @return Doctor
     */
    public function setNewPaymentGate($newPaymentGate)
    {
        $this->newPaymentGate = $newPaymentGate;

        return $this;
    }

    /**
     * Get newPaymentGate
     *
     * @return string
     */
    public function getNewPaymentGate()
    {
        return $this->newPaymentGate;
    }

    /**
     * Set paymentGateEffective
     *
     * @param \DateTime $paymentGateEffective
     *
     * @return Doctor
     */
    public function setPaymentGateEffective($paymentGateEffective)
    {
        $this->paymentGateEffective = $paymentGateEffective;

        return $this;
    }

    /**
     * Get paymentGateEffective
     *
     * @return \DateTime
     */
    public function getPaymentGateEffective()
    {
        return $this->paymentGateEffective;
    }

    public  function getCurrentPaymentGate(){
        $result = '';
        if(empty($this->getPaymentGateEffective())){
            $result = $this->getPaymentGate();
        } else {
            $effect = $this->getPaymentGateEffective()->format('Y-m-d');
            if(strtotime($effect) < strtotime(date('Y-m-d'))){
                $result = $this->getNewPaymentGate();
            } else {
                $result = $this->getPaymentGate();
            }
        }
       return $result;
    }

    /**
     * Add rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     *
     * @return Doctor
     */
    public function addRx(\UtilBundle\Entity\Rx $rx)
    {
        $this->rxes[] = $rx;

        return $this;
    }

    /**
     * Remove rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     */
    public function removeRx(\UtilBundle\Entity\Rx $rx)
    {
        $this->rxes->removeElement($rx);
    }

    /**
     * Get rxes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRxes()
    {
        return $this->rxes;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     *
     * @return Doctor
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Get displayName for document
     *
     * @return string
     */
    public function showName()
    {
        $name = $this->displayName;
        $info = $this->getPersonalInformation();
        if(!empty($info)){
            if(empty($name)){
                $name = $info->getFullName(false);
            }
            $title = $info->getTitle();
            $name = $title. ' '. $name;
        }
        return $name;
    }

    /**
     * Add masterProxyAccount
     *
     * @param \UtilBundle\Entity\MasterProxyAccount $masterProxyAccount
     *
     * @return Doctor
     */
    public function addMasterProxyAccount(\UtilBundle\Entity\MasterProxyAccount $masterProxyAccount)
    {
        $this->masterProxyAccounts[] = $masterProxyAccount;

        return $this;
    }

    /**
     * Remove masterProxyAccount
     *
     * @param \UtilBundle\Entity\MasterProxyAccount $masterProxyAccount
     */
    public function removeMasterProxyAccount(\UtilBundle\Entity\MasterProxyAccount $masterProxyAccount)
    {
        $this->masterProxyAccounts->removeElement($masterProxyAccount);
    }

    /**
     * Get masterProxyAccounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMasterProxyAccounts()
    {
        return $this->masterProxyAccounts;
    }
    
    /**
     * Set partnerClientId
     *
     * @param string $partnerClientId
     *
     * @return Doctor
     */
    public function setPartnerClientId($partnerClientId)
    {
        $this->partnerClientId = $partnerClientId;

        return $this;
    }

    /**
     * Get partnerClientId
     *
     * @return integer
     */
    public function getPartnerClientId()
    {
        return $this->partnerClientId;
    }

    public function get3pa()
    {
        $agents = $this->getAgentDoctors();
        foreach ($agents as $value) {
            if ($value->getIsPrimary()) {
                continue;
            }

            $agent = $value->getAgent();
            if ($agent->getIsActive() && 
                    empty($value->getDeletedOn()) && 
                    $agent->getIs3paAgent()) {
                return $agent;
            }
        }

        return false;
    }

    /**
     * Set isApply3rdAgent
     *
     * @param boolean $isApply3rdAgent
     *
     * @return Doctor
     */
    public function setIsApply3rdAgent($isApply3rdAgent)
    {
        $this->isApply3rdAgent = $isApply3rdAgent;

        return $this;
    }

    /**
     * Get isApply3rdAgent
     *
     * @return boolean
     */
    public function getIsApply3rdAgent()
    {
        return $this->isApply3rdAgent;
    }

    /**
     * Set isCustomizeMedicineEnabled
     *
     * @param boolean $isCustomizeMedicineEnabled
     *
     * @return Doctor
     */
    public function setIsCustomizeMedicineEnabled($isCustomizeMedicineEnabled)
    {
        $this->isCustomizeMedicineEnabled = $isCustomizeMedicineEnabled;

        return $this;
    }

    /**
     * Get isCustomizeMedicineEnabled
     *
     * @return boolean
     */
    public function getIsCustomizeMedicineEnabled()
    {
        return $this->isCustomizeMedicineEnabled;
    }
}
