<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;
use Symfony\Component\Finder\Finder;

class CourierPoDailyCommand extends ContainerAwareCommand
{
    /**
     * Courier Po Daily
     * app:courier-po-daily [Y-m-d]
     * @author vinh.nguyen
     */
    protected function configure()
    {
        $this->setName('app:courier-po-daily')
             ->addArgument('day', InputArgument::OPTIONAL, 'Focus on the day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //import data from csv file
        //$this->import();

        $date = new \DateTime();
        $output->writeln('============= Start ===========');
        $output->writeln($date->format("Y-m-d H:i:s").': Courier Purchase Order Daily');
        
        $day = $input->getArgument('day');

        $result = $this->getContainer()->get('microservices.po')->courierPoDaily($day);
        if($result) {
            $output->writeln('More information:');
            foreach($result as $k=>$v) {
                $output->writeln($k.": ".$v);
            }
        }

        $output->writeln('============= End ===========');
    }

    /**
     * import data
     * file dir: uploads/courier_po_daily.csv
     */
    private function import()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $ps = $em->getRepository('UtilBundle:PlatformSettings')->getGstRate();
        $psGstRate = $ps['gstRate'] / 100;


        $courierObj = $em->getRepository('UtilBundle:Courier')->getUsedCourer();
        $courierInfo = $em->getRepository('UtilBundle:Courier')->getCourier($courierObj->getId());

        $items = $this->getDataCSV();

        foreach($items as $item) {
            $poDate = $item['poDate'];
            $poRunningNumber = $em->getRepository('UtilBundle:CourierPoDaily')->getPORunningNumber($poDate);
            $poNumber = Common::generatePONumber(array(
                'type' => 'pharmacy',
                'poDate' => $poDate,
                'courierRateCode' => '',
                'prefix' => '',
                'poRunningNumber' => $poRunningNumber
            ));

            if($item['courierRate'] != null)
                $courierRateObj = $em->getRepository('UtilBundle:CourierRate')->find($item['courierRate']);
            else
                $courierRateObj = null;

            //update info
            $item['courierRate'] = $courierRateObj;
            $item['cycle'] = $poDate->format("Y").".".$poDate->format("W");
            $item['poNumber'] = $poNumber;

            $customerReference = str_replace('/DP', '', $poNumber);
            $customerReference = str_replace('/', '', $customerReference);
            $item['customerReference'] = $customerReference;

            //daily create
            $em->getRepository('UtilBundle:CourierPoDaily')->create($item);
        }

        echo "Total record has been imported: ".count($items);
        die;
    }

    /**
     * get data from csv
     **/
    private function getDataCSV()
    {
        $ignoreFirstLine = true;
        $fileIn = $this->getContainer()->get('kernel')->getRootDir()."/../web/uploads/";
        $fileName = "courier_po_daily.csv";

        $finder = new Finder();
        $finder->files()
            ->in($fileIn)
            ->name($fileName);

        foreach ($finder as $file) { $csv = $file; }

        $rows = array();
        if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, ";")) !== FALSE) {
                $i++;
                if ($ignoreFirstLine && $i == 1) { continue; }

                $item = explode(',', $data[0]);

                $rows[] = array(
                    'courierRate'      => $item[0],
                    'poDate'           => new \DateTime($item[2]),
                    'cycle'            => null,
                    'poNumber'         => null,
                    'excludeGSTAmount' => $item[5],
                    'gstAmount'        => $item[6],
                    'amount'           => $item[7],
                    'courierName'      => $item[8],
                    'courierEmail'     => $item[9],
                    'isGst'            => $item[10],
                    'postCode'         => $item[11],
                    'customerReference' => null,
                );
            }
            fclose($handle);
            asort($rows);
        }

        return $rows;
    }
}