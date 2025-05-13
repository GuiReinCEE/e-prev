<?php
class ADO_expansao_empresas_instituicoes
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

    public function insert_comunidade( entity_expansao_empresas_instituicoes_comunidades $entidade )
    {
        $bReturn = false;

        $entidade->set_cd_empresas_instituicoes_comunidades( getNextval('expansao', 'empresas_instituicoes_comunidades', 'cd_empresas_instituicoes_comunidades', $this->db) );

        $this->dal->createQuery("

            INSERT INTO expansao.empresas_instituicoes_comunidades
            (
                cd_empresas_instituicoes_comunidades
                , cd_emp_inst
                , cd_comunidade
                , dt_exclusao
            )
             VALUES (
                {cd_empresas_instituicoes_comunidades}
                , {cd_emp_inst}
                , '{cd_comunidade}'
                , NULL
            );

        ");

        $this->dal->setAttribute( "{cd_empresas_instituicoes_comunidades}", (int)$entidade->get_cd_empresas_instituicoes_comunidades() );
        $this->dal->setAttribute( "{cd_emp_inst}", (int)$entidade->get_cd_emp_inst() );
        $this->dal->setAttribute( "{cd_comunidade}", (int)$entidade->get_cd_comunidade() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_expansao_empresas_instituicoes.insert_comunidade() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    public function delete_comunidade( entity_expansao_empresas_instituicoes_comunidades $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE expansao.empresas_instituicoes_comunidades 
               SET dt_exclusao = CURRENT_TIMESTAMP 
             WHERE cd_empresas_instituicoes_comunidades = {cd_empresas_instituicoes_comunidades}; 

        ");

        $this->dal->setAttribute( "{cd_empresas_instituicoes_comunidades}", (int)$entidade->get_cd_empresas_instituicoes_comunidades() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_expansao_empresas_instituicoes.delete_comunidade() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    public function fetch_all_comunidade( $empresa )
    {
        $this->dal->createQuery("

            SELECT a.*, b.codigo as cd_comunidade, b.descricao as descricao_comunidade
              FROM expansao.empresas_instituicoes_comunidades a 
              JOIN public.listas b
                   ON a.cd_comunidade = b.codigo
             WHERE cd_emp_inst = {cd_emp_inst} 
               AND a.dt_exclusao IS NULL AND b.dt_exclusao IS NULL
          ORDER BY b.descricao

        ");
        $this->dal->setAttribute( '{cd_emp_inst}', (int)$empresa );
        $result = $this->dal->getResultset();
        
        $comunidades = array();
        while($row = pg_fetch_array($result))
        {
            $comunidade_empresa = new entity_expansao_empresas_instituicoes_comunidades_extended();
            $comunidade = new entity_public_listas();

            $comunidade_empresa->set_cd_empresas_instituicoes_comunidades( $row['cd_empresas_instituicoes_comunidades'] );
            $comunidade_empresa->set_cd_emp_inst( $row['cd_emp_inst'] );
            $comunidade_empresa->set_cd_comunidade( $row['cd_comunidade'] );

            $comunidade->set_codigo( $row['cd_comunidade'] );
            $comunidade->set_descricao( $row['descricao_comunidade'] );

            $comunidade_empresa->comunidade = $comunidade;

            $comunidades[sizeof($comunidades)] = $comunidade_empresa;
        }

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_expansao_empresas_instituicoes.fetch_all_comunidade() ao executar comando SQL de consulta. ' . $this->dal->getMessage() );
        }

        return $comunidades;
    }

    /**
     * Cria array de objetos da estrutura de lista de comunidades que nao foram incluidas na empresa
     * 
     * @return Array[] Coleзгo de objetos de listas
     */
    public function fetch_comunidade_nao_incluida( $cd_empresa )
    {
        $this->dal->createQuery("

            SELECT codigo, descricao, divisao, valor
              FROM listas
             WHERE categoria = 'CACS' 
               AND dt_exclusao IS NULL AND descricao != ''
            
            EXCEPT
            
            SELECT b.codigo, b.descricao, b.divisao, b.valor
              FROM expansao.empresas_instituicoes_comunidades a
        INNER JOIN listas b 
                   ON a.cd_comunidade = b.codigo
             WHERE cd_emp_inst = {cd_emp_inst} 
               AND b.descricao != ''
               AND a.dt_exclusao IS NULL 
               AND b.dt_exclusao IS NULL

          ORDER BY descricao

        ");
        $this->dal->setAttribute( '{cd_emp_inst}', (int)$cd_empresa );
        $result = $this->dal->getResultset();
        
        $comunidades = array();
        while($row = pg_fetch_array($result))
        {
            $comunidade = new entity_public_listas();
            $comunidade->set_codigo( $row['codigo'] );
            $comunidade->set_descricao( $row['descricao'] );
            $comunidade->set_divisao( $row['divisao'] );
            $comunidade->set_valor( $row['valor'] );
            $comunidades[sizeof($comunidades)] = $comunidade;
        }

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.fetchAll() ao executar comando SQL de consulta. ' . $this->dal->getMessage());
        }
        
        return $comunidades;
    }

}

?>