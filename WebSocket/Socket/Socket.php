<?php declare(strict_types=1);

namespace Socket;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class Socket implements MessageComponentInterface
{

    public const OPENSSL_METHOD = 'AES-128-ECB';

    /** @var \SplObjectStorage[] $users */
    protected $users = [];

    /**
     * @param string $hash
     * @return array
     */
    public static function getUserIdentifier(string $hash): string
    {
        return openssl_decrypt($hash, static::OPENSSL_METHOD, USER_AUTH_SALT);
    }

    /**
     * @param string $identifier
     * @return string
     */
    public static function generateUserHash(string $identifier): string
    {
        return openssl_encrypt($identifier, static::OPENSSL_METHOD, USER_AUTH_SALT);
    }

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

        $user = self::getUserIdentifier($conn->httpRequest->getUri()->getQuery());

        // Check if the provided hash was invalid
        if (!$user) {
            $conn->close();
            return;
        }

        $conn->clientId = $user;

        // Setup a storage for the user's connections
        $this->users[self::userIdentifier($user)][] = $conn;
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).  SendMessage to $conn will not result in an error if it has already been closed.
     * @param ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    public function onClose(ConnectionInterface $conn): void
    {
        $conn->close();

        if (count($this->users[self::userIdentifier($conn->clientId)]) === 0) {
            unset($this->users[self::userIdentifier($conn->clientId)]);
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

        if (count($this->users[self::userIdentifier($conn->clientId)]) === 0) {
            unset($this->users[self::userIdentifier($conn->clientId)]);
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

        if(is_numeric($data['to_user']) && self::isSysProcess($from->clientId)){
            $userConnections = $this->users[self::userIdentifier($data['to_user'])];

            foreach ($userConnections as $connection) {
                /** @var ConnectionInterface $connection */
                $connection->send(json_encode($data['data']));
            }
        }
    }


    /**
     * @param string $user
     * @return string
     */
    private static function userIdentifier($user): string
    {
        return $user === WS_SYSTEM_USER_ID ? $user : 'user_' . $user;
    }

    /**
     * @param string $user
     * @return bool
     */
    private static function isSysProcess($user): bool
    {
        return self::userIdentifier($user) === WS_SYSTEM_USER_ID;
    }
}
