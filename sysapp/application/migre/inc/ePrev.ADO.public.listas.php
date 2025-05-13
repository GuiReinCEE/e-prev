<?php
class ADO_public_listas
{
    // DAL
    private $db;
    private $dal;

    function __construct( $_db )
    {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct()
    {
        $this->dal = null;
    }
    
    public function load(entity_public_listas $entidade)
    {
        $this->dal->createQuery("

            SELECT *
              FROM public.listas
            WHERE codigo = '{codigo}'

        ");

        $this->dal->setAttribute( "{codigo}", $entidade->get_codigo() );

        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) 
        {
            $result = null;
            throw new Exception('Erro em ADO_public_listas.load() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }
        else 
        {
            $row = pg_fetch_array($result);
            if ($row) 
            {
                $entidade->set_codigo( $row["codigo"] );
                $entidade->set_descricao( $row["descricao"] );
                $row = null;
                $result = null;
            }
        }
        return true;
    }
    public function change(entity_public_listas $entidade)
    {
        $this->dal->createQuery("

            UPDATE public.listas SET descricao = '{descricao}' WHERE codigo = '{codigo}'

        ");

        $this->dal->setAttribute( "{codigo}", $entidade->get_codigo() );
        $this->dal->setAttribute( "{descricao}", $entidade->get_descricao() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError()) 
        {
            $result = null;
            throw new Exception('Erro em ADO_public_listas.change() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }
        return true;
    }

}
?>