<?php

namespace UtilBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Constant;

/**
 * the command should be run one time for update percentage into rx (order)
 * strike:673
 * @author thu.tranq
 */
class UpdatePercentageForRxCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('util:rx:update-percentage')
             ->setDescription('Update doctor medicine and platform medicine percentage for rxs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = new \DateTime();
        $output->writeln($date->format("Y-m-d H:i:s") . ': Start update doctor medicine and platform medicine percentage to rx' . "\n");

        $em = $this->getContainer()->get("doctrine")->getManager();
        $psRepo = $em->getRepository('UtilBundle:PlatformSettings');
        $psObj  = $psRepo->getPlatFormSetting();

        if (!isset($psObj)) {
            return;
        }
        $localCountryId = $psObj['operationsCountryId'];

        // get rxs
        $rxRepo = $em->getRepository('UtilBundle:Rx');
        $rxQB   = $rxRepo->createQueryBuilder('r');
        $result = $rxQB->getQuery()->getResult();

        // platformSharePercentage
        $pspRepo = $em->getRepository('UtilBundle:PlatformSharePercentages');
        $localMedicineSetting = $pspRepo->getPSPercentageByType(Constant::AREA_TYPE_LOCAL, Constant::MST_MEDICINE);
        $overseaMedicineSetting = $pspRepo->getPSPercentageByType(Constant::AREA_TYPE_OVERSEA, Constant::MST_MEDICINE);

        if (empty($localMedicineSetting) or empty($overseaMedicineSetting)) {
            $output->writeln($date->format("Y-m-d H:i:s") . ': Nothing setting to update' . "\n");
            return;
        }
        // update percentage for rx
         foreach ($result as $rx) {
            $patientPrimaryResidenceCountryId = $rx->getPatient()->getPrimaryResidenceCountry()->getId();
            
             if ($patientPrimaryResidenceCountryId != $localCountryId) {
                // oversea
                $doctorMedicinePercentage   = $overseaMedicineSetting['doctorPercentage'];
                $platformMedicinePercentage = $overseaMedicineSetting['platformPercentage'];
             } else {
                //local
                $doctorMedicinePercentage   = $localMedicineSetting['doctorPercentage'];
                $platformMedicinePercentage = $localMedicineSetting['platformPercentage'];
             }
            $rx->setDoctorMedicinePercentage($doctorMedicinePercentage);
            $rx->setPlatformMedicinePercentage($platformMedicinePercentage);
            $em->persist($rx);


            $output->writeln($date->format("Y-m-d H:i:s") . ": Update doctorMedicinePercentage: {$doctorMedicinePercentage}, platformMedicinePercentage: {$platformMedicinePercentage} to rx id:{$rx->getId()} sucessfully \n");
         }
         $em->flush();
    }
}

