<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Utils;
use UtilBundle\Utility\MonthlyPdfHelper;
use UtilBundle\Utility\Constant;
use Symfony\Component\Console\Input\ArrayInput;

class MakeDoctorMonthlyPDFCommand extends ContainerAwareCommand
{
    /**
     * Generate data for monthly statment by specific date
     * if date agrument not passed, will generate data for the last month of current month
     * usage: php app/console doctor:monthly-statement:generate-data day[Y-m-d]
     * @author thu.tranq
     */
    protected function configure()
    {
        $this->setName('doctor:monthly-statement:make-pdf')
             ->addArgument('date', InputArgument::OPTIONAL, 'Making pdf files base on date')
             ->setDescription('Make pdfs file for doctors');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateTime = new \DateTime('now');
        $day = $input->getArgument('date');

        $em = $this->getContainer()->get('doctrine')->getManager();

        // $output->writeln($dateTime->format("Y-m-d H:i:s").': Start generating data for monthly statement');
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}-------------------GENERATE MONTHLY DATA----------------");

        $preMonth = empty($day)? new \DateTime('-1 month'): new \DateTime($day);
        $statementDate = MonthlyPdfHelper::getStatementDate($em, array('year' => $preMonth->format('Y'), 'month' => $preMonth->format('m')));
        
        $params = array(
            'month'         => $preMonth->format('m'), 
            'year'          => $preMonth->format('Y'),
            'statementDate' => $statementDate,
            'output'        => $output
        );
        $em->getRepository('UtilBundle:DoctorMonthlyStatement')->createDoctorMS($params);

        $dateTime = new \DateTime('now');
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}: Generating data ended.\n\n");



        // generate montly statement pdf
        $dateTime = new \DateTime('now');
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}-------------------GENERATE MONTHLY STATEMENT PDF----------------");
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')} Start making monthly pdf files");

        $command = $this->getApplication()->find('doctor:montly-statement:export-pdf');
        $arguments = array(
            'day' => $day
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        $dateTime = new \DateTime('now');
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}-------------------END MONTHLY STATEMENT PDF----------------");

        // generate invoice
        $output->writeln("\n");
        $dateTime = new \DateTime('now');
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}-------------------GENERATE INVOICE PDF----------------");
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')} Start making invoice pdf files");
        $doctors = $em->getRepository('UtilBundle:Doctor')->getDoctorForStatement(array());
        if(!empty($doctors)) {
            $isTaxInvoice = $em->getRepository('UtilBundle:PlatformSettings')->hasGST();

            // STRIKE 958
            $runningNumber = $em->getRepository('UtilBundle:RunningNumber')->findOneBy(array('runningNumberCode' => Constant::INVOICE_NUMBER_CODE));
            $runningNumber->setRunningNumberValue(1);
            $em->persist($runningNumber);
            $runningNumber = $em->getRepository('UtilBundle:RunningNumber')->findOneBy(array('runningNumberCode' => Constant::CREDIT_NOTE_CODE));
            $runningNumber->setRunningNumberValue(1);
            $em->persist($runningNumber);
            $em->flush();
            // End

            foreach($doctors as $doctor) {
                $command = $this->getApplication()->find('doctor:montly-tax-invoice:export-pdf');
                $arguments = array(
                    'day' => $day,
                    'doctor_id' => $doctor['id'],
                    'is_tax_invoice' => $isTaxInvoice
                );
                $input = new ArrayInput($arguments);
                $returnCode = $command->run($input, $output);
            }
        }
        $dateTime = new \DateTime('now');
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}-------------------END INVOICE PDF----------------");
    }
}