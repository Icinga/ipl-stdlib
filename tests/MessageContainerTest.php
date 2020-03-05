<?php

namespace ipl\Tests\Stdlib;

use ipl\Stdlib\MessageContainer;

class MessageContainerTest extends TestCase
{
    public function testGetNoMessagesWhenEmpty()
    {
        $this->assertEmpty(
            $this->getContainer()->getMessages()
        );
    }

    public function testMessagesCanBeAdded()
    {
        $this->assertEquals(
            $this->getContainer()
                ->addMessage('Message 1')
                ->addMessage(('Message 2'))
                ->getMessages(),
            ['Message 1', 'Message 2']
        );
    }

    public function testMessagesCanBeCleared()
    {
        $this->assertEmpty(
            $this->getContainer()
                ->addMessage('Message 1')
                ->addMessage(('Message 2'))
                ->clearMessages()
                ->getMessages()
        );
    }

    /**
     * @return MessageContainer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getContainer()
    {
        return $this->getMockForTrait(MessageContainer::class);
    }
}
