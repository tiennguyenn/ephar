<?php
namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use UtilBundle\Entity\User;
use UtilBundle\Utility\Constant;

class UserRepository extends EntityRepository
{

    /**
     * Check an email exists in database
     * @author toan.le
     * @param string $email
     * @return User|null
     */
    public function isExistUser($email) {
        return $this->findOneBy(array('email' => $email));
    }

    /**
     * Check an email exists in database
     * @author toan.le
     * @param string $email
     * @return User|null
     */
    public function isMatchPassword($pwd, $id) {
        return $this->findOneBy([
            'passwordHash' => md5($pwd),
            'id'           => $id
            ]);
    }

    /**
     * Check an email exists in database
     * @author toan.le
     * @param string $email
     * @return User|null
     */
    public function updatePassword($user, $pwd) {
        $em = $this->getEntityManager();
        $user->setPassWordHash(md5($pwd));
        $em->persist($user);
        $em->flush();
        return $user;
    }

    /**
     * Update session id
     * @author toan.le
     * @param string $email
     * @return User|null
     */
    public function updateSessionId($user, $session_id) {
        $em = $this->getEntityManager();
        $user->setSessionId($session_id);
        $em->persist($user);
        $em->flush();
        return $user;
    }

    /**
     * Get user for login
     */
    public function login($data){
        $results = [
            'message'   => '',
            'status'    => true
        ];
        if(isset($data['otp_code'])){
            $user = $this->createQueryBuilder('k')
                            ->where('k.otpCode = :otp')
                            ->andWhere('k.emailAddress = :email')
                            ->andWhere('k.isActive = 1')
                            ->setParameter('otp', $data['otp_code'])
                            ->setParameter('email', $data['email'])
                            ->setMaxResults(1)
                            ->getQuery()
                            ->getOneOrNullResult();
            $now = new \DateTime();
            if($user == null){
                $results['message'] = 'Invalid otp code';
                $results['status'] = false;
                $results['screen'] = 'confirm_otp';
            }elseif($user->getOtpExpiredAt() < $now){
                $this->removeOtpCode($user);
                $results['message'] = 'Otp code expired';
                $results['status'] = false;
                $results['screen'] = 'login';
            }
        } elseif (isset($data['google_auth_code'])) {
            $user = $this->createQueryBuilder('k')
                ->where('k.emailAddress = :emailAddress')
                ->andWhere('k.isActive = 1')
                ->setParameter('emailAddress', $data['_email'])
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            if($user == null){
                $results['message'] = 'User account not found';
                $results['status'] = false;
                $results['screen'] = 'login';
            }
        }
        else{
            $user = $this->createQueryBuilder('k')
                            ->where('k.emailAddress = :username')
                            ->andwhere('k.passwordHash = :password')
							->andWhere('k.isActive = 1')
                            ->setParameter('username', $data['_username'])
                            ->setParameter('password', md5($data['_password']))
                            ->setMaxResults(1)
                            ->getQuery()
                            ->getOneOrNullResult();
            if($user == null){
                $results['message'] = 'Invalid username or password';
                $results['status'] = false;
                $results['screen'] = 'login';
            }
        }
        $results['data'] = $user;
        return $results;
    }

    /**
    * Update last login
    * @author toan.le
    */
    public function updateLastLogin($user){
        $em = $this->getEntityManager();
        $user->setLastLogin(new \DateTime());

        $em->persist($user);

        $em->flush();

        return $user;
    }

    /**
     * Update expired time 
     */
    public function udpateExpiredTime($user, $expiredTime){
        $em = $this->getEntityManager();
        if($expiredTime != null){
            $dateExpired = new \DateTime();
            $dateExpired->modify('+'.$expiredTime.' seconds');
            $user->setExpiredPasswordChange($dateExpired);
        }else{
            $user->setExpiredPasswordChange($expiredTime);
        }

        $em->persist($user);

        $em->flush();

        return $user;
    }

    /**     
     * Insert an user to database on user_account table     
     * @author toan.le
     * @param Array $postUserData     
     * @param \FOS\UserBundle\Model\UserManagerInterface $userManager     
     * @return User     
     */    
    public function create($postUserData, $userManager){       

        $user = $userManager->createUser();  
        $user->setPlainPassword($postUserData['password']);        
        $user->setUsername(trim($postUserData['username']));        
        $user->setEmail(trim($postUserData['email']));      
        $user->addRole($postUserData['role']);
        $user->setLastLogin(new \DateTime());
        $userManager->updateUser($user);
        return $user;    
    }

