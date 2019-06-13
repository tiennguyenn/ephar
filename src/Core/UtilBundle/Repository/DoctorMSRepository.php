<?php

namespace UtilBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use UtilBundle\Utility\Constant;
use UtilBundle\Entity\DoctorMonthlyStatement;
use UtilBundle\Entity\DoctorMonthlyStatementLine;

class DoctorMSRepository extends EntityRepository
{
    /**
     * determine whether a statement is the first one
     * @param  array  $params
     * @return boolean
     */
    public function isTheFirstStatement($params) {
        $queryBuilder = $this->createQueryBuilder('ms');
        $queryBuilder->innerJoin('UtilBundle:DoctorMonthlyStatementLine', 'dmsl', 'WITH', "dmsl.doctorMonthlyStatement = ms.id and dmsl.doctor = {$params['doctorId']}")
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

    public function createDoctorMS($params)
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
        $dMS = $this->findOneBy($criteria);

        if ($dMS) {
            if (isset($params['output'])) {
                $params['output']->writeln("{$dateTime->format('Y-m-d H:i:s')}: The monthly statement data that will be sent on {$params['statementDate']->format('Y-m-d')} has been created already.\n Please clear data and run this command again.");
            }
            return true;
        }
        $dMS = new DoctorMonthlyStatement();
        $dMS->setMonth($params['month']);
        $dMS->setYear($params['year']);
        $dMS->setStatus(false);
        $dMS->setCreatedOn(new \DateTime());
        $dMS->setStatementDate($params['statementDate']);

        $em = $this->getEntityManager();

        $doctors = $em->getRepository('UtilBundle:Doctor')->getDoctorForStatement(array());
        foreach ($doctors as $value) {
            $doctorId = $value['id'];

            $dMSL = new DoctorMonthlyStatementLine();
            $dMSL->setStatus(false);
            $dMSL->setCreatedOn(new \DateTime());
            $dMSL->setIsExcludePayment(false);

            $doctor = $em->getRepository('UtilBundle:Doctor')->find($value['id']);
            $dMSL->setDoctor($doctor);

            $params['doctorId'] = $value['id'];
            $params['returnOrderValue'] = true;
            $result = $em->getRepository('UtilBundle:MarginShare')->getMonthlyDoctorFee($params);
            $doctorMonthlyFee = $result['doctorFee'];

            $params['monthlyFee'] = $doctorMonthlyFee;
            $dMSL->setDoctorMonthlyFee($doctorMonthlyFee);
            $dMSL->setOrderValue($result['orderValue']);

            $doctorCode = $doctor->getDoctorCode();
            $doctorCode = explode('-', $doctorCode);
            $cusRef = 'D' . (isset($doctorCode[3]) ? $doctorCode[3] : '') . '-';
            $cusRef .= sprintf("%'.02d", $dMS->getYear()) . '.';
            $cusRef .= sprintf("%'.02d", $dMS->getMonth());
            $dMSL->setCustomerReference($cusRef);

            //update $filename, $invoiceFilename
            if(isset($arrFiles[$doctorId])) {
                $dMSL->setFilename($arrFiles[$doctorId][0]);
                $dMSL->setInvoiceFilename($arrFiles[$doctorId][1]);
            }

            $dMSLRepo = $em->getRepository('UtilBundle:DoctorMonthlyStatementLine');
            $dMSLTotalAmount = $dMSLRepo->getPreMonthsStatementInfo($params, true);
            $dMSL->setTotalAmount($dMSLTotalAmount);

            $amountPaid = 0;
            $dMSL->setAmountPaid($amountPaid);

            $dMS->addLine($dMSL);

            $exceptionStatement = $this->calculateExceptionStatement($dMSL);
            $dMSL->setExceptionStatement($exceptionStatement);

            $outStandingAmount = $doctorMonthlyFee + $exceptionStatement - $amountPaid;
            $dMSL->setOutStandingAmount($outStandingAmount);

            if (isset($params['output'])) {
                $outputData = "{$dateTime->format('Y-m-d H:i:s')}: Build statement line data doctor {$params['doctorId']}";
                $params['output']->writeln($outputData);
            }
        }

        $projectedPD = $this->calculateProjectedPaymentDate(1, $params['statementDate']->format('Y-m-d'));
        $dMS->setProjectedPaymentDate($projectedPD);

        try {
            $em->persist($dMS);

            $em->flush();
        } catch(\Exception $ex) {
        }

        return true;
    }
    public function deleteDoctorMS($params)
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
        $doctorMS = $this->findOneBy($criteria);

