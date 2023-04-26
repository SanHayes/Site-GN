<?php
namespace app\index\controller;
use think\Config;
use think\Controller;
use think\Db;

class Mail extends Controller
{
    function sendMail()
    {
        $email = input('email');
        if(!$email){
            return json(['code'=>-102,'data'=>[],'msg'=>'请输入邮箱']);
        }else{
            if(check_user('username',$email)){
                return json(['code'=>-50,'data'=>[],'msg'=>'该用户名已存在']);
            }
        }
        $code = mt_rand(100000,999999);
        $content = '【Hantec团队】Your authentication code is '.$code.' , valid for minutes at 10';
        $user_code = Db::name('usercode')->where(['number'=>$email,'type'=>'mail'])->find();
        if($user_code){
            if(time() - $user_code['time']  < 60){
                return json(['code'=>-68,'data'=>null,'msg'=>'已发送验证码，请60秒后重试']);
            }
        }
        
        if($this->send_mail($email,'Hantec团队',$content)){
            $data['number'] = $email;
            $data['code'] = $code;
            $data['type'] = 'mail';
            $data['time'] = time();
            if(!$user_code){
                if(Db::name('usercode')->insertGetId($data)){
                    return json(['code'=>200,'data'=>$email,'msg'=>'success']);
                }else{
                    return json(['code'=>-62,'data'=>null,'msg'=>'操作失败2！']);
                }
            }else{
                if(Db::name('usercode')->where(['number'=>$email])->update($data)){
                    return json(['code'=>200,'data'=>$email,'msg'=>'success']);
                }else{
                    return json(['code'=>-62,'data'=>null,'msg'=>'操作失败3！']);
                }
            }
        }else{
            return json(['code'=>-62,'data'=>null,'msg'=>'操作失败1！']);
        }
    }
    function send_mail($tomail,$subject = '', $body = '')
    {
        $config = Config::get('think_email');
        vendor('phpmailer.PHPMailer');
        vendor('phpmailer.Exception');
        vendor('phpmailer.SMTP');
        $mail = new \PHPMailer\PHPMailer\PHPMailer();           //实例化PHPMailer对象
        $mail->CharSet = 'UTF-8';           //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();                    // 设定使用SMTP服务
        $mail->SMTPDebug = 0;               // SMTP调试功能 0=关闭 1 = 错误和消息 2 = 消息
        $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        $mail->SMTPSecure = $config['SMTP_Secure'];                 // 使用安全协议
        $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
        $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
        $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
        $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
        $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
        $mail->Subject    = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($tomail);
        $mail->addCustomHeader("X-Priority", "3");
        $mail->addCustomHeader("X-MSMail-Priority", "Normal");
        $mail->addCustomHeader("X-Mailer", "Microsoft Outlook Express 6.00.2900.2869");   //本文以outlook名义发送邮件，不会被当作垃圾邮件
        $mail->addCustomHeader("X-MimeOLE", "Produced By Microsoft MimeOLE V6.00.2900.2869");
        $mail->addCustomHeader("ReturnReceipt", "1");
        file_put_contents('/www/wwwroot/Site/Web/99/mail.txt',json_encode($mail->ErrorInfo));
        return $mail->Send() ? true : false;
    }
}
