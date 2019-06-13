<?php

namespace UtilBundle\Utility;

/**
 * Description of Utils
 * @author thu.tranq
 */
class Utils
{
    /**
     * insert logs into log table
     * @param  array $oldData
     * @param  array $newData
     * @param  array $params
     * @return
     */
    public static function saveLog($oldData, $newData, $author, $params, $em) {
        $isChanged = self::isChanged($oldData, $newData);
        if ($isChanged == false) {
            return;
        }
        $encodeOldData       = json_encode($oldData);
        $encodeNewData       = json_encode($newData);
        $params['entityId']  = isset($params['id']) ? $params['id'] : '';
        $params['action']    = isset($params['action']) ? $params['action'] : 'update';
        $params['oldValue']  = $encodeOldData;
        $params['newValue']  = $encodeNewData;
        $params['createdBy'] = $author;

        // insert data into log table
        $em->getRepository('UtilBundle:Log')->insert($params);
    }


    /**
     * the function is used to remove the oldValues and newValues are identical
     * @param  $logs
     * @return array
     */
    public static function cleanLogs($logs) {
        foreach ($logs as $key => $log) {
            if ( serialize($log['oldValue']) == serialize($log['newValue']) and !empty($log['oldValue'])) {
                unset($logs[$key]);
            }
        }

        return $logs;
    }

    /**
     * [decodeLog description]
     * @param  array $logs
     * @return array
     */
    public static function decodeLog($logs) {
        // Decode json
        for ($i = 0; $i < count($logs); $i++) {
            if (is_string($logs[$i]['oldValue'])) {
                $oldValue = json_decode($logs[$i]['oldValue'], true);
                if (is_array($oldValue)) {
                    $logs[$i]['oldValue'] = $oldValue;
                }
            }

            if (is_string($logs[$i]['newValue'])) {
                $newValue = json_decode($logs[$i]['newValue'], true);
                if (is_array($newValue)) {
                    if (isset($newValue['excludePaymentNote']) && !isset($newValue['note'])) {
                        $newValue['note'] = $newValue['excludePaymentNote'];
                    }
                    $logs[$i]['newValue'] = $newValue;
                }
            }
        }

        return $logs;
    }

    /**
     * detect the different between old data and new data before persit
     * @param  $oldData
     * @param  $newData
     * @return boolean
     */
    public static function isChanged($oldData, $newData) {
        if (serialize($oldData) != serialize($newData) || empty($oldData)) {
            return true;
        }
        return false;
    }

    /**
    * get working date by week
    * @param $listPHDate
    * @param $weeklyPoDay
    * @return DateTime
    **/
    public function getWorkingDateByWeek($listPHDate, $weeklyPoDay)
    {
        $now = new \DateTime(date("Y-m-d"));
        $dayNow = $now->format('w');
        $countDate = clone $now;

        if ($dayNow > $weeklyPoDay) {
            while ($weeklyPoDay < $dayNow) {
                $countDate->modify("-1 day");
                $weeklyPoDay++;
            }
        } elseif($dayNow < $weeklyPoDay) {
            while ($weeklyPoDay > $dayNow) {
                $countDate->modify("+1 day");
                $dayNow++;
            }
        }

        while (in_array($countDate, $listPHDate) || $countDate->format('w') == 0 || $countDate->format('w') == 6) {
            $countDate->modify("+1 day");
        }

        return $countDate;
    }

    /**
    * get working date
    **/
    public static function getWorkingDate($listPHDate, $frequency, $date = null)
    {
        $i = 1;
        $now = new \DateTime(date("Y-m-d"));
        if($date) {
            if (is_string($date)) {
                $date = new \DateTime($date);
                $countDate = clone $date;
            } else {
                $countDate = clone $date;
            }
        } else {
            $countDate = clone $now;
            $countDate->modify('first day of this month');
        }
        while ($i <= $frequency) {
            if($i > 1)
            $countDate->modify("+1 day");
            $day = $countDate->format('N');

            // exclude Saturday, Sunday and Publish Holiday
            if ($day == 6 || $day == 7 || in_array($countDate, $listPHDate))
            {
                $frequency++;
            }
            $i++;
        }

        return $countDate;
    }

