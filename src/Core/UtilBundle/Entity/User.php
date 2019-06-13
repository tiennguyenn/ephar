<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * User
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="email_unique", columns={"email_address"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User
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
     * @ORM\Column(name="global_id", type="integer", nullable=false)
     */
    private $globalId;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=150, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=150, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=1, nullable=true)
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_photo_url", type="string", length=350, nullable=true)
     */
    private $profilePhotoUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="email_address", type="string", length=250, nullable=false)
     */
    private $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=256, nullable=true)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="password_hash", type="string", length=350, nullable=false)
     */
    private $passwordHash;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_super_user", type="boolean", nullable=false)
     */
    private $isSuperUser;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked_out", type="boolean", nullable=false)
     */
    private $isLockedOut;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_lockout_enabled", type="boolean", nullable=false)
     */
    private $isLockoutEnabled;

    /**
     * @var integer
     *
     * @ORM\Column(name="otp_code", type="integer", nullable=true)
     */
    private $otpCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="otp_expired_at", type="datetime", nullable=true)
     */
    private $otpExpiredAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expired_password_change", type="datetime", nullable=true)
     */
    private $expiredPasswordChange;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * 
     *
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users", inversedBy="doctors",cascade={"persist", "remove" })
     * @ORM\JoinTable(name="user_role")
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=32, nullable=true)
     */
    private $sessionId;

    /**
     * @var string
     *
     * @ORM\Column(name="google_auth_secret", type="string", length=30, nullable=true)
     */
    private $googleAuthSecret;

    /**
     * @ORM\OneToMany(targetEntity="Doctor", mappedBy="user", cascade={"persist", "remove" })
     */
    private $doctors;

    /**
     * @var string
     *
     * @ORM\Column(name="user_ip", type="string", length=39, nullable=true)
     */
    private $userIp;

    /**
     * @var integer
     *
     * @ORM\Column(name="failed_login_count", type="integer", nullable=true)
     */
    private $failedLoginCount;

    /**
     * @var string
     *
     * @ORM\Column(name="e_signature", type="string", length=250, nullable=true)
     */
    private $eSignature;

    /**
     * @var string
     *
     * @ORM\Column(name="license_no", type="string", length=20, nullable=true)
     */
    private $licenseNo;

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
     * Set globalId
     *
     * @param integer $globalId
     * @return User
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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set profilePhotoUrl
     *
     * @param string $profilePhotoUrl
     * @return User
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
     * Set emailAddress
     *
     * @param string $emailAddress
     * @return User
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress
     *
     * @return string 
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set userName
     *
     * @param string $userName
     * @return User
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string 
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set passwordHash
     *
     * @param string $passwordHash
     * @return User
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    /**
     * Get passwordHash
     *
     * @return string 
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * Set isSuperUser
     *
     * @param boolean $isSuperUser
     * @return User
     */
    public function setIsSuperUser($isSuperUser)
    {
        $this->isSuperUser = $isSuperUser;

        return $this;
    }

    /**
     * Get isSuperUser
     *
     * @return boolean 
     */
    public function getIsSuperUser()
    {
        return $this->isSuperUser;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
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
     * Set isLockedOut
     *
     * @param boolean $isLockedOut
     * @return User
     */
    public function setIsLockedOut($isLockedOut)
    {
        $this->isLockedOut = $isLockedOut;

        return $this;
    }

    /**
     * Get isLockedOut
     *
     * @return boolean 
     */
    public function getIsLockedOut()
    {
        return $this->isLockedOut;
    }

    /**
     * Set isLockoutEnabled
     *
     * @param boolean $isLockoutEnabled
     * @return User
     */
    public function setIsLockoutEnabled($isLockoutEnabled)
    {
        $this->isLockoutEnabled = $isLockoutEnabled;

        return $this;
    }

    /**
     * Get isLockoutEnabled
     *
     * @return boolean 
     */
    public function getIsLockoutEnabled()
    {
        return $this->isLockoutEnabled;
    }

    /**
     * Set otpCode
     *
     * @param integer $otpCode
     * @return User
     */
    public function setOtpCode($otpCode)
    {
        $this->otpCode = $otpCode;

        return $this;
    }

    /**
     * Get otpCode
     *
     * @return integer 
     */
    public function getOtpCode()
    {
        return $this->otpCode;
    }

    /**
     * Set otpExpiredAt
     *
     * @param \DateTime $otpExpiredAt
     * @return User
     */
    public function setOtpExpiredAt($otpExpiredAt)
    {
        $this->otpExpiredAt = $otpExpiredAt;

        return $this;
    }

    /**
     * Get otpExpiredAt
     *
     * @return \DateTime 
     */
    public function getOtpExpiredAt()
    {
        return $this->otpExpiredAt;
    }

    /**
     * Set expiredPasswordChange
     *
     * @param \DateTime $expiredPasswordChange
     * @return User
     */
    public function setExpiredPasswordChange($expiredPasswordChange)
    {
        $this->expiredPasswordChange = $expiredPasswordChange;

        return $this;
    }

    /**
     * Get expiredPasswordChange
     *
     * @return \DateTime 
     */
    public function getExpiredPasswordChange()
    {
        return $this->expiredPasswordChange;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime 
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->doctors = new ArrayCollection();
    }


    /**
     * Add role
     *
     * @param \UtilBundle\Entity\Role $role
     *
     * @return User
     */
    public function addRole(\UtilBundle\Entity\Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * Remove role
     *
     * @param \UtilBundle\Entity\Role $role
     */
    public function removeRole(\UtilBundle\Entity\Role $role)
    {
        $this->roles->removeElement($role);
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
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
     * Set userIp
     *
     * @param string $userIp
     * @return User
     */
    public function setUserIp($userIp)
    {
        $this->userIp = $userIp;

        return $this;
    }

    /**
     * Get userIp
     *
     * @return string 
     */
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * Set failedLoginCount
     *
     * @param integer $failedLoginCount
     * @return User
     */
    public function setFailedLoginCount($failedLoginCount)
    {
        $this->failedLoginCount = $failedLoginCount;

        return $this;
    }

    /**
     * Get failedLoginCount
     *
     * @return integer 
     */
    public function getFailedLoginCount()
    {
        return $this->failedLoginCount;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId
     * @return User
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string 
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Add doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     *
     * @return User
     */
    public function addDoctor(\UtilBundle\Entity\Doctor $doctor)
    {
        $this->doctors[] = $doctor;

        return $this;
    }

    /**
     * Remove doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     */
    public function removeDoctor(\UtilBundle\Entity\Doctor $doctor)
    {
        $this->doctors->removeElement($doctor);
    }

    /**
     * Get doctors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDoctors()
    {
        return $this->doctors;
    }

    /**
     * Set eSignature
     *
     * @param string $eSignature
     * @return User
     */
    public function setESignature($eSignature)
    {
        $this->eSignature = $eSignature;

        return $this;
    }

    /**
     * Get eSignature
     *
     * @return string
     */
    public function getESignature()
    {
        return $this->eSignature;
    }

    /**
     * Set licenseNo
     *
     * @param string $licenseNo
     * @return User
     */
    public function setLicenseNo($licenseNo)
    {
        $this->licenseNo = $licenseNo;

        return $this;
    }

    /**
     * Get licenseNo
     *
     * @return string
     */
    public function getLicenseNo()
    {
        return $this->licenseNo;
    }

    /**
     * Set googleAuthSecret
     *
     * @param string $googleAuthSecret
     * @return User
     */
    public function setGoogleAuthSecret($googleAuthSecret)
    {
        $this->googleAuthSecret = $googleAuthSecret;

        return $this;
    }

    /**
     * Get googleAuthSecret
     *
     * @return string
     */
    public function getGoogleAuthSecret()
    {
        return $this->googleAuthSecret;
    }
}
