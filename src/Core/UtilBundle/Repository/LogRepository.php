<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UtilBundle\Entity\Log;
use \DateTime;
use UtilBundle\Utility\Constant;

/**
 * LogRepository
 * Author Luyen Nguyen
 * Date: 10/11/2017
 */
class LogRepository extends EntityRepository {

    /**
     * update
     * @param type $params
     */
    public function insert($params) {
        $log = new Log();

        if (isset($params['entityId'])) {
            $log->setEntityId($params['entityId']);
        }
        if (isset($params['title'])) {
            $log->setTitle($params['title']);
        }
        if (isset($params['action'])) {
            $log->setAction($params['action']);
        }
        if (isset($params['module'])) {
            $log->setModule($params['module']);
        }
        if (isset($params['oldValue'])) {
            $log->setOldValue($params['oldValue']);
        }
        if (isset($params['newValue'])) {
            $log->setNewValue($params['newValue']);
        }
        if (isset($params['createdBy'])) {
            $log->setCreatedBy($params['createdBy']);
        }

        $log->setCreatedOn(new DateTime("now"));
        $em = $this->getEntityManager();
        $em->persist($log);
        $em->flush();
    }

    /**
     * Get logs
     * @param type $params
     */
    public function getLogs($params) {
        $title = isset($params['title']) ? $params['title'] : '' ;
        $module = $params['module'];
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder
            ->select('f.id, f.title, f.oldValue, f.newValue, f.createdBy, f.createdOn,
                i.isRestored, i.id as issuesAttachmentLogId, ia.documentUrl, ia.documentName')
            ->leftJoin('UtilBundle:IssuesAttachmentLog', 'i', 'WITH', 'i.log = f.id')
            ->leftJoin('i.issueAttachment', 'ia')            
            ->where('f.module = :module')
            ->setParameter('module', $module);
        if (isset($params['title'])) {
            $queryBuilder->andWhere('f.title = :title')
                          ->setParameter('title', $title);
        }

        if (isset($params['entityId'])) {
            $queryBuilder->andWhere('f.entityId = :entityId')
                          ->setParameter('entityId', $params['entityId']);
        }

        if (isset($params['createdOn'])) {
            $queryBuilder->andWhere('date(f.createdOn) = :createdOn')
                          ->setParameter('createdOn', $params['createdOn']);
        }
        $queryBuilder->orderBy('f.id', 'DESC');
        $results = $queryBuilder->getQuery()->getResult();
        return $results;
    }

    /**
     * author bienmai
     * Get logs
     * @param type $params
     */
    public function getLogsCustomerCare($params) {
        $results = [];
        $title = isset($params['title']) ? $params['title'] : '' ;
        $module = $params['module'];
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder
            ->select('f.id, f.title, f.oldValue, f.newValue, f.createdBy, f.createdOn,
                i.isRestored, i.id as issuesAttachmentLogId, ia.documentUrl, ia.documentName,rs.status')
            ->leftJoin('UtilBundle:IssuesAttachmentLog', 'i', 'WITH', 'i.log = f.id')
            ->leftJoin('i.issueAttachment', 'ia')
            ->leftJoin('f.resolves', 'rs')
            ->where('f.module = :module')
            ->setParameter('module', $module);
        if (isset($params['title'])) {
            $queryBuilder->andWhere('f.title = :title')
                ->setParameter('title', $title);
        }

        if (isset($params['entityId'])) {
            $queryBuilder->andWhere('f.entityId = :entityId')
                ->setParameter('entityId', $params['entityId']);
        }
        $queryBuilder->orderBy('f.id', 'DESC');
        $resultsData = $queryBuilder->getQuery()->getResult();
        foreach ($resultsData as $record){
            if($record['status'] != 2){
                $results[] = $record;
            }
        }
        return $results;
    }

    
    /**
     * author bienmai
     * Get logs
     * @param type $params
     */
    public function getLogsRedispenseOrder($params) {
        $results = [];

        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder
            ->select('f.id, f.title, f.oldValue, f.newValue, f.createdBy, f.createdOn')
            ->where('f.module = :module')
            ->andWhere('f.title = :title')
            ->andWhere('f.entityId = :entityId')
            ->andWhere('f.action = :action')
            ->setParameter('module', Constant::MODULE_CC)
            ->setParameter('title', 're-dispense-log')
            ->setParameter('entityId', $params['entityId'])
            ->setParameter('action', 'redispense');

        $queryBuilder->orderBy('f.id', 'DESC');
        $results = $queryBuilder->getQuery()->getResult();
        return $results;
    }

    public function geLogsAgentFeeMedicine($params) {
        $results = [];
        $queryBuilder = $this->getEntityManager()
                            ->createQueryBuilder()
                            ->select('r,u.firstName,u.lastName')
                            ->from('UtilBundle:AgentFeeMedicineLog', 'r')
                            ->innerJoin('UtilBundle:User','u','WITH', 'u.id = r.userId')
                            ->where('r.agentFeeMedicineId = :agent_fee_medicine_id')
                            ->setParameter('agent_fee_medicine_id', $params['entityId']);

        $queryBuilder->orderBy('r.id', 'DESC');
        $results = $queryBuilder->getQuery()->getResult();

        return $results;
    }


}
