<?php

namespace UtilBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use UtilBundle\Utility\Constant;
use UtilBundle\Entity\AgentMonthlyStatement;
use UtilBundle\Entity\AgentMonthlyStatementLine;

class AgentMSRepository extends EntityRepository
{
    /**
     * determine whether a statement is the first one
     * @param  array  $params
     * @return boolean
     */
    public function isTheFirstStatement($params) {
        $queryBuilder = $this->createQueryBuilder('ms');
        $queryBuilder->innerJoin('UtilBundle:AgentMonthlyStatementLine', 'amsl', 'WITH', "amsl.agentMonthlyStatement = ms.id and amsl.agent = {$params['agentId']}")
                      ->addOrderBy('ms.year', 'asc')
                      ->addOrderBy('ms.month', 'asc')
                      ->setMaxResults(1);

      $result = $queryBuilder->getQuery()->getOneOrNullResult();
      if (!isset($result)) {
        return true;
      }
      if ($params['month'] ==  $result->getMonth() and $params['year'] == $result->getYear() ) {
        return true;
      }
      return false;
    }
    
    public function createAgentMS($params)
    {
        $dateTime = new \DateTime('now');
        $arrFiles = isset($params['files']) ? $params['files'] : array();

        if (!isset($params['month'])) {
            $params['month'] = date('m');
        }

        if (!isset($params['year'])) {
            $params['year'] = date('Y');
        }

        if(!isset($params['statementDate'])) {
            $params['statementDate'] = new \DateTime();
        }

        $criteria = array(
            'month' => $params['month'],
            'year'  => $params['year']
        );
        $aMS = $this->findOneBy($criteria);
        if ($aMS) {
            if (isset($params['output'])) {
                $params['output']->writeln("{$dateTime->format('Y-m-d H:i:s')}: The monthly statement data that will be sent on {$params['statementDate']->format('Y-m-d')} has been created already. \n Please clear data and run this command again.");
            }
            return true;
        }

        $aMS = new AgentMonthlyStatement();
        $aMS->setMonth($params['month']);
        $aMS->setYear($params['year']);
        $aMS->setStatus(false);
        $aMS->setCreatedOn(new \DateTime());
        $aMS->setStatementDate($params['statementDate']);

        $em = $this->getEntityManager();

        $agents = $em->getRepository('UtilBundle:Agent')->getAgentForStatement(array(), array());


        $platforms = $em->getRepository('UtilBundle:PlatformSettings')->findAll();
        $gstRate = 0;
        if (!empty($platforms)) {
            $platforms = $platforms[0];
            $gstRate = $platforms->getGstRate();
        }
        
        if (empty($agents) && isset($params['output'])) {
            $params['output']->writeln("{$dateTime->format('Y-m-d H:i:s')}: No agent found");
        }

        foreach ($agents as $key => $value) {
            $params['agentId'] = $value['id'];
            $result = $em->getRepository('UtilBundle:MarginShare')->getAgentFeeByMonth($params, true);

            $agentMonthlyFee = $result['agentMonthlyFee'];

            $agentId = $value['id'];
            $aMSL = new AgentMonthlyStatementLine();
            $aMSL->setStatus(false);
            $aMSL->setCreatedOn(new \DateTime());
            $aMSL->setIsExcludePayment(false);
            $aMSL->setPatientFee($result['patientFee']);

            $agent = $em->getRepository('UtilBundle:Agent')->find($value['id']);
            $aMSL->setAgent($agent);

            //get sub-agent
            $subAgent = $em->getRepository('UtilBundle:Agent')->findBy(array(
                'parent' => $agent->getId()
            ));
            if($subAgent) {
                foreach ($subAgent as $c) {
                    $params['agentId'] = $c->getId();
                    $mFee = $em->getRepository('UtilBundle:MarginShare')->getAgentFeeByMonth($params);

                    $agentMonthlyFee += $mFee;
                }
            }

            $params['agentId'] = $agentId;
            $aMSL->setAgentMonthlyFee($agentMonthlyFee);
            $params['monthlyFee'] = $agentMonthlyFee;

            $agentCode = $agent->getAgentCode();
            $agentCode = explode('-', $agentCode);
            $cusRef = 'A' . (isset($agentCode[3]) ? $agentCode[3] : '') . '-';
            $cusRef .= sprintf("%'.02d", $aMS->getYear()) . '.';
            $cusRef .= sprintf("%'.02d", $aMS->getMonth());
            $aMSL->setCustomerReference($cusRef);

            //update $filename
            if(isset($arrFiles[$agentId])) {
                $aMSL->setFilename($arrFiles[$agentId][0]);
            }

            $aMSLRepo = $em->getRepository('UtilBundle:AgentMonthlyStatementLine');
            $aMSLTotalAmount = $aMSLRepo->getPreMonthsStatementInfo($params, true);
            $aMSL->setTotalAmount($aMSLTotalAmount);

            $amountPaid = 0;
            $aMSL->setAmountPaid($amountPaid);

            $hasGST = $em->getRepository('UtilBundle:Agent')->hasGST($agentId);
            $agentGst = 0;
            if ($hasGST) {
                $psGstRate = $gstRate / 100;
                $agentGst = $agentMonthlyFee * $psGstRate / (1 + $psGstRate);
            }
            $aMSL->setAgentMonthlyFeeGst($agentGst);

            $aMS->addLine($aMSL);

            $exceptionStatement = $this->calculateExceptionStatement($aMSL);
            $aMSL->setExceptionStatement($exceptionStatement);

            $outStandingAmount = $agentMonthlyFee + $exceptionStatement - $amountPaid;
            $aMSL->setOutStandingAmount($outStandingAmount);

            if (isset($params['output'])) {
                $outputData = "{$dateTime->format('Y-m-d H:i:s')}: Build statement line data agent {$agentId}";
                $params['output']->writeln($outputData);
            }
        }

        $aMS->setTotalInvoiceAmount(0);

        $projectedPD = $em->getRepository('UtilBundle:DoctorMonthlyStatement')->calculateProjectedPaymentDate(2, $params['statementDate']->format('Y-m-d'));
        $aMS->setProjectedPaymentDate($projectedPD);

        try {
            $em->persist($aMS);
            $em->flush();
        } catch(\Exception $ex) {
        }

        return true;
    }

