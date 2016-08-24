<?php

namespace Becklyn\BugsnagBundle\Report;

use Bugsnag\Report;


/**
 *
 */
interface ReportTransformerInterface
{
    /**
     * Transforms the report
     *
     * @param Report      $report
     * @param array $monologData
     */
    public function transformReport (Report $report, array $monologData);
}
