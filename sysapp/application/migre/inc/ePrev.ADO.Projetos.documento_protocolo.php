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
class ADO_projetos_documento_protocolo {

    // DAL
    private $db;
    private $dal;
    private $select_define = "";

    function ADO_projetos_documento_protocolo( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function set_select_define( $value )
    {
        $this->select_define = $value;
    }

    public function fetchAll()
    {
        echo 'ok';
        $this->dal->createQuery("

            SELECT ano
                 , contador
                 , cd_documento_protocolo
                 , dt_cadastro
                 , cd_usuario_cadastro
                 , dt_envio
                 , cd_usuario_envio
                 , dt_ok
                 , cd_usuario_ok
                 , dt_exclusao
                 , cd_usuario_exclusao
                 , motivo_exclusao
                 , ordem_itens
              FROM projetos.documento_protocolo
             WHERE dt_exclusao IS NULL
          ORDER BY ano DESC, contador DESC;

        ");
        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception(
				'Erro em ADO_projetos_documento_protocolo.fetchAll() ao executar comando SQL de consulta.'.$this->dal->getMessage()
			);
        }

        return $result;
    }

    public function setFetchByFilter_select_define($value)
    {
        $this->select_define = $value;
    }

    public function fetchByFilter( entity_projetos_documento_protocolo $filtro )
    {
        if ($this->select_define=="")
        {
            $this->dal->createQuery("
    
                SELECT a.ano
                     , a.contador
                     , a.cd_documento_protocolo
                     , a.dt_cadastro
                     , a.cd_usuario_cadastro
                     , a.dt_envio
                     , a.cd_usuario_envio
                     , a.dt_ok
                     , a.cd_usuario_ok
                     , a.dt_exclusao
                     , a.cd_usuario_exclusao
                     , a.motivo_exclusao
                     , a.ordem_itens
                     , b.guerra
                     , c.guerra
                     , d.guerra
                  FROM projetos.documento_protocolo a
             LEFT JOIN projetos.usuarios_controledi b
                       ON a.cd_usuario_cadastro = b.codigo
             LEFT JOIN projetos.usuarios_controledi c
                       ON a.cd_usuario_envio = c.codigo
             LEFT JOIN projetos.usuarios_controledi d
                       ON a.cd_usuario_ok = d.codigo
    
                 {WHERE}
          
                ORDER BY a.ano DESC, a.contador DESC;

            ");
		} 
        else 
        {
            $this->dal->createQuery("

                SELECT " . $this->select_define . "
                  FROM projetos.documento_protocolo a
             LEFT JOIN projetos.usuarios_controledi b
                       ON a.cd_usuario_cadastro = b.codigo
             LEFT JOIN projetos.usuarios_controledi c
                       ON a.cd_usuario_envio = c.codigo
             LEFT JOIN projetos.usuarios_controledi d
                       ON a.cd_usuario_ok = d.codigo

                 {WHERE}

                ORDER BY a.ano DESC, a.contador DESC;

			");
        }
        $this->select_define = "";

        // Filtros
        $where = " WHERE a.dt_exclusao IS NULL ";
        $aux = " AND ";
        if ($filtro->get_ano()!="")
        {
			$where .= $aux . " a.ano = {ano} ";
            $aux = " AND ";
		}
        if ( $filtro->get_contador()!="" )
        {
			$where .= $aux . " a.contador = {contador} ";
            $aux = " AND ";
		}
        // os nao recebidos pela gad incluem dt_ok e dt_indexacao NULLs
		if ($filtro->get_dt_ok()=="null")
        {
            $where .= $aux . " ( a.dt_ok IS NULL OR a.dt_indexacao IS NULL ) ";
            $aux = " AND ";
        }
        if ($filtro->get_dt_envio()=="NOT null")
        {
            $where .= $aux . " a.dt_envio IS NOT NULL ";
            $aux = " AND ";
        }

        $this->dal->setWhere( $where );
        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$filtro->get_cd_documento_protocolo() );
        $this->dal->setAttribute( "{ano}", (int)$filtro->get_ano() );
        $this->dal->setAttribute( "{contador}", (int)$filtro->get_contador() );
        // Filtros

        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.fetchByFilter() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return $result;
    }

    public function loadById( $entidade )
    {
        $this->dal->createQuery("

            SELECT a.ano
                 , a.contador
                 , a.cd_documento_protocolo
                 , TO_CHAR(a.dt_cadastro, 'DD/MM/YYYY HH:MI') AS dt_cadastro
                 , a.cd_usuario_cadastro
                 , TO_CHAR(a.dt_envio, 'DD/MM/YYYY HH:MI') AS dt_envio
                 , a.cd_usuario_envio
                 , TO_CHAR(a.dt_ok, 'DD/MM/YYYY HH:MI') AS dt_ok
                 , a.cd_usuario_ok
                 , TO_CHAR(a.dt_exclusao, 'DD/MM/YYYY HH:MI') AS dt_exclusao
                 , ordem_itens
                 , cd_usuario_exclusao
                 , b.guerra as guerra_cadastro
                 , b.usuario as usuario_cadastro
                 , c.guerra as guerra_envio
                 , d.guerra as guerra_ok
              FROM projetos.documento_protocolo a
         LEFT JOIN projetos.usuarios_controledi b
                   ON a.cd_usuario_cadastro = b.codigo
         LEFT JOIN projetos.usuarios_controledi c
                   ON a.cd_usuario_envio = c.codigo
         LEFT JOIN projetos.usuarios_controledi d
                   ON a.cd_usuario_ok = d.codigo

             WHERE a.dt_exclusao IS NULL 
               AND cd_documento_protocolo = {cd_documento_protocolo}

        ");

        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$entidade->get_cd_documento_protocolo() );

        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) 
        {
            $result = null;
            throw new Exception('Erro em ADO_projetos_documento_protocolo.loadById() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }
        else 
        {
            $row = pg_fetch_array($result);
            if ($row) 
            {
				$entidade->set_ano( $row["ano"] );
				$entidade->set_contador( $row["contador"] );
				$entidade->set_dt_cadastro( $row["dt_cadastro"] );
				$entidade->set_cd_usuario_cadastro( $row["cd_usuario_cadastro"] );
				$entidade->set_dt_envio( $row["dt_envio"] );
				$entidade->set_cd_usuario_envio($row["cd_usuario_envio"]);
				$entidade->set_dt_ok( $row["dt_ok"] );
				$entidade->set_cd_usuario_ok($row["cd_usuario_ok"]);
				$entidade->set_dt_exclusao( $row["dt_exclusao"] );
				$entidade->set_cd_usuario_exclusao($row["cd_usuario_exclusao"]);
				$entidade->set_ordem_itens($row["ordem_itens"]);

                $usuario_cadastro = new entity_projetos_usuarios_controledi();
                $usuario_cadastro->set_codigo( $row["cd_usuario_cadastro"] );
                $usuario_cadastro->set_guerra( $row["guerra_cadastro"] );
                $usuario_cadastro->set_usuario( $row["usuario_cadastro"] );
                $entidade->set_usuario_cadastro( $usuario_cadastro );

                $usuario_envio = new entity_projetos_usuarios_controledi();
                $usuario_envio->set_codigo( $row["cd_usuario_envio"] );
                $usuario_envio->set_guerra( $row["guerra_envio"] );
                $entidade->set_usuario_envio( $usuario_envio );

                $usuario_ok = new entity_projetos_usuarios_controledi();
                $usuario_ok->set_codigo( $row["cd_usuario_ok"] );
                $usuario_ok->set_guerra( $row["guerra_ok"] );
                $entidade->set_usuario_OK( $usuario_ok );

                $row = null;
                $result = null;
			}
        }
        return true;
    }

    public function insert( entity_projetos_documento_protocolo $entidade )
    {
        $bReturn = false;

        $entidade->set_cd_documento_protocolo( getNextval("projetos", "documento_protocolo", "cd_documento_protocolo", $this->db) );

        $entidade->set_ano( date("Y") );

        $this->dal->createQuery("

            SELECT MAX(contador)+1 as ultimo
              FROM projetos.documento_protocolo
             WHERE ano = {ano};

        ");

        $this->dal->setAttribute( "{ano}", (int)$entidade->get_ano() );
        $rstSequencia = $this->dal->getResultset();

        $entidade->set_contador( "1" );
        if ( $rstSequencia )
        {
            if($row = pg_fetch_array($rstSequencia))
            {
                if ($row["ultimo"]!="") {
                    $entidade->set_contador( $row["ultimo"] );
				}
            }
        }

        $this->dal->createQuery("

            INSERT INTO projetos.documento_protocolo(
                    ano
                  , contador
                  , cd_documento_protocolo
                  , dt_cadastro
                  , cd_usuario_cadastro
                  , dt_envio
                  , cd_usuario_envio
                  , dt_ok
                  , cd_usuario_ok
                  , dt_exclusao
                  , cd_usuario_exclusao
                  , motivo_exclusao 
                  , ordem_itens 
                  )
            VALUES ({ano}
                  , {contador}
                  , {cd_documento_protocolo}
                  , CURRENT_TIMESTAMP
                  , {cd_usuario_cadastro}
                  , null
                  , null
                  , null
                  , null
                  , null
                  , null
                  , null 
                  , '{ordem_itens}' 
                  )

        ");

        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$entidade->get_cd_documento_protocolo() );
        $this->dal->setAttribute( "{ano}", (int)$entidade->get_ano() );
        $this->dal->setAttribute( "{contador}", (int)$entidade->get_contador() );
        $this->dal->setAttribute( "{cd_usuario_cadastro}", (int)$entidade->get_cd_usuario_cadastro() );
        $this->dal->setAttribute( "{ordem_itens}", $entidade->get_ordem_itens() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    public function update( entity_projetos_documento_protocolo $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.documento_protocolo
               SET ano={ano}
                 , contador={contador}
                 , dt_cadastro='{dt_cadastro}'
                 , cd_usuario_cadastro={cd_usuario_cadastro}
                 , dt_envio={dt_envio}
                 , cd_usuario_envio={cd_usuario_envio}
                 , dt_ok={dt_ok}
                 , cd_usuario_ok={cd_usuario}
                 , dt_exclusao={dt_exclusao}
                 , cd_usuario_exclusao={cd_usuario_exclusao}
                 , motivo_exclusao='{motivo_exclusao}'
                 , ordem_itens='{ordem_itens}'
             WHERE cd_documento_protocolo = {cd_documento_protocolo}

        ");

        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$entidade->get_documento_protocolo() );
        $this->dal->setAttribute( "{ano}", (int)$entidade->get_ano() );
        $this->dal->setAttribute( "{contador}", (int)$entidade->get_contador() );
        $this->dal->setAttribute( "{dt_cadastro}", $entidade->get_dt_cadastro() );
        $this->dal->setAttribute( "{cd_usuario_cadastro}", (int)$entidade->get_cd_usuario_cadastro() );
        $this->dal->setAttribute( "{cd_usuario_envio}", (int)$entidade->get_cd_usuario_envio() );
        $this->dal->setAttribute( "{cd_usuario_ok}", (int)$entidade->get_cd_usuario_ok() );
        $this->dal->setAttribute( "{cd_usuario_exclusao}", (int)$entidade->get_cd_usuario_exclusao() );
        $this->dal->setAttribute( "{ordem_itens}", $entidade->get_ordem_itens() );

        if ($entidade->get_dt_envio()!="") 
        {
            $this->dal->setAttribute( "{dt_envio}", "'" . $entidade->get_dt_envio_Ymd() . "'" );
        }
        else
        {
            $this->dal->setAttribute( "{dt_envio}", "null" );
        }

        if ($entidade->get_dt_ok()!="") 
        {
            $this->dal->setAttribute( "{dt_ok}", "'" . $entidade->get_dt_ok_Ymd() . "'" );
        }
        else
        {
            $this->dal->setAttribute( "{dt_ok}", "null" );
        }
        
        if ($entidade->get_dt_exclusao()!="") 
        {
            $this->dal->setAttribute( "{dt_exclusao}", "'" . $entidade->get_dt_exclusao_Ymd() . "'" );
        }
        else
        {
            $this->dal->setAttribute( "{dt_exclusao}", "null" );
        }

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.update() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
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
              FROM projetos.documento_protocolo
             WHERE cd_documento_protocolo IN ({cd_documento_protocolo})

        ");

        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$value );
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.delete() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    /**
     * Grava na tabela o código do usuário logado da GAP que enviou para GAD o protocolo (cd_usuario_envio), grava também a data atual (dt_envio)
     * 
     * @param entity_projetos_documento_protocolo $entidade Preencher obrigatoriamente atributos cd_documento_protocolo e cd_usuario_envio
     *        p.ex: 
     *              $ent = new entity_projetos_documento_protocolo();
     *              $ent->set_cd_documento_protocolo( 15 );
     *              $ent->set_cd_usuario_envio( 91 );
     *              $ado = new ADO_projetos_documento_protocolo( $db );
     *              $ret = $ado->updateReceive( $ent );
     */
    public function updateSend( entity_projetos_documento_protocolo $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.documento_protocolo
               SET cd_usuario_envio={cd_usuario_envio}
                 , dt_envio=CURRENT_TIMESTAMP 
             WHERE cd_documento_protocolo={cd_documento_protocolo};

        ");

        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$entidade->get_cd_documento_protocolo() );
        $this->dal->setAttribute( "{cd_usuario_envio}", (int)$entidade->get_cd_usuario_envio() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.updateSend() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    /**
     * Grava na tabela o código do usuário logado da GAD que recebeu o protocolo (cd_usuario_ok), grava também a data atual (dt_ok)
     * 
     * @param entity_projetos_documento_protocolo $entidade Preencher obrigatoriamente atributos cd_documento_protocolo e cd_usuario_ok
     *        p.ex: 
     *              $ent = new entity_projetos_documento_protocolo();
     *              $ent->set_cd_documento_protocolo( 15 );
     *              $ent->set_cd_usuario_ok( 91 );
     *              $ado = new ADO_projetos_documento_protocolo( $db );
     *              $ret = $ado->updateReceive( $ent );
     */
    public function updateReceive( entity_projetos_documento_protocolo $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.documento_protocolo
               SET cd_usuario_ok={cd_usuario_ok}
                 , dt_ok=CURRENT_TIMESTAMP 
             WHERE cd_documento_protocolo={cd_documento_protocolo};

        ");

        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$entidade->get_cd_documento_protocolo() );
        $this->dal->setAttribute( "{cd_usuario_ok}", (int)$entidade->get_cd_usuario_ok() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.updateReceive() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    /**
     * Grava na tabela a data e o motivo da exclusao da mensagem
     * 
     * @param entity_projetos_documento_protocolo $entidade Preencher obrigatoriamente atributos cd_documento_protocolo e motivo_exclusao
     *        p.ex: 
     *              $ent = new entity_projetos_documento_protocolo();
     *              $ent->set_cd_documento_protocolo( 15 );
     *              $ent->set_motivo_exclusao( 'teste' );
     *              $ado = new ADO_projetos_documento_protocolo( $db );
     *              $ret = $ado->updateCancel( $ent );
     */
    public function updateCancel( entity_projetos_documento_protocolo $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.documento_protocolo
               SET dt_exclusao=CURRENT_TIMESTAMP
                 , motivo_exclusao='{motivo_exclusao}'
                 , cd_usuario_exclusao = {cd_usuario_exclusao}
             WHERE cd_documento_protocolo={cd_documento_protocolo};

        ");

        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$entidade->get_cd_documento_protocolo() );
        $this->dal->setAttribute( "{motivo_exclusao}", $entidade->get_motivo_exclusao() );
        $this->dal->setAttribute( "{cd_usuario_exclusao}", (int)$entidade->get_cd_usuario_exclusao() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.updateCancel() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }

        return $bReturn;
    }

    public function updateOrdem( entity_projetos_documento_protocolo $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.documento_protocolo
               SET ordem_itens = '{ordem_itens}'
             WHERE cd_documento_protocolo = {cd_documento_protocolo};

        ");

        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$entidade->get_cd_documento_protocolo() );
        $this->dal->setAttribute( "{ordem_itens}", $entidade->get_ordem_itens() );

        $result = $this->dal->executeQuery();
        echo($this->dal->getMessage());

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.updateOrdem() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    public function item_FetchAll( $cd_documento_protocolo, $nao_devolvidos_apenas = false )
    {
        // define order by da lista
        $this->dal->createQuery( "
 
            SELECT ordem_itens 
              FROM projetos.documento_protocolo 
             WHERE cd_documento_protocolo = {cd_documento_protocolo}

        " );
        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$cd_documento_protocolo );
        $ordem_itens = $this->dal->getScalar();
        
        if ($this->select_define!="") {
            $this->dal->createQuery("
    
                SELECT " . $this->select_define . "
                  FROM projetos.documento_protocolo_item a
                  JOIN projetos.documento_protocolo b 
                       ON a.cd_documento_protocolo = b.cd_documento_protocolo
             LEFT JOIN projetos.usuarios_controledi c
                       ON a.cd_usuario_cadastro = c.codigo
             LEFT JOIN tipo_documentos d
                       ON a.cd_tipo_doc = d.cd_tipo_doc
                 WHERE a.dt_exclusao IS NULL
                   AND a.cd_documento_protocolo = {cd_documento_protocolo}
             {ORDER_BY}
    
            ");
		}
        else
        {
            $this->dal->createQuery("

                SELECT a.cd_documento_protocolo_item 
                     , a.cd_documento_protocolo 
                     , a.cd_tipo_doc 
                     , a.cd_empresa 
                     , a.cd_registro_empregado 
                     , a.seq_dependencia 
                     , TO_CHAR(a.dt_cadastro, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro 
                     , a.cd_usuario_cadastro 
                     , a.dt_exclusao 
                     , a.cd_usuario_exclusao 
                     , b.ano 
                     , b.contador 
                     , c.guerra as guerra_cadastro
                     , a.fl_recebido
                     , d.cd_tipo_doc
                     , d.nome_documento
                     , a.ds_processo
                     , a.observacao
                     , to_char(a.dt_indexacao, 'DD/MM/YYYY') AS dt_indexacao
                     , a.ds_observacao_indexacao
                     , a.nr_folha
                     , a.motivo_devolucao
                     , to_char(a.dt_devolucao, 'DD/MM/YYYY') AS dt_devolucao
                     , a.cd_usuario_devolucao
					 , a.arquivo
					 , a.arquivo_nome
                     , a.fl_descartar
                  FROM projetos.documento_protocolo_item a
                  JOIN projetos.documento_protocolo b 
                       ON a.cd_documento_protocolo = b.cd_documento_protocolo
             LEFT JOIN projetos.usuarios_controledi c
                       ON a.cd_usuario_cadastro = c.codigo
             LEFT JOIN tipo_documentos d
                       ON a.cd_tipo_doc = d.cd_tipo_doc
                 WHERE a.dt_exclusao IS NULL
                   AND a.cd_documento_protocolo = {cd_documento_protocolo}

                       {NAO_DEVOLVIDOS_APENAS}

             {ORDER_BY}
			 

            ");
        }
        $this->select_define = "";

        if($nao_devolvidos_apenas)
        {
        	$this->dal->setAttribute( "{NAO_DEVOLVIDOS_APENAS}", " AND a.dt_devolucao IS NULL " );
        }
        else
        {
        	$this->dal->setAttribute( "{NAO_DEVOLVIDOS_APENAS}", "" );
        }

        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$cd_documento_protocolo );
		
		$this->dal->setAttribute("{ORDER_BY}", " ORDER BY a.cd_documento_protocolo_item ");
		/*
        if ($ordem_itens=="P")
        {
            $this->dal->setAttribute( "{ORDER_BY}"
                                    , " ORDER BY a.cd_empresa
                                              , a.cd_registro_empregado
                                              , a.seq_dependencia
                                              , a.cd_tipo_doc" );
		}
        else if ($ordem_itens=="T")
        {
            $this->dal->setAttribute( "{ORDER_BY}"
                                    , " ORDER BY a.cd_tipo_doc
                                              , a.cd_empresa
                                              , a.cd_registro_empregado
                                              , a.seq_dependencia" );
        }
        else if ($ordem_itens=="C" || trim($ordem_itens)=="")
        {
            $this->dal->setAttribute( "{ORDER_BY}"
                                    , " ORDER BY a.cd_documento_protocolo_item
            " );
        }
        else if ( $ordem_itens=="S" )
        {
        	$this->dal->setAttribute( "{ORDER_BY}"
                                    , " ORDER BY a.ds_processo
            " );
        }
		*/
		
        $result = $this->dal->getResultset();
        if ( $this->dal->haveError() )
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.item_FetchAll() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return $result;
    }

    public function item_Insert( entity_projetos_documento_protocolo_item $entidade )
    {
        $bReturn = false;

        $entidade->set_cd_documento_protocolo_item( getNextval("projetos", "documento_protocolo_item", "cd_documento_protocolo_item", $this->db) );

        $this->dal->createQuery("

            INSERT INTO projetos.documento_protocolo_item
                    (
                        cd_documento_protocolo_item
                      , cd_documento_protocolo
                      , cd_tipo_doc
                      , cd_empresa
                      , cd_registro_empregado
                      , seq_dependencia
                      , dt_cadastro
                      , cd_usuario_cadastro
                      , dt_exclusao
                      , cd_usuario_exclusao
                      , observacao
                      , ds_processo
                      , nr_folha
                      , arquivo
                      , arquivo_nome
                    )
                VALUES (
                        {cd_documento_protocolo_item}
                      , {cd_documento_protocolo}
                      , {cd_tipo_doc}
                      , {cd_empresa}
                      , {cd_registro_empregado}
                      , {seq_dependencia}
                      , CURRENT_TIMESTAMP
                      , {cd_usuario_cadastro}
                      , null
                      , null
                      , '{observacao}'
                      , '{ds_processo}'
                      , {nr_folha}
                      , '{arquivo}'
                      , '{arquivo_nome}'
                      );
        ");

        $this->dal->setAttribute( "{cd_documento_protocolo_item}", (int)$entidade->get_cd_documento_protocolo_item() );
        $this->dal->setAttribute( "{cd_documento_protocolo}", (int)$entidade->get_cd_documento_protocolo() );
	    $this->dal->setAttribute( "{cd_tipo_doc}", String::if_blank_return($entidade->get_cd_tipo_doc(), "null") );
        if($entidade->get_cd_empresa()=="")
		{
			$this->dal->setAttribute( "{cd_empresa}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_empresa}", (int)$entidade->get_cd_empresa() );
		}
        if($entidade->get_cd_registro_empregado()=="")
		{
			$this->dal->setAttribute( "{cd_registro_empregado}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{cd_registro_empregado}", (int)$entidade->get_cd_registro_empregado() );
		}
		if($entidade->get_seq_dependencia()=="")
		{
			$this->dal->setAttribute( "{seq_dependencia}", "null" );
		}
		else
		{
			$this->dal->setAttribute( "{seq_dependencia}", (int)$entidade->get_seq_dependencia() );
		}
        $this->dal->setAttribute( "{cd_usuario_cadastro}", (int)$entidade->get_cd_usuario_cadastro() );
        $this->dal->setAttribute( "{observacao}", $entidade->get_observacao() );
        $this->dal->setAttribute( "{ds_processo}", $entidade->get_ds_processo() );
        $this->dal->setAttribute( "{nr_folha}", (int)$entidade->get_nr_folha() );
        $this->dal->setAttribute( "{arquivo}", $entidade->get_arquivo() );
        $this->dal->setAttribute( "{arquivo_nome}", $entidade->get_arquivo_nome() );

        $result = $this->dal->executeQuery();
        $this->dal->getMessage();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.item_Insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    public function item_Update( entity_projetos_documento_protocolo_item $entidade )
    {
        // do nothing
    }

    public function item_Delete( entity_projetos_documento_protocolo_item $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.documento_protocolo_item
               SET dt_exclusao=CURRENT_TIMESTAMP
                 , cd_usuario_exclusao = {cd_usuario_exclusao}
             WHERE cd_documento_protocolo_item={cd_documento_protocolo_item};

        ");

        $this->dal->setAttribute( "{cd_documento_protocolo_item}", (int)$entidade->get_cd_documento_protocolo_item() );
        $this->dal->setAttribute( "{cd_usuario_exclusao}", (int)$entidade->get_cd_usuario_exclusao() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.item_Delete() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    /**
     * Muda a flag "fl_recebido" da tabela de documentos, recebe a entidade com preenchimento
     * obrigatório dos atributos fl_recebido e cd_documento_protocolo_item
     */
    public function item_update_recebido( entity_projetos_documento_protocolo_item $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

            UPDATE projetos.documento_protocolo_item
               SET fl_recebido = '{fl_recebido}'
             WHERE cd_documento_protocolo_item = {cd_documento_protocolo_item}

        ");

        $this->dal->setAttribute( "{fl_recebido}", $entidade->get_fl_recebido() );
        $this->dal->setAttribute( "{cd_documento_protocolo_item}", (int)$entidade->get_cd_documento_protocolo_item() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_documento_protocolo.item_update_recebido() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

}
?>