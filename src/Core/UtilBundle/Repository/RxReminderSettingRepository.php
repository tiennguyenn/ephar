<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * RxReminderSettingRepository
 * Author Luyen Nguyen
 * Date: 10/13/2017
 */
class RxReminderSettingRepository extends EntityRepository {

    /**
     * Rx Reminder Cycle One Setting
     * @return type
     */
    public function getRxReminderCycleOneSetting() {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->select('f')
                ->where('f.reminderCode = :reminderCode1')
                ->orWhere('f.reminderCode = :reminderCode2')
                ->orWhere('f.reminderCode = :reminderCode3')
                ->orWhere('f.reminderCode = :reminderCode4')
                ->orWhere('f.reminderCode = :reminderCode5')
                ->setParameter('reminderCode1', 'RCC1P1')
                ->setParameter('reminderCode2', 'RCC1P2')
                ->setParameter('reminderCode3', 'RCC1P3')
                ->setParameter('reminderCode4', 'RCC1FP')
                ->setParameter('reminderCode5', 'RCC1FD');
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Rx Reminder Cycle Two Setting
     * @return type
     */
    public function getRxReminderCycleTwoSetting() {
        $queryBuilder = $this->createQueryBuilder('f');
        $queryBuilder->select('f')
                ->where('f.reminderCode = :reminderCode1')
                ->orWhere('f.reminderCode = :reminderCode2')
                ->orWhere('f.reminderCode = :reminderCode3')
                ->orWhere('f.reminderCode = :reminderCode4')
                ->orWhere('f.reminderCode = :reminderCode5')
                ->setParameter('reminderCode1', 'RCC2FOS')
                ->setParameter('reminderCode2', 'RCC2GPS')
                ->setParameter('reminderCode3', 'RCC2FP')
                ->setParameter('reminderCode4', 'RCC2FD')
                ->setParameter('reminderCode5', 'RCC2FA');
        return $queryBuilder->getQuery()->getResult();
    }

}
