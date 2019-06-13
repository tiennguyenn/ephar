<?php

namespace AdminBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use UtilBundle\Utility\Constant;

class SMSSendCommand extends ContainerAwareCommand
{
    /**
     * SMS send
     * php app/console app:sms-send [Y-m-d]
     * @author vinh.nguyen
     */
    protected function configure()
    {
        $this->setName('app:sms-send')
             ->addArgument('day', InputArgument::OPTIONAL, 'Focus on the day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('============= Start ===========');
        $date = new \DateTime();
        $output->writeln($date->format("Y-m-d H:i:s").': SMS send');
        
        $repoSMS = $this->getContainer()->get("doctrine")->getManager()->getRepository('UtilBundle:SmsSend');
        $serviceSMS = $this->getContainer()->get('microservices.sms');
        
        $day = $input->getArgument('day', 'now');
        $params = array(
            'timeToSend' => new \DateTime($day),
            'status'     => Constant::SMS_STATUS_SENT,
            'counterMax' => $this->getContainer()->getParameter('email_send_counter')
        );
        
        $list = $repoSMS->getListBy($params);
        $failRes = array();

        if(!empty($list)) {
            foreach ($list as $item) {
                //send sms
                $phoneNumber = $item['to'];
                $sentRes = $serviceSMS->sendMessage(array(
                    'to'      => $phoneNumber,
                    'message' => $item['content']
                ));

                //update changed
                if($sentRes) {
                    $status = Constant::SMS_STATUS_SENT;
                    $output->writeln("OK     Send SMS to: " . $phoneNumber);
                } else {
                    $status = Constant::SMS_STATUS_ERROR;
                    $output->writeln("Error  Send SMS to: " . $phoneNumber);
                    $failRes[] = $phoneNumber;
                }
                $updated = $repoSMS->update(array(
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