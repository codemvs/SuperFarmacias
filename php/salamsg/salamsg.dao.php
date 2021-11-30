<?php 
require_once '../libs/database.php';
class ChatDAO {
    private $database;
    function __construct(){        
        $this->database = Database::getInstance();        
    }
    public function agregarUsuarioClienteDao($usuario)
    {
        $con = null;
        try { 
            $query = 'INSERT INTO tblusuario(nombre, correo, idTipoUsuario) 
            VALUES (:nombre, :correo, :id_tipo_usuario);';            
            $con = $this->database->connect();
            $respQuery = $con->prepare($query);
            
            $con ->beginTransaction();

            $respQuery->execute([                
                'nombre'=>$usuario->nombre,
                'correo'=>$usuario->correo,
                'id_tipo_usuario'=>$usuario->idTipoUsuario                    
            ]);
            $idUsuario = $con->lastInsertId();
            $con ->commit();
            return $idUsuario;
        }catch(PDOException $ex){
            if($con != null){
                $gbd->rollBack();
            }
            throw new Exception($ex->getMessage());
        }
        
    }
    public function agregarCanalUsuario($idUsuario)
    {
        $ID_DEFAULT_ADMIN = 1; // Usuario default
        $con = null;
        try { 
            $query = 'INSERT INTO tblcanal(idAdmin, idCliente) 
            VALUES (:idAdmin, :idCliente);';            
                $con = $this->database->connect();
                $respQuery = $con->prepare($query);

                $con ->beginTransaction();
                $respQuery->execute([                
                    'idAdmin'=>$ID_DEFAULT_ADMIN,
                    'idCliente'=>$idUsuario                
                ]);
                $idCanal = $con->lastInsertId();
                $con ->commit();

                return $idCanal;
        }catch(PDOException $ex){
            if($con!=null){
                $con ->rollBack();
            }
            throw new Exception($ex->getMessage());
        }
    }
    public function enviarMensajes($idUsuario, $idCanal, $mensaje)
    {        
        $con = null;
        try {             
            $query = 'INSERT INTO tblmensaje(idUsuario, idCanal,mensaje) 
            VALUES (:idUsuario,:idCanal,:mensaje);';            
            $con = $this->database->connect();
            $respQuery = $con->prepare($query);

            $con ->beginTransaction();
            $respQuery->execute([                
                'idUsuario'=>$idUsuario,
                'idCanal'=>$idCanal,
                'mensaje'=>$mensaje                                             
            ]);
            $con->commit();

        }catch(PDOException $ex){
            if($con!=null){
                $con ->rollBack();
            }
            throw new Exception($ex->getMessage());
        }
    }
    public function buscarUsuarioPorCorreoDao($correo)
    {
        try { 
            $usuario = new Usuario();

            $query = 'SELECT idUsuario, nombre, correo, idTipoUsuario, c.idCanal 
                        FROM tblusuario u 
                        left join tblcanal c 
                        on u.idUsuario = c.idCliente
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

            $usuario -> idUsuario = $usuarioDB['idUsuario'];
            $usuario -> nombre = $usuarioDB['nombre'];
            $usuario -> correo = $usuarioDB['correo'];
            $usuario -> idTipoUsuario = $usuarioDB['idTipoUsuario'];
            $usuario -> idCanal = $usuarioDB['idCanal'];
            
            return $usuario;

        }catch(PDOException $ex){
            throw new Exception($ex->getMessage());
        }

        
    }

    public function obtenerUsuariosClienteCanal()
    {
        try { 
            

            $query = 'SELECT c.idCanal, u.idUsuario,u.nombre, u.correo, u.idTipoUsuario,
                             (SELECT COUNT(1) FROM tblmensaje WHERE idCanal = c.idCanal and visto = 0 ) as mensajeNoLeido 
                                FROM tblcanal c 
                                INNER JOIN tblusuario u 
                    ON c.idCliente = u.idUsuario
                    ORDER BY c.idCanal DESC;';            

            $respQuery = $this->database->connect()->prepare($query);
            
            $respQuery->execute();
            $respQuery->setFetchMode(PDO::FETCH_ASSOC);

            $totalEncontrado = $respQuery->rowCount();

            if($totalEncontrado == 0){
                return null;
            }

            $usuariosDB = $respQuery->fetchAll();
            
            return $usuariosDB;

        }catch(PDOException $ex){
            throw new Exception($ex->getMessage());
        }

        
    }
    public function obtenerMensajesPorCanal($idCanal)
    {
        try { 
            

            $query = 'SELECT * FROM tblmensaje WHERE idCanal = :idCanal;';            

            $respQuery = $this->database->connect()->prepare($query);
            
            $respQuery->execute([
                'idCanal'=>$idCanal
            ]);
            $respQuery->setFetchMode(PDO::FETCH_ASSOC);

            $totalEncontrado = $respQuery->rowCount();

            if($totalEncontrado == 0){
                return null;
            }

            $mensajes = $respQuery->fetchAll();
            
            return $mensajes;

        }catch(PDOException $ex){
            throw new Exception($ex->getMessage());
        }
        
    }
    public function actualizarEstatusMensajeVisto($idUsuario,$idCanal)
    {
        try { 
            

            $query = 'update tblmensaje set visto = 1 where idCanal = :idCanal;';            

            $respQuery = $this->database->connect()->prepare($query);
            
            $respQuery->execute([                
                'idCanal'=>$idCanal
            ]);
            
        }catch(PDOException $ex){
            throw new Exception($ex->getMessage());
        }
        
    }
}
?> 