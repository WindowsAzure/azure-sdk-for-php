<?php

/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0.
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 *
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */

namespace Tests\unit\WindowsAzure\ServiceBus;

use Tests\framework\ServiceBusRestProxyTestBase;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\ServiceBus\ServiceBusRestProxy;
use WindowsAzure\ServiceBus\Models\BrokeredMessage;
use WindowsAzure\ServiceBus\Models\ListQueuesOptions;
use WindowsAzure\ServiceBus\Models\ListRulesOptions;
use WindowsAzure\ServiceBus\Models\ListTopicsOptions;
use WindowsAzure\ServiceBus\Models\ListSubscriptionsOptions;
use WindowsAzure\ServiceBus\Models\QueueDescription;
use WindowsAzure\ServiceBus\Models\QueueInfo;
use WindowsAzure\ServiceBus\Models\ReceiveMessageOptions;
use WindowsAzure\ServiceBus\Models\RuleInfo;
use WindowsAzure\ServiceBus\Models\SubscriptionInfo;
use WindowsAzure\ServiceBus\Models\TopicInfo;

/**
 * Unit tests for ServiceBusRestProxy class.
 *
 * @category  Microsoft
 *
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 *
 * @version   Release: 0.4.5_2016-09
 *
 * @link      https://github.com/WindowsAzure/azure-sdk-for-php
 */