        $em = $this->getEntityManager();
        if ($doctorMS) {
            $em->remove($doctorMS);
        }

        $em->flush();
    }

    public function listDoctorMS($params)
    {
        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage*$page;

        $selectStr = 'ms.id,
            ms.month,
            ms.year,
            ms.projectedPaymentDate,
            ms.totalAmount,
            ms.totalAmountPaid,
            ms.totalExceptionStatement,
            ms.outStandingAmount,            
            ms.status,
            msl.postDate';        
        
        $queryBuilder = $this->createQueryBuilder('ms')
            ->select($selectStr)
            ->leftJoin('UtilBundle:DoctorMonthlyStatementLine', 'msl', 'WITH', 'msl.doctorMonthlyStatement = ms.id AND msl.postDate is not null')            
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
            $queryBuilder->andWhere('ms.totalAmount>=:minAmount');
            $queryBuilder->setParameter('minAmount', $params['minAmount']);
        }

        if (isset($params['maxAmount']) && $params['maxAmount'] != '') {
            $queryBuilder->andWhere('ms.totalAmount<=:maxAmount');
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

        $totalResult = count($queryBuilder->getQuery()->getArrayResult());

        $queryBuilder->setFirstResult($startRecord);
        $queryBuilder->setMaxResults($perPage);
        //$paginator = new Paginator($query, false);

        $result = array(
            'sort'  => $sort,
            'order' => $order,
            'data'  => $queryBuilder->getQuery()->getArrayResult(),
            'totalResult' => $totalResult
        );

        return $result;
    }

    public function listDoctorCS($params)
    {
        $perPage = ($params['perPage'] > 0)? $params['perPage']: Constant::PER_PAGE_DEFAULT;
        $page = ($params['page'] > 0)? $params['page']: Constant::PAGE_DEFAULT;
        $startRecord = $perPage*$page;

        $doctorMSId = isset($params['id']) ? $params['id'] : 0;

        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:DoctorMonthlyStatementLine')
            ->createQueryBuilder('msl')
            ->select('msl')
            ->addSelect('msl.doctorMonthlyFee - ifelse(msl.exceptionDoctorMonthlyFee is null, 0, msl.exceptionDoctorMonthlyFee) as tadD')
            //->where('msl.doctorMonthlyFee > 0 OR msl.outStandingAmount > 0')
            ->innerJoin('msl.doctor', 'd')
            ->innerJoin('d.personalInformation', 'p')
            ->setFirstResult($startRecord)
            ->setMaxResults($perPage);

        if (isset($params['isExcludePayment'])) {
            $queryBuilder->addSelect('ms')
                ->addSelect('msl.doctorMonthlyFee - ifelse(msl.amountPaid is null, 0, msl.amountPaid) as osdA')
                ->andWhere('msl.isExcludePayment=:isExcludePayment')
                ->innerJoin('msl.doctorMonthlyStatement', 'ms')
                ->setParameter('isExcludePayment', $params['isExcludePayment']);
        } else {
            $queryBuilder->andWhere('msl.doctorMonthlyStatement=:doctorMSId')
                ->setParameter('doctorMSId', $doctorMSId);
        }

        if (!empty($params['doctorFilter'])) {
            $orX = $queryBuilder->expr()->orX();
            $literal = $queryBuilder->expr()->literal("%{$params['doctorFilter']}%");
            $orX->add($queryBuilder->expr()->like('d.doctorCode', $literal));
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

        if(isset($params['hasAmount']) && $params['hasAmount'] == 1) {
            $queryBuilder->andWhere('msl.doctorMonthlyFee > 0 OR msl.outStandingAmount > 0');
        }

        if (isset($params['statusFilter']) && is_numeric($params['statusFilter']) && isset($params['isExcludePayment'])) {
			$queryBuilder->andWhere($queryBuilder->expr()->eq('msl.status', $params['statusFilter']));
        }

        if (isset($params['minAmount']) && $params['minAmount'] != '') {
            $queryBuilder->andWhere('msl.doctorMonthlyFee >= :minAmount');
            $queryBuilder->setParameter('minAmount', $params['minAmount']);
        }

        if (isset($params['maxAmount']) && $params['maxAmount'] != '') {
            $queryBuilder->andWhere('msl.doctorMonthlyFee <= :maxAmount');
            $queryBuilder->setParameter('maxAmount', $params['maxAmount']);
        }

		if (isset($params['ids']) && !empty($params['ids'])) {
			$queryBuilder->andWhere($queryBuilder->expr()->in('msl.id', ':ids'))
						->setParameter('ids', $params['ids']);
		}

        $sort  = 'd.doctorCode';
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

    public function getListDoctorStatementLineForInstruction($id)
    {
        $queryBuilder = $this->createQueryBuilder('ms')
            ->select('  b.swiftCode as bicCode, ba.accountName as accountName, ba.accountNumber as accountNumber, msl.id as lineId, msl.doctorMonthlyFee as amount, msl.invoiceNumber as invoiceNumber, msl.customerReference as customerReference')
            ->innerJoin('ms.lines', 'msl')
            ->innerJoin('msl.doctor', 'd')
            ->innerJoin('d.personalInformation', 'p')
            ->innerJoin('d.bankAccount', 'ba')
            ->innerJoin('ba.bank', 'b')
            ->where('ms.id=:doctorMSId')
            ->andWhere('msl.isExcludePayment=:isExcludePayment')
            ->setParameter('doctorMSId', $id)
            ->setParameter('isExcludePayment', false);

        $queryBuilder->andWhere('msl.status IN (0,3)')
            ->andWhere("msl.doctorMonthlyFee > 0 or msl.outStandingAmount > 0");
        $queryBuilder->orderBy('d.doctorCode', 'DESC');

        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }
    /*
    * author Bien
    * get list line exception for export UOB
    */
    public function getListDoctorStatementLineForInstructionException($ids)
    {
        if(empty($ids))
        {
            return [];
        }
        $queryBuilder = $this->createQueryBuilder('ms')

            ->select('b.swiftCode as bicCode, ba.accountName as accountName, ba.accountNumber as accountNumber, msl.id as lineId, msl.doctorMonthlyFee as amount, msl.invoiceNumber as invoiceNumber, msl.customerReference as customerReference')
            ->innerJoin('ms.lines', 'msl')
            ->innerJoin('msl.doctor', 'd')
            ->innerJoin('d.personalInformation', 'p')
            ->innerJoin('d.bankAccount', 'ba')
            ->innerJoin('ba.bank', 'b');

        $queryBuilder->where($queryBuilder->expr()->in('msl.id',$ids))
            ->andWhere($queryBuilder->expr()->eq('msl.isExcludePayment',true))
            ->andWhere($queryBuilder->expr()->eq('msl.issueStatus',1));

        $queryBuilder->andWhere($queryBuilder->expr()->in('msl.status',[0,3]))
            ->andWhere("msl.doctorMonthlyFee > 0 or msl.outStandingAmount > 0");
        $queryBuilder->orderBy('d.doctorCode', 'DESC');

        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }




    public function listDoctorCSByCriteria($criteria)
    {
        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:DoctorMonthlyStatementLine')
            ->createQueryBuilder('msl')
            ->innerJoin('msl.doctor', 'd')
            ->innerJoin('d.personalInformation', 'p');

        if (isset($criteria['doctorMSId'])) {
            $queryBuilder->where('msl.doctorMonthlyStatement=:doctorMSId')
                ->andWhere('msl.isExcludePayment=:isExcludePayment')
                ->setParameter('doctorMSId', $criteria['doctorMSId'])
                ->setParameter('isExcludePayment', false);
        } else {
            $msLineArray = isset($criteria['msLineArray']) ? $criteria['msLineArray'] : array();
            $queryBuilder->where('msl.id IN (:msLineArray)')
                ->setParameter('msLineArray', $msLineArray, Connection::PARAM_STR_ARRAY);
        }

		if (isset($criteria['hasInvoice']) && $criteria['hasInvoice']) {
			$queryBuilder->andWhere('msl.invoiceFilename IS NOT NULL');
		}

		if (isset($criteria['issueStatus'])) {
			$queryBuilder->andWhere('msl.issueStatus = ' . $criteria['issueStatus']);
		}

		$queryBuilder->andWhere('msl.status IN (0,3)');
        //$queryBuilder->orderBy('msl.customerReference', 'ASC');
		$queryBuilder->orderBy('d.doctorCode', 'DESC');

        $result = $queryBuilder->getQuery()->getResult();

        return $result;
    }

    /**
     * Get projected payment date
     *
     * @param int $type Is doctor, agent, pharmacy, courier
     * 1 for doctor, 2 for agent
     * @return DateTime
     */
    public function calculateProjectedPaymentDate($type = 1, $manualDate = null, $manualFrequency = null)
    {
        $em = $this->getEntityManager();
        $paymentDateSetting = $em->getRepository('UtilBundle:PaymentDateSetting')->findOneBy(array());
        $frequency = $paymentDateSetting->getPayDoctors();
        if (2 == $type) {
            $frequency = $paymentDateSetting->getPayAgents();
        } elseif (3 == $type) {
            $frequency = $paymentDateSetting->getPayPharmacy();
        } elseif (4 == $type) {
            $frequency = $paymentDateSetting->getPayCourier();
        }
        if ($manualFrequency) {
            $frequency = $manualFrequency;
        }

        $interval = new \DateInterval('P1D');

        if(!empty($manualDate))
            $currentDate = new \DateTime($manualDate);
        else
            $currentDate = new \DateTime();

        $currentDate->sub($interval);

        $publicDate = array();
        $publicHoliday = $em->getRepository('UtilBundle:PublicHoliday')->findAll();
        foreach ($publicHoliday as $value) {
            $publicDate[] = $value->getPublicDate()->format('Ymd');
        }

        $weekend = array('Saturday', 'Sunday');

        $flag = 0;
        while ($flag <= $frequency) {
            $currentDate->add($interval);

            if (in_array($currentDate->format('l'), $weekend)) {
                continue;
            }
            if (in_array($currentDate->format('Ymd'), $publicDate)) {
                continue;
            }

            $flag ++;
        }

        return $currentDate;
    }

    public function getCreditNote($doctorMSId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("d.name, d.url, d.createdOn, p.title, p.firstName, p.lastName, dt.doctorCode
            ")
            ->from('UtilBundle:Rx', 'rx')
            ->innerJoin('rx.doctor', 'dt')
            ->innerJoin('dt.personalInformation', 'p')
            ->innerJoin('UtilBundle:Resolve', 'r', 'WITH', 'r.rx = rx.id')
            ->innerJoin('UtilBundle:ResolveRefund', 'r1', 'WITH', 'r1.resolve = r.id')
            ->innerJoin('UtilBundle:ResolveRefundCreditNote', 'r2', 'WITH', 'r2.resolveRefund = r1.id')
            ->innerJoin('r2.document', 'd')
            ->where('rx.doctor = :doctor')
            ->setParameter('doctor', $doctorMSId)
            ;

        $result = $qb->getQuery()->getArrayResult();

        return $result;
    }

    public function getDoctorCS($id)
    {
        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:DoctorMonthlyStatementLine')
            ->createQueryBuilder('msl')
            ->addSelect('ms')
            ->innerJoin('msl.doctor', 'd')
            ->innerJoin('d.personalInformation', 'p')
            ->innerJoin('msl.doctorMonthlyStatement', 'ms')
            ->where('msl.id=:id')
            ->setParameter('id', $id)
            ->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /*
    * author bien
    * get statement last month
    */
    public function getXeroMonthlyStatmentCheck($m, $y){
        $queryBuilder = $this->createQueryBuilder('dms');
        $queryBuilder->where($queryBuilder->expr()->eq('dms.month',"'".$m."'"))
            ->andWhere($queryBuilder->expr()->eq('dms.year',"'".$y."'"))
            ->getQuery();
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function updateExceptionForNextMonth(DoctorMonthlyStatementLine $line)
    {
        if (empty($line)) {
            return;
        }

        $input = $line->getExceptionStatement();
        if (empty($input)) {
            return;
        }

        $year  = $line->getDoctorMonthlyStatement()->getYear();
        $month = $line->getDoctorMonthlyStatement()->getMonth();

        $dateTime = new \DateTime(implode('-', array($year, $month, 1)));
        if (!$dateTime) {
            return;
        }
        $dateTime = $dateTime->add(new \DateInterval('P1M'));

        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:DoctorMonthlyStatementLine')
            ->createQueryBuilder('msl')
            ->innerJoin('msl.doctorMonthlyStatement', 'ms')
            ->where('ms.year = :year')
            ->andWhere('ms.month = :month')
            ->andWhere('msl.doctor = :doctor')
            ->setParameter('year', $dateTime->format('Y'))
            ->setParameter('month', $dateTime->format('m'))
            ->setParameter('doctor', $line->getDoctor())
            ->setMaxResults(1);

        $data = $queryBuilder->getQuery()->getOneOrNullResult();
        if (empty($data)) {
            return;
        }

        $exceptionStatement = $data->getExceptionStatement();
        $data->setExceptionStatement($exceptionStatement + $input);

        $outStandingAmount = $data->getOutStandingAmount();
        $data->setOutStandingAmount($outStandingAmount + $input);

        $totalExceptionStatement = $data->getDoctorMonthlyStatement()->getTotalExceptionStatement();
        $data->getDoctorMonthlyStatement()->setTotalExceptionStatement($totalExceptionStatement + $input);

        $totalOutStandingAmount = $data->getDoctorMonthlyStatement()->getOutStandingAmount();
        $data->getDoctorMonthlyStatement()->setOutStandingAmount($totalOutStandingAmount + $input);

        $this->getEntityManager()->persist($data->getDoctorMonthlyStatement());

        $this->updateExceptionForNextMonth($data);
    }

    private function calculateExceptionStatement(DoctorMonthlyStatementLine $line)
    {
        $result = 0;

        if (empty($line)) {
            return $result;
        }

        $year  = $line->getDoctorMonthlyStatement()->getYear();
        $month = $line->getDoctorMonthlyStatement()->getMonth();

        $dateTime = new \DateTime(implode('-', array($year, $month, 1)));
        if (!$dateTime) {
            return $result;
        }

        $where = "STR_TO_DATE(CONCAT(ms.year, '-', ms.month, '-', '01'), '%Y-%m-%d') < :dateTime";
        $queryBuilder = $this->getEntityManager()
            ->getRepository('UtilBundle:DoctorMonthlyStatementLine')
            ->createQueryBuilder('msl')
            ->select('SUM(msl.doctorMonthlyFee) as total')
            ->innerJoin('msl.doctorMonthlyStatement', 'ms')
            ->where('msl.doctor = :doctor')
            ->andWhere('msl.isExcludePayment = :isExcludePayment')
            ->andWhere('msl.status <> :status')
            ->andWhere($where)
            ->setParameter('doctor', $line->getDoctor())
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

    public function updateDoctorMonthlyStatementList()
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

            $totalAmount = $totalAmountPaid = $totalExceptionStatement = 0;

            $isPaid = $isProcessing = true;
            $isFI = false;
            foreach ($lines as $item) {
                $doctorMonthlyFee = $item->getDoctorMonthlyFee();
                $exceptionStatement = $this->calculateExceptionStatement($item);

                if (empty($doctorMonthlyFee * 1) && empty($exceptionStatement * 1)) {
                    continue;
                }

                $status = $item->getStatus();
                if ($item->getIsExcludePayment() && 1 != $status) {
                    $exceptionStatement += $doctorMonthlyFee;
                }
                $item->setExceptionStatement($exceptionStatement);

                if ($item->getIsExcludePayment()) {
                    $doctorMonthlyFee = 0;
                }

                $amountPaid = $item->getAmountPaid();
                if ($item->getIsExcludePayment()) {
                    $amountPaid = 0;
                }

                $outStandingAmount = $doctorMonthlyFee + $exceptionStatement - $amountPaid;
                $item->setOutStandingAmount($outStandingAmount);

                $em->persist($item);

                $totalExceptionStatement += $exceptionStatement;
                $totalAmountPaid += $amountPaid;
                $totalAmount += $doctorMonthlyFee;

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

}
