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

class PharmacyPoDailyCommand extends ContainerAwareCommand
{
    /**
     * Pharmacy Po Daily
     * app:pharmacy-po-daily [Y-m-d]
     * @author vinh.nguyen
     */
    protected function configure()
    {
        $this->setName('app:pharmacy-po-daily')
             ->addArgument('day', InputArgument::OPTIONAL, 'Focus on the day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //import data from csv file
        //$this->import();

        $output->writeln('============= Start ===========');
        $date = new \DateTime();
        $output->writeln($date->format("Y-m-d H:i:s").': Pharmacy Purchase Order Daily');
        
        $day = $input->getArgument('day');

        $result = $this->getContainer()->get('microservices.po')->pharmacyPoDaily($day);
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
     * file dir: uploads/pharmacy_po_daily.csv
     */
    private function import()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $ps = $em->getRepository('UtilBundle:PlatformSettings')->getGstRate();
        $psGstRate = $ps['gstRate'] / 100;

        $pharmacyObj = $em->getRepository('UtilBundle:Pharmacy')->getUsedPharmacy();

        $items = $this->getDataCSV();

        foreach($items as $item) {
            $poDate = $item['poDate'];
            $poRunningNumber = $em->getRepository('UtilBundle:PharmacyPoDaily')->getPORunningNumber($poDate);
            $poNumber = Common::generatePONumber(array(
                'type' => 'pharmacy',
                'poDate' => $poDate,
                'courierRateCode' => '',
                'prefix' => '',
                'poRunningNumber' => $poRunningNumber
            ));

            //update info
            $item['pharmacy'] = $pharmacyObj;
            $item['cycle'] = $poDate->format("Y").".".$poDate->format("W");
            $item['poNumber'] = $poNumber;
            $item['customerReference'] = str_replace('/', '', $poNumber);

            //daily create
            $em->getRepository('UtilBundle:PharmacyPoDaily')->create($item);
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
        $fileName = "pharmacy_po_daily.csv";

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
                    'pharmacy'         => null,
                    'poDate'           => new \DateTime($item[1]),
                    'cycle'            => null,
                    'poNumber'         => null,
                    'totalAmount'      => $item[4],
                    'excludeGstAmount' => $item[5],
                    'includeGstAmount' => $item[6],
                    'gstAmount'        => $item[7],
                    'customerReference' => null
                );

            }
            fclose($handle);
            asort($rows);
        }

        return $rows;
    }
}