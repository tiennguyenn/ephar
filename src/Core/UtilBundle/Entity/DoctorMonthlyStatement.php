<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorMonthlyStatement
 *
 * @ORM\Table(name="doctor_monthly_statement")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\DoctorMSRepository")
 */
class DoctorMonthlyStatement
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
     * @ORM\Column(name="month", type="integer", nullable=true)
     */
    private $month;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="total_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $totalAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="projected_payment_date", type="date", nullable=true)
     */
    private $projectedPaymentDate;

    /**
     * @var string
     *
     * @ORM\Column(name="out_standing_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $outStandingAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="after_excludement_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $afterExcludementAmount;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="statement_date", type="date", nullable=true)
     */
    private $statementDate;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_filename", type="string", length=100, nullable=true)
     */
    private $invoiceFilename;

    /**
     * @var string
     *
     * @ORM\Column(name="total_amount_paid", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $totalAmountPaid;

    /**
     * @var string
     *
     * @ORM\Column(name="total_exception_statement", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $totalExceptionStatement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="date", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     *
     * @ORM\OneToMany(targetEntity="DoctorMonthlyStatementLine", mappedBy="doctorMonthlyStatement", cascade={"persist", "remove"})
     */
    private $lines;

    public function __construct()
    {
        $this->lines = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @var DoctorMonthlyStatementLine
     */
    public function addLine(DoctorMonthlyStatementLine $line)
    {
        $line->setDoctorMonthlyStatement($this);
        $this->lines->add($line);
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
     * Set month
     *
     * @param integer $month
     * @return DoctorMonthlyStatement
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer 
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return DoctorMonthlyStatement
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set totalAmount
     *
     * @param string $totalAmount
     * @return DoctorMonthlyStatement
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get totalAmount
     *
     * @return string 
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Set projectedPaymentDate
     *
     * @param \DateTime $projectedPaymentDate
     * @return DoctorMonthlyStatement
     */
    public function setProjectedPaymentDate($projectedPaymentDate)
    {
        $this->projectedPaymentDate = $projectedPaymentDate;

        return $this;
    }

    /**
     * Get projectedPaymentDate
     *
     * @return \DateTime 
     */
    public function getProjectedPaymentDate()
    {
        return $this->projectedPaymentDate;
    }

    /**
     * Set outStandingAmount
     *
     * @param string $outStandingAmount
     * @return DoctorMonthlyStatement
     */
    public function setOutStandingAmount($outStandingAmount)
    {
        $this->outStandingAmount = $outStandingAmount;

        return $this;
    }

    /**
     * Get outStandingAmount
     *
     * @return string 
     */
    public function getOutStandingAmount()
    {
        return $this->outStandingAmount;
    }

    /**
     * Set afterExcludementAmount
     *
     * @param string $afterExcludementAmount
     * @return DoctorMonthlyStatement
     */
    public function setAfterExcludementAmount($afterExcludementAmount)
    {
        $this->afterExcludementAmount = $afterExcludementAmount;

        return $this;
    }

    /**
     * Get afterExcludementAmount
     *
     * @return string 
     */
    public function getAfterExcludementAmount()
    {
        return $this->afterExcludementAmount;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return DoctorMonthlyStatement
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set statementDate
     *
     * @param \DateTime $statementDate
     * @return DoctorMonthlyStatement
     */
    public function setStatementDate($statementDate)
    {
        $this->statementDate = $statementDate;

        return $this;
    }

    /**
     * Get statementDate
     *
     * @return \DateTime 
     */
    public function getStatementDate()
    {
        return $this->statementDate;
    }

    /**
     * Set invoiceFilename
     *
     * @param string $invoiceFilename
     * @return DoctorMonthlyStatement
     */
    public function setInvoiceFilename($invoiceFilename)
    {
        $this->invoiceFilename = $invoiceFilename;

        return $this;
    }

    /**
     * Get invoiceFilename
     *
     * @return string 
     */
    public function getInvoiceFilename()
    {
        return $this->invoiceFilename;
    }

    /**
     * Set totalAmountPaid
     *
     * @param string $totalAmountPaid
     * @return DoctorMonthlyStatement
     */
    public function setTotalAmountPaid($totalAmountPaid)
    {
        $this->totalAmountPaid = $totalAmountPaid;

        return $this;
    }

    /**
     * Get totalAmountPaid
     *
     * @return string 
     */
    public function getTotalAmountPaid()
    {
        return $this->totalAmountPaid;
    }

    /**
     * Set totalExceptionStatement
     *
     * @param string $totalExceptionStatement
     * @return DoctorMonthlyStatement
     */
    public function setTotalExceptionStatement($totalExceptionStatement)
    {
        $this->totalExceptionStatement = $totalExceptionStatement;

        return $this;
    }

    /**
     * Get totalExceptionStatement
     *
     * @return string 
     */
    public function getTotalExceptionStatement()
    {
        return $this->totalExceptionStatement;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return DoctorMonthlyStatement
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
     * @return DoctorMonthlyStatement
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
     * Remove line
     *
     * @param \UtilBundle\Entity\DoctorMonthlyStatementLine $line
     */
    public function removeLine(\UtilBundle\Entity\DoctorMonthlyStatementLine $line)
    {
        $this->lines->removeElement($line);
    }

    /**
     * Set isXeroSync
     *
     * @param boolean $isXeroSync
     *
     * @return DoctorMonthlyStatement
     */
    public function setIsXeroSync($isXeroSync)
    {
        $this->isXeroSync = $isXeroSync;

        return $this;
    }

    /**
     * Get isXeroSync
     *
     * @return boolean
     */
    public function getIsXeroSync()
    {
        return $this->isXeroSync;
    }
}
