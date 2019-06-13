<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;

class UserActorsRepository extends EntityRepository
{
    public function getAgentLogins($request) {
        $search = $request->get('search', '');
		$search = strtolower(trim($search));
        $limit = $request->get('length', 10);
        $status = $request->get('status', '2');
        $sort = $request->get('sort', array());
        $id = $request->get('id', 0);
        $page = $request->get('page', 1);
		$offset = ($page - 1) * $limit;
		
		$query = $this->createQueryBuilder('ua')
					->join('ua.user', 'u')
					->innerJoin('UtilBundle:Agent', 'a', 'WITH', 'ua.entityId = a.id');
		
		$query->where($query->expr()->eq('ua.entityId', ':id'))
				->andWhere("ua.role IN (:roles)")
				->setParameter('id', $id)
				->setParameter('roles', array(Constant::AGENT_ROLE, Constant::SUB_AGENT_ROLE));

        switch ($status) {
            case 2: $query->andWhere('ua.deletedOn is null ');
                break;
            case 1:
                $query->andWhere('ua.deletedOn is null ')
                        ->andWhere('u.isActive = 1');
                break;
            case 0:
                $query->andWhere('ua.deletedOn is null ')
                        ->andWhere('u.isActive = 0');
                break;
            default :
                $query->andWhere('ua.deletedOn is not null ');
                break;
        }
		
		if (!empty($search)) {
			$query->andWhere("CONCAT(u.firstName, ' ', u.lastName) LIKE :name OR u.emailAddress LIKE :email")
					->setParameter('name', '%' . $search . '%')
					->setParameter('email', '%' . $search . '%');
		}
		
		$total = $query->select('COUNT(ua)')->getQuery()->getSingleScalarResult();
		
		$query->setMaxResults($limit)
                ->setFirstResult($offset);

        $this->generateSort($query, $sort);
		
        $agentLogins = $query->select("ua.id, u.emailAddress, CONCAT(u.firstName, ' ', u.lastName) AS name, ua.createdOn, u.lastLogin, u.isActive, a.isActive as agentStatus, u.userIp")->getQuery()->execute();
		
        $data = array();
        foreach ($agentLogins as $item) {
            array_push($data, array(
                'id' => $item['id'],
                'hashId' => Common::encodeHex($item['id']),
                'email' => $item['emailAddress'],
                'name' => $item['name'],
                'registerDate' => $item['createdOn']->format('d M y'),
                'status' => $item['isActive'],
                'lastLogin' => $item['lastLogin'] ? $item['lastLogin']->format('d M y h:s:i') : null,
				'agentStatus' => $item['agentStatus'],
                'passwordStatus' => $item['userIp']
                )
            );
        }

        return array('data' => $data, 'total' => $total);
    }
	
