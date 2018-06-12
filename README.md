Mailjet Yii2 integration
=========================

This extension allow the developper to use [Mailjet](https://www.mailjet.com/) as an email transport.


[![Latest Stable Version](https://poser.pugx.org/sweelix/yii2-mailjet/v/stable)](https://packagist.org/packages/sweelix/yii2-mailjet)
[![Build Status](https://api.travis-ci.org/pgaultier/yii2-mailjet.svg?branch=master)](https://travis-ci.org/pgaultier/yii2-mailjet)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pgaultier/yii2-mailjet/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pgaultier/yii2-mailjet/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/pgaultier/yii2-mailjet/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/pgaultier/yii2-mailjet/?branch=master)
[![License](https://poser.pugx.org/sweelix/yii2-mailjet/license)](https://packagist.org/packages/sweelix/yii2-mailjet)

[![Latest Development Version](https://img.shields.io/badge/unstable-devel-yellowgreen.svg)](https://packagist.org/packages/sweelix/yii2-mailjet)
[![Build Status](https://travis-ci.org/pgaultier/yii2-mailjet.svg?branch=devel)](https://travis-ci.org/pgaultier/yii2-mailjet)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pgaultier/yii2-mailjet/badges/quality-score.png?b=devel)](https://scrutinizer-ci.com/g/pgaultier/yii2-mailjet/?branch=devel)
[![Code Coverage](https://scrutinizer-ci.com/g/pgaultier/yii2-mailjet/badges/coverage.png?b=devel)](https://scrutinizer-ci.com/g/pgaultier/yii2-mailjet/?branch=devel)

Installation
------------

If you use composer for installing packages, then you can update your composer.json like this:

``` json
{
    "require": {
        "sweelix/yii2-mailjet": "*"
    }
}
```

or use the command:

```
composer require sweelix/yii2-mailjet
```

How to use it
------------

Add extension to your configuration:

``` php
return [
    // ...
    'components' => [
        'mailer' => [
            'class' => 'sweelix\mailjet\Mailer',
            'apiKey' => '<your mailjet api key>',
            'apiSecret' => '<your mailjet api secret>',
        ],
    ],
    // ...
];
```

You can send email as follows (using mailjet templates):

``` php
Yii::$app->mailer->compose('contact/html')
     ->setFrom('from@domain.com')
     ->setTo($form->email)
     ->setSubject($form->subject)
     ->setTemplateId(12345)
     ->setTemplateModel([
         'firstname' => $form->firstname,
         'lastname' => $form->lastname,
     ->send();

```

You can send multiple emails with only one API call as follows:

``` php
$messages = [];
$messages[] = Yii::$app->mailer->compose()
     ->setFrom('from@domain.com')
     ->setTo(recipient2@example.org)
     ->setSubject($form->subject)
     ->setTemplateId(1234);

$messages[] = Yii::$app->mailer->compose()
     ->setFrom('from@domain.com')
     ->setTo('recipient2@example.org')
     ->setSubject($form->subject)
     ->setTemplateId(1234);

// add more messages as needed, but be aware of MailJet's limit of max. 50 recipients (email addresses?) per API call

// option 1: call sendMultiple(), returns number of successfully sent messages
$successCount = Yii::$app->mailer->sendMultiple($messages);
// Mailer->apiResponse contains the last API response as \Mailjet\Response object, with detailed info for every (sent or failed) message
$apiResponse = Yii::$app->mailer->apiResponse;


// option 2: call sendMultiple() passing true as second parameter, directly returns \Mailjet\Response object
$apiResponse = Yii::$app->mailer->sendMultiple($messages, true);
```

Use the methods `$message->setTextBody($text)` and `$message->setHtmlBody($html)` to set the email content directly, without using a MailJet template.

For further instructions refer to the [related section in the Yii Definitive Guide](http://www.yiiframework.com/doc-2.0/guide-tutorial-mailing.html)


Running the tests
-----------------

Please note that the tests need PHPUnit 5 to run.

Before running the tests, you should edit the file tests/_bootstrap.php and change the defines:

``` php
// ...
define('MAILJET_FROM', '<sender>');
define('MAILJET_KEY', '<key>');
define('MAILJET_SECRET', '<secret>');
define('MAILJET_TO', '<target>');
define('MAILJET_TEMPLATE', 218932);

define('MAILJET_TEST_SEND', false);
// ...

```

to match your [Mailjet](https://www.mailjet.com/) configuration.

Contributing
------------

All code contributions - including those of people having commit access -
must go through a pull request and approved by a core developer before being
merged. This is to ensure proper review of all the code.

Fork the project, create a [feature branch ](http://nvie.com/posts/a-successful-git-branching-model/), and send us a pull request.