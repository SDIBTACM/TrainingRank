<?php
/**
 *
 * Created by Dream.
 * User: Boxjan
 * Datetime: 2019-01-13 18:18
 */

namespace App\Units\Monolog;

use Monolog\Formatter\FormatterInterface;

class AppLogFormatter implements FormatterInterface
{
    public function format(array $record)
    {
        return sprintf("[%s] [%s] [%s] %s\n", $record['datetime']->format('Y-m-d H:i:s.u O'), $record['level_name'], getmypid(), $record['message']);
    }

    public function formatBatch(array $records)
    {
        foreach ($records as $key => $record) {
            $records[$key] = $this->format($record);
        }

        return $records;
    }

}

