<?php

namespace AdminBundle\Form;

use AdminBundle\Utilities\Constant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class MessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $options['data'];
        $to = isset($data['to']) ? $data['to'] : "";
        $cc = "";
        $bcc = "";
        $subject = "";
        $body = "";
        $sentTo = "";
        if(!empty($data)) {
            switch ($data['type']) {
                case 'reply':
                    $to = ($data['senderName'] == 'Gmedes Admin')? 'Gmedes Admin': $data['senderEmail'];
                    $subject .= "RE: ";
                    break;
                case 'replyall':
                    $subject .= "RE: ";
                    if(!empty($data['relevantReceiver'])) {
                        foreach($data['relevantReceiver'] as $item) {
                            $str = $item['receiverEmail'];
                            if($item['receiverType'] == 0) 
                                $to .= !empty($to)?",".$str:$str;
                            // elseif($item['receiverType'] == 1) 
                            //     $cc .= !empty($cc)?",".$str: $str;
                            // elseif($item['receiverType'] == 2) 
                            //     $bcc .= !empty($bcc)?",".$str: $str;
                        }
                    } else {
                        $to = $data['senderEmail'];
                    }
                    break;
                case 'forward':
                    $subject .= "FW: ";
                    // $sentTo = "";
                    // foreach($data['relevantReceiver'] as $item) {
                    //     if($item['receiverType'] == 0) 
                    //         $sentTo .= $item['receiverEmail'].",";
                    // }
                    break;
                }
            $subject .= $data['subject'];
            // $body .= "<br>From: ".$data['senderName']." ".$data['senderEmail']."
            // <br>Sent: ".$data['sentDate']->format('M d, Y g:i A')."
            // <br>To: $sentTo";

            if(!in_array($data['type'], array('reply', 'replyall', 'forward'))) {
                $body .= $data['body'];
            } else {
                $body .= "<br><blockquote>".$data['body']."</blockquote>";
            }
        }
        $builder
            ->add('to', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Enter Email',
                    'class'       => 'form-control',
                ),
                'data'       => $to,
                'required'  => false
            ))
            // ->add('bcc', TextType::class, array(
            //     'attr' => array(
            //         'placeholder' => 'Enter Email',
            //         'class'       => 'form-control'
            //     ),  
            //     'data'       => $bcc,
            //     'required'  => false
            // ))
            // ->add('cc', TextType::class, array(
            //     'attr' => array(
            //         'placeholder' => 'Enter Email',
            //         'class'       => 'form-control'
            //     ),
            //     'data'       => $cc,
            //     'required'  => false
            // ))
            ->add('subject', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Enter Subject',
                    'class'       => 'form-control'
                ),
                'data' => $subject
            ))
            ->add('message', TextareaType::class, array(
                'attr' => array(
                    'class'       => 'inbox-editor inbox-wysihtml5 form-control ignore',
                    'rows'        => 12
                ),
                'data' => $body
            ))
            ->add('postType', HiddenType::class
            );
        if(isset($data['id'])) {
            $builder
            ->add('id', HiddenType::class, array(
                'data'       => $data['id']
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'messages';
    }
}