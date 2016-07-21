<?php

namespace Becklyn\BugsnagBundle\Report\Transformer;

use Bugsnag\Report;
use Becklyn\BugsnagBundle\Monolog\Handler\BugsnagMonologHandler;
use Becklyn\BugsnagBundle\Report\ReportTransformer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 *
 */
class NotFoundTransformer implements ReportTransformer
{
    /**
     * @inheritDoc
     */
    public function transformReport (Report $report, array $monologData)
    {
        if (isset($monologData["context"]["exception"]) && $monologData["context"]["exception"] instanceof NotFoundHttpException)
        {
            $report->setSeverity(BugsnagMonologHandler::BUGSNAG_SEVERITY_INFO);
        }
    }
}
