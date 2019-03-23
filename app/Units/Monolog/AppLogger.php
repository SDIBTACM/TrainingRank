<?php
/**
 *
 * Created by Dream.
 * User: Boxjan
 * Datetime: 2019-01-13 19:55
 */

namespace App\Units\Monolog;


class AppLogger
{
    /**
     * 自定义日志实例
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new AppLogFormatter());
        }
    }
}