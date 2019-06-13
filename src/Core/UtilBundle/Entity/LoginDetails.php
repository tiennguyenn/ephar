<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LoginDetails
 *
 * @ORM\Table(name="login_details", indexes={@ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity
 */
class LoginDetails
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user_name", type="string", length=60, nullable=false)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=50, nullable=false)
     */
    private $password;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_denied_access", type="boolean", nullable=false)
     */
    private $isDeniedAccess;

    /**
     * @var boolean
     *
     * @ORM\Column(name="force_password_change", type="boolean", nullable=false)
     */
    private $forcePasswordChange;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



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
     * Set userName
     *
     * @param string $userName
     * @return LoginDetails
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
     * Set password
     *
     * @param string $password
     * @return LoginDetails
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set isDeniedAccess
     *
     * @param boolean $isDeniedAccess
     * @return LoginDetails
     */
    public function setIsDeniedAccess($isDeniedAccess)
    {
        $this->isDeniedAccess = $isDeniedAccess;

        return $this;
    }

    /**
     * Get isDeniedAccess
     *
     * @return boolean 
     */
    public function getIsDeniedAccess()
    {
        return $this->isDeniedAccess;
    }

    /**
     * Set forcePasswordChange
     *
     * @param boolean $forcePasswordChange
     * @return LoginDetails
     */
    public function setForcePasswordChange($forcePasswordChange)
    {
        $this->forcePasswordChange = $forcePasswordChange;

        return $this;
    }

    /**
     * Get forcePasswordChange
     *
     * @return boolean 
     */
    public function getForcePasswordChange()
    {
        return $this->forcePasswordChange;
    }

    /**
     * Set user
     *
     * @param \UtilBundle\Entity\User $user
     * @return LoginDetails
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
}