    public function deleteAgentMS($params)
    {
        if (!isset($params['month'])) {
            $params['month'] = date('m');
        }

        if (!isset($params['year'])) {
            $params['year'] = date('Y');
        }

        $criteria = array(
            'month' => $params['month'],
            'year'  => $params['year']
        );
        $agentMS = $this->findOneBy($criteria);

        $em = $this->getEntityManager();
        if ($agentMS) {
            $em->remove($agentMS);
        }

        $em->flush();
    }

    public function listAgentMS($params)
    {
        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage*$page;

        $queryBuilder = $this->createQueryBuilder('ms')
            ->select('ms.id,
            ms.month,
            ms.year,
            ms.projectedPaymentDate,
            ms.totalAmount,
            ms.totalInvoiceAmount,
            ms.totalAmountPaid,
            ms.totalExceptionStatement,
            ms.outStandingAmount,
            ms.status,
            msl.postDate')
            ->leftJoin('UtilBundle:AgentMonthlyStatementLine', 'msl', 'WITH', 'msl.agentMonthlyStatement = ms.id AND msl.postDate is not null')
            ->setFirstResult($startRecord)
            ->setMaxResults($perPage)
            ->groupBy('ms.id');

        if (!empty($params['monthYear'])) {
            $date = date_create_from_format('M Y', $params['monthYear']);
            $month = $date->format('m');
            $year = $date->format('Y');

            $queryBuilder->andWhere('ms.year=:year');
            $queryBuilder->andWhere('ms.month=:month');
            $queryBuilder->setParameter('year', $year);
            $queryBuilder->setParameter('month', $month);
        }

        if (isset($params['minAmount']) && $params['minAmount'] != '') {
            $queryBuilder->andWhere('ms.totalAmount >= :minAmount');
            $queryBuilder->setParameter('minAmount', $params['minAmount']);
        }

        if (isset($params['maxAmount']) && $params['maxAmount'] != '') {
            $queryBuilder->andWhere('ms.totalAmount <= :maxAmount');
            $queryBuilder->setParameter('maxAmount', $params['maxAmount']);
        }

        if (isset($params['statusFilter']) && is_numeric($params['statusFilter'])) {
			$queryBuilder->andWhere($queryBuilder->expr()->eq('ms.status', $params['statusFilter']));
        }

        $sort  = 'ms.year';
        $order = 'desc';

        if (!empty($params['sorting'])) {
            list($sort, $order) = explode('-', $params['sorting']);
            $queryBuilder->orderBy($sort, $order);
            if ('ms.year' == $sort) {
                $queryBuilder->addOrderBy('ms.month', $order);
            }
        } else {
            $queryBuilder->orderBy($sort, $order);
            $queryBuilder->addOrderBy('ms.month', $order);
        }

        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query, false);

        $result = array(
            'sort'  => $sort,
            'order' => $order,
            'data'  => $queryBuilder->getQuery()->getArrayResult()
        );

        return $result;
    }

