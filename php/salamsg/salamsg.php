<?php 
require_once '../models/response.model.php'; 

require_once 'salamsg.operations.php';

try{
    $chatOperations = new ChatOperations();

    if($_SERVER['REQUEST_METHOD']) {

        if(empty($_POST['method'])){
            throw new Exception('Request method invalid');  
        }

        switch($_POST['method']){
            case 'agregar_usuario_cliente':
                $chatOperations->agregarUsuarioCliente($_POST);
            break;
            case 'obtener_usuarios':
                $chatOperations->obtenerUsuarios();
            break;
            case 'obtener_menesajes':
                $chatOperations -> obtenerMensajes($_POST);
                break;
            case 'enviar_mensaje':
                $chatOperations -> enviarMensaje($_POST);
                break;
            case 'act_est_visto':
                $chatOperations -> actualizarEstatusMensajeVisto($_POST);
                break;
            case 'eliminar_msg':
                $chatOperations -> eliminarMensaje($_POST);
                break;
            case 'validar_correo':
                $chatOperations -> validarCorreo($_POST);
                break;
            case 'login':
                $chatOperations -> validarUsrPass($_POST);
                break;
            case 'eliminar_chat':
                $chatOperations -> eliminarChat($_POST);
                break;
            default:
            throw new Exception('Request method invalid');  
            break;
        }
    }
    
}catch(Exception $ex){
    $responseModel = new ResponseModel();
    $responseModel ->success = false;            
    $responseModel ->messageError = $ex->getMessage();
    echo json_encode($responseModel);
}
    
?>