<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace OmniIconDeps\Symfony\Component\DependencyInjection\Loader\Configurator;

use OmniIconDeps\Symfony\Component\Mailer\Bridge\AhaSend\Transport\AhaSendTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Amazon\Transport\SesTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Azure\Transport\AzureTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Google\Transport\GmailTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Infobip\Transport\InfobipTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Mailchimp\Transport\MandrillTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\MailerSend\Transport\MailerSendTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Mailgun\Transport\MailgunTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Mailjet\Transport\MailjetTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Mailomat\Transport\MailomatTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\MailPace\Transport\MailPaceTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Mailtrap\Transport\MailtrapTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\MicrosoftGraph\Transport\MicrosoftGraphTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Postal\Transport\PostalTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Postmark\Transport\PostmarkTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Resend\Transport\ResendTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Scaleway\Transport\ScalewayTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Sendgrid\Transport\SendgridTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Bridge\Sweego\Transport\SweegoTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Transport\NativeTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Transport\NullTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Transport\SendmailTransportFactory;
use OmniIconDeps\Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
return static function (ContainerConfigurator $container) {
    $container->services()->set('mailer.transport_factory.abstract', AbstractTransportFactory::class)->abstract()->args([service('event_dispatcher'), service('http_client')->ignoreOnInvalid(), service('logger')->ignoreOnInvalid()])->tag('monolog.logger', ['channel' => 'mailer']);
    $factories = ['ahasend' => AhaSendTransportFactory::class, 'amazon' => SesTransportFactory::class, 'azure' => AzureTransportFactory::class, 'brevo' => BrevoTransportFactory::class, 'gmail' => GmailTransportFactory::class, 'infobip' => InfobipTransportFactory::class, 'mailchimp' => MandrillTransportFactory::class, 'mailersend' => MailerSendTransportFactory::class, 'mailgun' => MailgunTransportFactory::class, 'mailjet' => MailjetTransportFactory::class, 'mailomat' => MailomatTransportFactory::class, 'mailpace' => MailPaceTransportFactory::class, 'microsoftgraph' => MicrosoftGraphTransportFactory::class, 'native' => NativeTransportFactory::class, 'null' => NullTransportFactory::class, 'postal' => PostalTransportFactory::class, 'postmark' => PostmarkTransportFactory::class, 'mailtrap' => MailtrapTransportFactory::class, 'resend' => ResendTransportFactory::class, 'scaleway' => ScalewayTransportFactory::class, 'sendgrid' => SendgridTransportFactory::class, 'sendmail' => SendmailTransportFactory::class, 'smtp' => EsmtpTransportFactory::class, 'sweego' => SweegoTransportFactory::class];
    foreach ($factories as $name => $class) {
        $container->services()->set('mailer.transport_factory.' . $name, $class)->parent('mailer.transport_factory.abstract')->tag('mailer.transport_factory');
    }
};
