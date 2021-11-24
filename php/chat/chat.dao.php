<?php 
require_once '../libs/database.php';
class ChatDAO {
    private $database;
    function __construct(){        
        $this->database = Database::getInstance();        
    }
    public function agregarUsuarioClienteDao($usuario)
    {
        try { 
            $query = 'INSERT INTO usuarios(nombre, correo, id_tipo_usuario) 
            VALUES (:nombre, :correo, :id_tipo_usuario)';            

            $respQuery = $this->database->connect()->prepare($query);
            
            $respQuery->execute([                
                'nombre'=>$usuario->nombre,
                'correo'=>$usuario->correo,
                'id_tipo_usuario'=>$usuario->id_tipo_usuario                    
            ]);
                

        }catch(PDOException $ex){
            throw new Exception($ex->getMessage());
        }

        
    }
    public function buscarUsuarioPorCorreoDao($correo)
    {
        try { 
            $usuario = new Usuario();

            $query = 'SELECT id_usuarios, nombre, correo, id_tipo_usuario 
                        FROM usuarios
                        WHERE correo =:correo LIMIT 1;';            

            $respQuery = $this->database->connect()->prepare($query);
            
            $respQuery->execute([                                
                'correo'=>$correo,                
            ]);
            $respQuery->setFetchMode(PDO::FETCH_ASSOC);

            $totalEncontrado = $respQuery->rowCount();

            if($totalEncontrado == 0){
                return null;
            }

            $usuarioDB = $respQuery->fetchAll()[0];

            $usuario -> id_usuario = $usuarioDB['id_usuarios'];
            $usuario -> nombre = $usuarioDB['nombre'];
            $usuario -> correo = $usuarioDB['correo'];
            $usuario -> id_tipo_usuario = $usuarioDB['id_tipo_usuario'];
            
            return $usuario;

        }catch(PDOException $ex){
            throw new Exception($ex->getMessage());
        }

        
    }
}
?> 