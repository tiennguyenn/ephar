<?php
/**
 * Created by PhpStorm.
 * User: nanang
 * Date: 22/01/19
 * Time: 11:05
 */

namespace AdminBundle\Command;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Common;
use UtilBundle\Utility\Constant;

class GenerateDoctorGuideCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('app:doctor-guide-generate')
            ->addArgument('country', InputArgument::OPTIONAL, 'SG or Parkway', 'Parkway');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get("doctrine")->getManager();
        $doctorGuideDocs = $em->getRepository('UtilBundle:FileDocumentLog')->getDoctorGuides($input->getArgument('country'));

        $output->writeln('Generating Doctor User\'s Guide');
        $output->writeln('===========================================');

        if ($doctorGuideDocs) {

            $output->writeln('Start: ' . date('d-m-Y H:i:s'));
            $output->writeln('----------------------------------------');
            foreach ($doctorGuideDocs as $doc) {

                $documentLogId = $doc->getId();
                $documentLog = $doc;

                $params = array(
                    'title' => $documentLogId . '_' . Constant::DOCUMENT_NAME_DOCTOR_USER_GUIDE,
                    'content' => $documentLog->getContentAfter()
                );

                $fileName = str_replace(" ", "_", $params['title']) . '.pdf';
                $locationDir = $this->getContainer()->getParameter('upload_directory') . '/doctor_guide';

                if (!file_exists($locationDir . '/' . $fileName)) {
                    $output->writeln('Generating ' . $fileName);

                    try {
                        $options = new Options();
                        $options->set('isRemoteEnabled', TRUE);
                        $dompdf = new Dompdf($options);

                        $dompdf->loadHtml('<div>'.$params['content'].'</div>');
                        $dompdf->setPaper('A4', 'portrait');
                        $dompdf->render();
                        $outputRes = $dompdf->output();

                        if (Common::createDirIfNotExists($locationDir)) {
                            file_put_contents($locationDir . '/' . $fileName, $outputRes);
                        }
                        $output->writeln('Generated!');
                    } catch (\Exception $exception) {
                        $output->writeln('Error: ' . $exception->getMessage());
                    }
                    $output->writeln('----------------------------------------');
                }
            }
            $output->writeln('Finish: ' . date('d-m-Y H:i:s'));
        } else {
            $output->writeln('Nothing to generate');
        }
    }
}