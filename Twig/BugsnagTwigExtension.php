<?php

namespace Becklyn\BugsnagBundle\Twig;

use Becklyn\BugsnagBundle\Client\JavaScript;


/**
 *
 */
class BugsnagTwigExtension extends \Twig_Extension
{
    /**
     * @var JavaScript
     */
    private $javaScriptClient;



    /**
     * @param JavaScript $javaScriptClient
     */
    public function __construct (JavaScript $javaScriptClient)
    {
        $this->javaScriptClient = $javaScriptClient;
    }



    /**
     * @inheritDoc
     */
    public function getFunctions ()
    {
        return [
            new \Twig_SimpleFunction("bugsnagJavaScriptClient", [$this->javaScriptClient, "generateClientHtml"], ["is_safe" => ["html"]]),
        ];
    }



    /**
     * @inheritDoc
     */
    public function getName ()
    {
        return self::class;
    }
}
