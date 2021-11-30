<?php 
require 'models/response.model.php';
include_once 'config/config.php';

require 'libs/PHPMailer-master/src/Exception.php';
require 'libs/PHPMailer-master/src/PHPMailer.php';
require 'libs/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

    switch($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            sendEmail();            
            break;
        default:
            $responseModel = new ResponseModel();
            $responseModel ->success = false;            
            $responseModel ->messageError = 'Request method invalid';
            echo json_encode($responseModel);
            
        break;
    }


    function sendEmail(){
        $responseModel = new ResponseModel();
        try{
            $params = $_POST;  
            
            if(empty($params['name'])){
                throw new Exception('El nombre es requerido');
            }
            if(empty($params['email'])){
                throw new Exception('El correo es requerido');
            }
            if( !filter_var($params['email'],FILTER_VALIDATE_EMAIL) ) {
                throw new Exception('El formato del correo es inválido');
            }
            if(empty($params['message'])){
                throw new Exception('El mensaje es requerido');
            }
            
            $sendEmail = new SendEmail();            
            
            $sendEmail -> Gmail(
                                $params['email'],
                                $params['name'],
                                'Consulta '.$params['name'].' a Super Farmacia',
                                $params['message']
                            );

            $responseModel ->success = true;
            $responseModel ->data = 'El correo se envío con éxito';    
              

        }catch(Exception $e){
            $responseModel ->success = false;            
            $responseModel ->messageError = $e->getMessage();
        }finally{
            echo json_encode($responseModel);
        }
        

    }
    class SendEmail{
        
        
        private $userName;
        private $password;
        private $nombreCorreoServidor;

        private $correoDestinoDelAministrador;
        private $tituloCorreoDestinoDelAministrador;

        private $mail; 
        function __construct(){
            
            $this->userName = constant('CORREO_SMTP');
            $this->password = constant('SECRET_CORREO_SMTP');
            $this->nombreCorreoServidor = constant('NOMBRE_CORREO_SMTP');

            $this->correoDestinoDelAministrador = constant('CORREO_SF_ADMIN');
            $this->tituloCorreoDestinoDelAministrador = constant('NOMBRE_SF_ADMIN');

            $this->initPHPMailer();

        }
        private function initPHPMailer(){
            $this->mail = new PHPMailer();
            $this->mail->IsSMTP();
            $this->mail->Mailer = "smtp";
            $this->mail->SMTPDebug  = 0;  
            $this->mail->SMTPAuth   = TRUE;
            $this->mail->SMTPSecure = "tls";
            $this->mail->Port       = 587;
            $this->mail->Host       = "smtp.gmail.com";
            $this->mail->Username   = $this->userName;
            $this->mail->Password   = $this->password;
            $this->mail->IsHTML(true);
            
        }

        
        public function Gmail($correoRemitente, $nombreRemitente, $tituloCorreo, $body) {            
            try
            {   

                $this->mail->SetFrom($this->userName, $this->nombreCorreoServidor);
                $this->mail->AddAddress($this->correoDestinoDelAministrador, $this->tituloCorreoDestinoDelAministrador);
                $this->mail->AddReplyTo($correoRemitente, $nombreRemitente);
                
                $this->mail->Subject = $tituloCorreo;
                $content = $body;

                $this->mail->MsgHTML($content); 
                if(!$this->mail->Send()) {
                    throw new Exception('Ocurrio un problema al enviar el correo, intentelo mas tarde');            
                } 

            } catch(Exception $ex){
                throw new Exception( $ex->getMessage() );         
            }
        }
        
    }
?>