<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Pusher extends BaseConfig
{
    /**
     * Pusher App ID
     */
    public string $appId;

    /**
     * Pusher App Key (Public Key)
     */
    public string $appKey;

    /**
     * Pusher App Secret
     */
    public string $appSecret;

    /**
     * Pusher Cluster
     */
    public string $appCluster;

    /**
     * Use TLS for secure connection
     */
    public bool $useTLS;

    public function __construct()
    {
        $this->appId      = env('pusher.appId', '');
        $this->appKey     = env('pusher.appKey', '');
        $this->appSecret  = env('pusher.appSecret', '');
        $this->appCluster = env('pusher.appCluster', 'ap1');
        $this->useTLS     = env('pusher.useTLS', true);
    }
}
