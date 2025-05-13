<?php
/**
 * Classe para acesso a dados de projetos.avaliacao 
 * 
 * @access public
 * @package ePrev
 * @subpackage ADO
 * @require ePrev.Util.Message.php
 * @require ePrev.DAL.DBConnection.php
 */
class ADO_projetos_avaliacao
{
    // DAL
    private $db;
    private $dal;

    function ADO_projetos_avaliacao( $_db )
    {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function comite_ToString( $dt_periodo, $cd_usuario_avaliado )
    {
        $resultado = "";

        $this->dal->createQuery("

                SELECT puc.guerra
                FROM projetos.avaliacao_comite pac
          INNER JOIN projetos.usuarios_controledi puc
                     ON pac.cd_usuario_avaliador = puc.codigo
               WHERE pac.dt_periodo = {dt_periodo} 
                 AND pac.cd_usuario_avaliado = {cd_usuario_avaliado}
				 AND pac.dt_exclusao IS NULL
            ORDER BY puc.guerra

        ");

        $this->dal->setAttribute( "{dt_periodo}", $dt_periodo );
        $this->dal->setAttribute( "{cd_usuario_avaliado}", (int)$cd_usuario_avaliado );
        
        $result = $this->dal->getResultset();
        
        if ($result) {
            $virgula = "";
            while( $row = pg_fetch_array($result) )
            {
                $resultado .= $virgula . $row["guerra"];
                $virgula = ", ";
            }
		}

        if( $this->dal->haveError() )
        {
            throw new Exception( 'Erro em ADO_projetos_avaliacao.comite_ToString() ao executar comando SQL de consulta. '.$this->dal->getMessage() );
        }
        
        pg_free_result($result);
        $result = null;
        $row = null;

        return $resultado;
    }
    
    /**
     * ADO_projetos_avaliacao.update
     * 
     * @param entity_projetos_avaliacao_extended $entidade O atributo dt_conclusao deve ser NULL ou CURRENT_TIMESTAMP ou BRANCO, outro valor gera erro.
     */
    public function update( entity_projetos_avaliacao_extended $entidade )
    {
        $bReturn = false;
        $this->dal->createQuery("

                 UPDATE  projetos.avaliacao
                    SET  dt_conclusao={dt_conclusao}
                  WHERE  cd_avaliacao = {cd_avaliacao}

        ");

        $this->dal->setAttribute( "{cd_avaliacao}", (int)$entidade->get_cd_avaliacao() );

        if($entidade->get_dt_conclusao()==''){ $entidade->set_dt_conclusao('null'); }
        $this->dal->setAttribute( "{dt_conclusao}", $entidade->get_dt_conclusao() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.update() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    /**
     * ADO_projetos_avaliacao.insert
     * 
     * @param entity_projetos_avaliacao_extended $entidade O atributo dt_conclusao deve ser NULL ou CURRENT_TIMESTAMP ou BRANCO, outro valor gera erro.
     */
    public function insert( entity_projetos_avaliacao_extended $entidade )
    {
        $bReturn = false;
        
        $entidade->set_cd_avaliacao( getNextval("projetos", "avaliacao", "cd_avaliacao", $this->db) );
        $this->dal->createQuery("

                 INSERT INTO projetos.avaliacao
                    (
                                cd_avaliacao
                                , cd_usuario_avaliador
                                , tipo
                                , dt_criacao
                                , cd_avaliacao_capa
                                , dt_conclusao
                    )
                 VALUES 
                    (
                                {cd_avaliacao}
                                , {cd_usuario_avaliador}
                                , '{tipo}'
                                , CURRENT_TIMESTAMP
                                , {cd_avaliacao_capa}
                                , {dt_conclusao}
                    )

        ");

        $this->dal->setAttribute( "{cd_avaliacao}", (int)$entidade->get_cd_avaliacao() );
        $this->dal->setAttribute( "{cd_usuario_avaliador}", (int)$entidade->get_cd_usuario_avaliador() );
        $this->dal->setAttribute( "{tipo}", $entidade->get_tipo() );
        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$entidade->get_cd_avaliacao_capa() );
        
        if($entidade->get_dt_conclusao()==''){ $entidade->set_dt_conclusao('null'); }
        $this->dal->setAttribute( "{dt_conclusao}", $entidade->get_dt_conclusao() );
            
        
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    public function change_status( $cd_usuario_avaliado, $dt_periodo, $new_status )
    {
        $bReturn = false;
        $this->dal->createQuery("

                 UPDATE  projetos.avaliacao SET etapa = 7, 
                         dt_ult_atualizacao = CURRENT_DATE, 
                         status = '{status}'
                 WHERE   cd_usuario_avaliado = {cd_usuario_avaliado}
                   AND   dt_periodo = {dt_periodo}

        ");

        $this->dal->setAttribute( "{status}", $new_status );
        $this->dal->setAttribute( "{cd_usuario_avaliado}", (int)$cd_usuario_avaliado );
        $this->dal->setAttribute( "{dt_periodo}", $dt_periodo );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.change_status() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
            
        }
        
        return $bReturn;
    }
    
    public function generate_queries__comp_inst__insert($cd_avaliacao, $array_institucionais )
    {
        $bReturn = false;

        $sql = "

            DELETE 
              FROM projetos.avaliacoes_comp_inst 
             WHERE cd_avaliacao = {cd_avaliacao}; 

        ";
        $sql = str_replace( '{cd_avaliacao}', pg_escape_string($cd_avaliacao), $sql );

        foreach( $array_institucionais as $row )
        {
            $sql .= "
                INSERT INTO projetos.avaliacoes_comp_inst
                                        (
                                        cd_avaliacao, cd_comp_inst, grau 
                                        ) 
                                    VALUES
                                        ( 
                                        {cd_avaliacao}, {cd_comp_inst}, {grau} 
                                        ) ; 
                ";
            $sql = str_replace( '{cd_avaliacao}', intval($row->get_cd_avaliacao()), $sql );
            $sql = str_replace( '{cd_comp_inst}', intval($row->get_cd_comp_inst()), $sql );
            $sql = str_replace( '{grau}', floatval($row->get_grau()), $sql );
        }

        $this->dal->createQuery($sql);
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.execute_queries() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
            
        }
        
        return $bReturn;
    }
    
    public function generate_queries__comp_espec__insert( $cd_avaliacao, $array_especificas )
    {
        $bReturn = false;

        $sql = " 

            DELETE 
              FROM projetos.avaliacoes_comp_espec
             WHERE cd_avaliacao = {cd_avaliacao}; 

        ";
        $sql = str_replace( '{cd_avaliacao}', pg_escape_string($cd_avaliacao), $sql );

        foreach( $array_especificas as $row )
        {
            $sql .= "
                INSERT INTO projetos.avaliacoes_comp_espec
                                        (
                                        cd_avaliacao, cd_comp_espec, grau 
                                        ) 
                                    VALUES
                                        ( 
                                        {cd_avaliacao}, {cd_comp_espec}, {grau} 
                                        ) ; 
                ";
            $sql = str_replace( '{cd_avaliacao}', intval($row->get_cd_avaliacao()), $sql );
            $sql = str_replace( '{cd_comp_espec}', intval($row->get_cd_comp_espec()), $sql );
            $sql = str_replace( '{grau}', floatval($row->get_grau()), $sql );
        }

        $this->dal->createQuery($sql);
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.execute_queries() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
            
        }

        return $bReturn;
    }

    public function generate_queries__resp__insert( $cd_avaliacao, $array_responsabilidades )
    {
        $bReturn = false;

        $sql = " 

            DELETE 
              FROM projetos.avaliacoes_responsabilidades
             WHERE cd_avaliacao = {cd_avaliacao}; 

        ";
        $sql = str_replace( '{cd_avaliacao}', pg_escape_string($cd_avaliacao), $sql );

        foreach( $array_responsabilidades as $row )
        {
            $sql .= "
                INSERT INTO projetos.avaliacoes_responsabilidades
					(
					cd_avaliacao, cd_responsabilidade, grau 
					) 
				VALUES
					( 
					{cd_avaliacao}, {cd_responsabilidade}, {grau} 
					);
                ";
            $sql = str_replace( '{cd_avaliacao}', intval($row->get_cd_avaliacao()), $sql );
            $sql = str_replace( '{cd_responsabilidade}', intval($row->get_cd_responsabilidade()), $sql );
            $sql = str_replace( '{grau}', floatval($row->get_grau()), $sql );
        }

        $this->dal->createQuery($sql);
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.execute_queries() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }

        return $bReturn;
    }

    public function execute_queries( $sql )
    {
        $bReturn = false;

        echo 'queries - ' . $sql; exit;

        $this->dal->createQuery($sql);

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.execute_queries() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }

        return $bReturn;
    }

    public function avaliacao_aspecto__insert(entity_projetos_avaliacao_aspecto $entidade)
    {
    	$bReturn = false;

        $entidade->cd_avaliacao_aspecto = getNextval("projetos", "avaliacao_aspecto", "cd_avaliacao_aspecto", $this->db);
    	$this->dal->createQuery("

	    	INSERT INTO projetos.avaliacao_aspecto
			(
				  cd_avaliacao_aspecto
				, cd_avaliacao
				, aspecto
				, resultado_esperado
				, acao
			)
			VALUES (
				  {cd_avaliacao_aspecto}
				, {cd_avaliacao}
				, '{aspecto}'
				, '{resultado_esperado}'
				, '{acao}'
			)

    	");
		$this->dal->setAttribute( "{cd_avaliacao_aspecto}", (int)$entidade->cd_avaliacao_aspecto);
		$this->dal->setAttribute( "{cd_avaliacao}", (int)$entidade->cd_avaliacao);
		$this->dal->setAttribute( "{aspecto}", $entidade->aspecto );
		$this->dal->setAttribute( "{resultado_esperado}", $entidade->resultado_esperado);
		$this->dal->setAttribute( "{acao}", $entidade->acao);
		$result = $this->dal->executeQuery();

		if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.avaliacao_aspecto__insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }

        return $bReturn;
    } 
    public function avaliacao_aspecto__update(entity_projetos_avaliacao_aspecto $entidade)
    {
    	$bReturn = false;

    	$this->dal->createQuery("

	    	UPDATE projetos.avaliacao_aspecto
			SET   
				  cd_avaliacao = {cd_avaliacao}
				, aspecto = '{aspecto}'
				, resultado_esperado = '{resultado_esperado}'
				, acao = '{acao}'
			WHERE cd_avaliacao_aspecto = {cd_avaliacao_aspecto}

    	");
		$this->dal->setAttribute( "{cd_avaliacao_aspecto}", (int)$entidade->cd_avaliacao_aspecto );
		$this->dal->setAttribute( "{cd_avaliacao}",(int) $entidade->cd_avaliacao );
		$this->dal->setAttribute( "{aspecto}", $entidade->aspecto );
		$this->dal->setAttribute( "{resultado_esperado}", $entidade->resultado_esperado );
		$this->dal->setAttribute( "{acao}", $entidade->acao );
		$result = $this->dal->executeQuery();

		if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.avaliacao_aspecto__update() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }

        return $bReturn;
    } 
    public function avaliacao_aspecto__delete( $pk )
    {
    	$bReturn = false;

    	$this->dal->createQuery("

	    	DELETE 
	    	FROM projetos.avaliacao_aspecto 
	    	WHERE cd_avaliacao_aspecto = {cd_avaliacao_aspecto}

    	");
		$this->dal->setAttribute( "{cd_avaliacao_aspecto}", (int)$pk );
		$result = $this->dal->executeQuery();

		if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.avaliacao_aspecto__delete() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }

        return $bReturn;
    }

    public function avaliacao_aspecto__load_by_pk( entity_projetos_avaliacao_aspecto $entidade )
    {
    	$bReturn = false;

    	$this->dal->createQuery("

	    	SELECT * 
	    	FROM projetos.avaliacao_aspecto 
	    	WHERE cd_avaliacao_aspecto = {cd_avaliacao_aspecto}

    	");
		$this->dal->setAttribute( "{cd_avaliacao_aspecto}", (int)$entidade->cd_avaliacao_aspecto );
		$result = $this->dal->getResultset();

		if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.avaliacao_aspecto__load_by_pk() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
        	if( $row = pg_fetch_array($result) )
        	{
        		$entidade->cd_avaliacao = $row['cd_avaliacao'];
        		$entidade->aspecto = $row['aspecto'];
        		$entidade->resultado_esperado = $row['resultado_esperado'];
        		$entidade->acao = $row['acao'];
        	}
        }

        return $bReturn;
    }

    public function avaliacao_aspecto__clone( $origem, $destino )
    {
    	$bReturn = false;

    	$this->dal->createQuery("

			INSERT INTO projetos.avaliacao_aspecto ( cd_avaliacao, aspecto, resultado_esperado, acao ) 
			SELECT {cd_avaliacao_destino}, aspecto, resultado_esperado, acao FROM projetos.avaliacao_aspecto 
			WHERE cd_avaliacao = {cd_avaliacao_origem};

    	");
		$this->dal->setAttribute( "{cd_avaliacao_origem}", (int)$origem );
		$this->dal->setAttribute( "{cd_avaliacao_destino}", (int)$destino );
		$result = $this->dal->executeQuery();

		if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.avaliacao_aspecto__clone() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }

        return $bReturn;
    }

    public function avaliador__clone( $cd_avaliacao )
    {
    	$bReturn = false;

    	$this->dal->createQuery("

			INSERT INTO projetos.avaliacao (cd_usuario_avaliador, tipo, dt_criacao, cd_avaliacao_capa)
			SELECT (SELECT MAX(cd_usuario_avaliador) FROM projetos.avaliacao_capa c WHERE c.cd_avaliacao_capa=a.cd_avaliacao_capa) as cd_superior
				, 'S'
				, dt_criacao
				, cd_avaliacao_capa 
			FROM projetos.avaliacao a
			WHERE cd_avaliacao = {cd_avaliacao};
			
			INSERT INTO projetos.avaliacoes_comp_inst ( cd_avaliacao, cd_comp_inst, grau )
			SELECT
				(  SELECT MAX(cd_avaliacao) FROM projetos.avaliacao a WHERE cd_avaliacao_capa in (select cd_avaliacao_capa FROM projetos.avaliacao WHERE cd_avaliacao={cd_avaliacao})  )
				, cd_comp_inst
				, grau
			FROM projetos.avaliacoes_comp_inst
			WHERE cd_avaliacao = {cd_avaliacao};
			
			INSERT INTO projetos.avaliacoes_comp_espec ( cd_avaliacao, cd_comp_espec, grau )
			SELECT
				(  SELECT MAX(cd_avaliacao) FROM projetos.avaliacao a WHERE cd_avaliacao_capa in (select cd_avaliacao_capa FROM projetos.avaliacao WHERE cd_avaliacao={cd_avaliacao})  )
				, cd_comp_espec
				, grau
			FROM projetos.avaliacoes_comp_espec 
			WHERE cd_avaliacao = {cd_avaliacao};
			
			INSERT INTO projetos.avaliacoes_responsabilidades ( cd_avaliacao, cd_responsabilidade, grau )
			SELECT
				(  SELECT MAX(cd_avaliacao) FROM projetos.avaliacao a WHERE cd_avaliacao_capa in (select cd_avaliacao_capa FROM projetos.avaliacao WHERE cd_avaliacao={cd_avaliacao})  )
				, cd_responsabilidade
				, grau
			FROM projetos.avaliacoes_responsabilidades s
			WHERE cd_avaliacao = {cd_avaliacao};
			
			INSERT INTO projetos.avaliacao_aspecto ( cd_avaliacao, aspecto, resultado_esperado, acao ) 
			SELECT 
				(  SELECT MAX(cd_avaliacao) FROM projetos.avaliacao a WHERE cd_avaliacao_capa in (select cd_avaliacao_capa FROM projetos.avaliacao WHERE cd_avaliacao={cd_avaliacao})  )
				, aspecto
				, resultado_esperado
				, acao 
			 FROM projetos.avaliacao_aspecto 
			WHERE cd_avaliacao = {cd_avaliacao};

    	");
		$this->dal->setAttribute( "{cd_avaliacao}", (int)$cd_avaliacao );
		$result = $this->dal->executeQuery();

		if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao.avaliador__clone() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }

        return $bReturn;
    }
}
?>