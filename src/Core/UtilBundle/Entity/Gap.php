<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gaps
 *
 * @ORM\Table(name="gap", indexes={@ORM\Index(name="FK_gaps", columns={"issue_type_id"})})
 * @ORM\Entity
 */
class Gap
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
     * @ORM\Column(name="gap_number", type="string", length=50, nullable=true)
     */
    private $gapNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="gap_label", type="string", length=250, nullable=true)
     */
    private $gapLabel;

    /**
     * @var string
     *
     * @ORM\Column(name="actions", type="text", length=65535, nullable=true)
     */
    private $actions;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @var \GapIssueType
     *
     * @ORM\ManyToOne(targetEntity="GapIssueType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issue_type_id", referencedColumnName="id")
     * })
     */
    private $issueType;



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
     * Set gapNumber
     *
     * @param string $gapNumber
     *
     * @return Gaps
     */
    public function setGapNumber($gapNumber)
    {
        $this->gapNumber = $gapNumber;

        return $this;
    }

    /**
     * Get gapNumber
     *
     * @return string
     */
    public function getGapNumber()
    {
        return $this->gapNumber;
    }

    /**
     * Set actions
     *
     * @param string $actions
     *
     * @return Gaps
     */
    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Get actions
     *
     * @return string
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return Gaps
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
     * Set issueType
     *
     * @param \UtilBundle\Entity\GapIssueType $issueType
     *
     * @return Gaps
     */
    public function setIssueType(\UtilBundle\Entity\GapIssueType $issueType = null)
    {
        $this->issueType = $issueType;

        return $this;
    }

    /**
     * Get issueType
     *
     * @return \UtilBundle\Entity\GapIssueType
     */
    public function getIssueType()
    {
        return $this->issueType;
    }

    /**
     * Set gapLabel
     *
     * @param string $gapLabel
     *
     * @return Gap
     */
    public function setGapLabel($gapLabel)
    {
        $this->gapLabel = $gapLabel;

        return $this;
    }

    /**
     * Get gapLabel
     *
     * @return string
     */
    public function getGapLabel()
    {
        return $this->gapLabel;
    }
}
