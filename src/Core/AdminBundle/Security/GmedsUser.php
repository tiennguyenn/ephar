<?php

namespace AdminBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Phuc Duong
 */
class GmedsUser implements UserInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var string
     */
    private $session_token;

    /**
     * @var timestamp
     */
    private $expire_at;

    /**
     * @var string
     */
    private $display_name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var boolean
     */
    private $status;

    /**
     * @var boolean
     */
    private $is_root;

    /**
     * @var timestamp
     */
    private $created_at;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var array
     */
    private $permissions;

    /**
     * @var object
     */
    private $platformSetting;

    /**
     * @var object
     */
    private $loggedUser;

    /**
     * @var boolean
     */
    private $isConfirmed;

    /**
     * @var string
     */
    private $avatar;

    /**
     * @var string
     */
    private $userCode;

    /**
     * @var string
     */
    private $doctorName;

    /**
     * @var string
     */
    private $doctorEmail;

    /**
     * @var boolean
     */
    private $isMPA;


    /**
     * $var timestamp
     */
    private $updatedTermCondition;

    /**
     * Constructor
     *
     * @param string $username
     * @param string $password
     * @param string $salt
     * @param array $roles
     */
    public function __construct($username, $password, $salt, array $roles, array $permissions)
    {
        $this->username = $username;
        $this->password = $password;
        $this->salt     = $salt;
        $this->roles    = $roles;
		$this->permissions = $permissions;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::getRoles()
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @author Phuc Duong
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::getSalt()
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::getPassword()
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Getter of email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Setter of email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Getter of ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter of ID
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::getUsername()
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\Security\Core\User\UserInterface::eraseCredentials()
     */
    public function eraseCredentials()
    {
    }

    /**
     * Getter of display name
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * Setter of display name
     *
     * @param string $display_name
     */
    public function setDisplayName($display_name)
    {
        $this->display_name = $display_name;
    }

    /**
     * @param $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param $permissions
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Getter of expire_at
     *
     * @return timestamp
     */
    public function getExpireAt()
    {
        return $this->expire_at;
    }

    /**
     * Setter of expire_at
     *
     * @param timestamp $expire
     */
    public function setExpireAt($expire_at)
    {
        $this->expire_at = $expire_at;
    }

    /**
     * Getter of status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Setter of status
     *
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Getter of created_at
     *
     * @return timestamp
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Setter of created_at
     *
     * @param timestamp $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * Getter of platformSetting
     *
     * @return timestamp
     */
    public function getPlatformSetting()
    {
        return $this->platformSetting;
    }

    /**
     * Setter of platformSetting
     *
     * @param object $platformSetting
     */
    public function setPlatformSetting($platformSetting)
    {
        $this->platformSetting = $platformSetting;
    }

    /**
     * Getter of loggedUser
     *
     * @return timestamp
     */
    public function getLoggedUser()
    {
        return $this->loggedUser;
    }

    /**
     * Setter of loggedUser
     *
     * @param object $loggedUser
     */
    public function setLoggedUser($loggedUser)
    {
        $this->loggedUser = $loggedUser;
    }

    /**
     * Getter of status
     *
     * @return boolean
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

    /**
     * Setter of isConfirmed
     *
     * @param boolean $isConfirmed
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = $isConfirmed;
    }

    /**
     * Getter of avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Setter of avatar
     *
     * @param string $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }
    
    /**
     * Getter of userCode
     *
     * @return string
     */
    public function getUserCode()
    {
        return $this->userCode;
    }

    /**
     * Setter of userCode
     *
     * @param string $userCode
     */
    public function setUserCode($userCode)
    {
        $this->userCode = $userCode;
    }

    /**
     * Getter of doctorName
     *
     * @return string
     */
    public function getDoctorName()
    {
        return $this->doctorName;
    }

    /**
     * Setter of doctorName
     *
     * @param string $doctorName
     */
    public function setDoctorName($doctorName)
    {
        $this->doctorName = $doctorName;
    }

    /**
     * Getter of doctorEmail
     *
     * @return string
     */
    public function getDoctorEmail()
    {
        return $this->doctorEmail;
    }

    /**
     * Setter of doctorEmail
     *
     * @param string $doctorEmail
     */
    public function setDoctorEmail($doctorEmail)
    {
        $this->doctorEmail = $doctorEmail;
    }

    /**
     * Getter of isMPA
     *
     * @return boolean
     */
    public function getIsMPA()
    {
        return $this->isMPA;
    }

    /**
     * Setter of isMPA
     *
     * @param string $isMPA
     */
    public function setIsMPA($isMPA)
    {
        $this->isMPA = $isMPA;
    }

    public function hasPermission($name)
    {
        return in_array($name, $this->permissions);
        
    }
    
    /**
     * Set updatedTermCondition
     *
     * @param timestamp $updatedTermCondition
     */
    public function setUpdatedTermCondition($updatedTermCondition)
    {
        $this->updatedTermCondition = $updatedTermCondition;

        return $this;
    }

    /**
     * Get updatedTermCondition
     *
     * @return timestamp
     */
    public function getUpdatedTermCondition()
    {
        return $this->updatedTermCondition;
    }
}
