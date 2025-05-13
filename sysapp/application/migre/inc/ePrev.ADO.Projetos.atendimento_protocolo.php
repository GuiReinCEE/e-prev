<?php
/**
 * Classe para acesso a dados de divulgacao.divulgacao 
 * 
 * @access public
 * @package ePrev
 * @subpackage ADO
 * @require ePrev.Util.Message.php
 * @require ePrev.DAL.DBConnection.php
 */
class ADO_projetos_atendimento_protocolo {

    // DAL
    private $db;
    private $dal;

    function ADO_projetos_atendimento_protocolo( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function fetchAll()
    {
        $this->dal->createQuery("

            SELECT    a.cd_atendimento_protocolo
                    , a.nome
                    , a.tipo
                    , a.identificacao
                    , to_char(a.dt_criacao, 'DD-MM-YYYY HH:MI') AS dt_criacao
                    , to_char(a.dt_recebimento, 'DD-MM-YYYY HH:MI') AS dt_recebimento
                    , a.cd_usuario_recebimento
                    , a.cd_usuario_criacao
                    , a.destino
                    , a.cd_empresa
                    , a.cd_registro_empregado
                    , a.seq_dependencia
                    , b.guerra as nome_gap
                    , c.guerra as nome_gad
                    , a.cd_atendimento_protocolo_tipo
                    , a.cd_atendimento_protocolo_discriminacao
              FROM projetos.atendimento_protocolo a
        INNER JOIN projetos.usuarios_controledi b
                   ON a.cd_usuario_criacao = b.codigo
         LEFT JOIN projetos.usuarios_controledi c
                   ON a.cd_usuario_recebimento = c.codigo
          ORDER BY a.dt_criacao DESC

        ");
        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_projetos_atendimento_protocolo.fetchAll() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return $result;
    }
    
    public function fetchByFilter( helper_correspondencia_gap__fetch_by_filter $filtro )
    {
        $where = "";
        $this->dal->createQuery("

            SELECT    a.cd_atendimento_protocolo
                    , a.nome
                    , papt.nome as tipo_nome
                    , papd.nome as discriminacao_nome
                    , a.identificacao
                    , to_char(a.dt_criacao, 'DD/MM/YYYY HH24:MI') AS dt_criacao
                    , to_char(a.dt_recebimento, 'DD/MM/YYYY HH24:MI') AS dt_recebimento
                    , a.cd_usuario_recebimento
                    , a.cd_usuario_criacao
                    , a.destino
                    , a.cd_empresa
                    , a.cd_registro_empregado
                    , a.seq_dependencia
                    , to_char(a.dt_cancelamento, 'DD-MM-YYYY HH24:MI') AS dt_cancelamento
                    , a.motivo_cancelamento
                    , b.guerra as nome_gap
                    , c.guerra as nome_gad
                    , a.cd_atendimento_protocolo_tipo
                    , a.cd_atendimento_protocolo_discriminacao
                    , a.cd_atendimento
                    , a.cd_encaminhamento

              FROM projetos.atendimento_protocolo a
        INNER JOIN projetos.usuarios_controledi b
                   ON a.cd_usuario_criacao = b.codigo
         LEFT JOIN projetos.usuarios_controledi c
                   ON a.cd_usuario_recebimento = c.codigo
		 LEFT JOIN projetos.atendimento_protocolo_tipo papt
		           ON a.cd_atendimento_protocolo_tipo = papt.cd_atendimento_protocolo_tipo
		 LEFT JOIN projetos.atendimento_protocolo_discriminacao papd
		           ON a.cd_atendimento_protocolo_discriminacao = papd.cd_atendimento_protocolo_discriminacao
		           
            {WHERE}

          ORDER BY a.dt_criacao DESC

        ");

        // Filtros
        $aux = " WHERE ";
        if ($filtro->getcd_atendimento_protocolo_discriminacao()!="")
        {
			$where .= $aux . " a.cd_atendimento_protocolo_discriminacao = {cd_atendimento_protocolo_discriminacao} ";
            $aux = " AND ";
		}
        if ($filtro->getcd_atendimento_protocolo_tipo()!="")
        {
			$where .= $aux . " a.cd_atendimento_protocolo_tipo = {cd_atendimento_protocolo_tipo} ";
            $aux = " AND ";
		}
        if ($filtro->getcd_empresa()!="")
        {
			$where .= $aux . " a.cd_empresa = {cd_empresa} ";
            $aux = " AND ";
		}
        if ($filtro->getcd_registro_empregado()!="")
        {
            $where .= $aux . " a.cd_registro_empregado = {cd_registro_empregado} ";
            $aux = " AND ";
        }
        if ($filtro->getseq_dependencia()!="")
        {
            $where .= $aux . " a.seq_dependencia = {seq_dependencia} ";
            $aux = " AND ";
        }
        if ($filtro->get_nome()!="")
        {
            $where .= $aux . " UPPER(a.nome) like UPPER('%{nome}%') ";
            $aux = " AND ";
        }
        if ($filtro->getcd_atendimento()!="")
        {
            $where .= $aux . " a.cd_atendimento = {cd_atendimento} ";
            $aux = " AND ";
        }
        if ($filtro->getcd_encaminhamento()!="")
        {
            $where .= $aux . " a.cd_encaminhamento = {cd_encaminhamento} ";
            $aux = " AND ";
        }
        
        if ($filtro->dt_criacao__inicial!='' AND $filtro->dt_criacao__final!='')
        {
        	if($filtro->hr_criacao__inicial!="" && $filtro->hr_criacao__final!="")
        	{
        		$where .= $aux . " a.dt_criacao BETWEEN TO_TIMESTAMP('{dt_criacao_inicial} {hr_criacao_inicial}', 'DD/MM/YYYY HH24:MI') AND TO_TIMESTAMP('{dt_criacao_final} {hr_criacao_final}', 'DD/MM/YYYY HH24:MI') ";
	            $aux = " AND ";
        	}
        	else
        	{
	            $where .= $aux . " DATE_TRUNC('day', a.dt_criacao) BETWEEN TO_DATE('{dt_criacao_inicial}', 'DD/MM/YYYY') AND TO_DATE('{dt_criacao_final}', 'DD/MM/YYYY') ";
	            $aux = " AND ";
        	}
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
        if ($filtro->getdt_recebimento()!="")
        {
            if ($filtro->getdt_recebimento()=="null") {
                $where .= $aux . " a.dt_recebimento IS NULL ";
                $aux = " AND ";
			} else {
                $where .= $aux . " DATE_TRUNC('day',a.dt_recebimento) = TO_DATE('{dt_recebimento}', 'DD/MM/YYYY') ";
                $aux = " AND ";
            }
        }
        if ($filtro->getdt_cancelamento()!="")
        {
            if ($filtro->getdt_cancelamento()=="null") {
                $where .= $aux . " a.dt_cancelamento IS NULL ";
                $aux = " AND ";
			} else {
                $where .= $aux . " DATE_TRUNC('day',a.dt_cancelamento) = TO_DATE('{dt_cancelamento}','DD/MM/YYYY') ";
                $aux = " AND ";
            }
        }
        if( $filtro->getcd_usuario_criacao()!="" )
        {
        	$where .= $aux . " cd_usuario_criacao = {cd_usuario_criacao} ";
            $aux = " AND ";
        }

        $this->dal->setWhere( $where );
        $this->dal->setAttribute( "{cd_atendimento_protocolo_tipo}", (int)$filtro->getcd_atendimento_protocolo_tipo() );
        $this->dal->setAttribute( "{cd_atendimento_protocolo_discriminacao}", (int)$filtro->getcd_atendimento_protocolo_discriminacao() );
        $this->dal->setAttribute( "{cd_empresa}", (int)$filtro->getcd_empresa() );
        $this->dal->setAttribute( "{cd_registro_empregado}", (int)$filtro->getcd_registro_empregado() );
        $this->dal->setAttribute( "{seq_dependencia}", (int)$filtro->getseq_dependencia() );
        $this->dal->setAttribute( "{nome}", $filtro->get_nome() );
        $this->dal->setAttribute( "{dt_criacao_inicial}", $filtro->dt_criacao__inicial );
        $this->dal->setAttribute( "{dt_criacao_final}", $filtro->dt_criacao__final );
        $this->dal->setAttribute( "{hr_criacao_inicial}", $filtro->hr_criacao__inicial );
        $this->dal->setAttribute( "{hr_criacao_final}", $filtro->hr_criacao__final );
        $this->dal->setAttribute( "{dt_recebimento}", $filtro->getdt_recebimento() );
        $this->dal->setAttribute( "{dt_cancelamento}", $filtro->getdt_cancelamento() );
        $this->dal->setAttribute( "{cd_usuario_criacao}", $filtro->getcd_usuario_criacao() );
        $this->dal->setAttribute( "{cd_atendimento}", (int)$filtro->getcd_atendimento() );
        $this->dal->setAttribute( "{cd_encaminhamento}", (int)$filtro->getcd_encaminhamento() );
        // Filtros

        $result = $this->dal->getResultset();
        // echo $this->dal->getMessage();
        
        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_projetos_atendimento_protocolo.fetchByFilter() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return $result;
    }
    
    public function loadById( $entidade )
    {
        $this->dal->createQuery("

            SELECT    a.cd_atendimento_protocolo
                    , a.nome
                    , a.tipo
                    , a.identificacao
                    , to_char(a.dt_criacao, 'DD-MM-YYYY HH:MI') AS dt_criacao
                    , a.cd_usuario_recebimento
                    , to_char(a.dt_recebimento, 'DD-MM-YYYY HH:MI') AS dt_recebimento
                    , a.cd_usuario_criacao
                    , a.destino
                    , a.cd_empresa
                    , a.cd_registro_empregado
                    , a.seq_dependencia
                    , to_char(a.dt_cancelamento, 'DD-MM-YYYY HH:MI') AS dt_cancelamento
                    , a.motivo_cancelamento
                    , b.guerra as nome_gap
                    , c.guerra as nome_gad
                    , a.cd_atendimento_protocolo_tipo
                    , a.cd_atendimento_protocolo_discriminacao
                    , a.cd_atendimento
                    , a.cd_encaminhamento
                    
              FROM projetos.atendimento_protocolo a
        INNER JOIN projetos.usuarios_controledi b
                   ON a.cd_usuario_criacao = b.codigo
         LEFT JOIN projetos.usuarios_controledi c
                   ON a.cd_usuario_recebimento = c.codigo

            WHERE cd_atendimento_protocolo = {cd_atendimento_protocolo}

          ORDER BY a.dt_criacao DESC
        ");

        $this->dal->setAttribute( "{cd_atendimento_protocolo}", (int)$entidade->getcd_atendimento_protocolo() );

        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) 
        {
            $result = null;
            throw new Exception('Erro em ADO_projetos_atendimento_protocolo.loadById() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }
        else 
        {
            $row = pg_fetch_array($result);
            if ($row) 
            {
				$entidade->setcd_empresa( $row["cd_empresa"] );
				$entidade->set_nome( $row["nome"] );
				$entidade->setcd_registro_empregado( $row["cd_registro_empregado"] );
				$entidade->setseq_dependencia( $row["seq_dependencia"] );
				$entidade->setdestino( $row["destino"] );
				$entidade->settipo( $row["tipo"] );
				$entidade->setidentificacao($row["identificacao"]);
				$entidade->setcd_usuario_criacao( $row["cd_usuario_criacao"] );
				$entidade->setdt_criacao($row["dt_criacao"]);
				$entidade->setcd_usuario_recebimento( $row["cd_usuario_recebimento"] );
				$entidade->setdt_recebimento($row["dt_recebimento"]);
				$entidade->setdt_cancelamento($row["dt_cancelamento"]);
				$entidade->setmotivo_cancelamento($row["motivo_cancelamento"]);
				$entidade->setcd_atendimento_protocolo_tipo($row["cd_atendimento_protocolo_tipo"]);
				$entidade->setcd_atendimento_protocolo_discriminacao($row["cd_atendimento_protocolo_discriminacao"]);
				$entidade->setcd_atendimento($row["cd_atendimento"]);
				$entidade->setcd_encaminhamento($row["cd_encaminhamento"]);

                // usuarios
                $usuario_1 = new entity_projetos_usuarios_controledi();
                $usuario_1->set_codigo( $row["cd_usuario_criacao"] );
                $usuario_1->set_guerra( $row["nome_gap"] );
                $entidade->setUsuarioCriacao( $usuario_1 );
                $usuario_2 = new entity_projetos_usuarios_controledi();
                $usuario_2->set_codigo( $row["cd_usuario_recebimento"] );
                $usuario_2->set_guerra( $row["nome_gad"] );
                $entidade->setUsuarioRecebimento( $usuario_2 );

                $row = null;
                $result = null;
			}
        }
        return true;
    }

    public function insert( entity_projetos_atendimento_protocolo $entidade )
    {
        $bReturn = false;

        $entidade->setcd_atendimento_protocolo( getNextval("projetos", "atendimento_protocolo", "cd_atendimento_protocolo", $this->db) );
        
        $this->dal->createQuery("

            INSERT INTO projetos.atendimento_protocolo(
                        cd_atendimento_protocolo
                      , nome
                      , tipo
                      , identificacao
                      , dt_criacao
                      , cd_usuario_recebimento
                      , cd_usuario_criacao
                      , destino
                      , cd_empresa
                      , cd_registro_empregado
                      , seq_dependencia
                      , cd_atendimento_protocolo_tipo
                      , cd_atendimento_protocolo_discriminacao
                      , cd_atendimento
                      , cd_encaminhamento
                      )
                VALUES ({cd_atendimento_protocolo}
                      , '{nome}'
                      , '{tipo}'
                      , '{identificacao}'
                      , CURRENT_TIMESTAMP
                      , {cd_usuario_recebimento}
                      , {cd_usuario_criacao}
                      , '{destino}'
                      , {cd_empresa}
                      , {cd_registro_empregado}
                      , {seq_dependencia} 
                      , {cd_atendimento_protocolo_tipo}
                      , {cd_atendimento_protocolo_discriminacao}
                      , {cd_atendimento}
                      , {cd_encaminhamento}
                      );

        ");

        $this->dal->setAttribute( "{cd_atendimento_protocolo}", (int)$entidade->getcd_atendimento_protocolo() );
        $this->dal->setAttribute( "{nome}", $entidade->get_nome() );
        $this->dal->setAttribute( "{tipo}", $entidade->gettipo() );
        $this->dal->setAttribute( "{identificacao}", $entidade->getidentificacao() );
        
		if($entidade->getcd_usuario_recebimento()=="")
		{
			$this->dal->setAttribute( "{cd_usuario_recebimento}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_usuario_recebimento}", (int)$entidade->getcd_usuario_recebimento() );
		}

		if($entidade->getcd_empresa()=="")
		{
			$this->dal->setAttribute( "{cd_empresa}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_empresa}", (int)$entidade->getcd_empresa() );
		}

		if($entidade->getcd_registro_empregado()=="")
		{
			$this->dal->setAttribute( "{cd_registro_empregado}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_registro_empregado}", (int)$entidade->getcd_registro_empregado() );
		}
		
		if($entidade->getseq_dependencia()=="")
		{
			$this->dal->setAttribute( "{seq_dependencia}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{seq_dependencia}", (int)$entidade->getseq_dependencia() );
		}
    
		if((int)$entidade->getcd_encaminhamento()==0)
		{
			$this->dal->setAttribute( "{cd_encaminhamento}", "null");
		}
		else
		{
			$this->dal->setAttribute( "{cd_encaminhamento}", (int)$entidade->getcd_encaminhamento());
		}

		$this->dal->setAttribute( "{cd_usuario_criacao}", (int)$entidade->getcd_usuario_criacao() );
        $this->dal->setAttribute( "{destino}", $entidade->getdestino() );

        $this->dal->setAttribute( "{cd_atendimento_protocolo_tipo}", (int)$entidade->getcd_atendimento_protocolo_tipo());
        $this->dal->setAttribute( "{cd_atendimento_protocolo_discriminacao}", (int)$entidade->getcd_atendimento_protocolo_discriminacao());
        
    	if((int)$entidade->getcd_atendimento()==0)
        {
        	$this->dal->setAttribute( "{cd_atendimento}", "null" );
        }
        else
        {
        	$this->dal->setAttribute( "{cd_atendimento}", (int)$entidade->getcd_atendimento() );
        }

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_atendimento_protocolo.insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    public function update( entity_projetos_atendimento_protocolo $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.atendimento_protocolo
               SET tipo='{tipo}'
                 , nome='{nome}'  
                 , identificacao='{identificacao}'
                 , cd_usuario_recebimento={cd_usuario_recebimento}
                 , cd_usuario_criacao='{cd_usuario_criacao}'
                 , destino='{destino}'
                 , cd_empresa={cd_empresa}
                 , cd_registro_empregado={cd_registro_empregado}
                 , seq_dependencia={seq_dependencia}
                 , cd_atendimento_protocolo_tipo={cd_atendimento_protocolo_tipo}
                 , cd_atendimento_protocolo_discriminacao={cd_atendimento_protocolo_discriminacao}
                 , cd_atendimento={cd_atendimento}
                 , cd_encaminhamento={cd_encaminhamento}
             WHERE cd_atendimento_protocolo={cd_atendimento_protocolo};

        ");

        $this->dal->setAttribute( "{cd_atendimento_protocolo}", (int)$entidade->getcd_atendimento_protocolo() );
        $this->dal->setAttribute( "{tipo}", $entidade->gettipo() );
        $this->dal->setAttribute( "{nome}", $entidade->get_nome() );
        $this->dal->setAttribute( "{identificacao}", $entidade->getidentificacao() );

		if($entidade->getcd_usuario_recebimento()=="")
		{
			$this->dal->setAttribute( "{cd_usuario_recebimento}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_usuario_recebimento}", (int)$entidade->getcd_usuario_recebimento() );
		}

		if($entidade->getcd_empresa()=="")
		{
			$this->dal->setAttribute( "{cd_empresa}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_empresa}", (int)$entidade->getcd_empresa() );
		}

		if($entidade->getcd_registro_empregado()=="")
		{
			$this->dal->setAttribute( "{cd_registro_empregado}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_registro_empregado}", (int)$entidade->getcd_registro_empregado() );
		}
		
		if($entidade->getseq_dependencia()=="")
		{
			$this->dal->setAttribute( "{seq_dependencia}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{seq_dependencia}", (int)$entidade->getseq_dependencia() );
		}

		$this->dal->setAttribute( "{cd_usuario_criacao}", (int)$entidade->getcd_usuario_criacao() );
        $this->dal->setAttribute( "{destino}", $entidade->getdestino() );
        $this->dal->setAttribute( "{cd_atendimento_protocolo_tipo}", (int)$entidade->getcd_atendimento_protocolo_tipo() );
        $this->dal->setAttribute( "{cd_atendimento_protocolo_discriminacao}", (int)$entidade->getcd_atendimento_protocolo_discriminacao() );
        
        if((int)$entidade->getcd_atendimento()==0)
        {
        	$this->dal->setAttribute( "{cd_atendimento}", "null" );
        }
        else
        {
        	$this->dal->setAttribute( "{cd_atendimento}", (int)$entidade->getcd_atendimento() );
        }
    	
        if((int)$entidade->getcd_encaminhamento()==0)
		{
			$this->dal->setAttribute( "{cd_encaminhamento}", "null");
		}
		else
		{
			$this->dal->setAttribute( "{cd_encaminhamento}", (int)$entidade->getcd_encaminhamento());
		}
        
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_atendimento_protocolo.update() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    /**
     * Grava na tabela o código do usuário que recebeu e a data de recebimento
     * 
     * @param entity_projetos_atendimento_protocolo $entidade Preencher obrigatoriamente atributos cd_atendimento_protocolo e cd_usuario_recebimento
     *        p.ex: 
     *              $ent = new entity_projetos_atendimento_protocolo();
     *              $ent->setcd_atendimento_protocolo( 15 );
     *              $ent->setcd_usuario_recebimento( 91 );
     *              $ado = new ADO_projetos_atendimento_protocolo( $db );
     *              $ret = $ado->updateReceive( $ent );
     */
    public function updateReceive( entity_projetos_atendimento_protocolo $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.atendimento_protocolo
               SET cd_usuario_recebimento={cd_usuario_recebimento}, dt_recebimento=CURRENT_TIMESTAMP 
             WHERE cd_atendimento_protocolo={cd_atendimento_protocolo};

        ");

        $this->dal->setAttribute( "{cd_atendimento_protocolo}", (int)$entidade->getcd_atendimento_protocolo() );
        $this->dal->setAttribute( "{cd_usuario_recebimento}", (int)$entidade->getcd_usuario_recebimento() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_atendimento_protocolo.updateReceive() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    /**
     * Grava na tabela a data e o motivo do cancelamento da mensagem
     * 
     * @param entity_projetos_atendimento_protocolo $entidade Preencher obrigatoriamente atributos cd_atendimento_protocolo e motivo_cancelamento
     *        p.ex: 
     *              $ent = new entity_projetos_atendimento_protocolo();
     *              $ent->setcd_atendimento_protocolo( 15 );
     *              $ent->setmotivo_cancelamento( 'teste' );
     *              $ado = new ADO_projetos_atendimento_protocolo( $db );
     *              $ret = $ado->updateReceive( $ent );
     */
    public function updateCancel( entity_projetos_atendimento_protocolo $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.atendimento_protocolo
               SET dt_cancelamento=CURRENT_TIMESTAMP
                 , motivo_cancelamento='{motivo_cancelamento}'
             WHERE cd_atendimento_protocolo={cd_atendimento_protocolo};

        ");

        $this->dal->setAttribute( "{cd_atendimento_protocolo}", (int)$entidade->getcd_atendimento_protocolo() );
        $this->dal->setAttribute( "{motivo_cancelamento}", $entidade->getmotivo_cancelamento() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_atendimento_protocolo.updateCancel() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
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
              FROM projetos.atendimento_protocolo
             WHERE cd_atendimento_protocolo IN ({cd_atendimento_protocolo});

        ");

        $this->dal->setAttribute( "{cd_atendimento_protocolo}", (int)$value );
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_atendimento_protocolo.delete() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
}
?>