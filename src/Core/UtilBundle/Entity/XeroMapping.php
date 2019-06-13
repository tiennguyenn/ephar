<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * XeroMapping
 *
 * @ORM\Table(name="xero_mapping", indexes={@ORM\Index(name="xero_document_type_id", columns={"xero_document_type_id"}), @ORM\Index(name="debit_account_code_id", columns={"debit_account_code_id"}), @ORM\Index(name="credit_account_code_id", columns={"credit_account_code_id"}), @ORM\Index(name="FK_xero_mapping", columns={"xero_component_id"}), @ORM\Index(name="FK_xero_mapping_header", columns={"xero_mapping_header_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroMappingRepository")
 */
class XeroMapping
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
     * @ORM\Column(name="order_destination", type="integer", nullable=true)
     */
    private $orderDestination;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_gateway", type="string", length=50, nullable=true)
     */
    private $paymentGateway;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string", length=50, nullable=true)
     */
    private $paymentMethod;

    /**
     * @var string
     *
     * @ORM\Column(name="narration", type="text", length=65535, nullable=true)
     */
    private $narration;

    /**
     * @var string
     *
     * @ORM\Column(name="payee", type="string", length=250, nullable=true)
     */
    private $payee;
    /**
     * @var string
     *
     * @ORM\Column(name="option_mapping", type="string", length=250, nullable=true)
     */
    private $option;
    /**
     * @var string
     *
     * @ORM\Column(name="tlr_column", type="string", length=250, nullable=true)
     */
    private $tlrColumn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_custom_narration", type="boolean", nullable=true)
     */
    private $isCustomNarration = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_pushed_xero", type="boolean", nullable=true)
     */
    private $isPushXero = '1';
    
    /**
     * @var string
     *
     * @ORM\Column(name="code_tmp", type="string", length=250, nullable=true)
     */
    private $codeTmp;

    /**
     * @var integer
     *
     * @ORM\Column(name="account_code_type_used", type="integer", nullable=true)
     */
    private $accountCodeTypeUsed;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_negative", type="integer", nullable=true)
     */
    private $isNegative;

    /**
     * @var string
     *
     * @ORM\Column(name="parent_code_tmp", type="string", length=250, nullable=true)
     */
    private $parentCodeTmp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \XeroMapping
     *
     * @ORM\ManyToOne(targetEntity="XeroMapping", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_mapping_header_id", referencedColumnName="id")
     * })
     */
    private $xeroMappingHeader;

    /**
     * @var \XeroComponent
     *
     * @ORM\ManyToOne(targetEntity="XeroComponent", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_component_id", referencedColumnName="id")
     * })
     */
    private $xeroComponent;

    /**
     * @var \XeroDocumentType
     *
     * @ORM\ManyToOne(targetEntity="XeroDocumentType", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_document_type_id", referencedColumnName="id")
     * })
     */
    private $xeroDocumentType;

    /**
     * @var \XeroAccountCode
     *
     * @ORM\ManyToOne(targetEntity="XeroAccountCode", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="debit_account_code_id", referencedColumnName="id")
     * })
     */
    private $debitAccountCode;

    /**
     * @var \XeroAccountCode
     *
     * @ORM\ManyToOne(targetEntity="XeroAccountCode", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="credit_account_code_id", referencedColumnName="id")
     * })
     */
    private $creditAccountCode;

    /**
     * @ORM\OneToMany(targetEntity="XeroMapping", mappedBy="xeroMappingHeader", cascade={"persist", "remove" })
     */
    private $children;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
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
     * Set orderDestination
     *
     * @param integer $orderDestination
     *
     * @return XeroMapping
     */
    public function setOrderDestination($orderDestination)
    {
        $this->orderDestination = $orderDestination;

        return $this;
    }

    /**
     * Get orderDestination
     *
     * @return integer
     */
    public function getOrderDestination()
    {
        return $this->orderDestination;
    }

    /**
     * Set paymentGateway
     *
     * @param string $paymentGateway
     *
     * @return XeroMapping
     */
    public function setPaymentGateway($paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;

        return $this;
    }

    /**
     * Get paymentGateway
     *
     * @return string
     */
    public function getPaymentGateway()
    {
        return $this->paymentGateway;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     *
     * @return XeroMapping
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * Set narration
     *
     * @param string $narration
     *
     * @return XeroMapping
     */
    public function setNarration($narration)
    {
        $this->narration = $narration;

        return $this;
    }

    /**
     * Get narration
     *
     * @return string
     */
    public function getNarration()
    {
        return $this->narration;
    }

    /**
     * Set payee
     *
     * @param string $payee
     *
     * @return XeroMapping
     */
    public function setPayee($payee)
    {
        $this->payee = $payee;

        return $this;
    }

    /**
     * Get payee
     *
     * @return string
     */
    public function getPayee()
    {
        return $this->payee;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return XeroMapping
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
     * Set xeroMappingHeader
     *
     * @param \UtilBundle\Entity\XeroMapping $xeroMappingHeader
     *
     * @return XeroMapping
     */
    public function setXeroMappingHeader(\UtilBundle\Entity\XeroMapping $xeroMappingHeader = null)
    {
        $this->xeroMappingHeader = $xeroMappingHeader;

        return $this;
    }

    /**
     * Get xeroMappingHeader
     *
     * @return \UtilBundle\Entity\XeroMapping
     */
    public function getXeroMappingHeader()
    {
        return $this->xeroMappingHeader;
    }

    /**
     * Set xeroComponent
     *
     * @param \UtilBundle\Entity\XeroComponent $xeroComponent
     *
     * @return XeroMapping
     */
    public function setXeroComponent(\UtilBundle\Entity\XeroComponent $xeroComponent = null)
    {
        $this->xeroComponent = $xeroComponent;

        return $this;
    }

    /**
     * Get xeroComponent
     *
     * @return \UtilBundle\Entity\XeroComponent
     */
    public function getXeroComponent()
    {
        return $this->xeroComponent;
    }

    /**
     * Set xeroDocumentType
     *
     * @param \UtilBundle\Entity\XeroDocumentType $xeroDocumentType
     *
     * @return XeroMapping
     */
    public function setXeroDocumentType(\UtilBundle\Entity\XeroDocumentType $xeroDocumentType = null)
    {
        $this->xeroDocumentType = $xeroDocumentType;

        return $this;
    }

    /**
     * Get xeroDocumentType
     *
     * @return \UtilBundle\Entity\XeroDocumentType
     */
    public function getXeroDocumentType()
    {
        return $this->xeroDocumentType;
    }

    /**
     * Set debitAccountCode
     *
     * @param \UtilBundle\Entity\XeroAccountCode $debitAccountCode
     *
     * @return XeroMapping
     */
    public function setDebitAccountCode(\UtilBundle\Entity\XeroAccountCode $debitAccountCode = null)
    {
        $this->debitAccountCode = $debitAccountCode;

        return $this;
    }

    /**
     * Get debitAccountCode
     *
     * @return \UtilBundle\Entity\XeroAccountCode
     */
    public function getDebitAccountCode()
    {
        return $this->debitAccountCode;
    }

    /**
     * Set creditAccountCode
     *
     * @param \UtilBundle\Entity\XeroAccountCode $creditAccountCode
     *
     * @return XeroMapping
     */
    public function setCreditAccountCode(\UtilBundle\Entity\XeroAccountCode $creditAccountCode = null)
    {
        $this->creditAccountCode = $creditAccountCode;

        return $this;
    }

    /**
     * Get creditAccountCode
     *
     * @return \UtilBundle\Entity\XeroAccountCode
     */
    public function getCreditAccountCode()
    {
        return $this->creditAccountCode;
    }

    /**
     * Set codeTmp
     *
     * @param string $codeTmp
     *
     * @return XeroMapping
     */
    public function setCodeTmp($codeTmp)
    {
        $this->codeTmp = $codeTmp;

        return $this;
    }

    /**
     * Get codeTmp
     *
     * @return string
     */
    public function getCodeTmp()
    {
        return $this->codeTmp;
    }

    /**
     * Set parentCodeTmp
     *
     * @param string $parentCodeTmp
     *
     * @return XeroMapping
     */
    public function setParentCodeTmp($parentCodeTmp)
    {
        $this->parentCodeTmp = $parentCodeTmp;

        return $this;
    }

    /**
     * Get parentCodeTmp
     *
     * @return string
     */
    public function getParentCodeTmp()
    {
        return $this->parentCodeTmp;
    }

    /**
     * Add child
     *
     * @param \UtilBundle\Entity\XeroMapping $child
     *
     * @return XeroMapping
     */
    public function addChild(\UtilBundle\Entity\XeroMapping $child)
    {
        $child->setXeroMappingHeader($this);
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \UtilBundle\Entity\XeroMapping $child
     */
    public function removeChild(\UtilBundle\Entity\XeroMapping $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set option
     *
     * @param string $option
     *
     * @return XeroMapping
     */
    public function setOption($option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set accountCodeTypeUsed
     *
     * @param integer $accountCodeTypeUsed
     *
     * @return XeroMapping
     */
    public function setAccountCodeTypeUsed($accountCodeTypeUsed)
    {
        $this->accountCodeTypeUsed = $accountCodeTypeUsed;

        return $this;
    }

    /**
     * Get accountCodeTypeUsed
     *
     * @return integer
     */
    public function getAccountCodeTypeUsed()
    {
        return $this->accountCodeTypeUsed;
    }

    /**
     * Set isNegative
     *
     * @param integer $isNegative
     *
     * @return XeroMapping
     */
    public function setIsNegative($isNegative)
    {
        $this->isNegative = $isNegative;

        return $this;
    }

    /**
     * Get isNegative
     *
     * @return integer
     */
    public function getIsNegative()
    {
        return $this->isNegative;
    }

    /**
     * Set isCustomNarration
     *
     * @param boolean $isCustomNarration
     *
     * @return XeroMapping
     */
    public function setIsCustomNarration($isCustomNarration)
    {
        $this->isCustomNarration = $isCustomNarration;

        return $this;
    }

    /**
     * Get isCustomNarration
     *
     * @return boolean
     */
    public function getIsCustomNarration()
    {
        return $this->isCustomNarration;
    }

    /**
     * Set isPushXero
     *
     * @param boolean $isPushXero
     *
     * @return XeroMapping
     */
    public function setIsPushXero($isPushXero)
    {
        $this->isPushXero = $isPushXero;

        return $this;
    }

    /**
     * Get isPushXero
     *
     * @return boolean
     */
    public function getIsPushXero()
    {
        return $this->isPushXero;
    }

    /**
     * Set tlrColumn
     *
     * @param string $tlrColumn
     *
     * @return XeroMapping
     */
    public function setTlrColumn($tlrColumn)
    {
        $this->tlrColumn = $tlrColumn;

        return $this;
    }

    /**
     * Get tlrColumn
     *
     * @return string
     */
    public function getTlrColumn()
    {
        return $this->tlrColumn;
    }
}
