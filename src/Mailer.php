<?php
/**
 * Mail.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package sweelix\mailjet
 */

namespace sweelix\mailjet;


use Mailjet\Client;
use Mailjet\Resources;
use yii\base\InvalidConfigException;
use yii\mail\BaseMailer;
use Exception;

/**
 * This component allow user to send an email
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package sweelix\mailjet
 * @since XXX
 * @todo implement batch messages using API
 */
class Mailer extends BaseMailer
{
    /**
     * @var string
     */
    public $apiKey;

    /**
     * @var string
     */
    public $apiSecret;

    /**
     * @var boolean
     */
    public $enable = true;

    /**
     * @var string
     */
    public $apiVersion = 'v3.1';

    /**
     * @var string
     */
    public $apiUrl;

    /**
     * @var bool
     */
    public $secured = true;

    /**
     * @inheritdoc
     */
    public $messageClass = 'sweelix\mailjet\Message';
    /**
     * @param Message $message
     * @since XXX
     * @throws InvalidConfigException
     */
    public function sendMessage($message)
    {
        try {
            if ($this->apiKey === null) {
                throw new InvalidConfigException('API Key is missing');
            }
            if ($this->apiSecret === null) {
                throw new InvalidConfigException('API Secret is missing');
            }
            $settings = [
                'secured' => $this->secured,
                'version' => $this->apiVersion,
            ];

            if ($this->apiUrl !== null) {
                $settings['url'] = $this->apiUrl;
            }

            $client = new Client($this->apiKey, $this->apiSecret, $this->enable, $settings);

            $fromEmails = Message::convertEmails($message->getFrom());
            $toEmails = Message::convertEmails($message->getTo());

            $mailJetMessage = [
                // 'FromEmail' => $fromEmails[0]['Email'],
                'From' => $fromEmails[0],
                'To' => $toEmails,
            ];
            /*
            if (isset($fromEmails[0]['Name']) === true) {
                $mailJetMessage['FromName'] = $fromEmails[0]['Name'];
            }
            */

            /*
            $sender = $message->getSender();
            if (empty($sender) === false) {
                $sender = Message::convertEmails($sender);
                $mailJetMessage['Sender'] = $sender[0];
            }
            */


            $cc = $message->getCc();
            if (empty($cc) === false) {
                $cc = Message::convertEmails($cc);
                $mailJetMessage['Cc'] = $cc;
            }

            $bcc = $message->getBcc();
            if (empty($cc) === false) {
                $bcc = Message::convertEmails($bcc);
                $mailJetMessage['Bcc'] = $bcc;
            }

            $attachments = $message->getAttachments();
            if ($attachments !== null) {
                $mailJetMessage['Attachments'] = $attachments;
            }

            $headers = $message->getHeaders();
            if (empty($headers) === false) {
                $mailJetMessage['Headers'] = $headers;
            }
            $mailJetMessage['TrackOpens'] = $message->getTrackOpens();
            $mailJetMessage['TrackClicks'] = $message->getTrackClicks();

            $templateModel = $message->getTemplateModel();
            if (empty($templateModel) === false) {
                $mailJetMessage['Variables'] = $templateModel;
            }

            $templateId = $message->getTemplateId();
            if ($templateId === null) {
                $mailJetMessage['Subject'] = $message->getSubject();
                $textBody = $message->getTextBody();
                if (empty($textBody) === false) {
                    $mailJetMessage['TextPart'] = $textBody;
                }
                $htmlBody = $message->getHtmlBody();
                if (empty($htmlBody) === false) {
                    $mailJetMessage['HTMLPart'] = $htmlBody;
                }
                $sendResult = $client->post(Resources::$Email, [
                    'body' => [
                        'Messages' => [
                            $mailJetMessage,
                        ]
                    ]
                ]);
            } else {
                $mailJetMessage['TemplateID'] = $templateId;
                $processLanguage = $message->getTemplateLanguage();
                if ($processLanguage === true) {
                    $mailJetMessage['TemplateLanguage'] = $processLanguage;
                }
                $sendResult = $client->post(Resources::$Email, [
                    'body' => [
                        'Messages' => [
                            $mailJetMessage,
                        ]
                    ]
                ]);
            }
            //TODO: handle error codes and log stuff
            return $sendResult->success();
        } catch (Exception $e) {
            throw $e;
        }
    }



}