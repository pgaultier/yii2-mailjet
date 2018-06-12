<?php
/**
 * Message.php
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


use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\mail\BaseMessage;
use Yii;
use yii\mail\MailerInterface;

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
class Message extends BaseMessage
{
    /**
     * @var string|array from
     */
    protected $from;

    /**
     * @var string|array from
     */
    protected $sender;

    /**
     * @var array
     */
    protected $to = [];

    /**
     * @var string|array reply to
     */
    protected $replyTo;

    /**
     * @var array
     */
    protected $cc = [];

    /**
     * @var array
     */
    protected $bcc = [];

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $textBody;

    /**
     * @var string
     */
    protected $htmlBody;

    /**
     * @var array
     */
    protected $attachments = [];

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string
     */
    protected $trackOpens = 'account_default';

    /**
     * @var string
     */
    protected $trackClicks = 'account_default';

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var integer
     */
    protected $templateId;

    /**
     * @var bool
     */
    protected $templateLanguage;

    /**
     * @var array model associated with the template
     */
    protected $templateModel = [];

    /**
     * @var bool
     */
    protected $inlineCss = true;

    protected $charset = 'utf-8';

    /**
     * @inheritdoc
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @inheritdoc
     */
    public function setCharset($charset)
    {
        throw new NotSupportedException();
    }

    /**
     * @inheritdoc
     */
    public function getFrom()
    {
        return self::stringifyEmails($this->from);
    }

    /**
     * @inheritdoc
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return array|string
     * @since XXX
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param string|array $sender
     * @return $this
     * @since XXX
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @inheritdoc
     */
    public function setTo($to)
    {
        if (is_string($to) === true) {
            $to = [$to];
        }
        $this->to = $to;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getReplyTo()
    {
        return self::stringifyEmails($this->replyTo);
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * @inheritdoc
     */
    public function setCc($cc)
    {
        if (is_string($cc) === true) {
            $cc = [$cc];
        }
        $this->cc = $cc;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBcc()
    {
        return $this->bcc;
    }

    /**
     * @inheritdoc
     */
    public function setBcc($bcc)
    {
        if (is_string($bcc) === true) {
            $bcc = [$bcc];
        }
        $this->bcc = $bcc;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string|null text body of the message
     * @since XXX
     */
    public function getTextBody()
    {
        return $this->textBody;
    }

    /**
     * @inheritdoc
     */
    public function setTextBody($text)
    {
        $this->textBody = $text;
        return $this;
    }

    /**
     * @return string|null html body of the message
     * @since XXX
     */
    public function getHtmlBody()
    {
        return $this->htmlBody;
    }

    /**
     * @inheritdoc
     */
    public function setHtmlBody($html)
    {
        $this->htmlBody = $html;
        return $this;
    }

    /**
     * @return string tag associated to the email
     * @since XXX
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param string $tag tag which should be associated to the email
     * @return $this
     * @since XXX
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @param string $trackOpens can be account_default, disabled, enabled
     * @return $this
     * @since XXX
     */
    public function setTrackOpens($trackOpens)
    {
        $this->trackOpens = $trackOpens;
        return $this;
    }

    /**
     * @return string tracking status
     * @since XXX
     */
    public function getTrackOpens()
    {
        return $this->trackOpens;
    }

    /**
     * @param string $trackClicks can be account_default, disabled, enabled
     * @return $this
     * @since XXX
     */
    public function setTrackClicks($trackClicks)
    {
        $this->trackClicks = $trackClicks;
        return $this;
    }

    /**
     * @return string tracking status
     * @since XXX
     */
    public function getTrackClicks()
    {
        return $this->trackClicks;
    }

    /**
     * @param integer $templateId template Id used. in this case, Subject / HtmlBody / TextBody are discarded
     * @return $this
     * @since XXX
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
        return $this;
    }

    /**
     * @return integer|null current templateId
     * @since XXX
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param integer $templateId template Id used. in this case, Subject / HtmlBody / TextBody are discarded
     * @return $this
     * @since XXX
     */
    public function setTemplateLanguage($processLanguage)
    {
        $this->templateLanguage = $processLanguage;
        return $this;
    }

    /**
     * @return integer|null current templateId
     * @since XXX
     */
    public function getTemplateLanguage()
    {
        return $this->templateLanguage;
    }
    /**
     * @param array $templateModel model associated with the template
     * @return $this
     * @since XXX
     */
    public function setTemplateModel($templateModel)
    {
        $this->templateModel = $templateModel;
        if (empty($this->templateModel) === false) {
            $this->templateLanguage = true;
        }
        return $this;
    }

    /**
     * @return array current template model
     * @since XXX
     */
    public function getTemplateModel()
    {
        return $this->templateModel;
    }

    /**
     * @param bool $inlineCss define if css should be inlined
     * @return $this
     * @since XXX
     */
    public function setInlineCss($inlineCss)
    {
        $this->inlineCss = $inlineCss;
        return $this;
    }

    /**
     * @return bool define if css should be inlined
     * @since XXX
     */
    public function getInlineCss()
    {
        return $this->inlineCss;
    }

    /**
     * @param string $headerName
     * @param string $headerValue
     * @since XXX
     */
    public function addHeader($headerName, $headerValue)
    {
        $this->headers[$headerName] = $headerValue;
    }

    /**
     * @return array|null headers which should be added to the mail
     * @since XXX
     */
    public function getHeaders()
    {
        return empty($this->headers) ? [] : $this->headers;
    }

    /**
     * @return array|null list of attachments
     * @since XXX
     */
    public function getAttachments()
    {
        if (empty($this->attachments) === true) {
            return null;
        } else {
            $attachments = array_map(function($attachment) {
                $item = [
                    'ContentType' => $attachment['ContentType'],
                    'Filename' => $attachment['Name'],
                    'Base64Content' => $attachment['Content'],
                ];
                if (isset($attachment['ContentID']) === true) {
                    $item['ContentID'] = $attachment['ContentID'];
                }
                return $item;
            }, $this->attachments);
            return $attachments;
        }
    }

    /**
     * @inheritdoc
     */
    public function attach($fileName, array $options = [])
    {
        $attachment = [
            'Content' => base64_encode(file_get_contents($fileName))
        ];
        if (!empty($options['fileName'])) {
            $attachment['Name'] = $options['fileName'];
        } else {
            $attachment['Name'] = pathinfo($fileName, PATHINFO_BASENAME);
        }
        if (!empty($options['contentType'])) {
            $attachment['ContentType'] = $options['contentType'];
        } else {
            $attachment['ContentType'] = 'application/octet-stream';
        }
        $this->attachments[] = $attachment;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attachContent($content, array $options = [])
    {
        $attachment = [
            'Content' => base64_encode($content)
        ];
        if (!empty($options['fileName'])) {
            $attachment['Name'] = $options['fileName'];
        } else {
            throw new InvalidParamException('Filename is missing');
        }
        if (!empty($options['contentType'])) {
            $attachment['ContentType'] = $options['contentType'];
        } else {
            $attachment['ContentType'] = 'application/octet-stream';
        }
        $this->attachments[] = $attachment;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function embed($fileName, array $options = [])
    {
        $embed = [
            'Content' => base64_encode(file_get_contents($fileName))
        ];
        if (!empty($options['fileName'])) {
            $embed['Name'] = $options['fileName'];
        } else {
            $embed['Name'] = pathinfo($fileName, PATHINFO_BASENAME);
        }
        if (!empty($options['contentType'])) {
            $embed['ContentType'] = $options['contentType'];
        } else {
            $embed['ContentType'] = 'application/octet-stream';
        }
        $embed['ContentID'] = 'cid:' . uniqid();
        $this->attachments[] = $embed;
        return $embed['ContentID'];
    }

    /**
     * @inheritdoc
     */
    public function embedContent($content, array $options = [])
    {
        $embed = [
            'Content' => base64_encode($content)
        ];
        if (!empty($options['fileName'])) {
            $embed['Name'] = $options['fileName'];
        } else {
            throw new InvalidParamException('Filename is missing');
        }
        if (!empty($options['contentType'])) {
            $embed['ContentType'] = $options['contentType'];
        } else {
            $embed['ContentType'] = 'application/octet-stream';
        }
        $embed['ContentID'] = 'cid:' . uniqid();
        $this->attachments[] = $embed;
        return $embed['ContentID'];
    }

    /**
     * Builds an array that represents the message as the MailJet API expects it
     * @return array message as array that the MailJet API expects
     */
    public function getMailJetMessage()
    {
        $fromEmails = Message::convertEmails($this->getFrom());
        $toEmails = Message::convertEmails($this->getTo());

        $mailJetMessage = [
            'From' => $fromEmails[0],
            'To' => $toEmails,
        ];
        /*
        if (isset($fromEmails[0]['Name']) === true) {
            $mailJetMessage['FromName'] = $fromEmails[0]['Name'];
        }
        */

        /*
        $sender = $this->getSender();
        if (empty($sender) === false) {
            $sender = Message::convertEmails($sender);
            $mailJetMessage['Sender'] = $sender[0];
        }
        */

        $cc = $this->getCc();
        if (empty($cc) === false) {
            $cc = Message::convertEmails($cc);
            $mailJetMessage['Cc'] = $cc;
        }

        $bcc = $this->getBcc();
        if (empty($cc) === false) {
            $bcc = Message::convertEmails($bcc);
            $mailJetMessage['Bcc'] = $bcc;
        }

        $attachments = $this->getAttachments();
        if ($attachments !== null) {
            $mailJetMessage['Attachments'] = $attachments;
        }

        $headers = $this->getHeaders();
        if (empty($headers) === false) {
            $mailJetMessage['Headers'] = $headers;
        }
        $mailJetMessage['TrackOpens'] = $this->getTrackOpens();
        $mailJetMessage['TrackClicks'] = $this->getTrackClicks();

        $templateModel = $this->getTemplateModel();
        if (empty($templateModel) === false) {
            $mailJetMessage['Variables'] = $templateModel;
        }

        $templateId = $this->getTemplateId();
        if ($templateId === null) {
            $mailJetMessage['Subject'] = $this->getSubject();
            $textBody = $this->getTextBody();
            if (empty($textBody) === false) {
                $mailJetMessage['TextPart'] = $textBody;
            }
            $htmlBody = $this->getHtmlBody();
            if (empty($htmlBody) === false) {
                $mailJetMessage['HTMLPart'] = $htmlBody;
            }
        } else {
            $mailJetMessage['TemplateID'] = $templateId;
            $processLanguage = $this->getTemplateLanguage();
            if ($processLanguage === true) {
                $mailJetMessage['TemplateLanguage'] = $processLanguage;
            }
        }

        return $mailJetMessage;
    }

    /**
     * @inheritdoc
     * @todo make real serialization to make message compliant with MailjetAPI
     */
    public function toString()
    {
        return serialize($this);
    }


    /**
     * @param array|string $emailsData email can be defined as string. In this case no transformation is done
     *                                 or as an array ['email@test.com', 'email2@test.com' => 'Email 2']
     * @return string|null
     * @since XXX
     */
    public static function stringifyEmails($emailsData)
    {
        $emails = null;
        if (empty($emailsData) === false) {
            if (is_array($emailsData) === true) {
                foreach ($emailsData as $key => $email) {
                    if (is_int($key) === true) {
                        $emails[] = $email;
                    } else {
                        if (preg_match('/[.,:]/', $email) > 0) {
                            $email = '"'. $email .'"';
                        }
                        $emails[] = $email . ' ' . '<' . $key . '>';
                    }
                }
                $emails = implode(', ', $emails);
            } elseif (is_string($emailsData) === true) {
                $emails = $emailsData;
            }
        }
        return $emails;
    }

    public static function convertEmails($emailsData)
    {
        $emails = [];
        if (empty($emailsData) === false) {
            if (is_array($emailsData) === true) {
                foreach ($emailsData as $key => $email) {
                    if (is_int($key) === true) {
                        $emails[] = [
                            'Email' => $email,
                        ];
                    } else {
                        /*if (preg_match('/[.,:]/', $email) > 0) {
                            $email = '"'. $email .'"';
                        }*/
                        $emails[] = [
                            'Email' => $key,
                            'Name' => $email,
                        ];
                    }
                }
            } elseif (is_string($emailsData) === true) {
                // "Test, Le" <email@plop.com>
                if (preg_match('/"([^"]+)"\s<([^>]+)>/', $emailsData, $matches) > 0) {
                    $emails[] = [
                        'Email' => $matches[2],
                        'Name' => $matches[1],
                    ];
                } else {
                    $emails[] = [
                        'Email' => $emailsData,
                    ];
                }
            }
        }
        return $emails;

    }
}