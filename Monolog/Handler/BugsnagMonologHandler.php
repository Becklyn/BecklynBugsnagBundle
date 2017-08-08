<?php

namespace Becklyn\BugsnagBundle\Monolog\Handler;

use Bugsnag\Client;
use Bugsnag\Report;
use Becklyn\BugsnagBundle\Report\ReportTransformerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;


/**
 *
 */
class BugsnagMonologHandler extends AbstractProcessingHandler
{
    /**
     * The bugsnag severity codes
     */
    const BUGSNAG_SEVERITY_INFO = "info";
    const BUGSNAG_SEVERITY_WARNING = "warning";
    const BUGSNAG_SEVERITY_ERROR = "error";

    /**
     * PSR error codes mapped to bugsnag error codes
     *
     * @var string[]
     */
    private static $severityMapping = [
        Logger::DEBUG     => self::BUGSNAG_SEVERITY_INFO,
        Logger::INFO      => self::BUGSNAG_SEVERITY_INFO,
        Logger::NOTICE    => self::BUGSNAG_SEVERITY_INFO,
        Logger::WARNING   => self::BUGSNAG_SEVERITY_WARNING,
        Logger::ERROR     => self::BUGSNAG_SEVERITY_ERROR,
        Logger::CRITICAL  => self::BUGSNAG_SEVERITY_ERROR,
        Logger::ALERT     => self::BUGSNAG_SEVERITY_ERROR,
        Logger::EMERGENCY => self::BUGSNAG_SEVERITY_ERROR,
    ];


    /**
     * @var Client
     */
    private $client;


    /**
     * @var ReportTransformerInterface[]
     */
    private $reportTransformers = [];



    /**
     * @param Client $client
     * @param int    $level
     * @param bool   $bubble
     */
    public function __construct (Client $client, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->client = $client;
    }



    /**
     * @param ReportTransformerInterface $transformer
     */
    public function addReportTransformer (ReportTransformerInterface $transformer)
    {
        $this->reportTransformers[] = $transformer;
    }



    /**
     * @inheritdoc
     */
    protected function write (array $record)
    {
        $severity = array_key_exists($record['level'], self::$severityMapping)
            ? self::$severityMapping[$record['level']]
            : self::BUGSNAG_SEVERITY_ERROR;

        $callback = function (Report $report) use ($severity, $record)
        {
            $report->setSeverity($severity);

            foreach ($this->reportTransformers as $transformer)
            {
                $returnValue = $transformer->transformReport($report, $record);

                // if one of the handlers decides that the report should not be sent, abort
                if (false === $returnValue)
                {
                    return false;
                }
            }
        };


        if (isset($record['context']['exception']))
        {
            $this->client->notifyException($record['context']['exception'], $callback);
        }
        else
        {
            $this->client->notifyError("error", (string) $record['message'], $callback);
        }
    }
}
