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
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');

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
