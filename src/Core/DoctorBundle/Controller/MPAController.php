<?php

namespace DoctorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UtilBundle\Utility\Constant;

class MPAController extends Controller
{
    /**
     * @Route("/mpa", name="mpa_index")
     */
    public function indexAction(Request $request)
    {
        $gmedUser = $this->getUser();

        if (!in_array(Constant::TYPE_MPA, $gmedUser->getRoles())) {
            throw $this->createAccessDeniedException('Unable to access this page!');
        }

        $gmedUser->setIsMPA(true);

        if ($request->isMethod('POST')) {
            $doctorId = $request->get('doctorId');

            $em = $this->getDoctrine()->getEntityManager();
            $doctor = $em->getRepository('UtilBundle:Doctor')->find($doctorId);
            if (empty($doctor)) {
                throw $this->createAccessDeniedException('Unable to access this page!');
            }

            $loggerUser = $gmedUser->getLoggedUser();
            $mpa = $em->getRepository('UtilBundle:MasterProxyAccount')->findOneBy(['user' => $loggerUser]);

            $listMapping = $mpa->getMpaDoctors();
            foreach ($listMapping as $map){
                if($map->getDoctor()->getId() == $doctorId ) {
                    $gmedUser->setPermissions($map->getPrivilege());
                    break;
                }
            }
            
            $gmedUser->setId($doctor->getId());

            $doctorName = $doctor->getPersonalInformation()->getFullName(false);
            $gmedUser->setDoctorName($doctorName);

            $doctorEmail = $doctor->getPersonalInformation()->getEmailAddress();
            $gmedUser->setDoctorEmail($doctorEmail);

            return $this->redirectToRoute('doctor_dashboard');
        }

        return $this->render('DoctorBundle:mpa:index.html.twig');
    }
}