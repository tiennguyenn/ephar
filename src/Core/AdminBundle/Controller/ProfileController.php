<?php

namespace AdminBundle\Controller;

use AdminBundle\Controller\BaseController;
use Google\Authenticator\GoogleAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Form\ProfileType;
use AgentBundle\Form\ChangePasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfileController extends BaseController
{
    /**
     * Admin Password Change Ajax
     * @Route("/profile/ajax-change-password", name="ajax_admin_change_password")
     * @author toan.le
     */
    public function changePasswordAjaxAction(Request $request)
    {
        $gmedUser = $this->getUser();
        $userId = $gmedUser->getId();

        $params = $request->request->all();
        $data = $params['ChangePasswordBundle_agent'];
        $user = $this->getDoctrine()->getRepository('UtilBundle:User')->isMatchPassword($data['current_password'],$userId);
        $results = [];
        
        if($user != null){
            if($data['new_password'] == $data['confirm_password']){
                $this->getDoctrine()->getRepository('UtilBundle:User')->updatePassword($user, $data['new_password']);
                $results = $results = [
                    'success'   => true,
                    'message'   => 'Change password successful.'
                ];
            }else{
                $results = [
                    'success'   => false,
                    'message'   => 'Confirm password does not match.'
                ];
            }
        } else {
            $results = [
                'success'   => false,
                'message'   => 'Current password does not match.'
            ];
        }

        return new JsonResponse($results);
    }

    /**
     * Admin Profile Change Ajax
     * @Route("/profile/change-profile", name="ajax_admin_change_profile")
     * @author toan.le
     */
    public function changeProfileAction(Request $request)
    {
        $gmedUser = $this->getUser();
        $adminId = $gmedUser->getId();
        $common = $this->get('util.common');
        $params = $request->request->all();
        $image = isset($_FILES['image']) ? $_FILES['image'] : '';

        if($image != ''){
            $image = $common->uploadfile($image,'admin/profile-image-'.$adminId);
            $params['ProfileBundle_admin']['image'] = $image;
        }
        $this->getDoctrine()->getRepository('UtilBundle:User')->updateProfile($adminId, $params['ProfileBundle_admin']);

        return new JsonResponse([
                    'success'   => true,
                    'message'   => 'Update profile successful.'
                ]);
    }

    /**
     * get request filter
     * @param $request
     * @return array
     * @author toan.le
     */
    private function getFilter($request)
    {
        $params = array(
            'page'    => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage' => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'userType' => $request->get('userType', Constant::USER_TYPE_DOCTOR),
            'term' => $request->get('term', ''),
            'status' => $request->get('status', 'all'),
            'sorting' => $request->get('sorting', ''),
            'from_date' => $request->get('from_date', ''),
            'to_date' => $request->get('to_date', '')
        );
        //additional filters
        $filters = $request->get('ps_filter', array());
        if(!empty($filters)){
            foreach($filters as $k=>$v){
                $params[$k] = $v;
            }
        }
        return $params;
    }

    /**
     * Admin Profile Personal Information
     * @Route("/profile", name="admin_profile")
     * @author toan.le
     */
    public function indexAction(Request $request)
    {
        $gmedUser = $this->getUser();
        $dataUser = $this->getDoctrine()->getRepository('UtilBundle:User')->get($gmedUser->getId());

        $optionsProfile = array(
            'attr'               => array(
                'id'    => 'personal-information-form',
                'class' => 'form-horizontal'
                ),
            'method'             => 'POST',
            'data'         => $dataUser,
        );

        $formProfile    = $this->createForm('AdminBundle\Form\ProfileType', array(), $optionsProfile);
        return $this->render('AdminBundle:profile:personal-information.html.twig',[
            'formProfile'        => $formProfile->createView(),
            'googleAuthSecret'   => $dataUser['googleAuthSecret']
            ]);
    }

    /**
     * Generate QR Code for google auth
     * @Route("/profile/ajax-generate-google-auth", name="admin_ajax_generate_google_auth")
     * @author nanang.cahya
     */
    public function ajaxGenerateGoogleAuthAction()
    {
        $gmedUser = $this->getUser();
        $dataUser = $this->getDoctrine()->getRepository('UtilBundle:User')->find($gmedUser->getId());
        $googleAuth = new GoogleAuthenticator();
        $googleAuthSecret = $googleAuth->generateSecret();
        $qrCode = $googleAuth->getUrl($dataUser->getEmailAddress(), $this->getRequest()->getHost(), $googleAuthSecret);

        if ($qrCode) {
            $response = array(
                'status' => true,
                'data' => array(
                    'qrCodeUrl' => $qrCode,
                    'secret' => $googleAuthSecret
                )
            );
        } else {
            $response = array(
                'status' => true,
                'data' => null
            );
        }

        return new JsonResponse($response);
    }

    /**
     * Remove google auth
     * @Route("/profile/ajax-remove-google-auth", name="admin_ajax_remove_google_auth")
     * @author nanang.cahya
     */
    public function ajaxRemoveGoogleAuth(Request $request)
    {
        $res = array('success' => true);
        try {
            $em = $this->getDoctrine()->getManager();
            $gmedUser = $this->getUser();
            $dataUser = $this->getDoctrine()->getRepository('UtilBundle:User')->findOneBy(array(
                'id' => $gmedUser->getId(),
                'googleAuthSecret' => $request->request->get('google_auth_secret')
            ));
            if ($dataUser) {
                $dataUser->setGoogleAuthSecret(null);
                $em->persist($dataUser);
                $em->flush();
            } else {
                $res['success'] = false;
            }
        } catch (\Exception $exception) {
            $res['success'] = false;
        }

        return new JsonResponse($res);
    }

    /**
     * Save google auth secret to database
     * @Route("/profile/ajax-save-google-auth", name="admin_ajax_save_google_auth")
     * @author nanang.cahya
     */
    public function ajaxSaveGoogleAuth(Request $request)
    {
        $res = array('status' => true);
        try{
            $googleAuth = new GoogleAuthenticator();
            $gmedUser = $this->getUser();
            $secret = $request->request->get('google_auth_secret');
            $dataUser = $this->getDoctrine()->getRepository('UtilBundle:User')->find($gmedUser->getId());
            $isValidGoogleAuth = $googleAuth->checkCode($secret, $request->request->get('google_auth_code'));
            if (!$isValidGoogleAuth) {
                $res['status'] = false;
                $res['message'] = 'Invalid google auth code';
            } else {
                $this->getDoctrine()->getRepository('UtilBundle:User')->updateGoogleAuthSecret($dataUser, $secret);
            }
        } catch (\Exception $exception) {
            $res['status'] = false;
        }

        return new JsonResponse($res);
    }

    /**
     * Admin Change Password
     * @Route("/profile/change-password", name="admin_change_password")
     * @author toan.le
     */
    public function changePasswordAction(Request $request)
    {
        $gmedUser = $this->getUser();
        $dataUser = $this->getDoctrine()->getRepository('UtilBundle:User')->get($gmedUser->getId());

        $optionsChangePassword = array(
            'attr'               => array(
                'id'    => 'change-password-form',
                'class' => 'form-horizontal'
                ),
            'method'             => 'POST'
        );
        $formChangePassword = $this->createForm('AgentBundle\Form\ChangePasswordType', array(), $optionsChangePassword);

        
        return $this->render('AdminBundle:profile:change-password.html.twig',[
            'formChangePassword' => $formChangePassword->createView(),
            ]);
    }
}
