<?php

namespace UtilBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use UtilBundle\Entity\AgentDoctor;
use UtilBundle\Entity\Doctor;
use UtilBundle\Utility\Constant;
use UtilBundle\Utility\Common;

class DoctorRepository extends EntityRepository {

    /**
     * create new
     * @param $params
     * @return null|object
     */
    public function create($params) {
        $em = $this->getEntityManager();
        $personalInformation = $em->getRepository('UtilBundle:PersonalInformation')->create($params);

        $doctor = new Doctor();

        $doctor->setPersonalinformationid($personalInformation->getId());
        $doctor->setCreatedon(new \DateTime());
        $doctor->setUpdatedon(new \DateTime());

        $em->persist($doctor);
        $em->flush();

        return $this->findOneBy(array('id' => $doctor->getId()));
    }

    public function getClinicData($id) {
        $doctor = $this->find($id);
        $clinics = $doctor->getClinics();
        $result = array();
        foreach ($clinics as $clinic) {
            if (!$clinic->getIsPrimary() && empty($clinic->getDeletedOn())) {
                $rs = array();
                $rs['id'] = $clinic->getId();
                $rs['name'] = $clinic->getBusinessName();
                $rs['logo'] = $clinic->getBusinessLogoUrl();
                $rs['email'] = $clinic->getEmail();
                $ca = $clinic->getBusinessAddress();
                $address = $ca->getAddress();

                $rs['city'] = $address->getCity()->getId();
                $rs['cityName'] = $address->getCity()->getName();
                $rs['country'] = $address->getCity()->getCountry()->getId();
                $rs['countryName'] = $address->getCity()->getCountry()->getName();
                $rs['state'] = $address->getCity()->getState()->getId();
                $rs['stateName'] = $address->getCity()->getState()->getName();
                $rs['zipCode'] = $address->getPostalCode();
                $rs['line1'] = $address->getLine1();
                $rs['line2'] = $address->getLine2();
                $rs['line3'] = $address->getLine3();

                $phone = $ca->getBusinessPhone();
                $rs['phoneLocation'] = $phone->getCountry()->getId();
                $rs['phoneLocationName'] = $phone->getCountry()->getName();
                $rs['phoneLocationCode'] = $phone->getCountry()->getPhoneCode();
                $rs['phoneArea'] = $phone->getAreaCode();
                $rs['phoneNumber'] = $phone->getNumber();
                array_push($result, $rs);
            }
        }

        return $result;
    }

    /**
     * update
     * @param $params
     */
    public function update($params) {

    }

    /**
     * delete
     * @param $id
     */
    public function delete($id) {

    }

    public function get($id) {

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

        $resultQuery = $query->getQuery()->getArrayResult();

        return array(
            'totalResult' => $totalResult,
            'data' => $resultQuery
        );
    }



    /*
     * select agent to display auto commplete
     * author :biển
     */

    public function selectDoctorAutoComplete($data)
    {
        $search = strtolower($data['text']);
        $query = $this->createQueryBuilder('a')
                    ->join('a.personalInformation', 'p');
        $status = $data['status'];
        switch ($status) {
            case 2: $query->Where('a.deletedOn is  null ');
                break;
            case 1:
                $query->Where('a.deletedOn is  null ')
                        ->AndWhere('a.isActive = 1');
                break;
            case 0:
                $query->Where('a.deletedOn is  null ')
                        ->AndWhere('a.isActive = 0');
                break;
            default :
                $query->Where('a.deletedOn is not  null ');
                break;
        }
        $query->andWhere('LOWER(CONCAT(p.firstName,\' \',p.lastName))  lIKE :name OR LOWER(a.doctorCode) like :doctorCode')
                ->setParameter('doctorCode', '%' . $search . '%')
                ->setParameter('name', '%' . $search . '%')
                ->setMaxResults(5);
        $agents = $query->getQuery()->getResult();
        $result = array();
        foreach ($agents as $obj) {
            $per = $obj->getPersonalInformation();
            $name = $per->getFirstName() . ' ' . $per->getLastName();
            if((strpos(strtolower($obj->getDoctorCode()), $search) !== false)) {
                array_push($result, array( 'name' => $obj->getDoctorCode()));
            } else {
                array_push($result, array('name' => $name));
            }

        }

        return $result;
    }
    /*
     * get List doctor for admin
     * @param :request
     */

