<?php 
namespace App\Config;

class DataBase
{
    
    private $user       ='';
    private $password   ='';
    private $host       ='';
    private $database   ='';
    
    private $conexion;
    
    protected $query;
    protected $rows     =array();
    protected $results;
    protected $error;
    protected $errno;
    protected $isError = false;
    
    // Constructor
    function __construct($db='')
    {   
        if($db)
        {
            $this->user = 'root'; //agoranet
            $this->password = ''; //Richard_111
            $this->host = 'localhost'; //104.203.241.156
            $this->database = $db;

            $this->openConnect();
        }else
        {
            throw new \Exception("Base de datos no encontrada", 1);
            
        }
    }

    // Abre la coneccion a la BD
    private function openConnect()
    {
        $this->conexion = new \mysqli($this->host, $this->user, $this->password, $this->database);
        if($this->conexion->connect_errno)
        {
            $this->error = mysqli_error($this->conexion);
            $this->errno = mysqli_errno($this->conexion);
            $this->isError = true;

            throw new \Exception($this->getErrorMessage());
            
        }
    }
    
    // Cierra la conexion a la BD
    private function closeConnect()
    {
        $this->conexion->close();
    }
    
    // Devuele true si hay un error en la consulta o la conexion
    protected function isError(){
        return $this->isError;
    }

    // Obtiene el error de una consulta o de una conexion
    protected function getError()
    {
        return $this->error;
    }

    // Obtiene el codigo del error
    protected function getErrno()
    {
        return $this->errno;
    }

    // Obtiene mensaje de error con el codigo
    protected function getErrorMessage()
    {
        return $this->getError().' Codigo de error: '.$this->getErrno();
    }

    // Ejecuta un query sencillo
    protected function execute_single_query()
    {
        $this->openConnect();

        $this->results = $this->conexion->query($this->query);
        if(!$this->results)
        {
            $this->error = mysqli_error($this->conexion);
            $this->errno = mysqli_errno($this->conexion);   
            $this->isError = true;
        }

        $this->closeConnect();
    }


    // Obtiene los resultss de un query
    protected function get_result_query()
    {
         $this->rows = array();
        
        $this->openConnect();
        $result = $this->conexion->query($this->query);

        if($this->conexion->connect_errno)
        {
            $this->error = mysqli_error($this->conexion);
            $this->errno = mysqli_errno($this->conexion);
            $this->isError = true;

        }else
        {
            
            while($this->rows[] = $result->fetch_assoc());
            
            $result->close();
            $this->closeConnect();
            array_pop($this->rows);
        }
    }

    // 
    protected function executeQuerySingle()
    {
        $this->execute_single_query();

        if($this->isError()){
            throw new \Exception("Error ".$this->getErrorMessage());

        }else if($this->results){
                
            return array(
                'message'   => 'Consulta exitosa',
                'state'     =>  true,
                'data'      =>  $this->results
            );

        }else{
            return array(
                'message'   => 'no hay resultados',
                'state'     =>  false,
                'data'      => array()
            );
        }
    }
    // 
    protected function getResultsFromQuery(){

        $this->execute_single_query();

        if($this->isError()){
            throw new \Exception("Error ".$this->getErrorMessage());

        }else if($this->results->num_rows > 0){

            $this->get_result_query();
                
            return array(
                'message'   => 'Consulta exitosa',
                'state'     =>  true,
                'data'      =>  $this->rows
            );

        }else{
            return array(
                'message'   => 'no hay resultados',
                'state'     =>  false,
                'data'      => array()
            );
        }
    }
}
