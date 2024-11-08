<?php

namespace App\Service;

use GuzzleHttp\Client;
use Brevo\Client\Configuration;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Api\TransactionalEmailsApi;

class BrevoEmailService
{
    public function mailSender($email)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', 'votre-api-key-ici');

        $apiInstance = new TransactionalEmailsApi(new Client(), $config);

        $sendSmtpEmail = new SendSmtpEmail([
            'subject' => 'from the PHP SDK!',
            'sender' => ['name' => 'Brevo', 'email' => 'contact@brevo.com'],
            'replyTo' => ['name' => 'Brevo', 'email' => 'contact@brevo.com'],
            'to' => [['name' => 'Max Mustermann', 'email' => $email]],
            'htmlContent' => '<html><body><h1>This is a transactional email {{params.bodyMessage}}</h1></body></html>',
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
