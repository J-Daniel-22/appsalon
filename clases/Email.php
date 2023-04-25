<?php 

    namespace Clases;
    use PHPMailer\PHPMailer\PHPMailer;

    class Email {

        public $email;
        public $nombre;
        public $token;

        public function __construct($nombre, $email, $token) {
                
            $this->nombre = $nombre;
            $this->email = $email;
            $this->token = $token;

        }

        public function enviarConfirmacion(){

            //Crear el objeto de email
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'dbb5fb2929a863';
            $mail->Password = '16516c816aa371';

            $mail->setFrom('cuentas@appsalon.com');
            $mail->addAddress("cuentas@appsalon.com", "AppSalon.com");
            $mail->Subject = 'Confirma tu cuenta';

            //Set html
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';


            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en App Salon, solo debes confirmar presionando el siguiente enlace</p>";      
            $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/confirmar?token=" . $this->token . "'>Confirmar Cuenta</a></p>"; 
            $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;

            //Enviar el email
            $mail->send();

        }


        public function enviarInstrucciones() {

            //Crear el objeto de email
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = 'dbb5fb2929a863';
            $mail->Password = '16516c816aa371';

            $mail->setFrom('cuentas@appsalon.com');
            $mail->addAddress("cuentas@appsalon.com", "AppSalon.com");
            $mail->Subject = 'Reestablece tu password';

            //Set html
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';


            $contenido = "<html>";
            $contenido .= "<p><strong>Hola ". $this->nombre ."</strong> Has solicitado reestablecer tu password,
            sigue el siguiente enlace para hacerlo</p>"; 
            $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/recuperar?token=" 
            . $this->token ."'>Reestablecer password</a></p>";
            $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;

            //Enviar el email
            $mail->send();


        }

    }