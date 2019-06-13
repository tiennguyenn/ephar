<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Constant;

class EmailSendCommand extends ContainerAwareCommand
{
    /**
     * Email send
     * php app/console app:email-send [Y-m-d]
     * @author vinh.nguyen
     */
    protected function configure()
    {
        $this->setName('app:email-send')
             ->addArgument('day', InputArgument::OPTIONAL, 'Focus on the day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('============= Start ===========');
        $date = new \DateTime();
        $output->writeln($date->format("Y-m-d H:i:s").': Email send');
        
        $repoEmail = $this->getContainer()->get("doctrine")->getManager()->getRepository('UtilBundle:EmailSend');
        $serviceEmail = $this->getContainer()->get('microservices.sendgrid.email');
        
        $day = $input->getArgument('day', 'now');
        $params = array(
            'timeToSend' => new \DateTime($day),
            'status'     => Constant::EMAIL_STATUS_SENT,
            'counterMax' => $this->getContainer()->getParameter('email_send_counter')
        );
        
        $list = $repoEmail->getListBy($params);
        
        $failRes = array();
        if(!empty($list)) {
            foreach ($list as $item) {
                //send email
                $emailAddress = trim($item['to']);
                $subject = trim($item['subject']);
                $template = 'AdminBundle:emails:email-send.html.twig';
                $sentRes = $serviceEmail->sendEmail(array(
                    'title' => !empty($subject)? $subject: "(no subject)",
                    'body'  => $this->getContainer()->get('templating')->render($template, array('content'=>$item['content'])),
                    'from'  => $this->getContainer()->getParameter('primary_email'),
                    'to'    => $emailAddress
                ));

                //update changed
                if($sentRes) {
                    $status = Constant::EMAIL_STATUS_SENT;
                    $output->writeln("OK     Send Email to: " . $emailAddress);
                } else {
                    $status = Constant::EMAIL_STATUS_ERROR;
                    $output->writeln("Error  Send Email to: " . $emailAddress);
                    $failRes[] = $emailAddress;
                }
                $updated = $repoEmail->update(array(
                    'id'      => $item['id'],
                    'counter' => ((int)$item['counter'] + 1),
                    'status'  => $status
                ));
            }
        }
        $total = count($list);
        $fail = count($failRes);
        $output->writeln("\nTotal result: ". $total );
        if($fail > 0) {
            $sent = ($total > $fail)? ($total - $fail): 0;
            $output->writeln(" - Success: " . $sent);
            $output->writeln(" - Fail: " . $fail);
        }
        $output->writeln('============= End ===========');
    }
}