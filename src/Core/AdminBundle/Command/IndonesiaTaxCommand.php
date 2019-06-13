<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Validator\Constraints\DateTime;

class IndonesiaTaxCommand extends ContainerAwareCommand {

    /**
     * update indonesia tax
     * php app/console app:indonesia-tax [Y-m-d]
     * @author vinh.nguyen
     */
    protected function configure() {
        $this->setName('app:indonesia-tax')
            ->addArgument('day', InputArgument::OPTIONAL, 'Focus on the time');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = new \DateTime();
        $output->writeln($now->format("Y-m-d H:i:s").': Start updating Indonesia Import Tax');

        $day = $input->getArgument('day');
        $date = new \DateTime( !empty($day)? $day: date("Y-m-d") );
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $indTaxList = $em->getRepository('UtilBundle:IndonesiaTax')->findBy(['effectDate' => $date]);
        if(!empty($indTaxList)) {
            foreach ($indTaxList as $obj) {
                $obj->setTaxValue($obj->getTaxValueNew());
                $obj->setUpdatedOn($now);
                $em->persist($obj);
                $output->writeln(' OK Value of '.strtoupper($obj->getTaxName()).' changed to: '.$obj->getTaxValueNew());
            }
            $em->flush();
        } else {
            $output->writeln(' No any change on Indonesia Import Tax');
        }
        $output->writeln('End');
    }
}
