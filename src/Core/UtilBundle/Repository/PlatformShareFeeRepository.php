<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UtilBundle\Utility\Constant;

class PlatformShareFeeRepository extends EntityRepository
{
    /**
     * update platform share percentage
     * @author vinh.nguyen
     */
    public function update($params)
    {
        $psObj = $this->findOneBy(array('id'=>$params['id']));
        if($psObj == null)
            return null;

        $takeEffectOn = new \DateTime($params['takeEffectOn']);
        $psObj->setNewPlatformPercentage($params['platformPercentage']);
        $psObj->setNewAgentPercentage($params['agentPercentage']);
        $psObj->setTakeEffectOn($takeEffectOn);
        $psObj->setUpdatedOn(new \DateTime());

        if ($takeEffectOn->diff(new \DateTime())->invert == 0) {
            $psObj->setPlatformPercentage($params['platformPercentage']);
            $psObj->setAgentPercentage($params['agentPercentage']);
        }

        $em = $this->getEntityManager();
        $em->persist($psObj);
        $em->flush();

        return $this->getPSPercentageById($psObj->getId());
    }

    /**
     * update on active / inactive
     * @author vinh.nguyen
     */
    public function updateOnActive($areaType, $isActive = false)
    {
        $qb = $this->createQueryBuilder('psf');
        $qb->update()
            ->set('psf.isActive', ':isActive')
            ->where('psf.areaType=:areaType')
            ->setParameter('isActive', $isActive)
            ->setParameter('areaType', $areaType);
        return $qb->getQuery()->execute();
    }

    public function getUpdateData()
    {
        $query = $this->createQueryBuilder('a')
                ->orWhere('a.takeEffectOn lIKE :date')
                ->setParameter('date', '%' . date("Y-m-d") . '%')
        ;
        return $query->getQuery()->getResult();
    }

    public function getAgentFeeMedicineUpdateData()
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('a')
            ->from('UtilBundle:AgentFeeMedicine', 'a')
            ->where('a.takeEffectOn lIKE :date')
            ->setParameter('date', '%' . date("Y-m-d") . '%')
        ;
        return $query->getQuery()->getResult();
    }

    /**
     * get platform share percentage by type
     * @author  vinh.nguyen
     * @return array
     */
    public function getPSPercentageByType($areaType, $msType)
    {
        $qb = $this->createQueryBuilder('psf');
        $qb->select('psf.id',
            'psf.platformPercentage',
            'psf.newPlatformPercentage',
            'psf.agentPercentage',
            'psf.newAgentPercentage',
            'psf.takeEffectOn',
            'psf.marginShareType',
            'psf.areaType',
            'psf.isActive'
            )
            ->where('psf.marginShareType=:msType AND psf.areaType=:areaType')
            ->setParameter('msType', $msType)
            ->setParameter('areaType', $areaType)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * get platform share percentage by id
     * @author  vinh.nguyen
     * @return array
     */
    public function getPSPercentageById($id)
    {
        $qb = $this->createQueryBuilder('psf');
        $qb->select('psf.id',
                'psf.platformPercentage',
                'psf.newPlatformPercentage',
                'psf.agentPercentage',
                'psf.newAgentPercentage',
                'psf.takeEffectOn',
                'psf.marginShareType',
                'psf.areaType',
                'psf.isActive'
        )
                ->where('psf.id = :id')
                ->setParameter('id', $id)
                ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function calculatePlatformShare($rxLine)
    {
        $em = $this->getEntityManager();

        $reviewFee = round($rxLine->getCostPrice(), 2);
        $rx = $rxLine->getRx();

        $params = ['patient' => $rx->getPatient()];
        $isLocalPatient = $em->getRepository('UtilBundle:Rx')->isLocalPatient($params);

        $areaType = Constant::AREA_TYPE_OVERSEA;
        if ($isLocalPatient) {
            $areaType = Constant::AREA_TYPE_LOCAL;
        }
        $msType = Constant::MST_SERVICE;

        $criteria = [
            'areaType' => $areaType,
            'marginShareType' => $msType,
            'isActive' => true
        ];
        $psp = $this->findOneBy($criteria);

        $platformSF = $agentSF = 0;
        if ($psp) {
            $platformSF = round($psp->getPlatformPercentage(), 2);
            $agentSF = round($psp->getAgentPercentage(), 2);
        }

        $listPrice = round($reviewFee + $platformSF + $agentSF, 2);

        return [
            'platformSF' => $platformSF,
            'agentSF' => $agentSF,
            'doctorSF' => $reviewFee,
            'listPrice' => $listPrice
        ];
    }

}