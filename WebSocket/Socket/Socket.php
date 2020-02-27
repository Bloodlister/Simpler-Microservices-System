<?php declare(strict_types=1);

namespace Socket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Socket implements MessageComponentInterface
{

    protected const WS_SYSTEMS = [
        'users_back',
        'users_front',
    ];

    /** @var \SplObjectStorage[] $users */
    protected $users = [];

    /**
     * When a new connection is opened it will be passed to this method
     * @param ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    public function onOpen(ConnectionInterface $conn): void
    {
        if(!$conn->httpRequest->getUri()->getQuery()){
            $conn->close();
            return;
        }

        $userUid = $conn->httpRequest->getUri()->getQuery();

        // Check if the provided hash was invalid
        if (!$userUid) {
            $conn->close();
            return;
        }

        $conn->clientId = $userUid;

        // Setup a storage for the user's connections
        $this->users[$userUid][] = $conn;
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $conn->close();

        if (count($this->users[$conn->clientId]) === 0) {
            unset($this->users[$conn->clientId]);
        }
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown,
     * the Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @throws \Exception
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $conn->close();

        if (count($this->users[$conn->clientId]) === 0) {
            unset($this->users[$conn->clientId]);
        }
    }

    /**
     * Triggered when a client sends data through the socket
     * @param \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param string $msg The message received
     * @throws \Exception
     */
    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $data = json_decode($msg, true);

        if($data['receiver'] && self::isSysProcess($from->clientId)){
            $userConnections = $this->users[$data['receiver']];

            foreach ($userConnections as $connection) {
                /** @var ConnectionInterface $connection */
                $connection->send(json_encode($data['data']));
            }
        }
    }

    /**
     * @param string $user
     * @return bool
     */
    private static function isSysProcess($user): bool
    {
        return in_array($user, static::WS_SYSTEMS);
    }
}