    public function listAgentCS($params)
    {
        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage*$page;

        $agentMSId = isset($params['id']) ? $params['id'] : 0;

        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:AgentMonthlyStatementLine')
            ->createQueryBuilder('msl')
            ->select('msl')
            ->addSelect('msl.agentMonthlyFee - ifelse(msl.exceptionAgentMonthlyFee is null, 0, msl.exceptionAgentMonthlyFee) as tadA')
            ->addSelect('i.invoiceTotalAmount as tiA')
            ->where('msl.agentMonthlyFee > 0')
            ->orWhere('msl.outStandingAmount > 0')
            ->leftJoin('msl.invoiceUpload', 'i')
            ->innerJoin('msl.agent', 'a')
            ->innerJoin('a.personalInformation', 'p')
            ->setFirstResult($startRecord)
            ->setMaxResults($perPage);

        if (isset($params['isExcludePayment'])) {
            $queryBuilder->addSelect('ms')
                ->addSelect('msl.agentMonthlyFee - ifelse(msl.amountPaid is null, 0, msl.amountPaid) as osdA')
                ->innerJoin('msl.agentMonthlyStatement', 'ms')
                ->where('msl.isExcludePayment=:isExcludePayment')
                ->setParameter('isExcludePayment', $params['isExcludePayment']);
        } else {
            $queryBuilder->andWhere('msl.agentMonthlyStatement=:agentMSId')
                ->setParameter('agentMSId', $agentMSId);
        }

        if (!empty($params['agentFilter'])) {
            $orX = $queryBuilder->expr()->orX();
            $literal = $queryBuilder->expr()->literal("%{$params['agentFilter']}%");
            $orX->add($queryBuilder->expr()->like('a.agentCode', $literal));
            $orX->add($queryBuilder->expr()->like("CONCAT(p.firstName, ' ', p.lastName)", $literal));

            $queryBuilder->andWhere($orX);
        }

        if (!empty($params['monthYear']) && isset($params['isExcludePayment'])) {
            $date = date_create_from_format('M Y', $params['monthYear']);
            $month = $date->format('m');
            $year = $date->format('Y');

            $queryBuilder->andWhere('ms.year=:year');
            $queryBuilder->andWhere('ms.month=:month');
            $queryBuilder->setParameter('year', $year);
            $queryBuilder->setParameter('month', $month);
        }

        if (isset($params['statusFilter']) && is_numeric($params['statusFilter']) && isset($params['isExcludePayment'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('msl.status', $params['statusFilter']));
        }

        if (isset($params['minAmount']) && $params['minAmount'] != '') {
            $queryBuilder->andWhere('msl.agentMonthlyFee>=:minAmount');
            $queryBuilder->setParameter('minAmount', $params['minAmount']);
        }

        if (isset($params['maxAmount']) && $params['maxAmount'] != '') {
            $queryBuilder->andWhere('msl.agentMonthlyFee<=:maxAmount');
            $queryBuilder->setParameter('maxAmount', $params['maxAmount']);
        }

		if (isset($params['ids']) && !empty($params['ids'])) {
			$queryBuilder->andWhere($queryBuilder->expr()->in('msl.id', ':ids'))
						->setParameter('ids', $params['ids']);
		}
		
        $sort  = 'a.agentCode';
        $order = 'desc';

        if (!empty($params['sorting'])) {
            list($sort, $order) = explode('-', $params['sorting']);
            $queryBuilder->orderBy($sort, $order);
        } else {
            $queryBuilder->orderBy($sort, $order);
        }

        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query, false);

        $result = array(
            'sort'  => $sort,
            'order' => $order,
            'data'  => $paginator
        );

        return $result;
    }

