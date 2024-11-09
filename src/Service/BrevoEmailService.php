<?php

namespace App\Service;

use Twig\Environment;
use GuzzleHttp\Client;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Api\TransactionalEmailsApi;

class BrevoEmailService
{
    
    public function mailSender($email, $name, Environment $twig)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('', '');

        $apiInstance = new TransactionalEmailsApi(new Client(), $config);

        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => 'from the PHP SDK!',
            'sender' => ['name' => 'Brevo', 'email' => 'nika.mamian@gmail.com'],
            'replyTo' => ['name' => 'Brevo', 'email' => 'nika.mamian@gmail.com'],
            'to' => [['name' => $name, 'email' => $email]],
            'htmlContent' => $twig->render('emails/welcome.html.twig', [
                'name' => $name,  // Add name here
            ]),
            'params' => ['bodyMessage' => 'made just for you!']
        ]);

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            print_r($result);
        } catch (Exception $e) {
            echo 'Exception when calling TransactionalEmailsApi->sendTransacEmail: ', $e->getMessage(), PHP_EOL;
        }
    }
}
