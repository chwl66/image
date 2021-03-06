<?php

declare (strict_types=1);

namespace app\command;



use think\console\Input;
use think\console\Output;

class ImageUpdate extends BaseTask
{

    protected function execute(Input $input, Output $output)
    {
        $this->initialTask(
            'app\crontab\Distribute',
            'execute',
            'ImageUpdate',
            (int)env('CRON_IMAGEUPDATE_TIME'),
            1
        );
        parent::execute($input, $output); // TODO: Change the autogenerated stub
    }
}