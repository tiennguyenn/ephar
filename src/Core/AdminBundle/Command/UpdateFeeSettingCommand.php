<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class UpdateFeeSettingCommand extends ContainerAwareCommand {

    /**
     * run 1 times/day at 0h:0':1s 
     * update new fee  value setting everyday
     * @author bien.mai
     */
    protected function configure() {
        $this->setName('app:update-fee-seting');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $fees = $em->getRepository('UtilBundle:FeeSetting')->getUpdateFeesetting();
        foreach ($fees as $obj) {
            $obj->setFee($obj->getNewFee());
            $em->persist($obj);
            $em->flush();
            $output->writeln('update fee setting id : '.$obj->getId());
        }
        if(count($fees) == 0) {
            $output->writeln('update 0 record  ');
        }

        $output->writeln('update platform share percentages');
        $list = $em->getRepository('UtilBundle:PlatformSharePercentages')->getUpdateData();
        foreach ($list as $value) {
            $value->setDoctorPercentage($value->getNewDoctorPercentage());
            $value->setAgentPercentage($value->getNewAgentPercentage());
            $value->setPlatformPercentage($value->getNewPlatformPercentage());
            $em->persist($value);
            $em->flush();
            $output->writeln('update platform share percentages id : '.$value->getId());
        }
        if(count($list) == 0) {
            $output->writeln('update 0 record  ');
        }

        $output->writeln('update platform share fees');
        $listFees = $em->getRepository('UtilBundle:PlatformShareFee')->getUpdateData();
        foreach ($listFees as $value) {
            $value->setPlatformPercentage($value->getNewPlatformPercentage());
            $value->getAgentPercentage($value->getNewAgentPercentage());
            $em->persist($value);
            $em->flush();
            $output->writeln('update platform share fee id : '.$value->getId());
        }
        if(count($listFees) == 0) {
            $output->writeln('update 0 record  ');
        }

        $output->writeln('update agent fee medicine');
        $listFees = $em->getRepository('UtilBundle:PlatformShareFee')->getAgentFeeMedicineUpdateData();
        foreach ($listFees as $value) {
            $value->setAgentFee($value->getNewAgentFee());
            $em->persist($value);
            $em->flush();
            $output->writeln('update agent fee medicine id : '.$value->getId());
        }
        if(count($listFees) == 0) {
            $output->writeln('update 0 record  ');
        }

        $output->writeln('update primary agent fees');
        $listFees = $em->getRepository('UtilBundle:AgentPrimaryCustomFee')->getCronjobUpdateFeeData();
        foreach ($listFees as $value) {
            $value->setPlatformPercentage($value->getNewPlatformPercentage());
            $value->getAgentPercentage($value->getNewAgentPercentage());
            $em->persist($value);
            $em->flush();
            $output->writeln('update primary agent fee id : '.$value->getId());
        }
        if(count($listFees) == 0) {
            $output->writeln('update 0 record  ');
        }
    }

}
