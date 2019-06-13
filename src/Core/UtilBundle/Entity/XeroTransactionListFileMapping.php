<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroTransactionListFileMapping
 *
 * @ORM\Table(name="xero_transaction_list_file_mapping")
 * @ORM\Entity
 */
class XeroTransactionListFileMapping
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
     * @ORM\Column(name="transaction_function_list", type="string", length=250, nullable=true)
     */
    private $transactionFunctionList;

    /**
     * @var string
     *
     * @ORM\Column(name="file_export_column", type="string", length=250, nullable=true)
     */
    private $fileExportColumn;



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
     * Set transactionFunctionList
     *
     * @param string $transactionFunctionList
     *
     * @return XeroTransactionListFileMapping
     */
    public function setTransactionFunctionList($transactionFunctionList)
    {
        $this->transactionFunctionList = $transactionFunctionList;

        return $this;
    }

    /**
     * Get transactionFunctionList
     *
     * @return string
     */
    public function getTransactionFunctionList()
    {
        return $this->transactionFunctionList;
    }

    /**
     * Set fileExportColumn
     *
     * @param string $fileExportColumn
     *
     * @return XeroTransactionListFileMapping
     */
    public function setFileExportColumn($fileExportColumn)
    {
        $this->fileExportColumn = $fileExportColumn;

        return $this;
    }

    /**
     * Get fileExportColumn
     *
     * @return string
     */
    public function getFileExportColumn()
    {
        return $this->fileExportColumn;
    }
}
