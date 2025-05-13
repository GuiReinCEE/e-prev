<?php
class ADO_projetos_atendimento_recadastro 
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

    public function fetchAll()
    {
        $this->dal->createQuery("

            SELECT    a.cd_atendimento_recadastro
                    , a.nome
                    , to_char(a.dt_criacao, 'DD-MM-YYYY HH:MI') AS dt_criacao
                    , a.cd_usuario_criacao
                    , a.cd_empresa
                    , a.cd_registro_empregado
                    , a.seq_dependencia
                    , b.guerra as nome_gap
                    , a.observacao
              FROM projetos.atendimento_recadastro a
        INNER JOIN projetos.usuarios_controledi b
                   ON a.cd_usuario_criacao = b.codigo
          ORDER BY a.dt_criacao DESC

        ");
        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_projetos_atendimento_recadastro.fetchAll() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return $result;
    }
    
    public function fetchByFilter( helper_recadastro_gap__fetch_by_filter $filtro )
    {
        $where = "";
        $this->dal->createQuery("

            SELECT    a.cd_atendimento_recadastro
                    , a.nome
                    , to_char(a.dt_criacao, 'DD-MM-YYYY HH:MI') AS dt_criacao
                    , to_char(a.dt_cancelamento, 'DD-MM-YYYY HH:MI') AS dt_cancelamento
                    , a.cd_usuario_criacao
                    , a.cd_empresa
                    , a.cd_registro_empregado
                    , a.seq_dependencia
                    , b.guerra as nome_gap
                    , a.observacao
                    , a.servico_social
                    , to_char( a.dt_atualizacao, 'DD/MM/YYYY HH24:MI' ) as dt_atualizacao
                    , a.cd_usuario_atualizacao
					, ua.guerra as nome_usuario_atualizacao
                    , pp.ddd
                    , pp.telefone
                    , pp.ddd_outro
                    , pp.telefone_outro

              FROM projetos.atendimento_recadastro a
        INNER JOIN projetos.usuarios_controledi b
                   ON a.cd_usuario_criacao = b.codigo
        INNER JOIN public.participantes pp 
        		   ON a.cd_empresa = pp.cd_empresa AND a.cd_registro_empregado = pp.cd_registro_empregado AND a.seq_dependencia = pp.seq_dependencia
		LEFT JOIN projetos.usuarios_controledi ua
                   ON a.cd_usuario_atualizacao = ua.codigo

            {WHERE}

          ORDER BY a.dt_criacao DESC

        ");

        // Filtros
        $aux = " WHERE ";
        if ($filtro->cd_empresa!="")
        {
			$where .= $aux . " a.cd_empresa = {cd_empresa} ";
            $aux = " AND ";
		}
        if ($filtro->cd_registro_empregado!="")
        {
            $where .= $aux . " a.cd_registro_empregado = {cd_registro_empregado} ";
            $aux = " AND ";
        }
        if ($filtro->seq_dependencia!="")
        {
            $where .= $aux . " a.seq_dependencia = {seq_dependencia} ";
            $aux = " AND ";
        }
        if ($filtro->dt_criacao__inicial!='' AND $filtro->dt_criacao__final!='')
        {
            $where .= $aux . " DATE_TRUNC('day', a.dt_criacao) BETWEEN TO_DATE('{dt_criacao_inicial}', 'DD/MM/YYYY') AND TO_DATE('{dt_criacao_final}', 'DD/MM/YYYY') ";
            $aux = " AND ";
        }
        else if ($filtro->dt_criacao__inicial!='')
        {
            $where .= $aux . " DATE_TRUNC('day', a.dt_criacao) = TO_DATE('{dt_criacao_inicial}', 'DD/MM/YYYY') ";
            $aux = " AND ";
        }
        else if ($filtro->dt_criacao__final!='')
        {
            $where .= $aux . " DATE_TRUNC('day', a.dt_criacao) = '{dt_criacao_final}' ";
            $aux = " AND ";
        }
        if ($filtro->dt_cancelamento!="")
        {
            if ($filtro->dt_cancelamento=="null") {
                $where .= $aux . " a.dt_cancelamento IS NULL ";
                $aux = " AND ";
			} else {
                $where .= $aux . " DATE_TRUNC('day',a.dt_cancelamento) = TO_DATE('{dt_cancelamento}','DD/MM/YYYY') ";
                $aux = " AND ";
            }
        }

        $this->dal->setWhere( $where );
        $this->dal->setAttribute( "{cd_empresa}", (int)$filtro->cd_empresa );
        $this->dal->setAttribute( "{cd_registro_empregado}", (int)$filtro->cd_registro_empregado );
        $this->dal->setAttribute( "{seq_dependencia}", (int)$filtro->seq_dependencia );
        $this->dal->setAttribute( "{dt_criacao_inicial}", $filtro->dt_criacao__inicial );
        $this->dal->setAttribute( "{dt_criacao_final}", $filtro->dt_criacao__final );
        $this->dal->setAttribute( "{dt_cancelamento}", $filtro->dt_cancelamento );
        // Filtros

        $result = $this->dal->getResultset();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_atendimento_recadastro.fetchByFilter() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return $result;
    }

    public function loadById( entity_projetos_atendimento_recadastro $entidade )
    {
        $this->dal->createQuery("

            SELECT    a.cd_atendimento_recadastro
                    , a.cd_empresa
                    , a.cd_registro_empregado
                    , a.seq_dependencia
                    , a.nome
                    , to_char(a.dt_criacao, 'DD-MM-YYYY HH:MI') AS dt_criacao
                    , a.cd_usuario_criacao
                    , to_char(a.dt_cancelamento, 'DD-MM-YYYY HH:MI') AS dt_cancelamento
                    , a.motivo_cancelamento
                    , b.guerra as nome_gap
                    , a.observacao
                    , a.dt_periodo
                    , a.servico_social
                    , to_char( a.dt_atualizacao, 'DD/MM/YYYY HH24:MI' ) as dt_atualizacao
                    , a.cd_usuario_atualizacao
					, ua.guerra as nome_usuario_atualizacao

              FROM projetos.atendimento_recadastro a
        INNER JOIN projetos.usuarios_controledi b
                   ON a.cd_usuario_criacao = b.codigo
        LEFT JOIN projetos.usuarios_controledi ua
                   ON a.cd_usuario_atualizacao = ua.codigo
            WHERE cd_atendimento_recadastro = {cd_atendimento_recadastro}

          ORDER BY a.dt_criacao DESC
        ");

        $this->dal->setAttribute( "{cd_atendimento_recadastro}", (int)$entidade->cd_atendimento_recadastro );

        $result = $this->dal->getResultset();

        if($this->dal->haveError()) 
        {
            $result = null;
            throw new Exception('Erro em ADO_projetos_atendimento_recadastro.loadById() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }
        else 
        {
            $row = pg_fetch_array($result);
            if($row) 
            {
				$entidade->cd_atendimento_recadastro = $row["cd_atendimento_recadastro"];
				$entidade->cd_empresa = $row["cd_empresa"];
				$entidade->cd_registro_empregado = $row["cd_registro_empregado"];
				$entidade->seq_dependencia = $row["seq_dependencia"];
				$entidade->nome = $row["nome"];
				$entidade->cd_usuario_criacao = $row["cd_usuario_criacao"];
				$entidade->dt_criacao = $row["dt_criacao"];
				$entidade->dt_cancelamento = $row["dt_cancelamento"];
				$entidade->motivo_cancelamento = $row["motivo_cancelamento"];
				$entidade->observacao = $row["observacao"];
				$entidade->dt_periodo = $row["dt_periodo"];
				$entidade->servico_social = $row["servico_social"];
				$entidade->dt_atualizacao = $row["dt_atualizacao"];
				$entidade->cd_usuario_atualizacao = $row["cd_usuario_atualizacao"];
				$entidade->nome_usuario_atualizacao = $row["nome_usuario_atualizacao"];

                // usuarios
                $usuario_1 = new entity_projetos_usuarios_controledi();
                $usuario_1->set_codigo( $row["cd_usuario_criacao"] );
                $usuario_1->set_guerra( $row["nome_gap"] );
                $entidade->usuarioCriacao = $usuario_1;

                $row = null;
                $result = null;
			}
        }
        return true;
    }

    public function insert( entity_projetos_atendimento_recadastro $entidade )
    {
        $bReturn = false;

        $entidade->cd_atendimento_recadastro = getNextval("projetos", "atendimento_recadastro", "cd_atendimento_recadastro", $this->db);

        $this->dal->createQuery("

            INSERT INTO projetos.atendimento_recadastro(
                        cd_atendimento_recadastro
                      , cd_empresa
                      , cd_registro_empregado
                      , seq_dependencia 
                      , nome
                      , dt_criacao
                      , cd_usuario_criacao
                      , observacao
                      , dt_periodo
                      , servico_social
                      )
                VALUES ({cd_atendimento_recadastro}
                      , {cd_empresa}
                      , {cd_registro_empregado}
                      , {seq_dependencia} 
                      , '{nome}'
                      , CURRENT_TIMESTAMP
                      , {cd_usuario_criacao}
                      , '{observacao}'
                      , {dt_periodo}
                      , '{servico_social}'
                      );

        ");

        $this->dal->setAttribute( "{cd_atendimento_recadastro}", (int)$entidade->cd_atendimento_recadastro );

		if($entidade->cd_empresa=="")
		{
			$this->dal->setAttribute( "{cd_empresa}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_empresa}", (int)$entidade->cd_empresa );
		}

		if($entidade->cd_registro_empregado=="")
		{
			$this->dal->setAttribute( "{cd_registro_empregado}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_registro_empregado}", (int)$entidade->cd_registro_empregado );
		}

		if($entidade->seq_dependencia=="")
		{
	        $this->dal->setAttribute( "{seq_dependencia}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{seq_dependencia}", (int)$entidade->seq_dependencia );
		}

        $this->dal->setAttribute( "{nome}", $entidade->nome );
        $this->dal->setAttribute( "{cd_usuario_criacao}", (int)$entidade->cd_usuario_criacao );
        $this->dal->setAttribute( "{observacao}", $entidade->observacao );
        $this->dal->setAttribute( "{dt_periodo}", $entidade->dt_periodo );
        $this->dal->setAttribute( "{servico_social}", $entidade->servico_social );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_atendimento_recadastro.insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    public function update( entity_projetos_atendimento_recadastro $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.atendimento_recadastro
               SET 
                   nome='{nome}'  
                 , cd_empresa={cd_empresa}
                 , cd_registro_empregado={cd_registro_empregado}
                 , seq_dependencia={seq_dependencia}
                 , observacao='{observacao}'
                 , dt_periodo={dt_periodo}
                 , servico_social='{servico_social}'
				, dt_atualizacao = current_timestamp
				, cd_usuario_atualizacao = {cd_usuario_atualizacao}

             WHERE cd_atendimento_recadastro={cd_atendimento_recadastro};

        ");

        $this->dal->setAttribute( "{cd_atendimento_recadastro}", (int)$entidade->cd_atendimento_recadastro );
        $this->dal->setAttribute( "{nome}", $entidade->nome );
        // $this->dal->setAttribute( "{cd_usuario_criacao}", (int)$entidade->cd_usuario_criacao );
        $this->dal->setAttribute( "{cd_usuario_atualizacao}", intval($_SESSION['Z']) );
        if($entidade->cd_empresa=="")
		{
			$this->dal->setAttribute( "{cd_empresa}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_empresa}", (int)$entidade->cd_empresa );
		}

		if($entidade->cd_registro_empregado=="")
		{
			$this->dal->setAttribute( "{cd_registro_empregado}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_registro_empregado}", (int)$entidade->cd_registro_empregado );
		}

		if($entidade->seq_dependencia=="")
		{
	        $this->dal->setAttribute( "{seq_dependencia}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{seq_dependencia}", (int)$entidade->seq_dependencia );
		}
        $this->dal->setAttribute( "{observacao}", $entidade->observacao );
        $this->dal->setAttribute( "{dt_periodo}", $entidade->dt_periodo );
        $this->dal->setAttribute( "{servico_social}", $entidade->servico_social );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_atendimento_recadastro.update() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    public function updateCancel( entity_projetos_atendimento_recadastro  $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.atendimento_recadastro
               SET dt_cancelamento = CURRENT_TIMESTAMP
                 , motivo_cancelamento = '{motivo_cancelamento}'
             WHERE cd_atendimento_recadastro = {cd_atendimento_recadastro};

        ");

        $this->dal->setAttribute( "{cd_atendimento_recadastro}", (int)$entidade->cd_atendimento_recadastro );
        $this->dal->setAttribute( "{motivo_cancelamento}", $entidade->motivo_cancelamento );

        $result = $this->dal->executeQuery();
        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_atendimento_recadastro.updateCancel() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    public function delete( $value )
    {
        $bReturn = false;

        $this->dal->createQuery("

            DELETE 
              FROM projetos.atendimento_recadastro
             WHERE cd_atendimento_recadastro IN ({cd_atendimento_recadastro});

        ");

        $this->dal->setAttribute( "{cd_atendimento_recadastro}", (int)$value );
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_atendimento_recadastro.delete() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
}
?>