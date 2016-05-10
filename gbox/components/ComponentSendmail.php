<?php
namespace app\components;
use Gbox\base\Component;
use PHPMailer\PHPMailer;
class ComponentSendmail extends Component
{
    private $mail;
    public function init ()
    {
        require_once 'ConfigSendmail/sendmail.php';
        require_once 'ConfigSendmail/smtp.php';

        $this->mail             = new PHPMailer();
        // $this->mail->SMTPDebug  = 3;  
        // $this->mail->Debugoutput = 'html';
        $this->mail->IsSMTP();
        $this->mail->Host       = $this->params['config']['host'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Port       = 465;
        $this->mail->Username   = $this->params['config']['user'];
        $this->mail->Password   = $this->params['config']['pass'];
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->CharSet    = 'UTF-8';
        $this->mail->setFrom($this->params['config']['from'], $this->params['config']['name']);
        $this->mail->setLanguage('es');

        /*if (!empty($params['responder']))
            $this->mail->AddReplyTo($params['responder']);

        if (is_array($params['adjunto']))
            $this->mail->addAttachment($params['adjunto'][0], $params['adjunto'][1]); 
        elseif (!empty($params['adjunto']))
            $this->mail->addAttachment($params['adjunto']); 

        if (!empty($params['cuerpo']))
            $this->mail->Body      = $params['cuerpo'];

        if (!empty($params['body']))
            $this->mail->Body      = self::_Body($params['body'][0], $params['body'][1]);

        if (!empty($params['asunto']))
            $this->mail->Subject   = $params['asunto'];

        if (!empty($params['alternativo']))
            $this->mail->BodyAlt   = $params['alternativo'];

		if (!empty($params['correo']))
            $this->mail->AddAddress($params['correo']);*/

		$this->mail->IsHTML(true); 
    }
    public function mail ()
    {
        return $this->mail;
    }
    public function send ()
    {
        return $this->mail->Send();
    }
    public function byReference (&$mail)
    {
        $mail = $this->mail;
    }
    public function loadNewConnection ($config = [])
    {
        $this->mail->Host       = $config['host'];
        $this->mail->Port       = $config['port'];
        $this->mail->Username   = $config['user'];
        $this->mail->Password   = $config['pass'];
        $this->mail->SMTPSecure = $config['secure'];
        $this->mail->setFrom($config['email'], $config['name']);
    }
}
?>