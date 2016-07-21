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
     * @inheritdoc
     */
    public function getFunctions ()
    {
        return [
            new \Twig_SimpleFunction("bugsnagJavaScriptClient", [$this->javaScriptClient, "generateClientHtml"], ["is_safe" => ["html"]]),
        ];
    }



    /**
     * @inheritdoc
     */
    public function getName ()
    {
        return self::class;
    }
}
