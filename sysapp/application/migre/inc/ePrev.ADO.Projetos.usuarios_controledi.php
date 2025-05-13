<?php
class ADO_projetos_usuarios_controledi
{
    // DAL
    private $db;
    private $dal;

    function ADO_projetos_usuarios_controledi( $_db )
    {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

	public function bloqueado_para_avaliacao($cd_usuario)
	{
		$this->dal->createQuery("

            SELECT dt_bloqueio FROM projetos.usuario_avaliacao_bloqueio
             WHERE cd_usuario={cd_usuario} and dt_exclusao is null

        ");

        $this->dal->setAttribute( "{cd_usuario}", intval($cd_usuario) );

        $result = $this->dal->getScalar();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_usuarios_controledi.bloqueado_para_avaliacao() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return ($result!='');
	}

    public function fetch_by_name( $nome )
    {
        $this->dal->createQuery("

            SELECT * FROM projetos.usuarios_controledi
             WHERE upper(nome) LIKE upper('%{nome}%')
               AND NOT tipo IN ('X', 'P');

        ");

        $this->dal->setAttribute( "{nome}", $nome );

        $result = $this->dal->getResultset();
        
        $usuarios = array();
        while( $row = pg_fetch_array($result) )
        {
            $usuario = new entity_projetos_usuarios_controledi_extended();
            $usuario->set_codigo( $row['codigo'] );
            $usuario->set_nome( $row['nome'] );
            
            $usuarios[ sizeof($usuarios) ] = $usuario;
        }

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_usuarios_controledi.fetch_by_name() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return $usuarios;
    }

    /**
     * Cria um array de gerencias agrupando os usuários
     * O usuário ainda recebe informações referentes a sua classificação na 
     * matriz salarial
     * 
     * Usuário: projetos.usuarios_controledi
     * Divisão: projetos.usuarios_controledi.divisao
     * Matriz Salarial: projetos.usuario_matriz
     * 
     * @return array() helper_usuarios_agrupados_por_divisao
     * 
     */
    public function listar_agrupando_por_gerencia()
    {
        $divisoes = array();

        $this->dal->createQuery("
            SELECT DISTINCT divisao
              FROM projetos.usuarios_controledi
             WHERE tipo IN ('U', 'G', 'N') AND divisao NOT IN ('CF', 'FC', 'DE') ;

        ");

        $result = $this->dal->getResultset();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_usuarios_controledi.listar_agrupando_por_gerencia() #1 ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        while( $row = pg_fetch_array( $result ) ) :
            $helper = new helper_usuarios_agrupados_por_divisao();
            $helper->divisao = $row['divisao'];

            $this->dal->createQuery("
                SELECT puc.*, pum.cd_usuario_matriz, pum.cd_matriz_salarial, pum.cd_escolaridade, pms.faixa, pms.cd_familias_cargos
                     , TO_CHAR( pum.dt_admissao , 'DD/MM/YYYY' ) AS dt_admissao
                     , TO_CHAR( pum.dt_promocao , 'DD/MM/YYYY' ) AS dt_promocao
                     , pum.tipo_promocao
                  FROM projetos.usuarios_controledi puc
             LEFT JOIN projetos.usuario_matriz pum
                       ON puc.codigo = pum.cd_usuario
             LEFT JOIN projetos.matriz_salarial pms
                       ON pum.cd_matriz_salarial = pms.cd_matriz_salarial
                 WHERE tipo NOT IN ('X', 'P') AND divisao NOT IN ('CF', 'FC')
                   AND divisao = '{divisao}'
              ORDER BY puc.nome
            ");

            $this->dal->setAttribute('{divisao}', $row['divisao']);
            $result_usuario = $this->dal->getResultset();

            if ($this->dal->haveError())
            {
                throw new Exception('Erro em ADO_projetos_usuarios_controledi.listar_agrupando_por_gerencia() #2 ao executar comando SQL de consulta. '.$this->dal->getMessage());
            }

            while( $row_usuario = pg_fetch_array($result_usuario) ) :
                $usuario = new entity_projetos_usuarios_controledi_extended();
                $usuario->set_codigo( $row_usuario['codigo'] );
                $usuario->set_nome( $row_usuario['nome'] );
                $usuario->set_cd_cargo( $row_usuario['cd_cargo'] );
                
                $usuario_matriz = new entity_projetos_usuario_matriz();
                $usuario_matriz->cd_usuario_matriz = $row_usuario['cd_usuario_matriz'];
                $usuario_matriz->cd_matriz_salarial = $row_usuario['cd_matriz_salarial'];
                $usuario_matriz->dt_admissao = $row_usuario['dt_admissao'];
                $usuario_matriz->dt_promocao = $row_usuario['dt_promocao'];
                $usuario_matriz->cd_escolaridade = $row_usuario['cd_escolaridade'];
                $usuario_matriz->tipo_promocao = $row_usuario['tipo_promocao'];
                
                $matriz_salarial = new entity_projetos_matriz_salarial();;
                $matriz_salarial->cd_familias_cargos = $row_usuario['cd_familias_cargos'];
                $matriz_salarial->faixa = $row_usuario['faixa'];
                
                $usuario_matriz->matriz_salarial = $matriz_salarial;

                $usuario->usuario_matriz = $usuario_matriz;
                $helper->usuarios[ sizeof($helper->usuarios) ] = $usuario;
            endwhile;
            
            $divisoes[ sizeof($divisoes) ] = $helper;
        endwhile;
        
        return $divisoes;
    }
    
    public function salvar_matriz( entity_projetos_usuario_matriz $usuario_matriz )
    {
        $this->dal->createQuery( "
            SELECT cd_usuario_matriz 
              FROM projetos.usuario_matriz 
             WHERE cd_usuario={cd_2}
        " );
        // $this->dal->setAttribute( '{cd_1}', $usuario_matriz->cd_matriz_salarial );
        $this->dal->setAttribute( '{cd_2}', (int)$usuario_matriz->cd_usuario );
        
        $res = $this->dal->getResultset();

        if( $row = pg_fetch_array($res) )
        {
            $usuario_matriz->cd_usuario_matriz = $row['cd_usuario_matriz'];
            
            $this->dal->createQuery("
    
                UPDATE   projetos.usuario_matriz
                   SET 
                         cd_matriz_salarial = {cd_matriz_salarial}
                       , cd_usuario = {cd_usuario}
                       , dt_admissao = TO_DATE( '{dt_admissao}' , 'DD/MM/YYYY' )
                       , dt_promocao = TO_DATE( '{dt_promocao}' , 'DD/MM/YYYY' )
                       , cd_escolaridade = {cd_escolaridade}
                       , tipo_promocao = '{tipo_promocao}'
                 WHERE   cd_usuario_matriz = {cd_usuario_matriz}
    
            ");
        }
        else
        {
            $this->dal->createQuery("

                INSERT INTO projetos.usuario_matriz
                (
                    cd_matriz_salarial
                    , cd_usuario
                    , dt_admissao
                    , dt_promocao
                    , cd_escolaridade
                    , tipo_promocao
                )
                 VALUES 
                (
                    {cd_matriz_salarial}
                    , {cd_usuario}
                    , TO_DATE( '{dt_admissao}' , 'DD/MM/YYYY' )
                    , TO_DATE( '{dt_promocao}' , 'DD/MM/YYYY' )
                    , {cd_escolaridade}
                    , '{tipo_promocao}'
                )

            ");
        }
        $this->dal->setAttribute( "{cd_usuario_matriz}", (int)$usuario_matriz->cd_usuario_matriz );
        $this->dal->setAttribute( "{cd_matriz_salarial}", (int)$usuario_matriz->cd_matriz_salarial );
        $this->dal->setAttribute( "{cd_usuario}", (int)$usuario_matriz->cd_usuario );
        $this->dal->setAttribute( "{dt_admissao}", $usuario_matriz->dt_admissao );
        $this->dal->setAttribute( "{dt_promocao}", $usuario_matriz->dt_promocao );
        $this->dal->setAttribute( "{cd_escolaridade}", (int)$usuario_matriz->cd_escolaridade );
        $this->dal->setAttribute( "{tipo_promocao}", $usuario_matriz->tipo_promocao );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_usuarios_controledi.salvar_matriz() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return $result;
    }
    
    /**
     * Retorna array com escolaridades possíveis para o usuário
     *
     * @param int $cd_usuario código do usuário 
     * @return hashtable_collection Escolaridades possíveis para o funcionário
     */
    public function escolaridades_por_usuario__get( $cd_usuario )
    {
        $this->dal->createQuery("

        	SELECT * 
        	  FROM projetos.escolaridade
          ORDER BY ordem
        
            /*
            SELECT DISTINCT(e.*)
			  FROM projetos.usuarios_controledi puc
			  JOIN projetos.cargos pc ON puc.cd_cargo = pc.cd_cargo
			  JOIN projetos.familias_escolaridades pfe ON pc.cd_familia = pfe.cd_familia
			  JOIN projetos.escolaridade e ON e.cd_escolaridade = pfe.cd_escolaridade
			 WHERE puc.codigo = {puc.codigo}
			*/

        ");

        $this->dal->setAttribute( "{puc.codigo}", (int)$cd_usuario );
        $result = $this->dal->getResultset();

		$return = new hashtable_collection();
        while( $row = pg_fetch_array($result) )
        {
        	$return->add($row['cd_escolaridade'], $row['nome_escolaridade']);
        }

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_usuarios_controledi.escolaridades_por_usuario__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return $return;
    }
}
?>