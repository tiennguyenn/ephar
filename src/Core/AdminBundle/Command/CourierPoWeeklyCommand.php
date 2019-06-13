<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Utils;

class CourierPoWeeklyCommand extends ContainerAwareCommand
{
    /**
     * Courier Po Weekly
     * app:courier-po-weekly [Y-m-d]
     * @author vinh.nguyen
     */
    protected function configure()
    {
        $this->setName('app:courier-po-weekly')
             ->addArgument('day', InputArgument::OPTIONAL, 'Focus on the time');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime('now');
        $day = $input->getArgument('day');

        //get platform settings
        $em = $this->getContainer()->get('doctrine')->getManager();
        $psObj = $em->getRepository('UtilBundle:PlatformSettings')->getPaymentSchedule();

        $wPoDay = (int)$psObj['deliveryFortnightlyPoDay'];
        $wPoTime = (int)$psObj['deliveryFortnightlyPoTime'];
        $targetDate = $psObj['deliveryTargetDate'];
        $ymdTargetDate = !empty($targetDate)? $targetDate->format('Y-m-d'): "";
        $wNowTime = (int)$now->format('Hi');
        $ymdNow = $now->format('Y-m-d');

        $lastDayOfWeek  = clone $now;
        $lastDayOfWeek->modify('Friday this week');

        //get holiday list
        $publicHoliday = $em->getRepository('UtilBundle:PublicHoliday')->listPHDates();
        $listPHDate = array();
        foreach ($publicHoliday as $value) {
            $listPHDate[] = $value['publicDate'];
        }

        $workingDate = Utils::getWorkingDateByWeek($listPHDate, $wPoDay);

        //set target date
        if((int)$now->format('w') == $wPoDay && $workingDate->format('Y-m-d') > $lastDayOfWeek->format('Y-m-d')) {
            $ps = $em->getRepository('UtilBundle:PlatformSettings')->find($psObj['id']);
            $ps->setDeliveryTargetDate($workingDate);
            $em->persist($ps);
            $em->flush();
        }

        if(($ymdNow == $workingDate->format('Y-m-d') && $wNowTime == $wPoTime)
            || ($ymdNow == $ymdTargetDate && $wNowTime == $wPoTime)
            || $day) {
            
            $output->writeln('============= Start ===========');
            $output->writeln($now->format("Y-m-d H:i:s").': Courier Purchase Order Weekly');

            //set target date, null
            if(empty($day)) {
                $ps = $em->getRepository('UtilBundle:PlatformSettings')->find($psObj['id']);
                $ps->setDeliveryTargetDate(null);
                $em->persist($ps);
                $em->flush();
            } 

            //$targetDate for PO
            if($ymdNow == $workingDate->format('Y-m-d'))
                $targetDate = null;

            $result = $this->getContainer()->get('microservices.po')->courierPoWeekly($day, $targetDate);

            if(!empty($result)) {
                foreach ($result as $k => $v) {
                    if($k == 'dates') {
                        $output->writeln(" - cycle: " . $v['cycle']);
                        $output->writeln(" - cycle_date: " . $v['start']->format("Y-m-d")." -> ".$v['end']->format("Y-m-d"));
                    } else {
                        $output->writeln(" - $k: $v");
                    }
                }
            } else {
                $output->writeln("No data created.");
            }

            $output->writeln('============= End ===========');
        } else {
            $output->writeln($now->format("Y-m-d H:i:s").': Courier Purchase Order Weekly');
            $output->writeln('NOTE: Skip now, It will be running by following on the schedule of time.');
        }
    }
}