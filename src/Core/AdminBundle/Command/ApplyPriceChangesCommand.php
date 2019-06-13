<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Constant;
use UtilBundle\Entity\AuditTrailPrice;

class ApplyPriceChangesCommand extends ContainerAwareCommand
{
    /**
     * Apply price changes
     * @author tuan.nguyen
     */
    protected function configure()
    {
        $this->setName('app:apply-price-changes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine")->getManager();

        $output->writeln('Apply Price Changes');

        $date = date('Y-m-d') . " 23:59:59";
        $params = array(
            'status' => Constant::DRUG_AUDIT_APPROVED,
            'take_effect_on' => new \DateTime($date)
        );

        $list = $em->getRepository('UtilBundle:DrugAudit')->listDrugAudit($params);
        if ($list) {
            $local = 0;
            $overseas = 0;
            $settingsList = $em->getRepository('UtilBundle:PlatformSettings')->findAll();
            foreach ($settingsList as $settingsItem) {
                $local = $settingsItem->getLocal();
                $overseas = $settingsItem->getOverseas();
                break;
            }

            $output->writeln('There are ' . count($list) . " price changes");
            foreach ($list as $item) {
                $priceType = $item->getPriceType();

                $method = 'set';
                $smethod = 'set';
                $gmethod = 'set';
                if ($priceType == 'list_price_domestic') {
                    $method .= 'ListPriceDomestic';
                    $smethod .= 'Local';
                    $gmethod .= 'LocalPricePercentage';
                } elseif ($priceType == 'list_price_international') {
                    $method .= 'ListPriceInternational';
                    $smethod .= 'Overseas';
                    $gmethod .= 'OverseasPricePercentage';
                } elseif ($priceType == 'cost_price') {
                    $method .= 'CostPrice';
                }

                $pharmacy = $item->getPharmacy();
                $drug = $item->getDrug();

                if ($pharmacy && $item->getDrugGroup()) {
                    $settings = null;
                    $settingsList = $em->getRepository('UtilBundle:PlatformSettings')->findAll();
                    foreach ($settingsList as $settingsItem) {
                        $settings = $settingsItem;
                        break;
                    }

                    $drugs = $em->getRepository('UtilBundle:Drug')->findBy(array(
                        'pharmacy' => $pharmacy
                    ));
                    $em->beginTransaction();
                    try {
                        $percent = $item->getNewCostPrice();
                        foreach ($drugs as $drugItem) {
                            $get = str_replace('set', 'get', $method);
                            $oldPrice = $drugItem->$get();

                            $costPrice = $drugItem->getCostPrice();
                            $drugItem->$method(round($costPrice + ($percent * $costPrice / 100), 2));
                            $em->persist($drugItem);

                            $log = $this->getLog($drugItem, $method, $oldPrice, $drugItem->$get());
                            $em->persist($log);

                            $em->flush();
                        }

                        if($gmethod != 'set') {
                            $item->getDrugGroup()->$gmethod($percent);
                        }

                        $item->setStatus(Constant::DRUG_AUDIT_APPLIED);
                        $item->setUpdatedOn(new \DateTime());
                        $em->persist($item);
                        $em->flush();

                        // if ($settings) {
                        //    $settings->$smethod($percent);
                        //    $em->persist($settings);
                        //    $em->flush();
                        // }

                        $em->commit();
                    } catch (\Exception $ex) {
                        $em->rollback();
                    }
                } elseif ($drug) {
                    $em->beginTransaction();
                    try {
                        $get = str_replace('set', 'get', $method);
                        $oldPrice = $drug->$get();

                        //get  new value from drugGroup
                        if($drug->getGroup()) {
                            $local = $drug->getGroup()->getLocalPricePercentage();
                            $overseas = $drug->getGroup()->getOverseasPricePercentage();
                        }

                        $drug->$method($item->getNewCostPrice());
                        if ($method == 'setCostPrice') {
                            $oldListPriceDomestic = $drug->getListPriceDomestic();
                            $oldListPriceInternational = $drug->getListPriceInternational();

                            $costPrice = $item->getNewCostPrice();
                            $listPriceDomestic = $costPrice + ($costPrice * $local / 100);
                            $listPriceInternational = $costPrice + ($costPrice * $overseas / 100);
                            $drug->setListPriceDomestic($listPriceDomestic);
                            $drug->setListPriceInternational($listPriceInternational);

                            $log = $this->getLog($drug, 'setListPriceDomestic', $oldListPriceDomestic,  $drug->getListPriceDomestic());
                            $em->persist($log);
                            $em->flush();

                            $log = $this->getLog($drug, 'setListPriceInternational', $oldListPriceInternational,  $drug->getListPriceInternational());
                            $em->persist($log);
                            $em->flush();
                        }

                        $em->persist($drug);
                        $em->flush();

                        $log = $this->getLog($drug, $method, $oldPrice, $drug->$get());
                        $em->persist($log);
                        $em->flush();

                        $item->setStatus(Constant::DRUG_AUDIT_APPLIED);
                        $item->setUpdatedOn(new \DateTime());
                        $em->persist($item);
                        $em->flush();

                        $em->commit();
                    } catch (\Exception $ex) {
                        $em->rollback();
                    }
                }
            }
        }

        // DoctorDrug
        $list = $em->getRepository('UtilBundle:DrugAudit')->listDoctorDrug($params);
        foreach ($list as $value) {
            $newValue = $value->getListPriceDomesticNew();
            if ($newValue) {
                $value->setListPriceDomestic($newValue);
            }

            $newValue = $value->getListPriceInternationalNew();
            if ($newValue) {
                $value->setListPriceInternational($newValue);
            }

            $value->setUpdatedOn(new \DateTime());
            $em->persist($value);
        }

        $em->flush();
    }

    private function getLog($drug, $method, $oldPrice, $newPrice)
    {
        $log = new AuditTrailPrice();
        $log->setTableName('Drug');
        $fieldName = 'cost_price';
        if ($method == 'setListPriceDomestic') {
            $fieldName = 'list_price_domestic';
        } elseif ($method == 'setListPriceInternational') {
            $fieldName = 'list_price_international';
        }
        $log->setFieldName($fieldName);
        $log->setEntityId($drug->getId());
        $log->setOldPrice($oldPrice);
        $log->setNewPrice($newPrice);
        $log->setEffectedOn(new \DateTime());
        $log->setCreatedOn(new \DateTime());

        return $log;
    }
}
