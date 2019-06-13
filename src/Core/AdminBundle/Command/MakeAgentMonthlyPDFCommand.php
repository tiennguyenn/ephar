<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Utils;
use UtilBundle\Utility\MonthlyPdfHelper;
use Symfony\Component\Console\Input\ArrayInput;

class MakeAgentMonthlyPDFCommand extends ContainerAwareCommand
{
    /**
     * Generate data for monthly statment by specific date
     * if date agrument not passed, will generate data for the last month of current month
     * agent:monthly-statement:generate-data day[Y-m-d]
     * @author thu.tranq
     */
    protected function configure()
    {
        $this->setName('agent:monthly-statement:make-pdf')
             ->addArgument('date', InputArgument::OPTIONAL, 'Make pdfs file base on date')
             ->setDescription('Make pdfs file base on date for agents');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateTime = new \DateTime('now');
        $day = $input->getArgument('date');

        $em          = $this->getContainer()->get('doctrine')->getManager();
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}-------------------GENERATE MONTHLY DATA----------------");

        $preMonth = empty($day)? new \DateTime('-1 month'): new \DateTime($day);
        $statementDate = MonthlyPdfHelper::getStatementDate($em, array('year' => $preMonth->format('Y'), 'month' => $preMonth->format('m')));
        $params = array(
            'month'         => $preMonth->format('m'), 
            'year'          => $preMonth->format('Y'),
            'statementDate' => $statementDate,
            'output'        => $output
        );
        $em->getRepository('UtilBundle:AgentMonthlyStatement')->createAgentMS($params);

        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}: Generating data ended.");

        $dateTime = new \DateTime('now');
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}-------------------END MONTHLY DATA----------------");

        // generate montly statement pdf
        $dateTime = new \DateTime('now');
        $output->writeln("\n");
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}-------------------GENERATE MONTHLY STATEMENT PDF----------------");

        $output->writeln("{$dateTime->format('Y-m-d H:i:s')} Start making monthly pdf files");
        $command = $this->getApplication()->find('agent:montly-statement:export-pdf');

        $arguments = array(
            'day' => $day
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
        $dateTime = new \DateTime('now');
        $output->writeln("{$dateTime->format('Y-m-d H:i:s')}-------------------END MONTHLY STATEMENT PDF----------------");
    }
}