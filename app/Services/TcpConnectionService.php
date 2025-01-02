<?php

namespace App\Services;

use Exception;

class TcpConnectionService
{
    /**
     * Open a TCP connection to the given host and port
     *
     * @param string $hostname
     * @param int $port
     * @param int $timeout
     * @return resource
     * @throws Exception
     */
    public function connect(string $hostname, int $port, int $timeout = 5)
    {
        $errno = 0;
        $errstr = '';

        $socket = @fsockopen(
            $hostname,
            $port,
            $errno,
            $errstr,
            timeout: $timeout
        );

        if (!$socket) {
            throw new Exception($errstr, $errno);
        }

        return $socket;
    }

    /**
     * Close a TCP connection
     *
     * @param resource $socket
     * @return void
     */
    public function close($socket): void
    {
        if (is_resource($socket)) {
            fclose($socket);
        }
    }
}