class ServiceBusRestProxyTest extends ServiceBusRestProxyTestBase
{
    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::createQueue
     */
    public function testCreateQueueWorks()
    {
        // Setup
        $queueName = 'createQueueWorks';
        $queueInfo = new QueueInfo($queueName);
        $this->safeDeleteQueue($queueName);

        // Test
        $queueInfo = $this->createQueue($queueInfo);

        // Assert
        $this->assertNotNull($queueInfo);
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::deleteQueue
     */
    public function testDeleteQueueWorks()
    {
        // Setup
        $queueName = 'testDeleteQueueWorks';
        $queueInfo = new QueueInfo($queueName);
        $this->safeDeleteQueue($queueName);
        $this->restProxy->createQueue($queueInfo);

        // Test
        $this->restProxy->deleteQueue($queueName);

        // Assert
        $this->assertNotNull($queueInfo);
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::deleteQueue
     */
    public function testDeleteQueueNonExistQueueFail()
    {
        // Setup 
        $queueName = 'IDoNotExist';
        $this->setExpectedException(get_class(
            new ServiceException(''))
        );

        // Test
        $this->restProxy->deleteQueue($queueName);

        // Assert
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::deleteQueue
     */
    public function testDeleteQueueSuccess()
    {
        // Setup 
        $queueName = 'testDeleteQueueSuccess';
        $createQueueInfo = new QueueInfo($queueName);
        $listQueuesOptions = new ListQueuesOptions();
        $listQueuesResult = $this->restProxy->listQueues($listQueuesOptions);

        foreach ($listQueuesResult->getQueueInfos() as $queueInfo) {
            $this->restProxy->deleteQueue($queueInfo->getTitle());
        }

        $this->restProxy->createQueue($createQueueInfo);

        // Test
        $this->restProxy->deleteQueue($queueName);
        $listQueuesResult = $this->restProxy->listQueues($listQueuesOptions);

        // Assert

        $this->assertEquals(
            0,
            count($listQueuesResult->getQueueInfos())
        );
    }

    /**
     * @covers \WindowsAzure\ServiceBus\ServiceBusRestProxy::listQueues
     * @covers \MicrosoftAzure\Storage\Queue\Models\ListQueuesResult::parseXml
     * @covers \WindowsAzure\ServiceBus\Models\QueueDescription::create
     */
    public function testListQueueSuccess()
    {
        // Setup 
        $queueName = 'testListQueueSuccess';
        $createQueueInfo = new QueueInfo($queueName);
        $listQueuesOptions = new ListQueuesOptions();
        $listQueuesResult = $this->restProxy->listQueues($listQueuesOptions);

        foreach ($listQueuesResult->getQueueInfos() as $queueInfo) {
            $this->restProxy->deleteQueue($queueInfo->getTitle());
        }
        $this->restProxy->createQueue($createQueueInfo);

        // Test
        $listQueuesResult = $this->restProxy->listQueues($listQueuesOptions);
        $this->restProxy->deleteQueue($queueName);

        // Assert
        $this->assertEquals(
            1,
            count($listQueuesResult->getQueueInfos())
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::sendQueueMessage
     */
    public function testSendQueueMessageWorks()
    {
        // Setup
        $queueName = 'sendQueueMessageWorksQueue';
        $queueInfo = new QueueInfo($queueName);
        $this->safeDeleteQueue($queueName);
        $this->createQueue($queueInfo);
        $brokeredMessage = new BrokeredMessage();
        $brokeredMessage->setBody('sendQueueMessageWorksMessage');

        // Test
        $this->restProxy->sendQueueMessage(
            'sendQueueMessageWorksQueue',
            $brokeredMessage
        );

        // Assert
        $this->assertNotNull($brokeredMessage);
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::receiveMessage
     */
    public function testReceiveMessageWorks()
    {
        // Setup
        $queueDescription = new QueueDescription();
        $queueName = 'testReceiveMessageWorksQueue';
        $queueInfo = new QueueInfo($queueName, $queueDescription);
        $this->safeDeleteQueue($queueName);
        $this->createQueue($queueInfo);
        $brokeredMessage = new BrokeredMessage();
        $expectedMessageText = 'testReceiveMessageWorks';
        $brokeredMessage->setBody($expectedMessageText);
        $this->restProxy->sendQueueMessage(
            $queueName,
            $brokeredMessage
        );
        $receiveMessageOptions = new ReceiveMessageOptions();
        $receiveMessageOptions->setTimeout(5);
        $receiveMessageOptions->setReceiveAndDelete();

        // Test
        $receivedMessage = $this->restProxy->receiveMessage(
            $queueName.'/messages/head',
            $receiveMessageOptions
        );

        // Assert
        $this->assertNotNull($receivedMessage);
        $this->assertEquals(
            $expectedMessageText,
            $receivedMessage->getBody()
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::receiveQueueMessage
     */
    public function testPeekLockMessageWorks()
    {
        // Setup
        $queueDescription = new QueueDescription();
        $queueName = 'testPeekLockMessageWorks';
        $expectedMessage = 'testPeekLockMessageWorksMessage';
        $queueInfo = new QueueInfo($queueName, $queueDescription);
        $this->safeDeleteQueue($queueName);
        $this->createQueue($queueInfo);
        $brokeredMessage = new BrokeredMessage();
        $brokeredMessage->setBody($expectedMessage);
        $this->restProxy->sendQueueMessage($queueName, $brokeredMessage);
        $receiveMessageOptions = new ReceiveMessageOptions();
        $receiveMessageOptions->setTimeout(5);
        $receiveMessageOptions->setPeekLock();

        // Test
        $receivedMessage = $this->restProxy->receiveQueueMessage(
            $queueName,
            $receiveMessageOptions
        );

        // Assert
        $actualMessage = $receivedMessage->getBody();
        $this->assertEquals(
            $expectedMessage,
            $actualMessage
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::deleteMessage
     */
    public function testDeleteMessageInvalidMessage()
    {
        // Setup 
        $queueDescription = new QueueDescription();
        $queueName = 'testDeleteMessageInvalidMessage';
        $queueInfo = new QueueInfo($queueName, $queueDescription);
        $this->safeDeleteQueue($queueInfo);
        $this->createQueue($queueInfo);
        $brokeredMessage = new BrokeredMessage();
        $this->setExpectedException(get_class(new \InvalidArgumentException()));

        // Test
        $this->restProxy->deleteMessage($brokeredMessage);

        // Assert
        $this->assertTrue(false);
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::deleteMessage
     */
    public function testDeleteMessageSuccess()
    {
        // Setup 
        $queueDescription = new QueueDescription();
        $queueName = 'testDeleteMessageSuccess';
        $expectedMessage = 'testDeleteMessageSuccess';
        $queueInfo = new QueueInfo($queueName, $queueDescription);
        $this->safeDeleteQueue($queueInfo);
        $this->createQueue($queueInfo);
        $brokeredMessage = new BrokeredMessage();
        $brokeredMessage->setBody($expectedMessage);

        // Test
        $this->restProxy->sendQueueMessage($queueName, $brokeredMessage);
        $receiveMessageOptions = new ReceiveMessageOptions();
        $receiveMessageOptions->setTimeout(5);
        $receiveMessageOptions->setPeekLock();
        $receivedMessage = $this->restProxy->receiveQueueMessage(
            $queueName,
            $receiveMessageOptions
        );
        $this->restProxy->deleteMessage($receivedMessage);

        // Assert
        $this->assertTrue(true);
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::receiveQueueMessage
     */
    public function testPeekLockedMessageCanBeCompleted()
    {
        // Setup
        $queueDescription = new QueueDescription();
        $queueName = 'testPeekLockMessageCanBeCompleted';
        $expectedMessage = 'testPeekLockMessageCanBeCompletedMessage';
        $queueInfo = new QueueInfo($queueName, $queueDescription);
        $this->safeDeleteQueue($queueName);
        $this->createQueue($queueInfo);
        $brokeredMessage = new BrokeredMessage();
        $brokeredMessage->setBody($expectedMessage);

        $this->restProxy->sendQueueMessage(
            $queueName,
            $brokeredMessage
        );

        $receiveMessageOptions = new ReceiveMessageOptions();
        $receiveMessageOptions->setTimeout(5);
        $receiveMessageOptions->setPeekLock();

        // Test
        $receivedMessage = $this->restProxy->receiveQueueMessage(
            $queueName,
            $receiveMessageOptions
        );

        // Assert
        $lockToken = $receivedMessage->getLockToken();
        $lockedUntil = $receivedMessage->getLockedUntilUtc();
        $this->assertNotNull($lockToken);
        $this->assertNotNull($lockedUntil);
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::unlockMessage
     */
    public function testPeekLockedMessageCanBeUnlocked()
    {
        // Setup
        $queueDescription = new QueueDescription();
        $queueName = 'testPeekLockMessageCanBeUnlocked';
        $expectedMessage = 'testPeekLockMessageCanBeUnlocked';
        $queueInfo = new QueueInfo($queueName, $queueDescription);
        $this->safeDeleteQueue($queueName);
        $this->createQueue($queueInfo);
        $brokeredMessage = new BrokeredMessage();
        $brokeredMessage->setBody($expectedMessage);
        $this->restProxy->sendQueueMessage($queueName, $brokeredMessage);
        $receiveMessageOptions = new ReceiveMessageOptions();
        $receiveMessageOptions->setTimeout(5);
        $receiveMessageOptions->setPeekLock();
        $peekedMessage = $this->restProxy->receiveQueueMessage(
            $queueName,
            $receiveMessageOptions
        );
        $lockToken = $peekedMessage->getLockToken();
        $lockedUntilUtc = $peekedMessage->getLockedUntilUtc();

        // Test
        $this->restProxy->unlockMessage($peekedMessage);
        $receiveMessageOptions->setReceiveAndDelete();
        $unlockedMessage = $this->restProxy->receiveQueueMessage(
            $queueName,
            $receiveMessageOptions
        );

        // Assert
        $this->assertNotNull($lockToken);
        $this->assertNotNull($lockedUntilUtc);
        $this->assertNull($unlockedMessage->getLockToken());
        $this->assertNull($unlockedMessage->getLockedUntilUtc());
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::sendMessage
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::sendQueueMessage
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::receiveMessage
     */
    public function testContentTypePassesThrough()
    {
        // Setup
        $queueName = 'testContnetTypePassesThrough';
        $queueDescription = new QueueDescription();
        $queueInfo = new QueueInfo($queueName, $queueDescription);
        $this->safeDeleteQueue($queueName);
        $this->createQueue($queueInfo);
        $expectedMessage = new BrokeredMessage();
        $expectedMessage->setBody('<data>testContentTypePassesThrough</data>');
        $expectedMessage->setContentType(Resources::XML_CONTENT_TYPE);
        $receiveMessageOptions = new ReceiveMessageOptions();
        $receiveMessageOptions->setTimeout(5);
        $receiveMessageOptions->setReceiveAndDelete();

        // Test
        $this->restProxy->sendQueueMessage($queueName, $expectedMessage);
        $actualMessage = $this->restProxy->receiveQueueMessage(
            $queueName,
            $receiveMessageOptions
        );

        // Assert
        $this->assertEquals(
            $expectedMessage->getBody(),
            $actualMessage->getBody()
        );

        $this->assertEquals(
            $expectedMessage->getContentType(),
            $actualMessage->getContentType()
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::createTopic
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::deleteTopic
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::listTopics
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::getTopic
     */
    public function testCreateListFetchAndDeleteTopicSuccess()
    {
        // Setup
        $topicName = 'createTopicSuccess';
        $topicInfo = new TopicInfo($topicName);
        $listTopicsOptions = new ListTopicsOptions();
        $listTopicsResult = $this->restProxy->listTopics($listTopicsOptions);

        foreach ($listTopicsResult->getTopicInfos() as $topicInfo) {
            $this->restProxy->deleteTopic($topicInfo->getTitle());
        }

        // Test
        $createTopicResult = $this->createTopic($topicInfo);
        $listTopicsResult = $this->restProxy->listTopics($listTopicsOptions);
        $getTopicResult = $this->restProxy->getTopic($topicName);
        $this->safeDeleteTopic($topicName);
        $listTopicsResult2 = $this->restProxy->listTopics($listTopicsOptions);

        // Assert
        $this->assertNotNull($createTopicResult);
        $this->assertNotNull($listTopicsResult);
        $this->assertNotNull($getTopicResult);
        $this->assertNotNull($listTopicsResult2);

        $this->assertEquals(
            1,
            count($listTopicsResult->getTopicInfos())
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::createSubscription
     */
    public function testSubscriptionCanBeCreatedOnTopics()
    {
        // Setup        
        $topicName = 'testCreateSubscriptionWorksTopic';
        $subscriptionName = 'testCreateSubscriptionWorksSubscription';
        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteTopic($topicName);
        $this->createTopic($topicInfo);

        // Test
        $subscriptionInfo = $this->createSubscription(
            $topicName,
            $subscriptionInfo
        );

        // Assert
        $this->assertNotNull($subscriptionInfo);
        $this->assertEquals(
            $subscriptionName,
            $subscriptionInfo->getTitle()
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::listSubscriptions
     */
    public function testSubscriptionsCanBeListed()
    {
        // Setup
        $topicName = 'testSubscriptionCanBeListed';
        $subscriptionName = 'sub';
        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $listSubscriptionOptions = new ListSubscriptionsOptions();
        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteTopic($topicName);
        $this->createTopic($topicInfo);
        $this->createSubscription($topicName, $subscriptionInfo);

        // Test
        $listSubscriptionsResult = $this->restProxy->listSubscriptions(
            $topicName,
            $listSubscriptionOptions
        );

        // Assert
        $this->assertNotNull($listSubscriptionsResult);
        $this->assertEquals(
            1,
            count($listSubscriptionsResult->getSubscriptionInfos())
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::getSubscription
     */
    public function testSubscriptionsDetailsMayBeFetched()
    {
        // Setup
        $topicName = 'testSubscriptionsDetailsMayBeFetched';
        $subscriptionName = 'sub';
        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteTopic($topicName);
        $this->createTopic($topicInfo);
        $this->createSubscription($topicName, $subscriptionInfo);

        // Test
        $subscriptionInfo = $this->restProxy->getSubscription(
            $topicName,
            $subscriptionName
        );

        // Assert
        $this->assertNotNull($subscriptionInfo);
        $this->assertEquals(
            $subscriptionName,
            $subscriptionInfo->getTitle()
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::deleteSubscription
     */
    public function testSubscriptionMayBeDeleted()
    {
        // Setup
        $topicName = 'testSubscriptionMayBeDeleted';
        $topicInfo = new TopicInfo($topicName);
        $subscriptionName = 'MySubscription';
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $listSubscriptionsOptions = new ListSubscriptionsOptions();
        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteTopic($topicName);
        $this->createTopic($topicInfo);
        $this->createSubscription($topicName, $subscriptionInfo);

        // Test
        $this->restProxy->deleteSubscription($topicName, $subscriptionName);
        $listSubscriptionsResult = $this->restProxy->listSubscriptions(
            $topicName,
            $listSubscriptionsOptions
        );
        $subscriptionInfo = $listSubscriptionsResult->getSubscriptionInfos();

        // Assert
        $this->assertNotNull($listSubscriptionsResult);
        $this->assertEquals(
            0,
            count($subscriptionInfo)
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::listSubscriptions
     */
    public function testSubscriptionCanBeListed()
    {
        // Setup
        $topicName = 'testSubscriptionMayBeDeleted';
        $topicInfo = new TopicInfo($topicName);
        $subscriptionName = 'MySubscription';
        $secondSubscriptionName = 'MySecondSubscription';
        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteSubscription($topicName, $secondSubscriptionName);
        $this->safeDeleteTopic($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $secondSubscriptionInfo =
            new SubscriptionInfo($secondSubscriptionName);
        $listSubscriptionOptions = new ListSubscriptionsOptions();

        $this->createTopic($topicInfo);
        $this->createSubscription($topicName, $subscriptionInfo);
        $this->createSubscription($topicName, $secondSubscriptionInfo);

        // Test
        $listSubscriptionsResult = $this->restProxy->listSubscriptions(
            $topicName,
            $listSubscriptionOptions
        );

        $subscriptionInfo = $listSubscriptionsResult->getSubscriptionInfos();

        $this->restProxy->deleteSubscription($topicName, $secondSubscriptionName);
        $this->restProxy->deleteSubscription($topicName, $subscriptionName);

        $emptyListSubscriptionsResult = $this->restProxy->listSubscriptions(
            $topicName,
            $listSubscriptionOptions
        );

        $emptySubscriptionInfo = $emptyListSubscriptionsResult->getSubscriptionInfos();

        // Assert
        $this->assertNotNull($listSubscriptionsResult);
        $this->assertNotNull($emptyListSubscriptionsResult);
        $this->assertEquals(
            2,
            count($subscriptionInfo)
        );

        $this->assertEquals(
            0,
            count($emptySubscriptionInfo)
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::sendTopicMessage
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::receiveMessage
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::receiveSubscriptionMessage
     */
    public function testSubscriptionWillReceiveMessage()
    {
        // Setup
        $topicName = 'testSubscriptionWillReceiveMessage';
        $subscriptionName = 'sub';
        $messageBody = '<p>testSubscriptionWillReceiveMessage</p>';
        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $brokeredMessage = new BrokeredMessage();
        $brokeredMessage->setBody($messageBody);
        $brokeredMessage->setContentType('text/html');
        $createTopicResult = $this->createTopic($topicInfo);
        $createSubscriptionResult = $this->createSubscription($topicName, $subscriptionInfo);
        $receiveMessageOptions = new ReceiveMessageOptions();
        $receiveMessageOptions->setTimeout(5);
        $receiveMessageOptions->setReceiveAndDelete();

        // Test
        $this->restProxy->sendTopicMessage($topicName, $brokeredMessage);
        $receivedMessage = $this->restProxy->receiveSubscriptionMessage($topicName, $subscriptionName, $receiveMessageOptions);

        // Assert
        $this->assertNotNull($createTopicResult);
        $this->assertNotNull($createSubscriptionResult);
        $this->assertNotNull($receivedMessage);
        $this->assertEquals(
            $messageBody,
            $receivedMessage->getBody()
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::sendTopicMessage
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::receiveMessage
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::receiveSubscriptionMessage
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::getSubscription
     */
    public function testReceiveMessageWillNotDeleteSubscription()
    {
        // Setup
        $topicName = 'testReceiveMessageWillNotDeleteSubscription';
        $subscriptionName = 'sub';
        $messageBody = '<p>testReceiveMessageWillNotDeleteSubscription</p>';
        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $brokeredMessage = new BrokeredMessage();
        $brokeredMessage->setBody($messageBody);
        $brokeredMessage->setContentType('text/html');
        $createTopicResult = $this->createTopic($topicInfo);
        $createSubscriptionResult = $this->createSubscription($topicName, $subscriptionInfo);
        $receiveMessageOptions = new ReceiveMessageOptions();
        $receiveMessageOptions->setTimeout(5);
        $receiveMessageOptions->setReceiveAndDelete();

        // Test
        $this->restProxy->sendTopicMessage($topicName, $brokeredMessage);
        $receivedMessage = $this->restProxy->receiveSubscriptionMessage($topicName, $subscriptionName, $receiveMessageOptions);
        $subscriptionInfo = $this->restProxy->getSubscription($topicName, $subscriptionName);

        // Assert
        $this->assertNotNull($subscriptionInfo);
        $this->assertEquals(
            $subscriptionName,
            $subscriptionInfo->getTitle()
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::createRule
     * @covers RuleInfo::parseXml
     * @covers RuleDescription::create
     */
    public function testRulesCanBeCreatedOnSubscription()
    {
        // Setup
        $topicName = 'testRulesCanBeCreatedOnSubscription';
        $subscriptionName = 'sub';
        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $ruleName = 'MyRule';
        $ruleInfo = new RuleInfo($ruleName);
        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteTopic($topicName);
        $this->createTopic($topicInfo);
        $this->createSubscription($topicName, $subscriptionInfo);

        // Test
        $createRuleResult = $this->createRule($topicName, $subscriptionName, $ruleInfo);
        $ruleInfo = $this->restProxy->getRule($topicName, $subscriptionName, $ruleName);

        // Assert
        $this->assertNotNull($createRuleResult);
        $this->assertEquals(
            $ruleName,
            $ruleInfo->getTitle()
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::createRule
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::listRules
     */
    public function testRulesCanBeListedAndDefaultRuleIsPrecreated()
    {
        // Setup
        $topicName = 'testRulesCanBeListedAndDefaultRuleIsPrecreated';
        $subscriptionName = 'sub';
        $ruleName = 'MyRule';
        $secondRuleName = 'MyRule2';
        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $ruleInfo = new RuleInfo($ruleName);
        $secondRuleInfo = new RuleInfo($secondRuleName);
        $listRulesOptions = new ListRulesOptions();

        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteTopic($topicName);

        $this->createTopic($topicInfo);
        $this->createSubscription($topicName, $subscriptionInfo);

        // Test
        $this->createRule($topicName, $subscriptionName, $ruleInfo);
        $this->createRule($topicName, $subscriptionName, $secondRuleInfo);
        $listRulesResult = $this->restProxy->listRules($topicName, $subscriptionName, $listRulesOptions);

        // Assert
        $this->assertNotNull($listRulesResult);
        $this->assertEquals(3, count($listRulesResult->getRuleInfos()));
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::getRule
     */
    public function testRuleDetailsMayBeFetched()
    {
        // Setup
        $topicName = 'testRuleDetailsMayBeFetched';
        $subscriptionName = 'sub';
        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteTopic($topicName);
        $this->createTopic($topicInfo);
        $this->createSubscription($topicName, $subscriptionInfo);

        // Test
        $getRuleResult = $this->restProxy->getRule($topicName, $subscriptionName, Resources::DEFAULT_RULE_NAME);

        // Assert
        $this->assertNotNull($getRuleResult);
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::getRule
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::deleteRule
     */
    public function testRuleMayBeDeleted()
    {
        // Setup
        $topicName = 'testRuleMayBeDeleted';
        $subscriptionName = 'sub';
        $firstRuleName = 'RuleNumberOne';
        $secondRuleName = 'RuleNumberTwo';

        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $firstRuleInfo = new RuleInfo($firstRuleName);
        $secondRuleInfo = new RuleInfo($secondRuleName);
        $listRulesOptions = new ListRulesOptions();

        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteTopic($topicName);

        $this->createTopic($topicInfo);
        $this->createSubscription($topicName, $subscriptionInfo);
        $this->createRule($topicName, $subscriptionName, $firstRuleInfo);
        $this->createRule($topicName, $subscriptionName, $secondRuleInfo);

        // Test
        $this->restProxy->deleteRule($topicName, $subscriptionName, $secondRuleName);
        $this->restProxy->deleteRule($topicName, $subscriptionName, $firstRuleName);
        $this->restProxy->deleteRule($topicName, $subscriptionName, Resources::DEFAULT_RULE_NAME);

        $listRulesResult = $this->restProxy->listRules($topicName, $subscriptionName, $listRulesOptions);
        $ruleInfo = $listRulesResult->getRuleInfos();

        // Assert
        $this->assertNotNull($ruleInfo);
        $this->assertEquals(0, count($ruleInfo));
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::listRules
     * @covers TopicInfo::parseXml
     * @covers TopicDescription::create
     * @covers SubscriptionInfo::parseXml
     * @covers SubscriptionDescription::create
     * @covers RuleInfo::parseXml
     * @covers RuleDescription::create
     */
    public function testListRulesDeserializePropertiesOfSqlFilter()
    {
        // Setup
        $topicName = 'testListRulesDeserializePropertiesOfSqlFilter';
        $subscriptionName = 'sub';
        $expected = 'OrderID=123';

        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteTopic($topicName);

        $createTopicResult = $this->createTopic($topicInfo);
        $subscriptionInfo = $this->createSubscription(
            $topicName,
            $subscriptionInfo
        );
        $rule = new RuleInfo('one');
        $rule->withSqlFilter('OrderID=123');
        $this->restProxy->createRule($topicName, $subscriptionName, $rule);

        // Test
        $listRulesResult = $this->restProxy->listRules($topicName, $subscriptionName);
        $ruleInfo = $listRulesResult->getRuleInfos();
        $ruleInfoInstance = $ruleInfo[1];
        $actualFilter = $ruleInfoInstance->getFilter();
        $actual = $actualFilter->getSqlExpression();

        // Assert
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::getRule
     */
    public function testRulesMayHaveActionAndFilter()
    {
        // Setup
        $topicName = 'testRulesMayHaveActionAndFilter';
        $subscriptionName = 'sub';

        $topicInfo = new TopicInfo($topicName);
        $subscriptionInfo = new SubscriptionInfo($subscriptionName);
        $this->safeDeleteSubscription($topicName, $subscriptionName);
        $this->safeDeleteTopic($topicName);

        $createTopicResult = $this->createTopic($topicInfo);
        $subscriptionInfo = $this->createSubscription(
            $topicName,
            $subscriptionInfo
        );
        $expectedRuleOne = new RuleInfo('one');
        $expectedRuleOne->withCorrelationFilter('my-id');

        $expectedRuleTwo = new RuleInfo('two');
        $expectedRuleTwo->withTrueFilter();

        $expectedRuleThree = new RuleInfo('three');
        $expectedRuleThree->withFalseFilter();

        $expectedRuleFour = new RuleInfo('four');
        $expectedRuleFour->withEmptyRuleAction();

        $expectedRuleFive = new RuleInfo('five');
        $expectedRuleFive->withSqlRuleAction('SET x = 5');

        $expectedRuleSix = new RuleInfo('six');
        $expectedRuleSix->withSqlFilter('x != 5');

        // Test
        $actualRuleOne = $this->restProxy->createRule(
            $topicName,
            $subscriptionName,
            $expectedRuleOne
        );

        $actualRuleTwo = $this->restProxy->createRule(
            $topicName,
            $subscriptionName,
            $expectedRuleTwo
        );

        $actualRuleThree = $this->restProxy->createRule(
            $topicName,
            $subscriptionName,
            $expectedRuleThree
        );

        $actualRuleFour = $this->restProxy->createRule(
            $topicName,
            $subscriptionName,
            $expectedRuleFour
        );

        $actualRuleFive = $this->restProxy->createRule(
            $topicName,
            $subscriptionName,
            $expectedRuleFive
        );

        $actualRuleSix = $this->restProxy->createRule(
            $topicName,
            $subscriptionName,
            $expectedRuleSix
        );

        // Assert
        $this->assertNotNull($createTopicResult);
        $this->assertNotNull($subscriptionInfo);

        $this->assertInstanceOf(
            'WindowsAzure\ServiceBus\Models\CorrelationFilter',
            $actualRuleOne->getFilter()
        );

        $this->assertInstanceOf(
            'WindowsAzure\ServiceBus\Models\TrueFilter',
            $actualRuleTwo->getFilter()
        );

        $this->assertInstanceOf(
            'WindowsAzure\ServiceBus\Models\FalseFilter',
            $actualRuleThree->getFilter()
        );

        $this->assertInstanceOf(
            'WindowsAzure\ServiceBus\Models\EmptyRuleAction',
            $actualRuleFour->getAction()
        );

        $this->assertInstanceOf(
            'WindowsAzure\ServiceBus\Models\SqlRuleAction',
            $actualRuleFive->getAction()
        );

        $this->assertInstanceOf(
            'WindowsAzure\ServiceBus\Models\SqlFilter',
            $actualRuleSix->getFilter()
        );
    }

    /**
     * @covers WindowsAzure\ServiceBus\ServiceBusRestProxy::receiveQueueMessage
     */
    public function testMessageMayHaveCustomProperties()
    {
        // Setup
        $queueName = 'testMessageMayHaveCustomProperties';
        $queueDescription = new QueueDescription();
        $queueInfo = new QueueInfo($queueName, $queueDescription);
        $expectedTestStringValue = 'testStringValue';
        $expectedTestIntValue = 38;
        $expectedTestDoubleValue = 3.14159;
        $expectedTestBooleanValue = true;
        $expectedTestBooleanFalseValue = false;
        $expectedTestArrayValue = array(2, 3, 5, 7);

        $this->safeDeleteQueue($queueName);
        $this->createQueue($queueInfo);
        $brokeredMessage = new BrokeredMessage();

        $brokeredMessage->setProperty('testString', $expectedTestStringValue);
        $brokeredMessage->setProperty('testInt', $expectedTestIntValue);
        $brokeredMessage->setProperty('testDouble', $expectedTestDoubleValue);
        $brokeredMessage->setProperty('testBoolean', $expectedTestBooleanValue);
        $brokeredMessage->setProperty('testBooleanFalse', $expectedTestBooleanFalseValue);

        $this->restProxy->sendQueueMessage($queueName, $brokeredMessage);
        $receiveMessageOptions = new ReceiveMessageOptions();
        $receiveMessageOptions->setTimeout(5);
        $receiveMessageOptions->setReceiveAndDelete();

        // Test
        $receivedMessage = $this->restProxy->receiveQueueMessage(
            $queueName,
            $receiveMessageOptions
        );

        // Assert
        $this->assertNotNull($receivedMessage);
        $this->assertEquals(
            $expectedTestStringValue,
            $receivedMessage->getProperty('testString')
        );

        $this->assertEquals(
            $expectedTestIntValue,
            $receivedMessage->getProperty('testInt')
        );

        $this->assertEquals(
            $expectedTestDoubleValue,
            $receivedMessage->getProperty('testDouble')
        );

        $this->assertEquals(
            $expectedTestBooleanValue,
            $receivedMessage->getProperty('testBoolean')
        );

        $this->assertEquals(
            $expectedTestBooleanFalseValue,
            $receivedMessage->getProperty('testBooleanFalse')
        );
    }
}
