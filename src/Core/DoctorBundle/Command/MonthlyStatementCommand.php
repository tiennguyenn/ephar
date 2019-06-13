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

class MonthlyStatementCommand extends ContainerAwareCommand
{
    /**
     * create montly statment pdf file
     * doctor:montly-statement:export-pdf [Y-m-d]
     * @author thu.tranq
     */
    protected function configure()
    {
        $this->setName('doctor:montly-statement:export-pdf')
            ->addArgument('day', InputArgument::OPTIONAL, 'Focus on the time');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $date = new \DateTime();
            $day = $input->getArgument('day');
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

            $doctorResults = $em->getRepository('UtilBundle:Doctor')->getPrimaryDoctor();

            if (empty($doctorResults)) {
                $output->writeln($date->format("Y-m-d H:i:s") . ': No any pdf file created!');
                return;
            }
            foreach ($doctorResults as  $doctor) {
                $params['doctorId'] = $doctor['id'];
                $results = $em->getRepository('UtilBundle:Rx')->getRxsByMonth($params);
                if (!empty($results)) {
                    $template   = 'DoctorBundle:rx\reports\pdf:monthly_statement.html.twig';
                    
                    // add check platform is_gst
                    $checkBankGst = !$em->getRepository('UtilBundle:PlatformSettings')->hasGST();
                    $doctorInfo = $em->getRepository('UtilBundle:Doctor')->getDoctorInfoForMontlyStatementPdf($params);
                    
                    $doctorInfo['doctorCountry'] = $em->getRepository('UtilBundle:Country')->getPlatformSettingCountryName();
                    $paymentInfo = MonthlyPdfHelper::getPaymentInfo($em, $params);
                    $params['statementDateNumber'] = $paymentInfo['statementDateNumber'];
                    $balanceInfo = MonthlyPdfHelper::getBalanceInfo($em, $params);
                    $params['currencyCode'] = isset($results['currencyCode']) ? $results['currencyCode'] : null;

                    // STRIKE-912
                    $monthYear = $preMonth->format('my');
                    $serialNumber = $doctorInfo['serialNumber'] + 1;
                    $serialNumberFormat = sprintf("%'.04d", $serialNumber);
                    $pieces = array($doctorInfo['doctorCode'], $monthYear, $serialNumberFormat);
                    $statementNo = implode('-', $pieces);
                    $em->getRepository('UtilBundle:Doctor')
                        ->updateSerialNumber($doctorInfo['snId'], $serialNumber);

                    $html = $this->getContainer()->get('templating')->render($template, 
                        array(
                           'data' => $results, 
                           'fromCommand' => true,
                           'doctor' => $doctorInfo, 
                           'paymentInfo' => $paymentInfo,
                           'balanceInfo' => $balanceInfo,
                           'isGst' => $checkBankGst,
                           'params' => $params,
                           'statementNo' => $statementNo
                        )
                    );

                    $amount = $this->getContainer()->get('session')->get('totalAmount');
                    $html = str_replace('[AMOUNT]', $amount, $html);

                    $fileName = $doctor['doctorCode'] . '_doctor_statements_' . $preMonth->format('mY') . '.pdf';

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

                    $dompdf = new Dompdf(array('isRemoteEnabled'=> true, 'isPhpEnabled' => true));
        
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();

                    $canvas = $dompdf->get_canvas();
                    $canvas->page_script('
                        $font = $fontMetrics->getFont("sans-serif");
                            $pdf->text(535, 800, "Page {$PAGE_NUM} of {$PAGE_COUNT}", $font, 8);
                    ');

                    if (file_put_contents($path . "/{$fileName}", $dompdf->output())) {
                        $flag = true;
                        $output->writeln("Create $fileName for doctor id: {$doctor['id']} successfully \n");
                        // update file name into doctor_monthly_statement_line
                        $dMSL = $em->getRepository('UtilBundle:DoctorMonthlyStatementLine')->getStatementLine($doctor['id'], $params['month'], $params['year']);
                        if (isset($dMSL)) {
                            $dMSL->setFileName($fileName);
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

