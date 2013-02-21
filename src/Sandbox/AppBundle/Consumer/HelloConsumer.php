<?php

namespace Sandbox\AppBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bridge\Monolog\Logger;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("app.hello_consumer", public=false)
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class HelloConsumer implements ConsumerInterface
{
    protected $logger;

    /**
     * @DI\InjectParams()
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function execute(AMQPMessage $msg)
    {
        $this->logger->notice($msg->body);
        echo $msg->body."\n";
    }
}