    /**
     * insert logs into audit_trail_price table
     * @param  array $params
     * @param  array $options
     * @return number
     */
    public static function saveLogPrice($params, $options = []) {
        try
        {
            if(!isset($params[0]))
                return false;

            if(!is_array($params[0]) || !array_filter($params))
                return false;

            $count = 0;
            foreach ($params as $key => $value) {
                $inputs               = [];
                $em                   = $value['em'];
                $inputs['tableName']  = $value['tableName'];
                $inputs['fieldName']  = $value['fieldName'];
                $inputs['entityId']   = $value['entityId'];
                $inputs['oldPrice']   = $value['oldPrice'];
                $inputs['newPrice']   = $value['newPrice'];
                $inputs['createdBy']  = $value['createdBy'];
                $inputs['effectedOn'] = isset($value['effectedOn']) ? $value['effectedOn'] : null;

                $item = $em->getRepository('UtilBundle:AuditTrailPrice')->getLastItem();
                if($item)
                {
                    if(
                        $item->getTableName() == $inputs['tableName'] && $item->getFieldName() == $inputs['fieldName'] &&
                        $item->getEntityId() == $inputs['entityId'] && $item->getOldPrice() == $inputs['oldPrice'] &&
                        $item->getNewPrice() == $inputs['newPrice'] && $item->getEffectedOn() == $inputs['effectedOn']
                    )
                    {
                        continue;
                    }
                }

                $em->getRepository('UtilBundle:AuditTrailPrice')->insert($inputs);
                $count += 1;
            }
            return $count;
        }
        catch(\Exception $e)
        {
            return false;
        }
    }

    public static function generatePassword()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $specials = "{}[]|\<>/!@#$%^&*()-_=+~";
        return substr(str_shuffle($chars), 0, 6) . substr(str_shuffle($specials), 0, 2);
    }

    /**
    * format taxId
    * @param string $taxId
    * @return string
    **/
    public static function formatTaxId($taxId)
    {
        try
        {
            if(strLen($taxId) != 15)
                return $taxId;

            $taxId = substr_replace($taxId, '.', 2, 0);
            $taxId = substr_replace($taxId, '.', 6, 0);
            $taxId = substr_replace($taxId, '.', 10, 0);
            $taxId = substr_replace($taxId, '-', 12, 0);
            $taxId = substr_replace($taxId, '.', 16, 0);
            return $taxId;
        }
        catch(\Exception $e)
        {
            throw new \Exception($e->getMessage());
        }
    }

    public static function filterRoleMpa($rolesIndex){
        $roles = array();
        $roles['patient_index'] = array('patient_index', 'patient_ajax_get_list','patient_list_rx_history','view_rx','patient_ajax_list_rx_history','edit_rx');
        $roles['patient_new'] = array("patient_new", "patient_edit", "patient_delete");
        $roles['list_rx'] = array('list_rx', 'ajax_get_list_rx');
        $roles['list_draft_rx'] = array('list_draft_rx','ajax_get_list_rx');
        $roles['list_pending_rx'] = array('list_pending_rx','ajax_get_list_rx');
        $roles['list_confirmed_rx'] = array('list_confirmed_rx','ajax_get_list_rx','view_rx');
        $roles['list_recalled_rx'] = array('list_recalled_rx', 'ajax_get_list_rx');
        $roles['list_failed_rx'] = array('list_failed_rx','ajax_get_list_rx');
        $roles['list_reported_rx'] = array('list_reported_rx','ajax_get_list_rx');
        $roles['list_scheduled_rx'] = array('list_scheduled_rx', 'ajax_get_list_rx');
        $roles['create_rx'] = array('create_rx', 'review_rx','edit_rx', 'confirm_rx','update_rx',
            'index_rx','ajax_get_list_patient',
            'ajax_get_rx_drug', 'patient_ajax_get_info_note_list',
            'check_edit_rx_session','ajax_get_favorites',
            'ajax_handle_favorite','ajax_get_top30',
            'ajax_get_drug', 'ajax_get_step2_content',
            'ajax_save_as_draft','ajax_save_update_rx',
            'delete_rx','ajax_get_check_stock',
            'doctor_rx_forward_rx_to_doctor', 'ajax_get_activities_log','doctor_rx_activities_print_logs'
        );
        $roles['send_to_patient'] = array('send_to_patient','doctor_rx_forward_rx_to_doctor');
        $roles['doctor_report_transaction_history'] = array('doctor_report_transaction_history', 'doctor_rx_report_auto_suggest_ajax', 'doctor_report_transaction_history_ajax','doctor_report_transaction_history_csv', 'doctor_rx_transaction_history_detail' );
        $roles['doctor_report_monthly_statement'] = array('doctor_report_monthly_statement','doctor_report_monthly_statement_ajax','doctor_report_monthly_statement_pdf','doctor_report_download_invoice');
        $roles['doctor_custom_selling_prices'] = array(
            'doctor_custom_selling_prices', 
            'doctor_custom_selling_prices_list',
            'doctor_custom_selling_prices_update_price',
            'doctor_custom_selling_prices_list_logs',
            'doctor_custom_selling_prices_logs',
            'doctor_custom_selling_prices_download_excel',
            'doctor_custom_selling_prices_upload_favorite_drugs'
        );

        $updateRole = ['doctor_dashboard', 'ajax_list_rx','ajax_closed_messages','patient_ajax_get_info','patient_ajax_get_info', 'patient_ajax_get_info_note_list', 'mpa_index','doctor_user_guide', 'doctor_medicine_list', 'doctor_ajax_medicine_list'];
        foreach ($rolesIndex as $item){
            $updateRole = array_merge($updateRole, $roles[$item]);
        }
        return $updateRole;
    }
}
