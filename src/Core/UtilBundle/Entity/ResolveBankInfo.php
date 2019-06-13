<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveBankInfo
 *
 * @ORM\Table(name="resolve_bank_info", indexes={@ORM\Index(name="FK_resolve_bank_info", columns={"resolve_id"})})
 * @ORM\Entity
 */
class ResolveBankInfo
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
     * @ORM\Column(name="bank_code", type="string", length=20, nullable=true)
     */
    private $bankCode;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_name", type="string", length=250, nullable=true)
     */
    private $bankName;

    /**
     * @var string
     *
     * @ORM\Column(name="bank_account_no", type="string", length=20, nullable=true)
     */
    private $bankAccountNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Resolve
     *
     * @ORM\ManyToOne(targetEntity="Resolve")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_id", referencedColumnName="id")
     * })
     */
    private $resolve;



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
     * Set bankCode
     *
     * @param string $bankCode
     *
     * @return ResolveBankInfo
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    /**
     * Get bankCode
     *
     * @return string
     */
    public function getBankCode()
    {
        return $this->bankCode;
    }

    /**
     * Set bankName
     *
     * @param string $bankName
     *
     * @return ResolveBankInfo
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * Get bankName
     *
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * Set bankAccountNo
     *
     * @param string $bankAccountNo
     *
     * @return ResolveBankInfo
     */
    public function setBankAccountNo($bankAccountNo)
    {
        $this->bankAccountNo = $bankAccountNo;

        return $this;
    }

    /**
     * Get bankAccountNo
     *
     * @return string
     */
    public function getBankAccountNo()
    {
        return $this->bankAccountNo;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return ResolveBankInfo
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
     * Set resolve
     *
     * @param \UtilBundle\Entity\Resolve $resolve
     *
     * @return ResolveBankInfo
     */
    public function setResolve(\UtilBundle\Entity\Resolve $resolve = null)
    {
        $this->resolve = $resolve;

        return $this;
    }

    /**
     * Get resolve
     *
     * @return \UtilBundle\Entity\Resolve
     */
    public function getResolve()
    {
        return $this->resolve;
    }
}
