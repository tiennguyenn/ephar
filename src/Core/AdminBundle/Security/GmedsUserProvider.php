<?php

namespace AdminBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use AdminBundle\Security\GmedsUser;
use UtilBundle\Utility\Constant;

/**
 * UserProvider class
 * 
 * @author Phuc Duong
 */

class GmedsUserProvider implements UserProviderInterface
{
    
    /**
     * @var array
     */
    private $users;
	
	private $container;
	
	private $em;
    
    /**
     * Constructor.
     *
     * @param array $users An array of users
     */
    public function __construct($container, $em, array $users = array())
    {
		$this->container = $container;
		
		$this->em = $em;
		
        $this->users = $users;
    }

    /**
     * Adds a new User to the provider.
     *
     * @param UserInterface $user A UserInterface instance
     *
     * @throws \LogicException
     */
    public function createUser(UserInterface $user)
    {
        if (isset($this->users[strtolower($user->getUsername())])) {
            throw new \LogicException('Another user with the same username already exist.');
        }

        $this->users[strtolower($user->getUsername())] = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
		$user = $this->em->getRepository('UtilBundle:User')->findOneBy(array(
			'emailAddress' => $username
		));

		if (!$user) {
            $ex = new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            $ex->setUsername($username);

            throw $ex;
        }
		
		$roles = [];
		foreach ($user->getRoles() as $role) {
			$roles[] = $role->getName();
		}

		$gmedsUser = new GmedsUser($user->getEmailAddress(), $user->getPasswordHash(), null, $roles);
		
		$expiredTime = $this->container->getParameter('expire_time_login');

		$displayName = "";
		
		if($roles[0] == Constant::TYPE_DOCTOR_NAME || $roles[0] == Constant::TYPE_AGENT_NAME || $roles[0] == Constant::TYPE_SUB_AGENT_NAME){
			$dataName = $this->em->getRepository('UtilBundle:User')->getFullNameById([
				'role' => $roles[0],
				'userId'   => $user->getId(),
				]);
			$displayName = $dataName['fullName'];
			if($roles[0] == Constant::TYPE_DOCTOR_NAME){
				$doctor = $this->em->getRepository('UtilBundle:Doctor')->findOneBy(['user' => $user->getId()]);
				$gmedsUser->setId($doctor->getId());
				$gmedsUser->setIsConfirmed($doctor->getIsConfirmed());
				$gmedsUser->setUpdatedTermCondition($doctor->getUpdatedTermCondition());
				$gmedsUser->setAvatar($doctor->getProfilePhotoUrl());
				$this->em->getRepository('UtilBundle:User')->removeOtpCode($user);
			}elseif($roles[0] == Constant::TYPE_AGENT_NAME || $roles[0] == Constant::TYPE_SUB_AGENT_NAME){
				$agent = $this->em->getRepository('UtilBundle:Agent')->findOneBy(['user' => $user->getId()]);
				$gmedsUser->setId($agent->getId());
				$gmedsUser->setIsConfirmed($agent->getIsConfirmed());
				$gmedsUser->setAvatar($agent->getProfilePhotoUrl());
			}
		} else {
			$displayName = $user->getFirstName() . ' ' . $user->getLastName();
			$gmedsUser->setId($user->getId());
			$gmedsUser->setAvatar($user->getProfilePhotoUrl());
		}

		$gmedsUser->setLoggedUser($user);
		$gmedsUser->setDisplayName($displayName);
		$gmedsUser->setEmail($user->getEmailAddress());
		$gmedsUser->setExpireAt(time() + $expiredTime);
		$platformSetting = $this->em->getRepository('UtilBundle:PlatformSettings')->getPlatFormSetting();
		$gmedsUser->setPlatformSetting($platformSetting);

        return $gmedsUser;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof GmedsUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'AdminBundle\Security\GmedsUser';
    }
    
}