<?php

namespace DoctorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Constant;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use UtilBundle\Utility\MonthlyPdfHelper;
use Dompdf\Dompdf;

class TaxInvoiceCommand extends ContainerAwareCommand
{
    /**
     * create montly statment pdf file
     * doctor:montly-tax-invoice:export-pdf [Y-m-d] [doctor_id] [is_tax_invoice]
     * @author thu.tranq
     */
    protected function configure()
    {
        $this->setName('doctor:montly-tax-invoice:export-pdf')
            ->addArgument('day', InputArgument::OPTIONAL, 'Focus on the time')
            ->addArgument('doctor_id', InputArgument::OPTIONAL, 'Doctor Id')
            ->addArgument('is_tax_invoice', InputArgument::OPTIONAL, 'Type of invoice');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $day = $input->getArgument('day');
            $doctorId = $input->getArgument('doctor_id');
            $date = new \DateTime();

            $preMonth = empty($day)? new \DateTime('-1 month'): new \DateTime($day);

            $em = $this->getContainer()->get("doctrine")->getManager();

            $platformSettingObj = $em->getRepository('UtilBundle:PlatformSettings')->getPaymentSchedule();
            if ($date->format('d') != $platformSettingObj['doctorStatementDate'] && !$day) {
                $output->writeln($date->format("Y-m-d H:i:s") . ': No any pdf file created!');
                return;
            }

            $params = array(
                'month' => (int)$preMonth->format('m'), 
                'year' => (int)$preMonth->format('Y')
            );
            $dMS = $em->getRepository('UtilBundle:DoctorMonthlyStatement')->findOneBy($params);
            $doctorResults = $em->getRepository('UtilBundle:Doctor')->getPrimaryDoctor($doctorId);

            if (empty($doctorResults)) {
                $output->writeln($date->format("Y-m-d H:i:s") . ': No any pdf file created!');
                return;
            }

            $fromCommand = true;
            $isTaxInvoice = $input->getArgument('is_tax_invoice');
            if (is_null($isTaxInvoice)) {
                $isTaxInvoice = $em->getRepository('UtilBundle:PlatformSettings')->hasGST();
            } else {
                $fromCommand = false;
            }
            $params['isTaxInvoice'] = $isTaxInvoice;
            foreach ($doctorResults as  $doctor) {
                $params['doctorId'] = $doctor['id'];
                $results = $em->getRepository('UtilBundle:Rx')->getRxsByMonth($params);
                if (!empty($results)) {
                    $template   = 'DoctorBundle:rx\reports\pdf:tax_invoice.html.twig';
                    $doctorInfo = $em->getRepository('UtilBundle:Doctor')->getDoctorInfoForMontlyStatementPdf($params);
                    
                    $doctorInfo['doctorCountry'] = $em->getRepository('UtilBundle:Country')->getPlatformSettingCountryName();
                    $doctorInfo['invoiceDate'] = '';

                    $paymentInfo = MonthlyPdfHelper::getPaymentInfo($em, $params);
                    $paymentInfo['amount'] = $results['totalAmount'];
                    $params['statementDateNumber'] = $paymentInfo['statementDateNumber'];
                    $balanceInfo = MonthlyPdfHelper::getBalanceInfo($em, $params);

                    //update invoiceNumber
                    $dMSL = $em->getRepository('UtilBundle:DoctorMonthlyStatementLine')->findOneBy(array(
                        'doctorMonthlyStatement' => $dMS,
                        'doctor' => $doctor['id']
                    ));
                    if($dMSL) {
                        $doctorInfo['invoiceDate'] = $dMS->getStatementDate();
                        $invoiceNumber = !empty($doctorInfo) ? $doctorInfo['gmedesTaxInvoice'] : "";
                        $dMSL->setInvoiceNumber($invoiceNumber);
                        $em->persist($dMSL);
                        $em->flush();
                    }

                    // STRIKE 958
                    $runningNumber = $em->getRepository('UtilBundle:RunningNumber')->findOneBy(array('runningNumberCode' => Constant::INVOICE_NUMBER_CODE));
                    $runningNumberValue = $runningNumber->getRunningNumberValue();
                    $runningNumber->setRunningNumberValue(++$runningNumberValue);
                    $em->persist($runningNumber);
                    $em->flush();
                    // End

                    $html = $this->getContainer()->get('templating')->render($template,
                            array(
                               'data' => $results, 
                               'fromCommand' => $fromCommand,
                               'doctor' => $doctorInfo, 
                               'paymentInfo' => $paymentInfo,
                               'balanceInfo' => $balanceInfo,
                               'hasTax' => $params['isTaxInvoice']
                            ));

                    $baseName = '_invoice_';
                    if ($params['isTaxInvoice']) {
                        $baseName = '_tax_invoice_';
                    }
                    $fileName = $doctor['doctorCode'] . $baseName . $preMonth->format('mY') . '.pdf';

                    $fs = new Filesystem();
                    $rootDir = $this->getContainer()->get('kernel')->getRootDir();
                    $rootDir = str_replace("app","", $rootDir);

                    $parammeters = $this->getContainer()->getParameter('media');
                    $path = $rootDir . $parammeters['doctor_report_path'];

                    if (!$fs->exists($path)) {
                        $fs->mkdir($path);
                    }

                    //clone a html
                    $pathHtml = $rootDir . $parammeters['doctor_report_path'].'/html';
                    $fileNameHtml = str_replace('.pdf', '.html', $fileName);
                    $contentHtml = str_replace("web/bundles", "bundles", $html);
                    if (!$fs->exists($pathHtml)) {
                        $fs->mkdir($pathHtml);
                    }
                    file_put_contents($pathHtml . "/{$fileNameHtml}", $contentHtml);

                    $dompdf = new Dompdf(array('isPhpEnabled' => true));
                    
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();

                    $GLOBALS['isTaxInvoice']  =  $isTaxInvoice;

                    $canvas = $dompdf->get_canvas();
                    $canvas->page_script('
                        $font = $fontMetrics->getFont("sans-serif");
                        if ($PAGE_NUM == 1) {
                            $y = 200;
                            if ($GLOBALS["isTaxInvoice"]) {
                                $y = 225;
                            }
                        }
                        $pdf->text(530, 800, "Page {$PAGE_NUM} of {$PAGE_COUNT}", $font, 8);
                    ');

                    if (file_put_contents($path . "/{$fileName}", $dompdf->output())) {

                        $output->writeln("Create $fileName for doctor id: {$doctor['id']} successfully\n\n");

                        // update file name into doctor_monthly_statement_line
                        $dMSL = $em->getRepository('UtilBundle:DoctorMonthlyStatementLine')->getStatementLine($doctor['id'], $params['month'], $params['year']);
                        if (isset($dMSL)) {
                            $dMSL->setInvoiceFilename($fileName);
                            $em->persist($dMSL);
                            $em->flush();
                        }
                    }

                }
            }
        } catch (Exception $e) {
            $output->writeln($e);
        }

    }
}

