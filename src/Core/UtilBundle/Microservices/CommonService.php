<?php

namespace UtilBundle\Microservices;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UtilBundle\Utility\Common;

class CommonService extends BaseService
{
    /**
     * get list country
     * @return array
     */
    public function getCountries()
    {
        try{
            $result = $this->em->getRepository('UtilBundle:Country')->getList();
            $this->data = $result['data'];
            $this->totalResult = $result['totalResult'];
        } catch(Exception $ex) {
            $this->message = $ex->getMessage();
            $this->success = false;
        }
        return $this->getResults();
    }

    /**
     * upload file
     * @return string
     */
    public function uploadfile($file,$filename, $hasExtension = false)
    {
        $target_dir = "uploads/";
        $fileSection = explode('/', $filename);
        array_pop($fileSection);
        $locationDir = $this->container->getParameter('upload_directory') . '/' . implode('/', $fileSection);

        if (Common::createDirIfNotExists($locationDir)) {
            $target_file = $target_dir . basename($file["name"]);
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

            if(!$hasExtension) {
                if(empty($imageFileType))
                $imageFileType = "png";
                $filename = $filename.'.'.$imageFileType;
            }

            $upload = move_uploaded_file($file['tmp_name'],$target_dir.$filename);
            if($upload)
            {
                return $target_dir.$filename;
            }
        }

        return '';
    }

    /**
     * upload file
     * @param array $headers
     * @param array $data
     * @return file
     */

    public function downloadCSV($data, $fileName)
    {
        $response = new Response($data);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'.csv"');
        return $response;
    }

} 
