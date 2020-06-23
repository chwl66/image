<?php

declare (strict_types=1);

namespace app\command;


use EasyTask\Task;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class BaseTask extends Command
{
    /**
     * @var string 类名称
     */
    private $class;
    /**
     * @var string 方法名称
     */
    private $action = 'execute';
    /**
     * @var String 任务别名
     */
    private $alas;
    /**
     * @var int 定时器间隔
     */
    private $time = 1;
    /**
     * @var int 定时器占用进程数
     */
    private $used = 1;

    protected function initialTask($class, $action, $alas, $time = 1, $used = 1)
    {
        $this->class = $class;
        $this->action = $action;
        $this->alas = $alas;
        $this->time = $time;
        $this->used = $used;
    }


    protected function configure()
    {
        //设置名称为task
        $this->setName('task')
            //增加一个命令参数
            ->addArgument('action', Argument::OPTIONAL, "action");
    }

    protected function execute(Input $input, Output $output)
    {
        //获取输入参数
        $action = trim($input->getArgument('action'));
        // 配置任务
        $task = new Task();
        $task->setRunTimePath(app()->getRuntimePath());
        $task->addClass($this->class, $this->action, $this->alas, $this->time, $this->used);
        // 根据命令执行
        if ($action == 'start') {
            $task->start();
        } elseif ($action == 'status') {
            $task->status();
        } elseif ($action == 'stop') {
            $task->stop(true);
        } else {
            exit('Command is not exist');
        }
    }
}