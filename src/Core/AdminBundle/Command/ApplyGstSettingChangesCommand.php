<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Constant;

class ApplyGstSettingChangesCommand extends ContainerAwareCommand
{
    /**
     * Apply price changes
     * @author toan.le
     */
    protected function configure()
    {
        $this->setName('app:apply-gst-changes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine")->getManager();
        
        $output->writeln('Apply Gst Changes');
        
        $date = new \DateTime();
        $date->modify('midnight');
        $date->format('Y-m-d H:i:s');
        $params = array(
            'isHasGst' => Constant::STATUS_GST_ENABLE,
            'effectiveDate' => $date
        );
        
        $gstSettings = $em->getRepository('UtilBundle:DoctorGstSetting')->findBy($params);
        
        if ($gstSettings != null) {
            $output->writeln('There are ' . count($gstSettings) . " records changes");
            foreach ($gstSettings as $item) {
                $newGst = $item->getNewGst();
                $currentGst = $item->getGst();

                $item->setGst($newGst);
                $item->setNewGst(null);
                $item->setEffectiveDate(null);

                $em->persist($item);
                $em->flush();
            }
        }
    }
}