<?php
class ADO_expansao_inscritos
{
    // DAL
    private $db;
    private $dal;

    function __construct( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function get_pacote( $cd_empresa, $cd_registro_empregado )
    {
        $resultado = '';
        $this->dal->createQuery("

            SELECT cd_pacote 
              FROM expansao.inscritos 
             WHERE cd_empresa = {cd_empresa}
               AND cd_registro_empregado = {cd_registro_empregado}

        ");
        $this->dal->setAttribute( '{cd_empresa}', (int)$cd_empresa );
        $this->dal->setAttribute( '{cd_registro_empregado}', (int)$cd_registro_empregado );
        $result = $this->dal->getResultset();
        
        if( $result )
        {
            if( $row = pg_fetch_array($result) )
            {
                $resultado = $row["cd_pacote"];
            }
        }
        
        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_expansao_inscritos.get_pacote() ao executar comando SQL de consulta. ' . $this->dal->getMessage() );
        }

        return $resultado;
    }
}
?>