    public function listAgentCSByCriteria($criteria)
    {
        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:AgentMonthlyStatementLine')
            ->createQueryBuilder('msl')
            ->innerJoin('msl.agent', 'a')
            ->innerJoin('a.personalInformation', 'p');

        if (isset($criteria['agentMSId'])) {
            $queryBuilder->where('msl.agentMonthlyStatement=:agentMSId')
                ->andWhere('msl.isExcludePayment=:isExcludePayment')
                ->setParameter('agentMSId', $criteria['agentMSId'])
                ->setParameter('isExcludePayment', false);
        } else {
            $msLineArray = isset($criteria['msLineArray']) ? $criteria['msLineArray'] : array();
            $queryBuilder->where('msl.id IN (:msLineArray)')
                ->setParameter('msLineArray', $msLineArray, Connection::PARAM_STR_ARRAY);
        }

		if (isset($criteria['hasInvoice']) && $criteria['hasInvoice']) {
			$queryBuilder->andWhere('msl.invoiceUpload IS NOT NULL');
		}
		
		if (isset($criteria['issueStatus'])) {
			$queryBuilder->andWhere('msl.issueStatus = ' . $criteria['issueStatus']);
		}
		
		$queryBuilder->andWhere('msl.status IN (0,3)');
        $queryBuilder->orderBy('msl.customerReference', 'ASC');
		
        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }

