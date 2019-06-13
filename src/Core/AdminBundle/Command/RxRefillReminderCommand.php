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

class RxRefillReminderCommand extends ContainerAwareCommand {

    /**
     * Configure
     * author luyen nguyen
     */
    protected function configure() {
        $this
                ->setName('app:rx-refill-reminder')
                ->setDescription('Rx Refill Reminder')
        ;
    }

    /**
     * Execute
     * @param InputInterface $input
     * @param OutputInterface $output
     * author luyen nguyen
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('No longer used it, please try other: app:rx-refill-reminder-repeat');
        exit;
        // access the container using getContainer()
        $date = new \DateTime();
        $output->writeln('============= '. $date->format("Y-m-d H:i:s") .' Start Refill reminder ===========');

        $rxRefillReminder = $this->getContainer()->get('microservices.rxRefillReminder');
        $results = $rxRefillReminder->sendEmailToPatient();
        foreach ($results as $result) {
            $output->writeln($result);
        }

        $date = new \DateTime();
        $output->writeln('============= '. $date->format("Y-m-d H:i:s") .' End Refill reminder ===========');
    }

}
