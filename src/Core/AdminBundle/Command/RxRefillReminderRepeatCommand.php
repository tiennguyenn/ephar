<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AdminBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RxRefillReminderRepeatCommand extends ContainerAwareCommand {

    /**
     * Configure
     * author Tuan Nguyen
     */
    protected function configure() {
        $this
                ->setName('app:rx-refill-reminder-repeat')
                ->setDescription('Rx Refill Reminder Repeat')
        ;
    }

    /**
     * Execute
     * @param InputInterface $input
     * @param OutputInterface $output
     * author Tuan Nguyen
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        // access the container using getContainer()
        $date = new \DateTime();
        $output->writeln('============= '. $date->format("Y-m-d H:i:s") .' Start Refill Reminder Repeat ===========');

        $rxRefillReminder = $this->getContainer()->get('microservices.rxRefillReminder');
        
		$results = $rxRefillReminder->remindPatientAndDoctor();
        
		foreach ($results as $result) {
            $output->writeln($result);
        }

        $date = new \DateTime();
        $output->writeln('============= '. $date->format("Y-m-d H:i:s") .' End Refill Reminder Repeat ===========');
    }

}