    public function getDoctorLogins($request) {
        $search = $request->get('search', '');
		$search = strtolower(trim($search));
        $limit = $request->get('length', 10);
        $status = $request->get('status', '2');
        $sort = $request->get('sort', array());
        $id = $request->get('id', 0);
        $page = $request->get('page', 1);
		$offset = ($page - 1) * $limit;
		
		$query = $this->createQueryBuilder('ua')
					->join('ua.user', 'u')
					->innerJoin('UtilBundle:Doctor', 'd', 'WITH', 'ua.entityId = d.id');
		
		$query->where($query->expr()->eq('ua.entityId', ':id'))
				->andWhere("ua.role = (:roles)")
				->setParameter('id', $id)
				->setParameter('roles', Constant::DOCTOR_ROLE);

        switch ($status) {
            case 2: $query->andWhere('ua.deletedOn is null ');
                break;
            case 1:
                $query->andWhere('ua.deletedOn is null ')
                        ->andWhere('u.isActive = 1');
                break;
            case 0:
                $query->andWhere('ua.deletedOn is null ')
                        ->andWhere('u.isActive = 0');
                break;
            default :
                $query->andWhere('ua.deletedOn is not null ');
                break;
        }
		
		if (!empty($search)) {
			$query->andWhere("CONCAT(u.firstName, ' ', u.lastName) LIKE :name OR u.emailAddress LIKE :email")
					->setParameter('name', '%' . $search . '%')
					->setParameter('email', '%' . $search . '%');
		}
		
		$total = $query->select('COUNT(ua)')->getQuery()->getSingleScalarResult();
		
		$query->setMaxResults($limit)
                ->setFirstResult($offset);

        $this->generateSort($query, $sort);
		
        $doctorLogins = $query->select("ua.id, u.emailAddress, CONCAT(u.firstName, ' ', u.lastName) AS name, ua.createdOn, u.lastLogin, u.isActive, d.isActive as doctorStatus, u.userIp")->getQuery()->execute();
		
        $data = array();
        foreach ($doctorLogins as $item) {
            array_push($data, array(
                'id' => $item['id'],
                'hashId' => Common::encodeHex($item['id']),
                'email' => $item['emailAddress'],
                'name' => $item['name'],
                'registerDate' => $item['createdOn']->format('d M y'),
                'status' => $item['isActive'],
                'lastLogin' => $item['lastLogin'] ? $item['lastLogin']->format('d M y h:s:i') : null,
				'doctorStatus' => $item['doctorStatus'],
                'passwordStatus' => $item['userIp']
                )
            );
        }

        return array('data' => $data, 'total' => $total);
    }
	
    private function generateSort($query, $data) {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'email':
                    $query->orderBy("u.emailAddress", $value);
                    break;
                case 'name':
                    $query->orderBy("u.firstName", $value);
                    $query->orderBy("u.lastName", $value);
                    break;
                case 'registerDate':
                    $query->orderBy("ua.createdOn", $value);
                    break;
                case 'lastLogin':
                    $query->orderBy("u.lastLogin", $value);
                    break;
            }
        }
    }
	
    /*
	 * Author: Tuan Nguyen
     * Get agent/doctor login for suggestion
     */

    public function getSuggestion($request)
    {
		$id = $request->get('id', null);
		$status = $request->get('status', 2);
		$search = strtolower(trim($request->get('term', '')));
		$role = $request->get('role', 'agent');
		
        $query = $this->createQueryBuilder('ua')
                    ->join('ua.user', 'u');
					
        switch ($status) {
            case 2: $query->Where('ua.deletedOn is  null ');
                break;
            case 1:
                $query->Where('ua.deletedOn is  null ')
                        ->AndWhere('u.isActive = 1');
                break;
            case 0:
                $query->Where('ua.deletedOn is  null ')
                        ->AndWhere('u.isActive = 0');
                break;
            default :
                $query->Where('ua.deletedOn is not  null ');
                break;
        }
		$query->andWhere("ua.entityId = $id");
		if ($role == 'agent') {
			$query->andWhere("ua.role IN (:roles)")
					->setParameter("roles", array(Constant::SUB_AGENT_ROLE, Constant::AGENT_ROLE));
		} elseif ($role == 'doctor') {
			$query->andWhere("ua.role = :role")
					->setParameter("role", Constant::DOCTOR_ROLE);
		}
			
        $query->andWhere("CONCAT(u.firstName,' ',u.lastName) lIKE :name OR u.emailAddress like :email")
                ->setParameter('name', '%' . $search . '%')
                ->setParameter('email', '%' . $search . '%')
                ->setMaxResults(5);
				
        $userActors = $query->getQuery()->getResult();
        $result = array();
        foreach ($userActors as $obj) {
            $user = $obj->getUser();
            $name = $user->getFirstName() . ' ' . $user->getLastName();
            if((strpos(strtolower($user->getEmailAddress()), $search) !== false)) {
                array_push($result, array( 'name' => $user->getEmailAddress()));
            } else {
                array_push($result, array('name' => $name));
            }

        }

        return $result;
    }
}