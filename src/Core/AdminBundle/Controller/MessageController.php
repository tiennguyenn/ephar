<?php

namespace AdminBundle\Controller;

use AdminBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AdminBundle\Form\MessageType;
use Symfony\Component\HttpFoundation\JsonResponse;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class MessageController extends BaseController
{
    /**
     * Messages compose
     * @Route("/messages/compose", name="message_compose")
     */
    public function composeAction(Request $request)
    {
        $type = $request->get('type', '');
        $id = $request->get('id', '');
        $em = $this->getDoctrine()->getManager();
        $gmedUser = $this->getUser();
        $userId = $gmedUser->getId();
        
        if(!empty($id)) {
            $data = $em->getRepository('UtilBundle:Message')->getDetail($userId, $id, $type);
            if($data == null)
                return $this->redirect($this->generateUrl('message'));
            $data['type'] = $type;
        } else {
            $data = array();
        }
        $options = array(
            'attr' => array(
                'id'    => 'frm-messages',
                'class' => 'inbox-compose form-horizontal',
                'enctype' => 'multipart/form-data',
                'action'  => $this->generateUrl('message_send')
                ),
            'method' => 'POST'
        );

        $form = $this->createForm(new MessageType(), $data, $options);
        
        return $this->render('AdminBundle:message:compose.html.twig',[
            'form'   => $form->createView(),
            'data'   => $data,
            'type'   => $type
            ]);
    }
    
    /**
     * Messages send
     * @Route("/messages/send", name="message_send")
     * @author vinh.nguyen
     */
    public function sendAction(Request $request)
    {
        $response = array();
        if($request->isMethod('POST')) {
            $postData = $request->get('messages', null);
            $em = $this->getDoctrine()->getManager();

            //upload file
            if(isset($postData['url_attachment'])) {
                $attachments = $postData['url_attachment'];
            } else {
                $attachments = array();
            }
            
            //sender
            $gmedUser = $this->getUser();
            $sender = $em->getRepository('UtilBundle:User')->find($gmedUser->getId());
            
            //content
            $contentData = array(
                'subject' => $postData['subject'],
                'body' => $postData['message']
            );            
            $content = $em->getRepository('UtilBundle:MessageContent')->create($contentData);
            
            //action type
            switch ($postData['postType']) {
                case 'send':
                    $emailTarget = Common::getTargetEmail($em, $postData['to'], 'admin');
                    
                    if(!empty($emailTarget)) {
                        //get display name of sender
                        $roles = [];
                        foreach ($sender->getRoles() as $role) {
                            $roles[] = $role->getName();
                        }

                        if($roles[0] == Constant::TYPE_DOCTOR_NAME 
                            || $roles[0] == Constant::TYPE_AGENT_NAME 
                            || $roles[0] == Constant::TYPE_SUB_AGENT_NAME) {
                            $dataName = $em->getRepository('UtilBundle:User')->getFullNameById([
                                'role' => $roles[0],
                                'userId' => $sender->getId(),
                            ]);
                            $senderName = $dataName['fullName'];
                            if(!empty($dataName['ucode']))
                                $senderName .= " (".$dataName['ucode'].")";
                        } else {
                            $senderName = $sender->getFirstName()." ".$sender->getLastName();
                        }
                        
                        foreach ($emailTarget as $item) {
                            $receiver = $em->getRepository('UtilBundle:User')->findOneBy(array('emailAddress'=>$item['email']));
                            
                            if($receiver) {
                                //get display name of receiver
                                $receiverRoles = [];
                                foreach ($receiver->getRoles() as $role) {
                                    $receiverRoles[] = $role->getName();
                                }

                                if($receiverRoles[0] == Constant::TYPE_DOCTOR_NAME 
                                    || $receiverRoles[0] == Constant::TYPE_AGENT_NAME 
                                    || $receiverRoles[0] == Constant::TYPE_SUB_AGENT_NAME) {
                                    $dataName = $em->getRepository('UtilBundle:User')->getFullNameById([
                                        'role' => $receiverRoles[0],
                                        'userId' => $receiver->getId(),
                                    ]);
                                    $receiverName = $dataName['fullName'];
                                    if(!empty($dataName['ucode']))
                                        $receiverName .= " (".$dataName['ucode'].")";
                                } else {
                                    $receiverName = $receiver->getFirstName()." ".$receiver->getLastName();
                                }
                                
                                $messageData = array(
                                    'content'       => $content,
                                    'sender'        => $sender,
                                    'senderName'    => isset($item['sendAll'])? 'Gmedes Admin': $senderName,
                                    'senderEmail'   => $sender->getEmailAddress(),
                                    'receiver'      => $receiver,
                                    'receiverName'  => $receiverName,
                                    'receiverEmail' => $receiver->getEmailAddress(),
                                    'receiverType'  => 0,
                                    'receiverGroup' => isset($item['receiverGroup'])? $item['receiverGroup']: null,
                                    'status'        => Constant::MESSAGE_INBOX,
                                    'sentDate'      => new \DateTime(),
                                );
                                if(isset($postData['id']) && !empty($postData['id'])) {
                                    $msgId = (int)$postData['id'];
                                    $msgObj = $em->getRepository('UtilBundle:Message')->find($msgId);
                                    if($msgObj != null && $msgObj->getStatus() == Constant::MESSAGE_DRAFT)
                                        $messageData['id'] = $msgId;
                                    else
                                        $messageData['parentMessageId'] = $msgId;
                                }
                                $em->getRepository('UtilBundle:Message')->create($messageData, $attachments);
                            }
                            
                        }
                        $this->get('session')->getFlashBag()->add('success', 'The message has been sent successfully');
                        return $this->redirect($this->generateUrl('message'));
                    }
                    break;
                    
                case 'trash':
                    $messageData = array(
                        'content'     => $content,
                        'sender'      => $sender,
                        'senderName'  => $sender->getUserName(),
                        'senderEmail' => $sender->getEmailAddress(),
                    );
                    $messageId = $em->getRepository('UtilBundle:Message')->create($messageData, $attachments);
                    $em->getRepository('UtilBundle:Message')->deleted($messageId);
                    $this->get('session')->getFlashBag()->add('success', 'The message has been discard successfully');
                    return $this->redirect($this->generateUrl('message'));
                    break;
                    
                case 'draft':
                    $messageData = array(
                        'content'     => $content,
                        'sender'      => $sender,
                        'senderName'  => $sender->getUserName(),
                        'senderEmail' => $sender->getEmailAddress(),
                        'status'      => Constant::MESSAGE_DRAFT
                    );
                    $em->getRepository('UtilBundle:Message')->create($messageData, $attachments);
                    $this->get('session')->getFlashBag()->add('success', 'A draft message has been created successfully');
                    return $this->redirect($this->generateUrl('message'));
                    break;
            }
        }
        return $this->redirect($this->generateUrl('message_compose'));
    }

    /**
     * Messages compose
     * @Route("/messages/view/{id}", name="message_view")
     * @author toan.le
     */
    public function viewAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $gmedUser = $this->getUser();
        $userId = $gmedUser->getId();
        $user = $em->getRepository('UtilBundle:User')->find($userId);
        $result = $em->getRepository('UtilBundle:Message')->getDetail($userId, $id);
// dump($result); exit;
        if($result == null)
            return $this->redirect($this->generateUrl('message'));
            
        //make as read
        if($result['receiverId'] == $userId && empty($result['readDate'])) {
            $em->getRepository('UtilBundle:Message')->markAsRead($id);
        }
        
        return $this->render('AdminBundle:message:view.html.twig',[
            'data' => $result,
            'currentEmail' => $user->getEmailAddress()
            ]);
    }
    
    /**
     * Messages change
     * @Route("/messages/change", name="message_change")
     * @author vinh.nguyen
     */
    public function changeAction(Request $request)
    {
        $act = $request->get('act', null);
        $ids = $request->get('ids', null);
        $type = $request->get('type', null);
        
        $response = array(
            'status' => false
        );
        if(!empty($ids) && !empty($act)) {
            $gmedUser = $this->getUser();
            $userId = $gmedUser->getId();
            $act = (int)$act;
            $listIds = explode(',', $ids);
            $em = $this->getDoctrine()->getManager();
            switch ($act) {
                case 1:
                    foreach ($listIds as $id)
                        $em->getRepository('UtilBundle:Message')->markAsRead($id);
                    break;

                case 2: 
                    if($type == 'sent') {
                        foreach ($listIds as $id) {
                            $msgObj = $em->getRepository('UtilBundle:Message')->find($id);
                            if($msgObj != null) {
                                $msgList = $em->getRepository('UtilBundle:Message')->findBy(array(
                                    'content' => $msgObj->getContent()->getId()
                                ));
                                
                                foreach ($msgList as $itemId) {
                                    $em->getRepository('UtilBundle:Message')->deleted($itemId, $type);
                                }
                            }
                        }
                    } else {
                        foreach ($listIds as $id)
                            $em->getRepository('UtilBundle:Message')->deleted($id, $type);
                    }
                    break;
            }

            $response = array(
                'status' => true,
                'data' => $em->getRepository('UtilBundle:Message')->getTotal($userId)
            );
        }

        return new JsonResponse($response);
    }
    
    /**
     * Messages compose
     * @Route("/messages/upload", name="message_upload")
     */
    public function uploadAction(Request $request)
    {
        $result = array();
        $file = $request->files->get('files');
        $result['files'][] = Common::messageUploadFile($this->container, $file);

        return new JsonResponse($result);
    }
    
    /**
     * download file
     * @Route("/messages/download", name="message_download")
     * @author vinh.nguyen
    **/
    public function downloadAction(Request $request)
    {
        $id = $request->get('id', null);
        if(!empty($id)) {
            $em = $this->getDoctrine()->getManager();
            $attachments = $em->getRepository('UtilBundle:Message')->getAttachmentBy($id);
            if($attachments) {
                $files = array();
                foreach ($attachments as $item) {
                    array_push($files, "../web/".$item['urlAttachment']);
                }

				$path = $this->get('kernel')->getRootDir();
                $zip = new \ZipArchive();
                $zipName = 'attachments-'.time().".zip";
				$zipFile = $path . "/../web/uploads/tmp" . $zipName;
                $zip->open($zipFile,  \ZipArchive::CREATE);
                foreach ($files as $f) {
                    $zip->addFromString(basename($f),  file_get_contents($f));
                }
                $zip->close();

                $response = new Response(file_get_contents($zipFile));
                $response->headers->set('Content-Type', 'application/zip');
                $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
                $response->headers->set('Content-length', filesize($zipFile));
				
				if(file_exists($zipFile)) {
					unlink($zipFile);
				}

                return $response;
            }
        }
    }
    
    /**
     * for suggestion
     * @Route("/messages/suggestion", name="message_suggestion")
     */
    public function suggestionAction(Request $request)
    {
        $terms = $request->get('term', '');
        $arrTerms = array_map('trim', explode(',', $terms));
        $term = end($arrTerms);

        $results = array();
        $em = $this->getDoctrine()->getManager();
        $results = $em->getRepository('UtilBundle:Message')->suggestionSearch($term);

        return new JsonResponse($results);
    }
    
    /**
     * Get list messages
     * @Route("/messages/list", name="message_list")
     * @author vinh.nguyen
     */
    public function ajaxListAction()
    {
        $request = $this->get('request');
        $gmedUser = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $type = $request->get('type', 'inbox');
        if($type == 'trash') {
            $sortName = 'deletedOn';
        } else if($type == 'draft') {
            $sortName = 'createdOn';
        } else {
            $sortName = 'sentDate';
        }  
        $params = array(
            'page'       => $request->get('page', Constant::PAGE_DEFAULT),
            'perPage'    => $request->get('perPage', Constant::PER_PAGE_DEFAULT),
            'type'       => $type,
            'term'       => $request->get('term', ''),
            'read_date'  => $request->get('read_date', ''),
            'sort_name'  => $request->get('sort_name', $sortName),
            'sort_order' => $request->get('sort_order', 'DESC'),
            'userId'     => $gmedUser->getId()
        );
        $filters = $request->get('filter', array());
        if(!empty($filters)) {
            foreach($filters as $k=>$v) {
                $params[$k] = $v;
            }
        }

        //process data
        $results = $em->getRepository('UtilBundle:Message')->getList($params);
        $template = 'AdminBundle:message:ajax_list.html.twig';
        $totalPages = isset($results['totalPages']) ? $results['totalPages'] : Constant::TOTAL_PAGE_DEFAULT;
        $pageUrl = $this->generateUrl('message_list', $params);

        //build paging
        $paginationHTML = Common::buildPagination(
            $this->container,
            $request,
            $totalPages,
            $params['page'],
            $params['perPage'],
            array(
                'pageUrl' => $pageUrl,
                 'onlyPrevNext' => true
             )
        );

        return $this->render($template, array(
            'data'           => $results['data'],
            'paginationHTML' => $paginationHTML,
            'currentPage'    => $params['page'],
            'perPage'        => $params['perPage'],
            'totalResult'    => $results['totalResult'],
            'type'           => $params['type'],
            'read_date'      => $params['read_date'],
            'sort_name'      => $params['sort_name'],
            'sort_order'     => $params['sort_order'] == 'DESC' ? 'ASC' : 'DESC',
        ));
    }
    
    /**
     * Messages
     * @Route("/messages/{type}", name="message")
     */
    public function indexAction($type = 'inbox')
    {
        return $this->render('AdminBundle:message:index.html.twig',[
            'type' => $type
            ]);
    }
}