    public function getDoctorsAdmin($request) {
        $search = strtolower($request->get('search', ''));
        $limit = $request->get('length', '');
        $status = $request->get('status', '2');
        $sort = $request->get('sort', array());

        if ($limit == -1) {
            $limit = null;
        }
        $ofset = $request->get('page', 1);

        $query = $this->createQueryBuilder('d')
                ->join('d.personalInformation', 'p');
        switch ($status) {
            case 2: $query->Where('d.deletedOn is  null ');
                break;
            case 1:
                $query->Where('d.deletedOn is  null ')
                        ->AndWhere('d.isActive = 1');
                break;
            case 0:
                $query->Where('d.deletedOn is  null ')
                        ->AndWhere('d.isActive = 0');
                break;
            default :
                $query->Where('d.deletedOn is not  null ');
                break;
        }
        $firstNumber = 0;
        if($limit){
            $firstNumber = ($ofset - 1) * $limit;
        }

        $query->andWhere('LOWER(CONCAT(p.firstName,\' \',p.lastName))  lIKE :name OR LOWER(d.doctorCode) like :doctorCode  ')
                ->setParameter('doctorCode', '%' . $search . '%')
                ->setParameter('name', '%' . $search . '%')
                ->setMaxResults($limit)
                ->setFirstResult($firstNumber);

        $this->generateSort($query, $sort);
        $doctors = $query->getQuery()->getResult();
        $data = array();
        foreach ($doctors as $obj) {
            $per = $obj->getPersonalInformation();
            $name = $per->getFirstName() . ' ' . $per->getLastName();
            $clinics = $obj->getClinics();
            $user = $obj->getUser();
            $country = '';
            $cityName = '';
            foreach ($clinics as $cl) {
                if ($cl->getIsPrimary()) {
                    $city = $cl->getBusinessAddress()->getAddress()->getCity();
                    if ($city) {
                        $cityName = $city->getName();
                        $country = $city->getCountry()->getName();
                    }
                }
            }
            $agentName = '';
            $agent = $obj->getAgents()->last();
            if(!empty($agent) && $agent->getId())
            {
                $agentName = $agent->getPersonalInformation()->getFullName();
            }
            array_push($data, array(
                'id' => $obj->getId(),
                'hashId' => Common::encodeHex($obj->getId()),
                'code' => $obj->getDoctorCode(),
                'name' => $name,
                'registerDate' => $obj->getCreatedOn() ? $obj->getCreatedOn()->format('d M y') : '',
                'email' => $obj->getPersonalInformation()->getEmailAddress(),
                'phone' => $this->getPhone($obj),
                'country' => $country,
                'city' => $cityName,
                'agent' => $agentName,
                'lastLogin' => $user != null && $user->getLastLogin() != null ? $user->getLastLogin()->format('d M y h:s:i') : null,
                'confirmed' => $obj->getIsConfirmed() >= Constant::STATUS_CONFIRM ? true : false,
                'status' => $obj->getIsActive()
                    )
            );
        }


        $total = $this->createQueryBuilder('d')
                ->select('count(d.id)')
                ->join('d.personalInformation', 'p');
        switch ($status) {
            case 2: $total->Where('d.deletedOn is  null ');
                break;
            case 1:
                $total->Where('d.deletedOn is  null ')
                        ->AndWhere('d.isActive = 1');
                break;
            case 0:
                $total->Where('d.deletedOn is  null ')
                        ->AndWhere('d.isActive = 0');
                break;
            default :
                $total->Where('d.deletedOn is not  null ');
                break;
        }
        $total = $total->andWhere('LOWER(CONCAT(p.firstName,\' \',p.lastName))  lIKE :name OR LOWER(d.doctorCode) like :doctorCode  ')
                ->setParameter('doctorCode', '%' . $search . '%')
                ->setParameter('name', '%' . $search . '%')
                ->getQuery()
                ->getSingleScalarResult();

        return array('data' => $data, 'total' => $total);
    }




