<?php

/**
 * ログを出力する
 *
 * @param mixed $data
 * @param int|null $line
 */
function log_output($data, $line = null)
{
    file_put_contents(getenv('LOG_PATH'), sprintf('[%d] %s' . PHP_EOL, $line, var_export($data, true)), FILE_APPEND);
}
