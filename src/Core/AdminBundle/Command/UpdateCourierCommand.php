<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class UpdateCourierCommand extends ContainerAwareCommand
{
    /**
     * run 1 times/day at 0h:0':1s 
     * Pharmacy Po Daily
     * @author bien.mai
     */
    protected function configure()
    {
        $this->setName('app:update-courier');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em=$this->getContainer()->get('doctrine')->getEntityManager();
        $rates = $em->getRepository('UtilBundle:CourierRate')->updateCourierRate();
        $output->writeln("Start update courier ". date("Y-m-d H:i:s"));
        $output->writeln("==================================");
        if(empty($rates)){
            $output->writeln("no data need to update");
        }

        foreach ($rates as $obj ){
            if($obj->getCostEffectDate() && strtotime($obj->getCostEffectDate()->format('Y-m-d')) == strtotime(date('Y-m-d')) ) {
                $obj->setCost($obj->getNewCost());    
                $obj->setNewCost(null); 
                $obj->setCostEffectDate(null);
            }
            if($obj->getListEffectDate() && strtotime($obj->getListEffectDate()->format('Y-m-d')) == strtotime(date('Y-m-d')) ) {
                $obj->setList($obj->getNewList()); 
                $obj->setNewList(null); 
                $obj->setListEffectDate(null);
            }
            if($obj->getIgPermitListEffectDate() && strtotime($obj->getIgPermitListEffectDate()->format('Y-m-d')) == strtotime(date('Y-m-d')) ) {
                $obj->setIgPermitFee($obj->getNewIgPermitFee());     
                $obj->setNewIgPermitFee(null);
                $obj->setIgPermitListEffectDate(null);
            }
            if($obj->getCollectionRateEffectDate() && strtotime($obj->getCollectionRateEffectDate()->format('Y-m-d')) == strtotime(date('Y-m-d')) ) {
                $obj->setCollectionRate($obj->getNewCollectionRate());
                $obj->setNewCollectionRate(null);
                $obj->setCollectionRateEffectDate(null);
            }
           
            $em->persist($obj);
            $em->flush();
            $output->writeln('update courier rate id : '.$obj->getId());
        }
        $output->writeln("==================================");
        $output->writeln("end update courier ". date("Y-m-d H:i:s"));

        $couriers = $em->getRepository('UtilBundle:Courier')->updateCourier();
        foreach ($couriers as $obj ){
            $obj->setMargin($obj->getNewMargin());
            $obj->setMarginEffectDate(null);
            $obj->setNewMargin(null);        
            
            
            $rates = $obj->getCourierRates();
            foreach ($rates as $rate){
                $output->writeln($rate->getCost()*(1+ $obj->getMargin()/100));
                $rate->setList($rate->getCost()*(1+ $obj->getMargin()/100));
                $rate->setListEffectDate(null);
            }
            
            $em->persist($obj);
            $em->flush();
            $output->writeln('update courier id : '.$obj->getId());
        }
        
    //    $output->writeln($object->getId());

       
    }
}
