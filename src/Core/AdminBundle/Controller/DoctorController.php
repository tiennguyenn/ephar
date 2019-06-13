<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\DateTime;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;
use UtilBundle\Utility\MonthlyPdfHelper;

class DoctorController extends BaseController
{
    /**
     * @Route("/doctor", name="doctor_index")
     */
    public function indexAction(Request $request)
    {
        //$doctor = $this->get('util.doctor')->getDoctors();
        return $this->render('AdminBundle:doctor:index.html.twig');
    }
}
