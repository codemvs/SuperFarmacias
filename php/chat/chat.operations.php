<?php 
require_once '../models/chat/usuario.model.php';
require_once 'chat.dao.php';

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
            $usuario -> id_tipo_usuario = $this->USUARIO_NORMAL;

            // validar si usuario existe
            $usuarioDB = $this->chat_dao-> buscarUsuarioPorCorreoDao( $usuario -> correo );
            $mensaje = 'Usario ya existe';
            if( $usuarioDB == null ){
                $this->chat_dao-> agregarUsuarioClienteDao( $usuario );
                $mensaje = 'El usuario se agrego con éxito';
            }

            $responseModel ->success = true;            
            $responseModel ->data = $mensaje;
            
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