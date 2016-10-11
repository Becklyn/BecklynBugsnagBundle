<?php

namespace Becklyn\BugsnagBundle\Client;


/**
 *
 */
class JavaScript
{
    /**
     * @var null|string
     */
    private $apiKey;



    /**
     * @param null|string $apiKey
     */
    public function __construct ($apiKey = null)
    {
        $this->apiKey = $apiKey;
    }



    /**
     * Generates the HTML tag for monitoring the application
     *
     * @return string
     */
    public function generateClientHtml ()
    {
        if (null !== $this->apiKey)
        {
            return '<script src="https://d2wy8f7a9ursnm.cloudfront.net/bugsnag-2.min.js" data-apikey="' . $this->apiKey . '"></script>'
                . '<script>Bugsnag.user={id:"?"};Bugsnag.metaData={request:{clientIp:"[filtered]"}};</script>';
        }

        return "";
    }
}