    /**
     * Update OTP code to database
     * @author toan.le
     * @param $params
     */
    public function updateOtpCode($user, $otpCode, $expiredTime) {
        $em = $this->getEntityManager();
        $dateExpired = new \DateTime();
        $dateExpired->modify('+'.$expiredTime.' seconds');
        
        $user->setOtpCode($otpCode);
        $user->setOtpExpiredAt($dateExpired);
        $em->persist($user);

        $em->flush();

        return $user;
    }

    /**
     * Update Google Authenticator Secret to database
     * @author toan.le
     * @param $params
     */
    public function updateGoogleAuthSecret($user, $secret) {
        $em = $this->getEntityManager();

        $user->setGoogleAuthSecret($secret);
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Remove expired otp time and otp code
     * @author toan.le
     * @param $params
     */
    public function removeOtpCode($user) {
        $em = $this->getEntityManager();

        $user->setOtpCode(null);

        $em->persist($user);

        $em->flush();
        
        return $user;
    }

    /**
     * update user
     * @param $params
     */
    public function updateProfile($userId, $params) {
        $em = $this->getEntityManager();
        
        $user = $this->findOneBy(array('id' => $userId));
        if(isset($params['image']) && $params['image'] != ''){
            $user->setProfilePhotoUrl($params['image']);
        }
        if(isset($params['e_signature']) && $params['e_signature'] != ''){
            $user->setESignature($params['e_signature']);
        }
        if(isset($params['license_no']) && $params['license_no'] != ''){
            $user->setLicenseNo($params['license_no']);
        }
        $user->setGender($params['gender']);
        $user->setFirstName($params['first_name']);
        $user->setLastName($params['last_name']);
        if(isset($params['email']) && $params['email'] != ''){
            if ($params['email'] != $user->getEmailAddress()) {
                $user->setGoogleAuthSecret(null);
            }
            $user->setEmailAddress($params['email']);
        }

        $em->persist($user);

        $em->flush();
        
        return $user;
    }

    /**
     * delete user
     * @param $userId
     */
    public function delete($userId) { }

    /**
     * get user by userId
     * @param $userId
     * @return null|object
     */
    public function get($userId) {
        $queryBuilder = $this->createQueryBuilder('f')
                            ->where('f.id = :userId')
                            ->setParameter('userId', $userId);

        //count total items
        $query = $queryBuilder
            ->setFirstResult(0)
            ->setMaxResults(100);

        $resultQuery =  $query->getQuery()->getArrayResult();

        return $resultQuery[0];
    }

    /**
     * get list users
     * @param $params
     * @return array
     */
    public function getList($params) {
        $queryBuilder = $this->createQueryBuilder('f');

        //count total items
        $totalResult = count($queryBuilder->getQuery()->getArrayResult());

        $query = $queryBuilder
            ->setFirstResult(0)
            ->setMaxResults(100);

        $resultQuery =  $query->getQuery()->getResult();

        $rows = array();
        if($resultQuery){
            foreach ($resultQuery as $row){
                $rows[] = $this->getResponseArray($row);
            }
        }

        return array(
            'totalResult' => $totalResult,
            'data' => $rows
        );
    }

    public function getFullNameById($params){

        $selectStr = "(CASE 
                      WHEN pi.lastName is not null and pi.lastName is not null
                        THEN CONCAT(pi.firstName, ' ',  pi.lastName)
                      WHEN pi.firstName is not null and pi.lastName is null
                        THEN pi.firstName    
                      WHEN pi.firstName is null and pi.lastName is not null 
                        THEN pi.lastName
                      ELSE ''
                   END) as fullName,
                   pi.title as title";

        $queryBuilder = $this->createQueryBuilder('u');
        $queryBuilder
            ->select($selectStr);
        if($params['role'] == Constant::TYPE_DOCTOR_NAME){
            $queryBuilder->addSelect('a.doctorCode as ucode');
            $queryBuilder->innerJoin('UtilBundle:Doctor', 'a', 'WITH', 'a.user = u.id');
        }elseif($params['role'] == Constant::TYPE_AGENT_NAME || $params['role'] == Constant::TYPE_SUB_AGENT_NAME){
            $queryBuilder->addSelect('a.agentCode as ucode');
            $queryBuilder->innerJoin('UtilBundle:Agent', 'a', 'WITH', 'a.user = u.id');
        }
        $queryBuilder->innerJoin('UtilBundle:PersonalInformation', 'pi', 'WITH', 'pi.id = a.personalInformation')
                    ->andWhere('u.id = :userId')
                    ->setParameter('userId', $params['userId']);
        $resultQuery =  $queryBuilder->getQuery()->getOneOrNullResult();
        return $resultQuery;
    }

