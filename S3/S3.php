<?php

namespace Riff\S3;

use Aws\S3\S3Client;

class S3
{
    private $bucket;
    private $client;

    public function __construct($config)
    {
        $this->config = $config;
        add_action('init', [$this, 'init']);
    }
    
    public function init()
    {
        $this->bucket = $this->config['bucket'];
        $this->client = S3Client::factory([
            'region' => $this->config['region'],
            'version' => $this->config['version'],
            'credentials' => [
                'key' => $this->config['key'],
                'secret' => $this->config['secret'],
            ],
        ]);

        $this->client->registerStreamWrapper();

        add_action('upload_dir', [$this, 'filterUploadDir']);

    }

    public function getBasedir()
    {
        return 's3://' . $this->bucket;
    }
    
    public function getBaseurl()
    {
        return 'https://' . $this->bucket . '.s3.amazonaws.com';
    }

    public function filterUploadDir($args)
    {

        $basedir = $this->getBasedir();
        $baseurl = $this->getBaseurl();
        $args['path'] = str_replace($args['basedir'], $basedir, $args['path']);
        $args['url'] = str_replace($args['baseurl'], $baseurl, $args['url']);
        $args['basedir'] = $basedir;
        $args['baseurl'] = $baseurl;

        return $args;
    }
}
