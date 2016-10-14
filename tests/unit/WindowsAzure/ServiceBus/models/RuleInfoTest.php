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

namespace Tests\unit\WindowsAzure\ServiceBus\models;

use WindowsAzure\ServiceBus\Internal\Action;
use WindowsAzure\ServiceBus\Internal\Filter;
use WindowsAzure\ServiceBus\Models\RuleDescription;
use WindowsAzure\ServiceBus\Models\RuleInfo;

/**
 * Unit tests for class WrapAccessTokenResult.
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
class RuleInfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers WindowsAzure\ServiceBus\Models\RuleInfo::__construct
     */
    public function testRuleInfoConstructor()
    {
        // Setup
        $expected = 'testRuleInfoName';

        // Test
        $ruleInfo = new RuleInfo($expected);
        $actual = $ruleInfo->getTitle();

        // Assert
        $this->assertNotNull($ruleInfo);
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    /** 
     * @covers RuleInfo::getRuleDescription
     * @covers RuleInfo::setRuleDescription
     */
    public function testGetSetRuleDescription()
    {
        // Setup
        $expected = new RuleDescription();
        $ruleInfo = new RuleInfo();

        // Test
        $ruleInfo->setRuleDescription($expected);
        $actual = $ruleInfo->getRuleDescription();

        // Assert 
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    /** 
     * @covers RuleInfo::getFilter
     * @covers RuleInfo::setFilter
     */
    public function testGetSetFilter()
    {
        // Setup
        $expected = new Filter();
        $ruleInfo = new RuleInfo();

        // Test
        $ruleInfo->setFilter($expected);
        $actual = $ruleInfo->getFilter();

        // Assert 
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    /** 
     * @covers RuleInfo::getAction
     * @covers RuleInfo::setAction
     */
    public function testGetSetAction()
    {
        // Setup
        $expected = new Action();
        $ruleInfo = new RuleInfo();

        // Test
        $ruleInfo->setAction($expected);
        $actual = $ruleInfo->getAction();

        // Assert 
        $this->assertEquals(
            $expected,
            $actual
        );
    }

    /** 
     * @covers RuleInfo::getName
     * @covers RuleInfo::setName
     */
    public function testGetSetName()
    {
        // Setup
        $expected = 'testName';
        $ruleInfo = new RuleInfo();

        // Test
        $ruleInfo->setName($expected);
        $actual = $ruleInfo->getName();

        // Assert 
        $this->assertEquals(
            $expected,
            $actual
        );
    }
}
