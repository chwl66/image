<?php


namespace api\provider;

use app\provider\Email;
use think\facade\Cache;

class imageApiProvider
{
    public function sendMailReminder($msg = 'Cookie已失效！请及时更新Cookie！', $apiType = "Qpic")
    {
        $emailReminder = Cache::get('EmailReminder_'.$apiType);
        $date = date('Y-m-d H:i:s');
        $lastEmailReminderDate = date('Y-m-d H:i:s', $emailReminder);
        $subject = "【重要】 $date $apiType CDN 出现错误！";
        $content = <<<EOT
apiType：$apiType  错误信息：$msg<br/>
当前时间：  $date
上次发送邮件时间：$lastEmailReminderDate
<br/><p style="color: red;">如未及时更新，本系统将一小时提醒一次！</p>
EOT;

        if (time() - $emailReminder > 3600) {
            Cache::set($apiType . 'EmailReminder', time());
            (new Email())->run($subject,hidove_config_get('system.email.system.email.replyMail'),$content);
        }
    }
}