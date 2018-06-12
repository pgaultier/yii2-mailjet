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
use yii\helpers\ArrayHelper;
use yii\mail\BaseMailer;

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
     * @var \Mailjet\Response
     */
    public $apiResponse;

    /**
     * @inheritdoc
     */
    public $messageClass = 'sweelix\mailjet\Message';


    /**
     * Sends the specified message.
     * @param Message $message
     * @since XXX
     * @throws InvalidConfigException
     */
    public function sendMessage($message)
    {
        $messages = [$message];
        $result = $this->sendMultiple($messages);
        return ($result == 1);
    }

    /**
     * Sends multiple messages at once.
     * @param Message[] $messages list of email messages, which should be sent.
     * @param boolean $returnResponse whether to return the count of successfully sent messages or MailJet's response object
     * @return int|\Mailjet\Response number of successfully sent messages, or MailJet's api response if $returnResponse is set to true
     * @throws InvalidConfigException
     * @todo implement workaround for MailJet's limit of max. 50 recipients (mail addresses?) per API call
     */
    public function sendMultiple(array $messages, $returnResponse = false)
    {
        $mailJetMessages = [];
        foreach ($messages as $message) {
            $mailJetMessages[] = $message->getMailJetMessage();
        }

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

            $this->apiResponse = $client->post(Resources::$Email, [
                'body' => [
                    'Messages' => $mailJetMessages,
                ]
            ]);

            //TODO: handle error codes and log stuff

            if ($returnResponse) {
                return $this->apiResponse;
            }

            // count successfully sent messages using MailJet's response
            // the format of the response body is:
            // ['Messages' => [
            //     0 => ['Status' => 'success', ...],
            //     1 => ['Status' => 'success', ...],
            //     ...
            // ]]
            $successCount = 0;
            $resultBody = $this->apiResponse->getBody();
            if ( ! empty($resultBody['Messages'])) {
                $resultStatusColumns = ArrayHelper::getColumn($resultBody['Messages'], 'Status');
                $statusCounts = array_count_values($resultStatusColumns);
                if (isset($statusCounts['success'])) {
                    $successCount = $statusCounts['success'];
                }
            }

            return $successCount;

        } catch (InvalidConfigException $e) {
            throw $e;
        }
    }
}