    /**
     * object to array
     * @param User $user
     * @return array
     */
    public function getResponseArray(User $user){
        return array(
            "id" => $user->getId(),
            "username" => $user->getUsername(),
            "email" => $user->getEmail(),
            "created_date" => $user->getCreatedDate()
        );
    }
    public function getReporter(){
        $query = $this->createQueryBuilder('u')
                  ->select('Concat(u.firstName,\' \',u.lastName) as name, u.id') 
                  ->join('u.roles', 'role')
                  ->where('role.name = :name')
                  ->groupBy('u.id')
                  ->setParameter('name', "Customer Care")
                  ->getQuery()->getResult();
        
        return $query;
 
    }

        /**
     * Find by id
     * @param type $id
     * @return type
     */
    public function findById ($id) {
        return $this->find($id);
    }
    
    public function getUserByRole($role = 1)
    {
        if(!is_array($role))
            $role = array($role);

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('u.emailAddress as email')
            ->from('UtilBundle:UserRole', 'ur')
            ->innerJoin('ur.user', 'u')
            ->innerJoin('ur.role', 'r')
            ->where('r.id IN (:role)')
            ->setParameter('role', $role);
        return $qb->getQuery()->getArrayResult();
    }
    
     /*
     * Author Bien
     */
    public function getValidListEmailByRole($role)
    {
        if(!is_array($role)){
            $role = array($role);
        }
        $em = $this->getEntityManager();
      
        $query = $this->createQueryBuilder('u')
                  ->select('u.emailAddress as email') 
                  ->join('u.roles', 'role')
                  ->where('role.id = :role')
                  ->groupBy('u.id')
                  ->setParameter('role', $role) ; 
        return $query->getQuery()->getArrayResult();
    }
    /*
     * Author Bien
     */
    public function getValidEmailByRole($role, $email)
    {
        if(!is_array($role)){
            $role = array($role);
        }
        $em = $this->getEntityManager();
      
        $query = $this->createQueryBuilder('u')
                  ->select('u.emailAddress as email') 
                  ->join('u.roles', 'role')
                  ->where('role.id IN (:role)')
                  ->andWhere('u.emailAddress = :email')
                  ->groupBy('u.id')
                  ->setParameter('role', $role)
                  ->setParameter('email', $email);        
        return $query->getQuery()->getSingleResult();
    }

    /**
     * @param array $params
     * @return boolean
     */
    public function canLogin($params)
    {
        $criteria = array(
            'emailAddress' => isset($params['_username']) ? $params['_username'] : ''
        );
        $user = $this->findOneBy($criteria);
        if (empty($user)) {
            return true;
        }

        $failedLoginCount = $user->getFailedLoginCount();
        if ($failedLoginCount < $params['maxCountLogin']) {
            return true;
        }

        $lastLoginDate = $user->getLastLogin();
        $dateAllowLogin = $lastLoginDate->modify('+' . $params['timeout'] . ' minute');

        if ($dateAllowLogin->diff(new \DateTime())->invert === 1) {
            return false;
        }

        return true;
    }

    /**
     * @param array $params
     */
    public function incrementFailedLoginCount($params)
    {
        $criteria = array(
            'emailAddress' => isset($params['_username']) ? $params['_username'] : ''
        );
        $user = $this->findOneBy($criteria);
        if (empty($user)) {
            return;
        }

        $userIp = $user->getUserIp();
        if ($userIp == $params['userIp']) {
            $failedLoginCount = $user->getFailedLoginCount();
            $failedLoginCount++;
        } else {
            $failedLoginCount = 0;
            $user->setUserIp($params['userIp']);
        }
        $user->setFailedLoginCount($failedLoginCount);

        $user->setLastLogin(new \DateTime());

        $em = $this->getEntityManager();

        $em->persist($user);
        $em->flush($user);
    }

    /**
     * @param array $params
     */
    public function resetLogin($user)
    {
        $user->setUserIp(NULL);
        $user->setFailedLoginCount(NULL);

        $em = $this->getEntityManager();

        $em->persist($user);
        $em->flush($user);
    }
} 
