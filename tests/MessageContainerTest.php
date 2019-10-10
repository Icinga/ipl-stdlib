<?php

namespace ipl\Tests\Stdlib;

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
     * @return \ipl\Stdlib\MessageContainer
     */
    protected function getContainer()
    {
        return $this->getMockForTrait('ipl\\Stdlib\\MessageContainer');
    }
}
