<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Utils;

class StatementToDoctorCommand extends ContainerAwareCommand
{
    /**
     * Send statement to doctors
     * php app/console app:statement-to-doctor [Y-m-d]
     * related to cronjob:
     * - doctor:montly-statement:export-pdf [Y-m-d]
     * - doctor:montly-tax-invoice:export-pdf [Y-m-d] [doctor_id] [is_tax_invoice]
     * @author vinh.nguyen
     */
    protected function configure()
    {
        $this->setName('app:statement-to-doctor')
             ->addArgument('day', InputArgument::OPTIONAL, 'Focus on the time');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateTime = new \DateTime('now');
        $day = $input->getArgument('day');

        //get platform settings
        $em = $this->getContainer()->get('doctrine')->getManager();
        $psObj = $em->getRepository('UtilBundle:PlatformSettings')->getPaymentSchedule();

        //get holiday list
        $publicHoliday = $em->getRepository('UtilBundle:PublicHoliday')->listPHDates();
        $listPHDate = array();
        foreach ($publicHoliday as $value) {
            $listPHDate[] = $value['publicDate'];
        }

        $workingDate = Utils::getWorkingDate($listPHDate, $psObj["doctorStatementDate"]);

        if($dateTime->format('d') == $workingDate->format('d') || $day) {
            $output->writeln('============= Start ===========');
            $output->writeln($dateTime->format("Y-m-d H:i:s").': Statement To Doctor');

            $result = $this->getContainer()->get('microservices.po')->sendStatementToDoctor($day);
            if($result) {
                $output->writeln("Total: ". count($result));
                foreach ($result as $k => $v) {
                    $output->writeln("Statement sent to doctor ".$v . ".");
                }
            } else {
                $output->writeln('No data found.');
            }

            $output->writeln('============= End ===========');
        } else {
            $output->writeln($dateTime->format("Y-m-d H:i:s").': Statement To Doctor');
            $output->writeln('NOTE: Skip now, It will be running by following on the schedule of time.');
        }
    }
}