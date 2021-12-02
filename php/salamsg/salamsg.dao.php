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
            $con = $this->database->connect();

            $con ->beginTransaction();

            $query = 'INSERT INTO tblmensaje(idUsuario, idCanal,mensaje) 
            VALUES (:idUsuario,:idCanal,:mensaje);';        
            $res =$this->existeCanal($idCanal);
            if( $res != 1 ){
                throw new Exception('Este usuario ya no esta disponible para mensajes ');
            }
                       
            $respQuery = $con->prepare($query);
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
    public function eliminarMensaje($idMensaje)
    {
        try { 
            

            $query = 'delete from tblmensaje where idMensaje = :idMensaje;';            

            $respQuery = $this->database->connect()->prepare($query);
            
            $respQuery->execute([                
                'idMensaje'=>$idMensaje
            ]);
            
        }catch(PDOException $ex){
            throw new Exception($ex->getMessage());
        }
        
    }
    public function validarUsrPass($correo, $passwd){
        try { 

            $query = 'SELECT count(1) as total FROM tblusuario where correo = :correo and password = :passwd;';            

            $respQuery = $this->database->connect()->prepare($query);
            
            $respQuery->execute([                
                'correo'=>$correo,
                'passwd'=>$passwd
            ]);
            $respQuery->setFetchMode(PDO::FETCH_ASSOC);
            $totalEncontrado = $respQuery->rowCount();
            if($totalEncontrado == 0){
                return null;
            }
            return $respQuery->fetchAll()[0]['total'];
        }catch(PDOException $ex){
            throw new Exception($ex->getMessage());
        }
    }
    public function eliminarChat($idUsuario, $idCanal)
    {        
        $con = null;
        try {             
            $con = $this->database->connect();
            $con ->beginTransaction();

            $query = 'DELETE FROM tblcanal WHERE idCanal = :idCanal;';               
            $respQuery = $con->prepare($query);                       
            $respQuery->execute([                
                'idCanal'=>$idCanal
            ]);

            $query = 'DELETE FROM tblmensaje WHERE idCanal = :idCanal;';               
            $respQuery = $con->prepare($query);                       
            $respQuery->execute([                
                'idCanal'=>$idCanal
            ]);

            $query = 'DELETE FROM tblusuario WHERE idUsuario = :idUsuario;';               
            $respQuery = $con->prepare($query);                       
            $respQuery->execute([                                                      
                'idUsuario'=>$idUsuario
            ]);

            $con->commit();

        }catch(PDOException $ex){
            if($con!=null){
                $con ->rollBack();
            }
            throw new Exception($ex->getMessage());
        }
    }
    private function existeCanal($idCanal){
        try{

        $query = 'SELECT COUNT(1) totalCanal FROM tblcanal WHERE idCanal = :idCanal;';            

            $respQuery = $this->database->connect()->prepare($query);
            
            $respQuery->execute([                
                'idCanal'=>$idCanal                
            ]);
            $respQuery->setFetchMode(PDO::FETCH_ASSOC);
            $totalEncontrado = $respQuery->rowCount();
            if($totalEncontrado == 0){
                return null;
            }
            $totalCanalEncontrado = $respQuery->fetchAll()[0]['totalCanal'];
            return $totalCanalEncontrado > 0;

        }catch(PDOException $ex){
            if($con!=null){
                $con ->rollBack();
            }
            throw new Exception($ex->getMessage());
        }
    }
}
?> 