    private function generateSort($em, $data) {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'doctorCode':
                    $em->orderBy("d.doctorCode", $value);
                    break;
                case 'doctorName':
                    $em->orderBy("p.lastName", $value);
                    $em->orderBy("p.firstName", $value);

                    break;
                case 'registerDate':
                    $em->orderBy("d.createdOn", $value);
                    break;
                case 'emailAddress':
                    $em->orderBy("p.emailAddress", $value);
                    break;
            }
        }
    }

    private function getPhone($obj) {
        $phoneNumber = '';
        //get mobile type
        $typeConfig = Constant::PHONE_TYPE;
        $doctorPhone = $obj->getDoctorPhones()->first();
        if ($doctorPhone) {
            $contact = $doctorPhone->getContact();
            $type = $contact->getPhoneType()->getType();
            $code = $contact->getCountry()->getPhoneCode();
            $phoneNumber = $code . $contact->getAreaCode() . $contact->getNumber();
        }

        return $phoneNumber;
    }


    /*
     * get List doctor for mpa
     * @param :request
     */

    public function getActiveDoctorsAdmin($request) {
        $search = strtolower($request->get('search-select', ''));
        $query = $this->createQueryBuilder('d')
            ->join('d.personalInformation', 'p')
            ->where('d.deletedOn is  null ');

        $query->select('d.id as id,CONCAT(p.firstName,\' \',p.lastName) as name')
            ->andWhere('LOWER(CONCAT(p.firstName,\' \',p.lastName))  lIKE :name ')
            ->setParameter('name', '%' . $search . '%');
        $doctors = $query->getQuery()->getResult();

        $list = array();
        foreach ($doctors as $item){
            $list[$item['id']] = $item;
        }
        return $list;

    }

    public function generateCode($code) {
        $result = 'D-' . date('my') . '-' . $code;

        $drCode = $this->createQueryBuilder('c')
                ->select('c.doctorCode')
                ->setMaxResults(1)
                ->orderBy('c.id ', 'DESC')
                ->getQuery()
                ->getResult();
        if (empty($drCode)) {

            return $result . '-0001';
        }
        $currentCode = $drCode[0]['doctorCode'];
        $data = explode("-", $currentCode);
        $currentNum = intval($data[3]);
        $currentNum++;
        $result .= '-' . str_pad($currentNum, 4, '0', STR_PAD_LEFT);

        return $result;
    }

    /**
     * suggestion search
     * @param $params
     * @return array
     * @author vinh.nguyen
     */
    public function suggestionSearch($term) {
        $term = trim(strtolower($term));
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("d.doctorCode,
                (CASE
                    WHEN pi.lastName is not null and pi.lastName is not null
                        THEN CONCAT(pi.firstName, ' ' ,  pi.lastName)
                    WHEN pi.firstName is not null and pi.lastName is null
                        THEN pi.firstName
                    WHEN pi.firstName is null and pi.lastName is not null
                        THEN pi.lastName
                    ELSE ' '
                END) as name")
                ->distinct()
                ->from('UtilBundle:Doctor', 'd')
                ->innerJoin('d.personalInformation', 'pi')
                ->where('LOWER(d.doctorCode) LIKE :term
                        OR LOWER(pi.firstName) LIKE :term
                        OR LOWER(pi.lastName) LIKE :term
                    ')
                ->setParameter('term', '%' . $term . '%')
                ->setFirstResult(0)
                ->setMaxResults(5);
        $lists = $qb->getQuery()->getArrayResult();

        $results = array();
        $column = 'doctorCode';
        foreach ($lists as $v) {
            if ($column == 'doctorCode' && strpos(strtolower($v['name']), $term) !== false)
                $column = 'name';
            $results[]['name'] = $v[$column];
        }

        return $results;
    }

    /**
     * get doctor information and some relative info
     * this function is used for montly pdf file on montly statment report page
     * @param  array $params
     * @return array
     */
    public function getDoctorInfoForMontlyStatementPdf($params) {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select("CONCAT(pi.title, ' ',d.displayName) as showName, d.displayName , d.doctorCode, a.postalCode, ci.name as city, d.gstNo,
                   ifelse(pi.firstName is null, pi.lastName, CONCAT(pi.title, ' ', pi.firstName, ' ', pi.lastName)) AS fullName, s.name as state, cl.businessName as clinicName, sn.gmedesTaxInvoice as gmedesTaxInvoice, sn.id as snId, sn.serialNumber,
                   a.line1, a.line2, a.line3, (ci.country) as countryId, c.name as country")
                ->from('UtilBundle:Doctor', 'd')
                ->innerJoin('d.personalInformation', 'pi')
                ->innerJoin('UtilBundle:Clinic', 'cl', 'WITH', 'cl.doctor = d.id')
                ->innerJoin('UtilBundle:ClinicAddress', 'ca', 'WITH', 'ca.id = cl.businessAddress')
                ->innerJoin('UtilBundle:Address', 'a', 'WITH', 'a.id = ca.address')
                ->innerJoin('UtilBundle:City', 'ci', 'WITH', 'ci.id = a.city')
                ->innerJoin('UtilBundle:State', 's', 'WITH', 's.id = ci.state')
                ->innerJoin('UtilBundle:Country', 'c', 'WITH', 'c.id = ci.country')
                ->leftJoin('UtilBundle:SequenceNumber', 'sn', 'WITH', 'd.sequenceNumbers = sn.id')
                ->where('d.id =:doctorId')
                ->andWhere('cl.isPrimary = 1')
                ->setParameter('doctorId', $params['doctorId']);

        $result = $qb->getQuery()->getOneOrNullResult();
        if (!empty($result)) {
            $text = 'SG';
            if (empty($params['isTaxInvoice'])) {
                $text .= 'INV';
            } else {
                $text .= 'TINV';
            }
            $pieces = array($text);
            $pieces[] = 'DR';
            $pieces[] = $params['year'];
            $pieces[] = sprintf("%'.02d", $params['month']);

            $runningNumber = $em->getRepository('UtilBundle:RunningNumber')->findOneBy(array('runningNumberCode' => Constant::INVOICE_NUMBER_CODE));
            $runningNumberValue = $runningNumber->getRunningNumberValue();
            $pieces[] = sprintf("%'.04d", $runningNumberValue);

            $taxInvoiceNumber = implode('-', $pieces);
            $result['gmedesTaxInvoice'] = $taxInvoiceNumber;
            $result['address'] = Common::getAddress($result);
            if(!empty($result['displayName'])){
                $result['fullName'] = $result['showName'];
            }
            return $result;
        }

        return array();
    }

    /**
     * get list doctor for send statement
     * @author vinh.nguyen
     */
    public function getDoctorForStatement($listEmail, $params = [])
    {
        $qb = $this->getEntityManager()->createQueryBuilder('d');
        $qb->select("d.id,
                d.doctorCode,
                site.name as siteName,
                ifelse(pi.firstName is null, pi.lastName, CONCAT(pi.title, ' ', pi.firstName, ' ', pi.lastName)) AS name,
                pi.emailAddress")
            ->from('UtilBundle:Doctor', 'd')
            ->innerJoin('UtilBundle:Clinic', 'cl', 'WITH', 'cl.doctor = d.id')
            ->innerJoin('UtilBundle:agentDoctor', 'ad', 'WITH', 'ad.doctor = d.id')
            ->innerJoin('UtilBundle:Agent', 'a', 'WITH', 'ad.agent = a.id')
            ->leftJoin('UtilBundle:Site', 'site', 'WITH', 'site.id = a.site')
            ->innerJoin('d.personalInformation', 'pi')
            ->where('d.isActive =:isActive')
            ->andWhere('d.deletedOn is null')
            ->andWhere('cl.isPrimary = 1')
            ->andWhere("pi.emailAddress is not null AND pi.emailAddress != ''")
            ->groupBy('d.id')
            ->orderBy('d.doctorCode', 'asc')
            ->setParameter('isActive', true);

        if(!empty($listEmail)) {
            $qb->andWhere("pi.emailAddress IN ('".implode("','",$listEmail)."')");
        }

        if (!empty($params)) {
            $qb->innerJoin('UtilBundle:DoctorMonthlyStatementLine', 'msl', 'WITH', 'msl.doctor = d')
            ->innerJoin('msl.doctorMonthlyStatement', 'ms')
            //->leftJoin('UtilBundle:MasterProxyAccountDoctor', 'mad', 'WITH', 'mad.doctor = d')
            ->andWhere('ms.month = :month AND ms.year = :year')
            ->andWhere('msl.totalAmount > 0')
            ->andWhere("msl.filename is not null AND msl.filename != ''")
            //->andWhere('mad.doctor IS NULL')
            ->andWhere('d.isConfirmed IS NOT NULL')
            ->setParameter('month', $params['month'])
            ->setParameter('year', $params['year']);
        }

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * get doctor having clinic with is primary is true
     * @author  thu.tranq
     * @return array doctorid
     */
    public function getPrimaryDoctor($doctorId=0) {
        $qb = $this->getEntityManager()->createQueryBuilder('d');
        $qb->select("d.id, d.doctorCode")
                ->from('UtilBundle:Doctor', 'd')
                ->innerJoin('UtilBundle:Clinic', 'cl', 'WITH', 'cl.doctor = d.id')
                ->where('d.isActive =:isActive')
                ->andWhere('d.deletedOn is null')
                ->andWhere('cl.isPrimary = 1')
                ->setParameter('isActive', true);

        if ($doctorId) {
            $qb->andWhere('d.id =:doctorId')
                ->setParameter('doctorId', $doctorId);
        }

        $result = $qb->getQuery()->getArrayResult();

        return (isset($result) && !empty($result)) ? $result : array();

    }

    /**
     * @author Tien Nguyen
     * @param Doctor $doctor
     * @param boolean $isCheck return boolean
     */
    public function getDoctorGSTCode($doctor, $isCheck = false)
    {
        if (!$doctor) {
            return Constant::GST_ZRS;
        }

        if (!is_object($doctor)) {
            $doctor = $this->find($doctor);
        }

        $today = new \DateTime();
        $gstEffectDate = $doctor->getGstEffectDate();
        if ($doctor->getIsGst() == true && $gstEffectDate <= $today) {
            $gstCode = Constant::GST_SRS;
        } else {
            $gstCode = Constant::GST_ZRS;
        }

        if ($isCheck) {
            $validGstArr = array(Constant::GST_SRS, Constant::GST_SRSGM);
            return in_array($gstCode, $validGstArr);
        }

        return $gstCode;
    }

    /**
     * To check doctor if has gst
     *
     * @param mixed $rx
     * @param int $type
     */
    public function hasGST($rx, $type = null, $params = array())
    {
        $em = $this->getEntityManager();

        if (is_numeric($rx)) {
            $rx = $em->getRepository('UtilBundle:Rx')->find($rx);
        }

        if (is_object($rx)) {
            $doctor  = $rx->getDoctor();
            $patient = $rx->getPatient();
        } else {
            $doctor  = $em->getRepository('UtilBundle:Doctor')->find($params['doctorId']);
            $patient = $em->getRepository('UtilBundle:Patient')->find($params['patientId']);
        }

        if (!$doctor || !$patient) {
            return false;
        }

        if (false == $this->getDoctorGSTCode($doctor, true)) {
            return false;
        }

        if (is_null($type)) {
            return true;
        }

        $rxRes = $em->getRepository('UtilBundle:Rx');
        $isLocalPatient = $rxRes->isLocalPatient(array('patient' => $patient));

        if (Constant::SETTING_GST_MEDICINE == $type) {
            if ($isLocalPatient) {
                return true;
            }
        }

        $dGSRespository = $em->getRepository('UtilBundle:DoctorGstSetting');

        $areaTypes = Constant::PATIENT_TYPE;

        $criteria = array(
            'doctor'  => $doctor,
            'feeType' => $type,
            'area'    => $areaTypes[$isLocalPatient]
        );
        $dgs = $dGSRespository->findOneBy($criteria);

        if (!$dgs) {
            return true;
        }

        if (false == $dgs->getIsHasGst()) {
            return false;
        }

        $arrAGst = array(Constant::GST_SRS, Constant::GST_SRSGM);

        $gstCode = $dgs->getGst()->getCode();
        if (!in_array($gstCode, $arrAGst)) {
            return false;
        }

        $today = new \DateTime();
        $today->setTime(23, 59, 59);
        $gstEffectDate = $dgs->getEffectiveDate();
        if ($gstEffectDate > $today) {
            return false;
        }

        return true;
    }

    /**
     * To get customs clearance gst code
     *
     * @param $rx Rx
     */
    public function getCCGSTCode($rx, $isLocalPatient = null)
    {
        if (!$rx) {
            return Constant::GST_ZRS;
        }

        $doctor  = $rx->getDoctor();
        $patient = $rx->getPatient();

        if (!$doctor || !$patient) {
            return Constant::GST_ZRS;
        }

        $settings = $this->getEntityManager()
            ->getRepository('UtilBundle:PlatformSettings')
            ->getPlatFormSetting();
        if (!$settings) {
            return Constant::GST_ZRS;
        }

        $shippingAddress = $rx->getShippingAddress();
        if (!$shippingAddress) {
            return Constant::GST_ZRS;
        }

        if (is_null($isLocalPatient)) {
            $isLocalPatient = $settings['operationsCountryId'] == $shippingAddress->getId();
        }

        if ($isLocalPatient) {
            return Constant::GST_ZRS;
        }


        $shippingCountryCode = $shippingAddress->getCity()
            ->getCountry()->getCode();

        $feeCode = null;
        if (Constant::SINGAPORE_CODE == $shippingCountryCode) {
            $feeCode = Constant::CCAF_SG;
        }
        if (Constant::INDONESIA_CODE == $shippingCountryCode) {
            $feeCode = Constant::CCAF_ID;
        }

        if (!$feeCode) {
            return Constant::GST_ZRS;
        }

        $criteria = array('feeCode' => $feeCode);
        $psgc = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettingGstCode')
            ->findOneBy($criteria);

        if (!$psgc) {
            return Constant::GST_ZRS;
        }

        $gstCode = $psgc->getGstCode();
        if (!$gstCode) {
            return Constant::GST_ZRS;
        }

        return $gstCode->getCode();
    }

    /**
     * To get patient shipping gst code
     *
     * @param $patient Patient
     * @param $isLocalPatient boolean
     */
    public function getShippingGSTCode($patient, $isLocalPatient = null)
    {
        $settings = $this->getEntityManager()
            ->getRepository('UtilBundle:PlatformSettings')
            ->getPlatFormSetting();

        $params = array(
            'patient'  => $patient,
            'settings' => $settings
        );

        if (!isset($isLocalPatient)) {
            $rxRes = $this->getEntityManager()->getRepository('UtilBundle:Rx');
            $isLocalPatient = $rxRes->isLocalPatient($params);
        }

        $feeCode = Constant::SF_PATIENT_OVERSEA;
        if ($isLocalPatient) {
            $feeCode = Constant::SF_PATIENT_LOCAL;
        }

        $criteria = array('feeCode' => $feeCode);
        $psgc = $this->getEntityManager()->getRepository('UtilBundle:PlatformSettingGstCode')
            ->findOneBy($criteria);

        if (!$psgc) {
            return Constant::GST_ZRS;
        }

        $gstCode = $psgc->getGstCode();
        if (!$gstCode) {
            return Constant::GST_ZRS;
        }

        return $gstCode->getCode();
    }


    public function updateSerialNumber($id, $value)
    {
        $em = $this->getEntityManager();
        $sequenceNumber = $em->getRepository('UtilBundle:SequenceNumber')->find($id);
        if (empty($sequenceNumber)) {
            return;
        }

        $sequenceNumber->setSerialNumber($value);
        $em->persist($sequenceNumber);
        $em->flush();
    }

    public function getDoctorActiveAgent($id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('d');
        $qb->select("ad")
            ->from('UtilBundle:AgentDoctor', 'ad')
            ->innerJoin('ad.agent', 'agent')
            ->where('ad.isActive = :isActive')
            ->andWhere('ad.isPrimary = :isActive')
            ->andWhere('ad.doctor = :doctorId')
            ->andWhere('ad.deletedOn is null')
            ->setParameter('doctorId', $id)
            ->setParameter('isActive', true)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }

    public function getDoctorActiveAgentByEmail($email)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('d');
        $qb->select("ad")
            ->from('UtilBundle:AgentDoctor', 'ad')
            ->innerJoin('ad.agent', 'agent')
            ->innerJoin('ad.doctor', 'd')
            ->innerJoin('d.user', 'u')
            ->where('ad.isActive = :isActive')
            ->andWhere('ad.isPrimary = :isActive')
            ->andWhere('u.emailAddress = :email')
            ->andWhere('ad.deletedOn is null')
            ->setParameter('email', $email)
            ->setParameter('isActive', true)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }

    public function getFavoriteDrugs($params, $doctorId)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder()
            ->from('UtilBundle:DoctorMedicalFavourite', 'dmf')
            ->innerJoin('dmf.drug', 'd')
            ->where('dmf.doctor = :doctorId')
            ->setParameter('doctorId', $doctorId);

        if (isset($params['name']) && !empty($params['name'])) {
            $qb->andWhere($qb->expr()->like('d.name', ':name'))
                ->setParameter('name', "%" . trim($params['name'], ".") . "%");
        }

        $qb->andWhere("d.deletedOn IS NULL")
            ->andWhere("d.discontinuedOn IS NULL");
        
        if (!empty($params['sort']) && !empty($params['dir'])) {
            $qb->addOrderBy("d.".$params['sort'], $params['dir']);
        } else {
            $qb->addOrderBy("d.createdOn", "desc");
        }

        $total = $qb->select('count(distinct d)')
            ->getQuery()
            ->getSingleScalarResult();

        $qb->select('distinct d.id, 
                    d.name, 
                    d.listPriceDomestic, 
                    d.listPriceInternational, 
                    d.costPriceToClinic,
                    d.costPriceToClinicOversea,
                    d.sku,
                    d.minimumOrderQuantity,
                    d.packQuantity');

        $page = $params['page'] - 1;
        $perPage = $params['limit'];
        $totalPages = 0;

        $qb->setFirstResult($page * $perPage);
        if ($perPage) {
            $qb->setMaxResults($perPage);
            $totalPages = ceil($total/$perPage);
        }

        $list = $qb->getQuery()->execute();

        return array(
            'totalResult' => $total,
            'totalPages' => $totalPages,
            'list' => $list
        );
    }

    public function getFavoriteDrugsWithAudit($doctorId)
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();
        $qb->from('UtilBundle:DoctorDrug', 'dd');
        $qb->innerJoin('dd.drug', 'd');
        $qb->where('dd.doctor = :doctorId')
            ->orderBy('dd.doctor')
            ->addOrderBy('dd.drug')
            ->addOrderBy('dd.effectiveDate')
            ->setParameter("doctorId", $doctorId);

        $qb->select("d.id, 
            dd.listPriceDomestic, 
            dd.listPriceInternational,
            dd.listPriceDomesticNew as newListPriceDomestic, 
            dd.listPriceInternationalNew as newListPriceInternational, 
            d.listPriceDomestic as drugListPriceDomestic,
            d.listPriceInternational as drugListPriceInternational, 
            dd.effectiveDate");

        $list = $qb->getQuery()->getResult();

        $result = array();
        $currentDate = new \DateTime();
        $currentDate->setTime(0, 0);

        $result = [];
        foreach ($list as $item) {
            $oldValue = $item['listPriceDomestic'];
            if (empty($oldValue)) {
                $oldValue = $item['drugListPriceDomestic'];
            }

            $interOldValue = $item['listPriceInternational'];
            if (empty($interOldValue)) {
                $interOldValue = $item['drugListPriceInternational'];
            }

            if ($item['newListPriceDomestic']) {
                $result[$item['id']]['old_value'] = $oldValue;
                if ($item['effectiveDate'] > $currentDate) {
                    $result[$item['id']]['new_value'] = $item['newListPriceDomestic'];
                    $result[$item['id']]['take_effect_on'] = $item['effectiveDate'];
                }
            }

            if ($item['newListPriceInternational']) {
                $result[$item['id']]['inter_old_value'] = $interOldValue;
                if ($item['effectiveDate'] > $currentDate) {
                    $result[$item['id']]['inter_new_value'] = $item['newListPriceInternational'];
                    $result[$item['id']]['inter_take_effect_on'] = $item['effectiveDate'];
                }
            }
        }

        return $result;
    }

    public function getCustomSellingPricesLogs($userId)
    {
        $em = $this->getEntityManager();

        $startDate = new \DateTime();
        $endDate = $startDate;
        $year = date('Y');
        $month = date("n");
        switch ($month) {
            case 1:
            case 2:
                $year -= 1;
                $month += 10;
                $endDate = new \DateTime("$year-$month-01 00:00:00");
                break;
            default:
                $month -= 2;
                $month = $month < 10 ? "0" . $month : $month;
                $endDate = new \DateTime("$year-$month-01 00:00:00");
                break;
        }

        $qb = $em->createQueryBuilder()
            ->from('UtilBundle:AuditTrailPrice', 'dd')
            ->innerJoin('UtilBundle:Drug', 'd', 'WITH', 'dd.entityId = d.id')
            ->innerJoin('UtilBundle:User', 'u', 'WITH', 'dd.createdBy = u.id')
            ->where('dd.tableName = :tableName')
            ->andWhere('dd.createdBy = :userId')
            ->setParameter('tableName', 'doctor_drug')
            ->setParameter('userId', $userId);

        $qb->andWhere($qb->expr()->lte('dd.createdOn', ':startDate'))
            ->andWhere($qb->expr()->gte('dd.createdOn', ':endDate'))
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        $result = $qb->select('dd', 'd.name', 'd.listPriceDomestic', 'd.listPriceInternational', 'u.firstName', 'u.lastName')
                    ->addOrderBy("dd.createdOn", "desc")
                    ->getQuery()
                    ->execute();

        $logs = [];
        foreach ($result as $item) {
            $value = $item[0];

            $temp = [];
            $temp['drug']['name'] = $item['name'];
            $temp['user']['firstName'] = $item['firstName'];
            $temp['user']['lastName'] = $item['lastName'];
            $temp['createdOn'] = $value->getCreatedOn();
            $temp['priceType'] = $value->getFieldName();
            $temp['oldCostPrice'] = number_format($value->getOldPrice(), 2);
            $temp['newCostPrice'] = number_format($value->getNewPrice(), 2);
            $temp['takeEffectOn'] = $value->getEffectedOn();
            $temp['isPercent'] = false;

            $logs[] = $temp;
        }

        return $logs;
    }
}
