<?php
/**
 * MailerTest.php
 *
 * PHP version 5.6+
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 */

namespace tests\unit;

use sweelix\mailjet\Mailer;
use sweelix\mailjet\Message;
use Yii;

/**
 * Test node basic functions
 *
 * @author Philippe Gaultier <pgaultier@sweelix.net>
 * @copyright 2010-2017 Philippe Gaultier
 * @license http://www.sweelix.net/license license
 * @version XXX
 * @link http://www.sweelix.net
 * @package tests\unit
 * @since XXX
 */
class MailerTest extends TestCase
{

    public function setUp()
    {
        $this->mockApplication([
            'components' => [
                'email' => $this->createTestEmailComponent()
            ]
        ]);
    }

    protected function createTestEmailComponent()
    {
        $component = new Mailer();
        $component->apiKey = MAILJET_KEY;
        $component->apiSecret = MAILJET_SECRET;
        return $component;
    }

    /**
     * Method to enable IDE auto-completion in other methods
     * @return null|Mailer
     * @throws \yii\base\InvalidConfigException
     */
    protected function getYiiMailerComponent()
    {
        return Yii::$app->get('mailer');
    }

    public function testGetMailjetMailer()
    {
        $mailer = $this->createTestEmailComponent();
        $this->assertInstanceOf(Mailer::className(), $mailer);
    }

    /**
     * @return Message test message instance.
     */
    protected function createTestMessage()
    {
        return Yii::$app->get('mailer')->compose();
    }


    public function testMultipleSend()
    {
        // allow disabling real tests
        if (MAILJET_TEST_SEND === true) {
            $mailer = Yii::$app->get('mailer');
            $messages = [];
            $numberOfTestMessages = 3;
            for ($i = 1; $i <= $numberOfTestMessages; $i++) {
                $message = $this->createTestMessage();
                $message->setFrom(MAILJET_FROM);
                $message->setTo(MAILJET_TO);
                $message->setSubject('Yii MailJet test message: testMultipleSend() - (' . $i . ' of ' . $numberOfTestMessages . ')');
                $message->setTextBody('Yii MailJet test body: testMultipleSend() - (' . $i . ' of ' . $numberOfTestMessages . ')');
                $messages[] = $message;
            }
            $successCount = $mailer->sendMultiple($messages);
            $this->assertEquals($numberOfTestMessages, $successCount);
        }
    }

    public function testMultipleSendReturnResponseBody()
    {
        // allow disabling real tests
        if (MAILJET_TEST_SEND === true) {
            $messages = [];
            $numberOfTestMessages = 3;
            for ($i = 1; $i <= $numberOfTestMessages; $i++) {
                $message = $this->createTestMessage();
                $message->setFrom(MAILJET_FROM);
                $message->setTo(MAILJET_TO);
                $message->setSubject('Yii MailJet test message: testMultipleSendReturnResponseBody() -  (' . $i . ' of ' . $numberOfTestMessages . ')');
                $message->setTextBody('Yii MailJet test body: testMultipleSendReturnResponseBody() - (' . $i . ' of ' . $numberOfTestMessages . ')');
                $messages[] = $message;
            }

            $mailer = $this->getYiiMailerComponent();
            $response = $mailer->sendMultiple($messages, true);

            $this->assertInstanceOf(\Mailjet\Response::class, $response);
            $this->assertEquals(200, $response->getStatus());
            $this->assertEquals(true, $response->success());

            $responseBody = $response->getBody();
            $this->assertArrayHasKey('Messages', $responseBody);
            $this->assertCount($numberOfTestMessages, $responseBody['Messages']);
            for ($i = 0; $i < $numberOfTestMessages; $i++) {
                $this->assertEquals('success', $responseBody['Messages'][$i]['Status']);
            }
        }
    }
}
