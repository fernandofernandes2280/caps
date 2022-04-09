<?php
namespace App\Controller\Comunication;



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Email{
	
	//Credencias de acesso ao SMTP
	const  HOST = 'smtp.hostinger.com';
	const USER = 'siscaps@flash380.com';
	const PASS = 'siscapsSisc4ps';
	const SECURE = 'TLS';
	const PORT = 587;
	const CHARSET = 'UTF-8';
	
	//Dados do Remetente
	const FROM_EMAIL = 'siscaps@flash380.com';
	const FROM_NAME = 'Siscaps';
	
	//Mensagem de erro do envio
	private $error;
	
	
	//Método responsavel por retornar a mensagem de erro do envio
	public function getError(){
		return $this->error;
	}
	
	/*Método responsável por enviar um email
	$addresses -> destinatário(s)
	$subject -> assunto
	$body -> corpo da mensagem
	$attachements = [] -> anexos (opcional)
	$ccs = [] -> cópias visiveis (opcional)
	$bccs = [] -> cópias ocultas (opcional)
	*/
	public function sendEmail($addresses, $subject, $body, $attachements = [], $ccs = [], $bccs = []){
		//Limpar a mensagem de erro
		$this->error = '';
		
		//Instancia de PHPMAILER
		$obMail = new PHPMailer(true);
		try {
			
			//Credencias de acesso ao SMTP
			$obMail->isSMTP(true);
			$obMail->Host = self::HOST;
			$obMail->SMTPAuth = true;
			$obMail->Username = self::USER;
			$obMail->Password = self::PASS;
			$obMail->SMTPSecure = self::SECURE;
			$obMail->Port = self::PORT;
			$obMail->CharSet = self::CHARSET;
			
			//Remetente
			$obMail->setFrom(self::FROM_EMAIL, self::FROM_NAME);
			
			//Destinatário(s)
			$addresses = is_array($addresses) ? $addresses : [$addresses];
			foreach ($addresses as $address){
				$obMail->addAddress($address);
			}
			
			//Anexo(s)
			$attachements = is_array($attachements) ? $attachements : [$attachements];
			foreach ($attachements as $attachement){
				$obMail->addAttachment($attachement);
			}
			
			//Cópias(s)
			$ccs = is_array($ccs) ? $ccs : [$ccs];
			foreach ($ccs as $cc){
				$obMail->addCC($cc);
			}
			
			//Cópias(s) Oculta
			$bccs = is_array($bccs) ? $bccs : [$bccs];
			foreach ($bccs as $bcc){
				$obMail->addBCC($bcc);
			}
			
			//Conteúdo do Email
			$obMail->isHTML(true);
			$obMail->Subject = $subject;
			$obMail->Body = $body;
			
			//Envia o email
			return $obMail->send();
			
			
		} catch (PHPMailerException $e) {
			$this->error = $e->getMessage();
			return false;
			
		}
	}
	
	
}