<?php


namespace app\controller\ajax;


use app\provider\Qrcode;
use think\facade\Request;

class Email
{
    public function test()
    {
        $domain = Request::domain();
        $url = url('', [], '', true);
        $sitename = hidove_config_get('system.base.sitename');
        $toAddresses = hidove_config_get('system.email.replyMail');
        $date = date('Y-m-d H:i:s');
        $qrcode = new Qrcode();
        $img2base64 = $qrcode->writeDataUri($url);

        $content = <<<ETO
  <h2 style="color:#f60">
  这是一封测试邮件！</h2>
  <img src="$img2base64"> 
    <p>您可以扫描二维码进入移动版的 $sitename 。</p>
    <p>$sitename 提供免费图片托管。
        <a href="$url" rel="noopener" target="_blank">$domain</a>
    </p>
<p>当前邮件发送时间：$date</p>
ETO;
        return $this->send('这是一封测试邮件', $toAddresses, $content);

    }
    public function send($subject,$toAddresses,$content)
    {
        try {
            $res = (new \app\provider\Email())->run($subject, $toAddresses, $content);
            if ($res) {
                return msg(200, '发送成功');
            }
            return msg(400, '发送失败');
        } catch (\Exception $e) {
            return msg(400, $e->getMessage());
        }

    }

}