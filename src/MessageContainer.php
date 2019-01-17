<?php

namespace ipl\Stdlib;

trait MessageContainer
{
    /** @var array Current messages */
    protected $messages = [];

    /**
     * Get whether there are any messages
     *
     * @return  bool
     */
    public function hasMessages()
    {
        return ! empty($this->messages);
    }

    /**
     * Get all messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set given messages, overrides existing ones
     *
     * @param string[] $messages
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->clearMessages();
        foreach ($messages as $message) {
            $this->addMessage($message);
        }

        return $this;
    }

    /**
     * Add a single message
     *
     * @param string $message
     * @param mixed ... Other optional parameters for sprintf-style messages
     * @return $this
     */
    public function addMessage($message)
    {
        $args = func_get_args();
        array_shift($args);
        if (empty($args)) {
            $this->messages[] = $message;
        } else {
            $this->messages[] = vsprintf($message, $args);
        }

        return $this;
    }

    /**
     * Drop eventually existing messages
     *
     * @return MessageContainer
     */
    public function clearMessages()
    {
        $this->messages = [];

        return $this;
    }
}