    public function getAgentCS($id)
    {
        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:AgentMonthlyStatementLine')
            ->createQueryBuilder('msl')
            ->addSelect('ms')
            ->where('msl.id=:id')
            ->innerJoin('msl.agent', 'a')
            ->innerJoin('a.personalInformation', 'p')
            ->innerJoin('msl.agentMonthlyStatement', 'ms')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /*
     * author bien
    * get statement last month
    *
    */
    public function getXeroMonthlyStatmentCheck($m, $y){
        $queryBuilder = $this->createQueryBuilder('dms')
            ->innerJoin('dms.lines','amsl');
        $queryBuilder->select('count(amsl.id)');
        $queryBuilder->where($queryBuilder->expr()->eq('dms.month',"'".$m."'"))
            ->andWhere($queryBuilder->expr()->eq('dms.year',"'".$y."'"))
            ->andWhere($queryBuilder->expr()->isNull("amsl.invoiceUpload"));
        $result = $queryBuilder->getQuery()->getSingleScalarResult();
        $data = '';

        if($result == 0){
            $data =  $this->createQueryBuilder('dms')
                ->innerJoin('dms.lines','amsl')
                ->where($queryBuilder->expr()->eq('dms.month',"'".$m."'"))
                ->andWhere($queryBuilder->expr()->eq('dms.year',"'".$y."'"))
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $data;
    }

    public function updateExceptionForNextMonth(AgentMonthlyStatementLine $line)
    {
        if (empty($line)) {
            return;
        }

        $input = $line->getExceptionStatement();
        if (empty($input)) {
            return;
        }

        $year  = $line->getAgentMonthlyStatement()->getYear();
        $month = $line->getAgentMonthlyStatement()->getMonth();

        $dateTime = new \DateTime(implode('-', array($year, $month, 1)));
        if (!$dateTime) {
            return;
        }
        $dateTime = $dateTime->add(new \DateInterval('P1M'));

        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:AgentMonthlyStatementLine')
            ->createQueryBuilder('msl')
            ->innerJoin('msl.agentMonthlyStatement', 'ms')
            ->where('ms.year = :year')
            ->andWhere('ms.month = :month')
            ->andWhere('msl.agent = :agent')
            ->setParameter('year', $dateTime->format('Y'))
            ->setParameter('month', $dateTime->format('m'))
            ->setParameter('agent', $line->getAgent())
            ->setMaxResults(1);

        $data = $queryBuilder->getQuery()->getOneOrNullResult();
        if (empty($data)) {
            return;
        }

        $exceptionStatement = $data->getExceptionStatement();
        $data->setExceptionStatement($exceptionStatement + $input);

        $outStandingAmount = $data->getOutStandingAmount();
        $data->setOutStandingAmount($outStandingAmount + $input);

        $totalExceptionStatement = $data->getAgentMonthlyStatement()->getTotalExceptionStatement();
        $data->getAgentMonthlyStatement()->setTotalExceptionStatement($totalExceptionStatement + $input);

        $totalOutStandingAmount = $data->getAgentMonthlyStatement()->getOutStandingAmount();
        $data->getAgentMonthlyStatement()->setOutStandingAmount($totalOutStandingAmount + $input);

        $this->getEntityManager()->persist($data->getAgentMonthlyStatement());

        $this->updateExceptionForNextMonth($data);
    }

    private function calculateExceptionStatement(AgentMonthlyStatementLine $line)
    {
        $result = 0;

        if (empty($line)) {
            return $result;
        }

        $year  = $line->getAgentMonthlyStatement()->getYear();
        $month = $line->getAgentMonthlyStatement()->getMonth();

        $dateTime = new \DateTime(implode('-', array($year, $month, 1)));
        if (!$dateTime) {
            return $result;
        }

        $where = "STR_TO_DATE(CONCAT(ms.year, '-', ms.month, '-', '01'), '%Y-%m-%d') < :dateTime";
        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:AgentMonthlyStatementLine')
            ->createQueryBuilder('msl')
            ->select('SUM(msl.agentMonthlyFee) as total')
            ->innerJoin('msl.agentMonthlyStatement', 'ms')
            ->where('msl.agent = :agent')
            ->andWhere('msl.isExcludePayment = :isExcludePayment')
            ->andWhere('msl.status <> :status')
            ->andWhere($where)
            ->setParameter('agent', $line->getAgent())
            ->setParameter('isExcludePayment', true)
            ->setParameter('status', 1)
            ->setParameter('dateTime', $dateTime)
            ->setMaxResults(1);

        $data = $queryBuilder->getQuery()->getOneOrNullResult();
        if (empty($data['total'])) {
            return $result;
        }

        return $data['total'];
    }

    public function updateAgentMonthlyStatementList()
    {
        $em = $this->getEntityManager();

        $queryBuilder = $this->createQueryBuilder('ms')
            ->where('ms.status <> :status')
            ->orderBy('ms.year', 'asc')
            ->addOrderBy('ms.month', 'asc')
            ->setParameter('status', 1);

        $list = $queryBuilder->getQuery()->getResult();
        foreach ($list as $value) {
            $lines = $value->getLines();
            if (0 == count($lines)) {
                continue;
            }

            $totalAmount = $totalAmountPaid = $totalExceptionStatement = $totalOutStandingAmount = 0;

            $isPaid = $isProcessing = true;
            $isFI = false;
            foreach ($lines as $item) {
                $agentMonthlyFee = $item->getAgentMonthlyFee();
                $exceptionStatement = $this->calculateExceptionStatement($item);

                if (empty($agentMonthlyFee * 1) && empty($exceptionStatement * 1)) {
                    continue;
                }

                $status = $item->getStatus();
                if ($item->getIsExcludePayment() && 1 != $status) {
                    $exceptionStatement += $agentMonthlyFee;
                }
                $item->setExceptionStatement($exceptionStatement);
                $totalExceptionStatement += $exceptionStatement;

                if ($item->getIsExcludePayment()) {
                    $agentMonthlyFee = 0;
                }
                $totalAmount += $agentMonthlyFee;

                $amountPaid = $item->getAmountPaid();
                if ($item->getIsExcludePayment()) {
                    $amountPaid = 0;
                }
                $totalAmountPaid += $amountPaid;

                $outStandingAmount = $agentMonthlyFee + $exceptionStatement - $amountPaid;
                $item->setOutStandingAmount($outStandingAmount);

                $em->persist($item);

                if (empty($outStandingAmount)) {
                    continue;
                }

                if (1 != $status) {
                    $isPaid = false;
                }
                if (3 != $status) {
                    $isProcessing = false;
                }
                if (2 == $status) {
                    $isFI = true;
                }
            }

            $totalOutStandingAmount = $totalAmount + $totalExceptionStatement - $totalAmountPaid;

            $value->setTotalAmount($totalAmount);
            $value->setTotalAmountPaid($totalAmountPaid);
            $value->setTotalExceptionStatement($totalExceptionStatement);
            $value->setOutStandingAmount($totalOutStandingAmount);

            if ($isPaid) {
                $status = 1;
            } else if ($isProcessing) {
                $status = 3;
            } else if ($isFI) {
                $status = 2;
            } else {
                $status = 0;
            }
            $value->setStatus($status);

            $em->persist($value);
        }

        $em->flush();
    }

    public function getListAgentStatementLineForInstruction($id)
    {
        $queryBuilder = $this->createQueryBuilder('ms')
            ->select('a.id as agentId, b.id as bankId,  b.swiftCode as bicCode, ba.accountName as accountName, ba.accountNumber as accountNumber, msl.id as lineId, msl.agentMonthlyFee as amount, iv.invoiceNumber as invoiceNumber, iv.receiveDate as receiveDate, msl.customerReference as customerReference')
            ->innerJoin('ms.lines', 'msl')
            ->innerJoin('msl.invoiceUpload', 'iv')
            ->innerJoin('msl.agent', 'a')
            ->innerJoin('a.personalInformation', 'p')
            ->innerJoin('a.bankAccount', 'ba')
            ->innerJoin('ba.bank', 'b')
            ->where('ms.id=:agentId')
            ->andWhere('msl.isExcludePayment=:isExcludePayment')
            ->setParameter('agentId', $id)
            ->setParameter('isExcludePayment', false);

        $queryBuilder->andWhere('msl.status IN (0,3)')
            ->andWhere("msl.agentMonthlyFee > 0 or msl.outStandingAmount > 0");

        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }
    /*
    * author Bien
    * get list line exception for export UOB
    */
    public function getListAgentStatementLineForInstructionException($ids)
    {
        if(empty($ids))
        {
            return [];
        }
        $queryBuilder = $this->createQueryBuilder('ms')

            ->select('b.swiftCode as bicCode, ba.accountName as accountName, ba.accountNumber as accountNumber, msl.id as lineId, msl.agentMonthlyFee as amount, iv.invoiceNumber as invoiceNumber, iv.receiveDate as receiveDate, msl.customerReference as customerReference')
            ->innerJoin('ms.lines', 'msl')
            ->innerJoin('msl.invoiceUpload', 'iv')
            ->innerJoin('msl.agent', 'd')
            ->innerJoin('d.personalInformation', 'p')
            ->innerJoin('d.bankAccount', 'ba')
            ->innerJoin('ba.bank', 'b');

        $queryBuilder->where($queryBuilder->expr()->in('msl.id',$ids))
            ->andWhere($queryBuilder->expr()->eq('msl.isExcludePayment',true))
            ->andWhere($queryBuilder->expr()->eq('msl.issueStatus',1));

        $queryBuilder->andWhere($queryBuilder->expr()->in('msl.status',[0,3]))
            ->andWhere("msl.agentMonthlyFee > 0 or msl.outStandingAmount > 0");

        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }

}
