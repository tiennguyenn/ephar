<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * GapIssueType
 *
 * @ORM\Table(name="gap_issue_type")
 * @ORM\Entity
 */
class GapIssueType
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
     * @ORM\Column(name="issue_name", type="string", length=250, nullable=true)
     */
    private $issueName;


    /**
     * @ORM\OneToMany(targetEntity="Gap", mappedBy="issueType", cascade={"persist", "remove" })
     */
    private $gaps;

    public function __construct() {
        $this->gaps = new ArrayCollection();
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
     * Set issueName
     *
     * @param string $issueName
     *
     * @return GapIssueType
     */
    public function setIssueName($issueName)
    {
        $this->issueName = $issueName;

        return $this;
    }

    /**
     * Get issueName
     *
     * @return string
     */
    public function getIssueName()
    {
        return $this->issueName;
    }

    /**
     * Add gap
     *
     * @param \UtilBundle\Entity\Gap $gap
     *
     * @return GapIssueType
     */
    public function addGap(\UtilBundle\Entity\Gap $gap)
    {
        $gap->setIssueType($this);
        $this->gaps[] = $gap;

        return $this;
    }

    /**
     * Remove gap
     *
     * @param \UtilBundle\Entity\Gap $gap
     */
    public function removeGap(\UtilBundle\Entity\Gap $gap)
    {
        $this->gaps->removeElement($gap);
    }

    /**
     * Get gaps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGaps()
    {
        return $this->gaps;
    }
}
