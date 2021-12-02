<?php 
require_once '../models/chat/usuario.model.php';
require_once 'salamsg.dao.php';

class ChatOperations {    
    private $chat_dao;

    private $USUARIO_NORMAL = 2; 


    public function __construct(){
        $this->chat_dao = new ChatDAO();
    }
    public function agregarUsuarioCliente( $postUsuario ) {
        try{
            $responseModel = new ResponseModel();
            $usuario = new Usuario();
            
            // Validar campos
            if( empty($postUsuario['nombre']) ) {
                $this->createException( 'El campo nombre es requerido' );
            }

            if( empty($postUsuario['correo']) ) {
                $this->createException( 'El campo correo es requerido' );
            }
            

            $usuario -> nombre = $postUsuario['nombre'];
            $usuario -> correo = $postUsuario['correo'];
            $usuario -> idTipoUsuario = $this->USUARIO_NORMAL;

            // validar si usuario existe
            $usuarioDB = $this->chat_dao-> buscarUsuarioPorCorreoDao( $usuario -> correo );
            
            if( $usuarioDB == null ) {

                $idUsuarioDB = $this->chat_dao-> agregarUsuarioClienteDao( $usuario );

                // crear nuevo usuario sala chat
                $idCanal = $this->chat_dao-> agregarCanalUsuario( $idUsuarioDB );

                // Agregar primer mensaje Admin
                $idAdmin = 1;
                $this->chat_dao->enviarMensajes( $idAdmin,
                 $idCanal,
                 'Hola '.$usuario->nombre.' ¿en que le puedo ayudar?'
                );
                
                $usuario -> idUsuario = $idUsuarioDB;
                $usuario -> idCanal = $idCanal;
                $usuarioDB = $usuario;
            }

            $responseModel ->success = true;            
            $responseModel ->data = $usuarioDB;
            
            echo json_encode($responseModel);

        }catch(Exception $e){
            throw $e;
        }
    }
    public function obtenerUsuarios(){
        try{
            $responseModel = new ResponseModel();
            
            $usuarios = $this->chat_dao -> obtenerUsuariosClienteCanal();

            $responseModel ->success = true;            
            $responseModel ->data = $usuarios;
            
            echo json_encode($responseModel);

        }catch(Exception $e){
            throw $e;
        }
    }
    public function obtenerMensajes($dataPost) {
        try{
            $responseModel = new ResponseModel();
            // Validar campos
            $idCanal = '';
            if( empty($dataPost['idCanal']) ) {
                
                $this->createException( 'El idCanal es requerido' );
            }
            $idCanal = $dataPost['idCanal'];
            $mensajes = $this->chat_dao -> obtenerMensajesPorCanal($idCanal);

            $responseModel ->success = true;            
            $responseModel ->data = $mensajes;
            
            echo json_encode($responseModel);

        }catch(Exception $e){
            throw $e;
        }
        
    }
    public function enviarMensaje($post) {
        try {
            $responseModel = new ResponseModel();
            // Validar campos
            if( empty($post['idUsuario']) ) {
                $this->createException( 'El idUsuario es requerido' );
            }
            if( empty($post['idCanal']) ) {
                $this->createException( 'El idCanal es requerido' );
            }
            if( empty($post['mensaje']) ) {
                $this->createException( 'El mensaje es requerido' );
            }
            
            $mensajes = $this->chat_dao -> enviarMensajes(
                $post['idUsuario'],
                $post['idCanal'],
                $post['mensaje']);
                
            $responseModel ->success = true;            
            $responseModel ->data = 'Mensaje enviado con éxito';

            echo json_encode($responseModel);

        }catch(Exception $e){
            throw $e;
        }
    }
    public function actualizarEstatusMensajeVisto($post) {
        try {
            $responseModel = new ResponseModel();
            // Validar campos
            if( empty($post['idUsuario']) ) {
                $this->createException( 'El idUsuario es requerido' );
            }
            if( empty($post['idCanal']) ) {
                $this->createException( 'El idCanal es requerido' );
            }
            
            $mensajes = $this->chat_dao -> actualizarEstatusMensajeVisto(
                $post['idUsuario'],
                $post['idCanal']
                );
                
            $responseModel ->success = true;            
            $responseModel ->data = 'Se actualizo con exito';

            echo json_encode($responseModel);

        }catch(Exception $e){
            throw $e;
        }
    }
    public function eliminarMensaje($post) {
        try {
            $responseModel = new ResponseModel();
            // Validar campos
            if( empty($post['idMensaje']) ) {
                $this->createException( 'El idMensaje es requerido' );
            }
            $mensajes = $this->chat_dao -> eliminarMensaje(                
                $post['idMensaje']
                );
                
            $responseModel ->success = true;            
            $responseModel ->data = 'Se elimino con exito';

            echo json_encode($responseModel);

        }catch(Exception $e){
            throw $e;
        }
    }
    public function validarCorreo($post) {
        try {
            $responseModel = new ResponseModel();
            $correo = 'correo';
            // Validar campos
            if( empty($post[$correo]) ) {
                $this->createException( 'El correo es requerido' );
            }
            if( !filter_var($post[$correo],FILTER_VALIDATE_EMAIL) ) {                
                $this->createException( 'El formato del correo es inválido' );
            }
            $usuario = $this->chat_dao -> buscarUsuarioPorCorreoDao(                
                $post[$correo]
                );
            $usuarioExiste = $usuario != null;
            $isAdmin = null;
            if($usuarioExiste){
                $isAdmin =  ($usuario->idTipoUsuario == 1);
            }

            $responseModel ->success = true;            
            $responseModel ->data = [
                "usrExiste" => $usuarioExiste,
                "isAdmin" =>$isAdmin
            ];

            echo json_encode($responseModel);

        }catch(Exception $e){
            throw $e;
        }
    }
    public function validarUsrPass($post) {
        try {
            $responseModel = new ResponseModel();
            $correo = 'correo';
            $passwd = 'contrasenia';
            // Validar campos
            if( empty($post[$correo]) ) {
                $this->createException( 'El correo es requerido' );
            }
            if( !filter_var($post[$correo],FILTER_VALIDATE_EMAIL) ) {                
                $this->createException( 'El formato del correo es inválido' );
            }
            if( empty($post[$passwd]) ) {
                $this->createException( 'La contrasenia es requerido' );
            }

            $total = $this->chat_dao -> validarUsrPass(                
                $post[$correo],
                $post[$passwd]
                );
            $usuarioExiste = $total > 0;

            if(!$usuarioExiste){
                $this->createException( 'Credenciales incorrectos' );
            }

            $responseModel ->success = true;            
            $responseModel ->data = [
                "usrValido" => $usuarioExiste
            ];

            echo json_encode($responseModel);

        }catch(Exception $e){
            throw $e;
        }
    }
    public function eliminarChat($post) {
        try {
            $responseModel = new ResponseModel();
            $idUsuario = 'idUsuario';
            $idCanal = 'idCanal';
            // Validar campos
            if( empty($post[$idUsuario]) ) {
                $this->createException( 'El idUsuario es requerido' );
            }
            if( empty($post[$idCanal]) ) {
                $this->createException( 'El idCanal es requerido' );
            }

            $total = $this->chat_dao -> eliminarChat(                
                $post[$idUsuario],
                $post[$idCanal]
                );
            

            $responseModel ->success = true;            
            $responseModel ->data = 'Se ha eliminado con exito';

            echo json_encode($responseModel);

        }catch(Exception $e){
            throw $e;
        }
    }
    
    private function createException($message){
        throw new Exception($message);
    }
}
?>