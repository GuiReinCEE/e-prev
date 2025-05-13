<?php
class ADO_projetos_avaliacao_capa
{
    // DAL
    private $db;
    private $dal;

    function ADO_projetos_avaliacao_capa( $_db )
    {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct()
    {
        $this->dal = null;
    }

    /**
     * Cria array de objetos da estrutura de avaliação (avaliacao_capa, avaliacao, avaliacao_comite, outros)
     * pode ser filtrado por cd_avaliacao_capa ou cd_usuario_avaliado que são informados no objeto
     * entity_projetos_avaliacao_capa_extended recebido por parametro, também pode ser filtrado pela PK (cd_avaliacao_capa)
     * 
     * @param entity_projetos_avaliacao_capa_extended $avaliacao_capa Opcional, parametros para preencher: - set_cd_avaliacao_capa - set_cd_usuario_avaliado
     * 
     * @return Array[]entity_projetos_avaliacao_capa_extended Coleção de objetos de avaliacao_capa
     */
    public function fetch_all( entity_projetos_avaliacao_capa_extended $avaliacao_capa )
    {
        $this->dal->createQuery("

            SELECT capa.cd_avaliacao_capa, capa.dt_periodo, capa.cd_usuario_avaliado, capa.cd_usuario_avaliador, capa.status, capa.grau_escolaridade, to_char(capa.dt_publicacao, 'DD/MM/YYYY HH24:MI:SS') as dt_publicacao, to_char(capa.dt_criacao, 'DD/MM/YYYY') as capa_dt_criacao, capa.tipo_promocao, capa.cd_matriz_salarial, capa.avaliador_responsavel_comite, capa.cd_cargo
                 , usuario_matriz_avaliado.cd_usuario_matriz, TO_CHAR(usuario_matriz_avaliado.dt_admissao, 'DD/MM/YYYY') AS dt_admissao, TO_CHAR(usuario_matriz_avaliado.dt_promocao, 'DD/MM/YYYY') AS dt_promocao, usuario_matriz_avaliado.cd_escolaridade, usuario_matriz_avaliado.tipo_promocao as tipo_promocao_um
                 , matriz_salarial.cd_matriz_salarial AS cd_matriz_salarial_avaliado, matriz_salarial.cd_familias_cargos, matriz_salarial.faixa, matriz_salarial.valor_inicial, matriz_salarial.valor_final
                 , avaliado.nome as nome_avaliado, avaliado.usuario as usuario_avaliado, avaliado.nome as guerra_avaliado, avaliado.cd_registro_empregado as re_avaliado, avaliado.divisao as divisao_avaliado
                 , avaliador.nome as nome_avaliador, avaliador.usuario as usuario_avaliador, avaliador.nome as guerra_avaliador, avaliador.cd_registro_empregado as re_avaliador
				 , pc.desc_cargo, pfc.nome_familia
                 , avaliacao_cargo.nome_cargo as avaliacao_cargo_nome
                 , avaliacao_cargo_familia.nome_familia as avaliacao_cargo_familia_nome

              FROM projetos.avaliacao_capa capa
        INNER JOIN projetos.usuarios_controledi avaliado
                   ON capa.cd_usuario_avaliado = avaliado.codigo
         LEFT JOIN projetos.usuario_matriz usuario_matriz_avaliado
                   ON usuario_matriz_avaliado.cd_usuario = avaliado.codigo
         LEFT JOIN projetos.matriz_salarial
                   ON matriz_salarial.cd_matriz_salarial = usuario_matriz_avaliado.cd_matriz_salarial
         LEFT JOIN projetos.usuarios_controledi avaliador
                   ON capa.cd_usuario_avaliador = avaliador.codigo
         LEFT JOIN projetos.cargos pc
                   ON pc.cd_cargo = avaliado.cd_cargo
         LEFT JOIN projetos.familias_cargos pfc
                   ON pfc.cd_familia = pc.cd_familia

		 LEFT JOIN projetos.cargos avaliacao_cargo
                   ON avaliacao_cargo.cd_cargo = capa.cd_cargo
         LEFT JOIN projetos.familias_cargos avaliacao_cargo_familia
                   ON avaliacao_cargo_familia.cd_familia = avaliacao_cargo.cd_familia

			 WHERE 
                   (
                        ( capa.cd_usuario_avaliado  = {cd_usuario_avaliado}  OR 0={cd_usuario_avaliado} )
                     OR ( capa.cd_usuario_avaliador = {cd_usuario_avaliador} OR 0={cd_usuario_avaliador} )
                   )
               AND ( capa.dt_periodo = {dt_periodo} OR 0={dt_periodo} )

               AND ( capa.cd_avaliacao_capa = {cd_avaliacao_capa} OR 0={cd_avaliacao_capa} )

                OR
                   (
                        0 < (    SELECT count(*) FROM projetos.avaliacao_comite comite WHERE comite.cd_avaliacao_capa = capa.cd_avaliacao_capa AND comite.dt_exclusao IS NULL AND comite.cd_usuario_avaliador={cd_usuario_avaliador} AND (capa.status='S' OR capa.status='C') AND (capa.dt_periodo={dt_periodo} OR 0={dt_periodo})    )
                   )

        ");

        if ($avaliacao_capa->get_dt_periodo()=="") 
        {
            $avaliacao_capa->set_dt_periodo( 0 );
        }
        if ($avaliacao_capa->get_cd_avaliacao_capa()=="") 
        {
            $avaliacao_capa->set_cd_avaliacao_capa( 0 );
        }
        if ($avaliacao_capa->get_cd_usuario_avaliado()=="") 
        {
            $avaliacao_capa->set_cd_usuario_avaliado( 0 );
		}
        if ($avaliacao_capa->get_cd_usuario_avaliador()=="") 
        {
            $avaliacao_capa->set_cd_usuario_avaliador( 0 );
		}
        
        $this->dal->setAttribute( "{dt_periodo}", $avaliacao_capa->get_dt_periodo() );
        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$avaliacao_capa->get_cd_avaliacao_capa() );
        $this->dal->setAttribute( "{cd_usuario_avaliado}", (int)$avaliacao_capa->get_cd_usuario_avaliado() );
        $this->dal->setAttribute( "{cd_usuario_avaliador}", (int)$avaliacao_capa->get_cd_usuario_avaliador() );

        $rs_capas = $this->dal->getResultset(true);

        $idx_capa=0; $Capas[$idx_capa] = null;
        while ($rw_capas = pg_fetch_array($rs_capas))
        {
			$capa = new entity_projetos_avaliacao_capa_extended();

            $capa->set_cd_avaliacao_capa( $rw_capas["cd_avaliacao_capa"] );
            $capa->set_dt_periodo( $rw_capas["dt_periodo"] );
            $capa->set_cd_usuario_avaliado( $rw_capas["cd_usuario_avaliado"] );
            $capa->set_cd_usuario_avaliador( $rw_capas["cd_usuario_avaliador"] );
            $capa->set_grau_escolaridade( $rw_capas["grau_escolaridade"] );
            $capa->set_status( $rw_capas["status"] );
            $capa->set_dt_publicacao( $rw_capas["dt_publicacao"] );
            $capa->set_dt_criacao( $rw_capas["capa_dt_criacao"] );
            $capa->set_tipo_promocao( $rw_capas["tipo_promocao"] );
            $capa->set_cd_matriz_salarial( $rw_capas["cd_matriz_salarial"] );
            $capa->set_avaliador_responsavel_comite( $rw_capas["avaliador_responsavel_comite"] );
            $capa->set_cd_cargo( $rw_capas["cd_cargo"] );

			// cargo gravado na capa
			$familia = new entity_projetos_familias_cargos();
            $familia->set_nome_familia( $rw_capas["avaliacao_cargo_familia_nome"] );

            $cargo = new entity_projetos_cargos();
            $cargo->set_desc_cargo( $rw_capas["avaliacao_cargo_nome"] );
            $cargo->set_familia( $familia );
            
            $capa->cargo = $cargo;
			// cargo gravado na capa

            $capa->avaliado = new entity_projetos_usuarios_controledi_extended();
            $capa->avaliado->set_codigo( $rw_capas["cd_usuario_avaliado"] );
            $capa->avaliado->set_nome( $rw_capas["nome_avaliado"] );
            $capa->avaliado->set_guerra( $rw_capas["guerra_avaliado"] );
            $capa->avaliado->set_divisao( $rw_capas["divisao_avaliado"] );
            $capa->avaliado->set_usuario( $rw_capas["usuario_avaliado"] );
            
            // MATRIZ SALARIAL - usuario_matriz
            $usuario_matriz = new entity_projetos_usuario_matriz_extended();
            $usuario_matriz->cd_usuario_matriz = $rw_capas['cd_usuario_matriz'];
            $usuario_matriz->dt_admissao = $rw_capas['dt_admissao'];
            $usuario_matriz->dt_promocao = $rw_capas['dt_promocao'];
            $usuario_matriz->cd_escolaridade = $rw_capas['cd_escolaridade'];
            $usuario_matriz->tipo_promocao = $rw_capas['tipo_promocao_um'];

            // MATRIZ SALARIAL - matriz_salarial
            $matriz_salarial = new entity_projetos_matriz_salarial_extended();
            $matriz_salarial->cd_matriz_salarial = $rw_capas['cd_matriz_salarial_avaliado'];
            $matriz_salarial->faixa = $rw_capas['faixa'];
            $matriz_salarial->valor_inicial = $rw_capas['valor_inicial'];
            $matriz_salarial->valor_final = $rw_capas['valor_final'];

            $usuario_matriz->matriz_salarial = $matriz_salarial;
            $capa->avaliado->usuario_matriz = $usuario_matriz;

            $familia = new entity_projetos_familias_cargos();
            $familia->set_nome_familia( $rw_capas["nome_familia"] );

            $cargo = new entity_projetos_cargos();
            $cargo->set_desc_cargo( $rw_capas["desc_cargo"] );
            $cargo->set_familia( $familia );
            
            $capa->avaliado->set_cargo( $cargo );
            $capa->avaliado->set_cd_registro_empregado( $rw_capas["re_avaliado"] );

            $capa->avaliador = new entity_projetos_usuarios_controledi_extended();
            $capa->avaliador->set_codigo( $rw_capas["cd_usuario_avaliador"] );
            $capa->avaliador->set_nome( $rw_capas["nome_avaliador"] );
            $capa->avaliador->set_guerra( $rw_capas["guerra_avaliador"] );
            $capa->avaliador->set_usuario( $rw_capas["usuario_avaliador"] );
            $capa->avaliador->set_cd_registro_empregado( $rw_capas["re_avaliador"] );

            $this->dal->createQuery("

                    SELECT avaliacao.cd_avaliacao, avaliacao.cd_usuario_avaliador, avaliacao.tipo, avaliacao.dt_criacao, avaliacao.cd_avaliacao_capa, avaliacao.dt_conclusao
                         , avaliador.nome as nome_avaliador, avaliador.usuario as usuario_avaliador, avaliador.guerra as guerra_avaliador, avaliador.cd_registro_empregado as re_avaliador
                      FROM projetos.avaliacao avaliacao
                INNER JOIN projetos.usuarios_controledi avaliador
                           ON avaliacao.cd_usuario_avaliador = avaliador.codigo
                     WHERE cd_avaliacao_capa = {cd_avaliacao_capa} ORDER BY avaliacao.tipo;

            ");
            $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$rw_capas["cd_avaliacao_capa"] );
            $rs_avaliacoes = $this->dal->getResultset();

            if ($this->dal->haveError())
            {
                throw new Exception('Erro em ADO_projetos_avaliacao_capa.fetch_all()::1 ao executar comando SQL de consulta. '.$this->dal->getMessage());
            }

            $Avaliacoes=null; $idx_avaliacao = 0; $Avaliacoes[$idx_avaliacao] = null;
            while ($rw_avaliacoes = pg_fetch_array($rs_avaliacoes))
            {
				$avaliacao = new entity_projetos_avaliacao_extended();

                $avaliacao->set_cd_avaliacao( $rw_avaliacoes["cd_avaliacao"] );
                $avaliacao->set_cd_usuario_avaliador( $rw_avaliacoes["cd_usuario_avaliador"] );
                $avaliacao->set_tipo( $rw_avaliacoes["tipo"] );
                $avaliacao->set_dt_criacao( $rw_avaliacoes["dt_criacao"] );
                $avaliacao->set_dt_conclusao( $rw_avaliacoes["dt_conclusao"] );

                $this->dal->createQuery("

                        SELECT *
                          FROM projetos.avaliacoes_comp_inst
                         WHERE cd_avaliacao = {cd_avaliacao}

                ");
                $this->dal->setAttribute( "{cd_avaliacao}", $rw_avaliacoes["cd_avaliacao"] );
                $rs_institucional = $this->dal->getResultset();

                if ($this->dal->haveError()) {
                    throw new Exception('Erro em ADO_projetos_avaliacao_capa.fetch_all()::2 ao executar comando SQL de consulta. '.$this->dal->getMessage());
                }

                $Institucionais=null; $idx_inst = 0; $Institucionais[$idx_inst] = null;
                while ($rw_institucional = pg_fetch_array($rs_institucional))
                {
                    $institucional = new entity_projetos_avaliacoes_comp_inst();
                    $institucional->set_cd_avaliacao( $rw_institucional["cd_avaliacao"] );
                    $institucional->set_cd_comp_inst( $rw_institucional["cd_comp_inst"] );
                    $institucional->set_grau( $rw_institucional["grau"] );
                    $Institucionais[$idx_inst] = $institucional;
                    $idx_inst++;
				}
                $avaliacao->competencias_institucionais = $Institucionais;

                $this->dal->createQuery("

                        SELECT *
                          FROM projetos.avaliacoes_comp_espec
                         WHERE cd_avaliacao = {cd_avaliacao}

                ");
                $this->dal->setAttribute( "{cd_avaliacao}", (int)$rw_avaliacoes["cd_avaliacao"] );
                $rs_especifica = $this->dal->getResultset();
                if ($this->dal->haveError()) {
                    throw new Exception('Erro em ADO_projetos_avaliacao_capa.fetch_all()::3 ao executar comando SQL de consulta. '.$this->dal->getMessage());
                }

                $Especificas = null; $idx_espec = 0; $Especificas[$idx_espec] = null;
                while ($rw_especifica = pg_fetch_array($rs_especifica))
                {
                    $especifica = new entity_projetos_avaliacoes_comp_espec();
                    $especifica->set_cd_avaliacao( $rw_especifica["cd_avaliacao"] );
                    $especifica->set_cd_comp_espec( $rw_especifica["cd_comp_espec"] );
                    $especifica->set_grau( $rw_especifica["grau"] );
                    $Especificas[$idx_espec] = $especifica;
                    $idx_espec++;
				}
                $avaliacao->competencias_especificas = $Especificas;

                $this->dal->createQuery("

                        SELECT *
                          FROM projetos.avaliacoes_responsabilidades
                         WHERE cd_avaliacao = {cd_avaliacao}

                ");
                $this->dal->setAttribute( "{cd_avaliacao}", (int)$rw_avaliacoes["cd_avaliacao"] );
                $rs_responsabilidade = $this->dal->getResultset();
                if ($this->dal->haveError())
                {
                    throw new Exception('Erro em ADO_projetos_avaliacao_capa.fetch_all()::4 ao executar comando SQL de consulta. '.$this->dal->getMessage());
                }

                $Responsabilidades=null; $idx_resp = 0; $Responsabilidades[$idx_resp] = null;
                while ($rw_responsabilidade = pg_fetch_array($rs_responsabilidade))
                {
                    $responsabilidade = new entity_projetos_avaliacoes_responsabilidades();
                    $responsabilidade->set_cd_avaliacao( $rw_responsabilidade["cd_avaliacao"] );
                    $responsabilidade->set_cd_responsabilidade( $rw_responsabilidade["cd_responsabilidade"] );
                    $responsabilidade->set_grau( $rw_responsabilidade["grau"] );
                    $Responsabilidades[$idx_resp] = $responsabilidade;
                    $idx_resp++;
				}
                $avaliacao->responsabilidades = $Responsabilidades;

                // INICIO DOS Aspectos
				$this->dal->createQuery("

                	SELECT * 
                	  FROM projetos.avaliacao_aspecto
                	 WHERE cd_avaliacao = {cd_avaliacao}

				");
				$this->dal->setAttribute('{cd_avaliacao}', (int)$rw_avaliacoes['cd_avaliacao']);
				$rs_aspec = $this->dal->getResultset();
                if ($this->dal->haveError())
                {
                    throw new Exception('Erro em ADO_projetos_avaliacao_capa.fetch_all()::#aspectos ao executar comando SQL de consulta. '.$this->dal->getMessage());
                }
				$avaliacao->aspectos = array();
                while( $rw_aspec = pg_fetch_array($rs_aspec) )
                {
                	$aspecto = new entity_projetos_avaliacao_aspecto();
                	$aspecto->cd_avaliacao_aspecto = $rw_aspec['cd_avaliacao_aspecto'];
                	$aspecto->cd_avaliacao = $rw_aspec['cd_avaliacao'];
                	$aspecto->aspecto = $rw_aspec['aspecto'];
                	$aspecto->resultado_esperado = $rw_aspec['resultado_esperado'];
                	$aspecto->acao = $rw_aspec['acao'];
                	$avaliacao->aspectos[ sizeof($avaliacao->aspectos) ] = $aspecto;
                }
                // FIM DOS Aspectos

                $Avaliacoes[$idx_avaliacao] = $avaliacao;
                $idx_avaliacao++;
			}
            $capa->avaliacoes = $Avaliacoes;

            $this->dal->createQuery("

                    SELECT comite.*
                         , avaliador.nome as avaliador_nome, avaliador.usuario as avaliador_usuario, avaliador.guerra as avaliador_guerra
                      FROM projetos.avaliacao_comite comite
                      INNER JOIN projetos.usuarios_controledi avaliador
                      ON comite.cd_usuario_avaliador = avaliador.codigo
                     WHERE cd_avaliacao_capa = {cd_avaliacao_capa}
					 AND comite.dt_exclusao IS NULL

            ");

            $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$rw_capas["cd_avaliacao_capa"] );
            $rs_comite = $this->dal->getResultset();
            $Integrantes=null; $idx_integ = 0; $Integrantes[$idx_integ] = null;
            while ($rw_comite = pg_fetch_array($rs_comite))
            {
				$comite = new entity_projetos_avaliacao_comite_extended();
                $comite->set_cd_avaliacao_comite( $rw_comite["cd_avaliacao_comite"] );
                $comite->set_fl_responsavel( $rw_comite["fl_responsavel"] );
                $comite->set_cd_usuario_avaliador( $rw_comite["cd_usuario_avaliador"] );

                $comite->avaliador = new entity_projetos_usuarios_controledi_extended();
                $comite->avaliador->set_codigo( $rw_comite["cd_usuario_avaliador"] );
                $comite->avaliador->set_nome( $rw_comite["avaliador_nome"] );
                $comite->avaliador->set_guerra( $rw_comite["avaliador_guerra"] );
                $comite->avaliador->set_usuario( $rw_comite["avaliador_usuario"] );

                $Integrantes[ $idx_integ ] = $comite;
                $idx_integ++;
			}
            $capa->comite = $Integrantes;

            $Capas[$idx_capa] = $capa;
            $idx_capa++;
		}

        return $Capas;
    }

    /**
     * Cria array de objetos da estrutura de avaliação (avaliacao_capa, avaliacao, avaliacao_comite, outros)
     * pode ser filtrado por cd_avaliacao_capa ou cd_usuario_avaliado que são informados no objeto
     * entity_projetos_avaliacao_capa_extended recebido por parametro, também pode ser filtrado pela PK (cd_avaliacao_capa)
     * 
     * @param helper__avaliacao_capa__fetch_by_filter Conjunto de filtros para executar na query principal
     * 
     * @return Array[]helper__avaliacao_capa__fetch_by_filter__entity Coleção de objetos da query executada
     */
    public function fetch_by_filter( helper__avaliacao_capa__fetch_by_filter__filter $helper )
    {
        $avaliacoes = array();

        // Prepare
        if ($helper->dt_periodo=='') $helper->dt_periodo=0;

        $this->dal->createQuery( "

            SELECT capa.cd_avaliacao_capa
                 , capa.grau_escolaridade
                 , capa.cd_usuario_avaliado
				 , avaliado.nome AS nome_avaliado
				 , capa.cd_usuario_avaliador
                 , avaliador.nome AS nome_avaliador
                 , capa.dt_periodo
                 , capa.media_geral
                 , capa.tipo_promocao
				 , capa.cd_cargo
				 , capa.fl_acordo
              FROM projetos.avaliacao_capa capa
        INNER JOIN projetos.usuarios_controledi avaliado
                   ON capa.cd_usuario_avaliado = avaliado.codigo
         LEFT JOIN projetos.usuarios_controledi avaliador
                   ON capa.cd_usuario_avaliador = avaliador.codigo
             WHERE dt_publicacao IS NOT NULL
               AND ( {dt_periodo} = 0 OR dt_periodo={dt_periodo} )
               AND ( {cd_usuario_avaliado} = 0 OR cd_usuario_avaliado={cd_usuario_avaliado} )
               AND ( '{avaliado.divisao}' = '' OR avaliado.divisao='{avaliado.divisao}' )
               AND ( '{capa.tipo_promocao}' = '' OR capa.tipo_promocao='{capa.tipo_promocao}' )

        " );

    	$this->dal->setAttribute( '{dt_periodo}', $helper->dt_periodo );
    	$this->dal->setAttribute( '{cd_usuario_avaliado}', (int)$helper->avaliado );
    	$this->dal->setAttribute( '{avaliado.divisao}', $helper->gerencia );
    	$this->dal->setAttribute( '{dt_publicacao}', $helper->dt_publicacao );
    	$this->dal->setAttribute( '{capa.tipo_promocao}', $helper->tipo_promocao );

    	$res = $this->dal->getResultset();

        $idx_capa=0; $Capas[$idx_capa] = null;
        while ($row = pg_fetch_array($res))
        {
        	$avaliacao = new helper__avaliacao_capa__fetch_by_filter__entity();
        	$avaliacao->cd_avaliacao_capa    = $row['cd_avaliacao_capa'];
        	$avaliacao->cd_usuario_avaliado  = $row['cd_usuario_avaliado'];
        	$avaliacao->nome_avaliado        = $row['nome_avaliado'];
        	$avaliacao->cd_usuario_avaliador = $row['cd_usuario_avaliador'];
        	$avaliacao->nome_avaliador       = $row['nome_avaliador'];
        	$avaliacao->periodo              = $row['dt_periodo'];
        	$avaliacao->tipo_promocao        = $row['tipo_promocao'];
        	$avaliacao->resultado_final      = $row['media_geral'];
        	$avaliacao->fl_acordo            = $row['fl_acordo'];
			
        	// EXPECTATIVAS
        	$this->dal->createQuery("
        	
        		SELECT aa.*
        		FROM projetos.avaliacao_aspecto aa
        		JOIN projetos.avaliacao a on a.cd_avaliacao = aa.cd_avaliacao
        		WHERE a.cd_avaliacao_capa = {cd_avaliacao_capa}
        		AND tipo='S'
        		 
        	");
        	$this->dal->setAttribute('{cd_avaliacao_capa}', (int)$row['cd_avaliacao_capa']);
        	$result = $this->dal->getResultset();
        	while($row_e = pg_fetch_array($result))
        	{
	        	$exp = new entity_projetos_avaliacao_aspecto();
	        	$exp->acao = $row_e['acao'];
	        	$exp->aspecto = $row_e['aspecto'];
	        	$exp->resultado_esperado = $row_e['resultado_esperado'];
	        	$avaliacao->expectativas[sizeof($avaliacao->expectativas)] = $exp;
        	}

        	$avaliacoes[ sizeof($avaliacoes) ] = $avaliacao;
        }

        return $avaliacoes;
    }

    public function listar_todas_avaliadas_pelo_superior()
    {
        $avaliacoes = array();

        // Prepare
        $this->dal->createQuery( "

            SELECT capa.cd_avaliacao_capa
                 , capa.grau_escolaridade
                 , avaliado.nome AS nome_avaliado
                 , avaliador.nome AS nome_avaliador
                 , capa.dt_periodo
                 , capa.media_geral
                 , capa.tipo_promocao
                 , capa.status
              FROM projetos.avaliacao_capa capa
        INNER JOIN projetos.usuarios_controledi avaliado
                   ON capa.cd_usuario_avaliado = avaliado.codigo
         LEFT JOIN projetos.usuarios_controledi avaliador
                   ON capa.cd_usuario_avaliador = avaliador.codigo
             WHERE capa.status IN ('A', 'F', 'E', 'S')

        " );

    	$res = $this->dal->getResultset();

        $idx_capa=0; $Capas[$idx_capa] = null;
        while ($row = pg_fetch_array($res))
        {
        	$avaliacao = new helper__avaliacao_capa__fetch_by_filter__entity();
        	$avaliacao->cd_avaliacao_capa = $row['cd_avaliacao_capa'];
        	$avaliacao->nome_avaliado = $row['nome_avaliado'];
        	$avaliacao->nome_avaliador = $row['nome_avaliador'];
        	$avaliacao->periodo = $row['dt_periodo'];
        	$avaliacao->tipo_promocao = $row['tipo_promocao'];
        	$avaliacao->resultado_final = $row['media_geral'];
        	$avaliacao->status = $row['status'];

        	$avaliacoes[ sizeof($avaliacoes) ] = $avaliacao;
        }

        return $avaliacoes;
    }

    public function fetch_para_promocao()
    {
        $this->dal->createQuery("

            SELECT capa.cd_avaliacao_capa, capa.dt_periodo, capa.cd_usuario_avaliado, capa.cd_usuario_avaliador, capa.status, capa.grau_escolaridade, to_char(capa.dt_publicacao, 'DD/MM/YYYY HH24:MI:SS') as dt_publicacao, to_char(capa.dt_criacao, 'DD/MM/YYYY') as capa_dt_criacao, capa.tipo_promocao, capa.cd_matriz_salarial, capa.avaliador_responsavel_comite, capa.cd_cargo
                 , usuario_matriz_avaliado.cd_usuario_matriz, TO_CHAR(usuario_matriz_avaliado.dt_admissao, 'DD/MM/YYYY') AS dt_admissao, TO_CHAR(usuario_matriz_avaliado.dt_promocao, 'DD/MM/YYYY') AS dt_promocao, usuario_matriz_avaliado.tipo_promocao as tipo_promocao_um
                 , matriz_salarial.cd_matriz_salarial AS cd_matriz_salarial_avaliado, matriz_salarial.cd_familias_cargos, matriz_salarial.faixa, matriz_salarial.valor_inicial, matriz_salarial.valor_final
                 , avaliado.nome as nome_avaliado, avaliado.usuario as usuario_avaliado, avaliado.guerra as guerra_avaliado, avaliado.cd_registro_empregado as re_avaliado, avaliado.divisao as divisao_avaliado
                 , avaliador.nome as nome_avaliador, avaliador.usuario as usuario_avaliador, avaliador.guerra as guerra_avaliador, avaliador.cd_registro_empregado as re_avaliador
                 , pc.desc_cargo, pfc.nome_familia

                 , avaliacao_cargo.nome_cargo as avaliacao_cargo_nome
                 , avaliacao_cargo_familia.nome_familia as avaliacao_cargo_familia_nome
			  
			  FROM projetos.avaliacao_capa capa
        INNER JOIN projetos.usuarios_controledi avaliado
                   ON capa.cd_usuario_avaliado = avaliado.codigo
         LEFT JOIN projetos.usuario_matriz usuario_matriz_avaliado
                   ON usuario_matriz_avaliado.cd_usuario = avaliado.codigo
         LEFT JOIN projetos.matriz_salarial
                   ON matriz_salarial.cd_matriz_salarial = usuario_matriz_avaliado.cd_matriz_salarial
         LEFT JOIN projetos.usuarios_controledi avaliador
                   ON capa.cd_usuario_avaliador = avaliador.codigo
         LEFT JOIN projetos.cargos pc
                   ON pc.cd_cargo = avaliado.cd_cargo
         LEFT JOIN projetos.familias_cargos pfc
                   ON pfc.cd_familia = pc.cd_familia


		 LEFT JOIN projetos.cargos avaliacao_cargo
                   ON avaliacao_cargo.cd_cargo = capa.cd_cargo
         LEFT JOIN projetos.familias_cargos avaliacao_cargo_familia
                   ON avaliacao_cargo_familia.cd_familia = avaliacao_cargo.cd_familia


             WHERE (capa.dt_publicacao is null AND capa.status = 'E') -- COM ADMINISTRADOR PARA NOMEAÇÃO E ENCAMINHAMENTO AO COMITÊ
		  ORDER BY avaliado.nome ASC
        ");

        $rs_capas = $this->dal->getResultset();

        $idx_capa=0; $Capas[$idx_capa] = null;
        while ($rw_capas = pg_fetch_array($rs_capas))
        {
			$capa = new entity_projetos_avaliacao_capa_extended();

            $capa->set_cd_avaliacao_capa( $rw_capas["cd_avaliacao_capa"] );
            $capa->set_dt_periodo( $rw_capas["dt_periodo"] );
            $capa->set_cd_usuario_avaliado( $rw_capas["cd_usuario_avaliado"] );
            $capa->set_cd_usuario_avaliador( $rw_capas["cd_usuario_avaliador"] );
            $capa->set_grau_escolaridade( $rw_capas["grau_escolaridade"] );
            $capa->set_status( $rw_capas["status"] );
            $capa->set_dt_publicacao( $rw_capas["dt_publicacao"] );
            $capa->set_dt_criacao( $rw_capas["capa_dt_criacao"] );
            $capa->set_tipo_promocao( $rw_capas["tipo_promocao"] );
            $capa->set_cd_matriz_salarial( $rw_capas["cd_matriz_salarial"] );
            $capa->set_avaliador_responsavel_comite( $rw_capas["avaliador_responsavel_comite"] );
            $capa->set_cd_cargo( $rw_capas["cd_cargo"] );

			// cargo gravado na capa
			$familia = new entity_projetos_familias_cargos();
            $familia->set_nome_familia( $rw_capas["avaliacao_cargo_familia_nome"] );

            $cargo = new entity_projetos_cargos();
            $cargo->set_desc_cargo( $rw_capas["avaliacao_cargo_nome"] );
            $cargo->set_familia( $familia );
            
            $capa->cargo = $cargo;
			// cargo gravado na capa

            $capa->avaliado = new entity_projetos_usuarios_controledi_extended();
            $capa->avaliado->set_codigo( $rw_capas["cd_usuario_avaliado"] );
            $capa->avaliado->set_nome( $rw_capas["nome_avaliado"] );
            $capa->avaliado->set_guerra( $rw_capas["guerra_avaliado"] );
            $capa->avaliado->set_usuario( $rw_capas["usuario_avaliado"] );
            
            // MATRIZ SALARIAL - usuario_matriz
            $usuario_matriz = new entity_projetos_usuario_matriz_extended();
            $usuario_matriz->cd_usuario_matriz = $rw_capas['cd_usuario_matriz'];
            $usuario_matriz->dt_admissao = $rw_capas['dt_admissao'];
            $usuario_matriz->dt_promocao = $rw_capas['dt_promocao'];
            $usuario_matriz->tipo_promocao = $rw_capas['tipo_promocao_um'];

            // MATRIZ SALARIAL - matriz_salarial
            $matriz_salarial = new entity_projetos_matriz_salarial_extended();
            $matriz_salarial->cd_matriz_salarial = $rw_capas['cd_matriz_salarial_avaliado'];
            $matriz_salarial->faixa = $rw_capas['faixa'];
            $matriz_salarial->valor_inicial = $rw_capas['valor_inicial'];
            $matriz_salarial->valor_final = $rw_capas['valor_final'];

            $usuario_matriz->matriz_salarial = $matriz_salarial;
            $capa->avaliado->usuario_matriz = $usuario_matriz;

            $familia = new entity_projetos_familias_cargos();
            $familia->set_nome_familia( $rw_capas["nome_familia"] );

            $cargo = new entity_projetos_cargos();
            $cargo->set_desc_cargo( $rw_capas["desc_cargo"] );
            $cargo->set_familia( $familia );

            $capa->avaliado->set_cargo( $cargo );
            $capa->avaliado->set_cd_registro_empregado( $rw_capas["re_avaliado"] );

            $capa->avaliador = new entity_projetos_usuarios_controledi_extended();
            $capa->avaliador->set_codigo( $rw_capas["cd_usuario_avaliador"] );
            $capa->avaliador->set_nome( $rw_capas["nome_avaliador"] );
            $capa->avaliador->set_guerra( $rw_capas["guerra_avaliador"] );
            $capa->avaliador->set_usuario( $rw_capas["usuario_avaliador"] );
            $capa->avaliador->set_cd_registro_empregado( $rw_capas["re_avaliador"] );

            $this->dal->createQuery("

                    SELECT avaliacao.cd_avaliacao, avaliacao.cd_usuario_avaliador, avaliacao.tipo, avaliacao.dt_criacao, avaliacao.cd_avaliacao_capa, avaliacao.dt_conclusao
                         , avaliador.nome as nome_avaliador, avaliador.usuario as usuario_avaliador, avaliador.guerra as guerra_avaliador, avaliador.cd_registro_empregado as re_avaliador
                      FROM projetos.avaliacao avaliacao
                INNER JOIN projetos.usuarios_controledi avaliador
                           ON avaliacao.cd_usuario_avaliador = avaliador.codigo
                     WHERE cd_avaliacao_capa = {cd_avaliacao_capa};

            ");
            $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$rw_capas["cd_avaliacao_capa"] );
            $rs_avaliacoes = $this->dal->getResultset();

            if ($this->dal->haveError())
            {
                throw new Exception('Erro em ADO_projetos_avaliacao_capa.fetch_all()::1 ao executar comando SQL de consulta. '.$this->dal->getMessage());
            }

            $Avaliacoes=null; $idx_avaliacao = 0; $Avaliacoes[$idx_avaliacao] = null;
            while ($rw_avaliacoes = pg_fetch_array($rs_avaliacoes))
            {
				$avaliacao = new entity_projetos_avaliacao_extended();

                $avaliacao->set_cd_avaliacao( $rw_avaliacoes["cd_avaliacao"] );
                $avaliacao->set_cd_usuario_avaliador( $rw_avaliacoes["cd_usuario_avaliador"] );
                $avaliacao->set_tipo( $rw_avaliacoes["tipo"] );
                $avaliacao->set_dt_criacao( $rw_avaliacoes["dt_criacao"] );
                $avaliacao->set_dt_conclusao( $rw_avaliacoes["dt_conclusao"] );

                $this->dal->createQuery("

                        SELECT *
                          FROM projetos.avaliacoes_comp_inst
                         WHERE cd_avaliacao = {cd_avaliacao}

                ");
                $this->dal->setAttribute( "{cd_avaliacao}", (int)$rw_avaliacoes["cd_avaliacao"] );
                $rs_institucional = $this->dal->getResultset();

                if ($this->dal->haveError()) {
                    throw new Exception('Erro em ADO_projetos_avaliacao_capa.fetch_all()::2 ao executar comando SQL de consulta. '.$this->dal->getMessage());
                }

                $Institucionais=null; $idx_inst = 0; $Institucionais[$idx_inst] = null;
                while ($rw_institucional = pg_fetch_array($rs_institucional))
                {
                    $institucional = new entity_projetos_avaliacoes_comp_inst();
                    $institucional->set_cd_avaliacao( $rw_institucional["cd_avaliacao"] );
                    $institucional->set_cd_comp_inst( $rw_institucional["cd_comp_inst"] );
                    $institucional->set_grau( $rw_institucional["grau"] );
                    $Institucionais[$idx_inst] = $institucional;
                    $idx_inst++;
				}
                $avaliacao->competencias_institucionais = $Institucionais;

                $this->dal->createQuery("

                        SELECT *
                          FROM projetos.avaliacoes_comp_espec
                         WHERE cd_avaliacao = {cd_avaliacao}

                ");
                $this->dal->setAttribute( "{cd_avaliacao}", (int)$rw_avaliacoes["cd_avaliacao"] );
                $rs_especifica = $this->dal->getResultset();
                if ($this->dal->haveError()) {
                    throw new Exception('Erro em ADO_projetos_avaliacao_capa.fetch_all()::3 ao executar comando SQL de consulta. '.$this->dal->getMessage());
                }

                $Especificas=null; $idx_espec = 0; $Especificas[$idx_espec] = null;
                while ($rw_especifica = pg_fetch_array($rs_especifica))
                {
                    $especifica = new entity_projetos_avaliacoes_comp_espec();
                    $especifica->set_cd_avaliacao( $rw_especifica["cd_avaliacao"] );
                    $especifica->set_cd_comp_espec( $rw_especifica["cd_comp_espec"] );
                    $especifica->set_grau( $rw_especifica["grau"] );
                    $Especificas[$idx_espec] = $especifica;
                    $idx_espec++;
				}
                $avaliacao->competencias_especificas = $Especificas;

                $this->dal->createQuery("

                        SELECT *
                          FROM projetos.avaliacoes_responsabilidades
                         WHERE cd_avaliacao = {cd_avaliacao}

                ");
                $this->dal->setAttribute( "{cd_avaliacao}", (int)$rw_avaliacoes["cd_avaliacao"] );
                $rs_responsabilidade = $this->dal->getResultset();
                if ($this->dal->haveError()) {
                    throw new Exception('Erro em ADO_projetos_avaliacao_capa.fetch_all()::4 ao executar comando SQL de consulta. '.$this->dal->getMessage());
                }

                $Responsabilidades=null; $idx_resp = 0; $Responsabilidades[$idx_resp] = null;
                while ($rw_responsabilidade = pg_fetch_array($rs_responsabilidade))
                {
                    $responsabilidade = new entity_projetos_avaliacoes_responsabilidades();
                    $responsabilidade->set_cd_avaliacao( $rw_responsabilidade["cd_avaliacao"] );
                    $responsabilidade->set_cd_responsabilidade( $rw_responsabilidade["cd_responsabilidade"] );
                    $responsabilidade->set_grau( $rw_responsabilidade["grau"] );
                    $Responsabilidades[$idx_resp] = $responsabilidade;
                    $idx_resp++;
				}
                $avaliacao->responsabilidades = $Responsabilidades;

                $Avaliacoes[$idx_avaliacao] = $avaliacao;
                $idx_avaliacao++;
			}
            $capa->avaliacoes = $Avaliacoes;

            $this->dal->createQuery("

                    SELECT comite.*
                         , avaliador.nome AS avaliador_nome, avaliador.usuario AS avaliador_usuario, avaliador.guerra AS avaliador_guerra
                      FROM projetos.avaliacao_comite comite
                INNER JOIN projetos.usuarios_controledi avaliador
                           ON comite.cd_usuario_avaliador = avaliador.codigo
                     WHERE cd_avaliacao_capa = {cd_avaliacao_capa}
					 AND comite.dt_exclusao IS NULL;

            ");

            $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$rw_capas["cd_avaliacao_capa"] );
            $rs_comite = $this->dal->getResultset();
            $Integrantes=null; $idx_integ = 0; $Integrantes[$idx_integ] = null;
            while ($rw_comite = pg_fetch_array($rs_comite))
            {
				$comite = new entity_projetos_avaliacao_comite_extended();
                $comite->set_cd_avaliacao_comite( $rw_comite["cd_avaliacao_comite"] );
                $comite->set_fl_responsavel( $rw_comite["fl_responsavel"] );
                $comite->set_cd_usuario_avaliador( $rw_comite["cd_usuario_avaliador"] );

                $comite->avaliador = new entity_projetos_usuarios_controledi_extended();
                $comite->avaliador->set_codigo( $rw_comite["cd_usuario_avaliador"] );
                $comite->avaliador->set_nome( $rw_comite["avaliador_nome"] );
                $comite->avaliador->set_guerra( $rw_comite["avaliador_guerra"] );
                $comite->avaliador->set_usuario( $rw_comite["avaliador_usuario"] );

                $Integrantes[ $idx_integ ] = $comite;
                $idx_integ++;
			}
            $capa->comite = $Integrantes;

            $Capas[$idx_capa] = $capa;
            $idx_capa++;
		}

        return $Capas;
    }

    public function insert( entity_projetos_avaliacao_capa_extended $entidade )
    {
        $bReturn = false;

        $entidade->set_cd_avaliacao_capa( getNextval("projetos", "avaliacao_capa", "cd_avaliacao_capa", $this->db) );
        $sql = "
            INSERT INTO projetos.avaliacao_capa
            (
                  cd_avaliacao_capa
                , cd_usuario_avaliado
                , dt_periodo
                , status
                , dt_criacao
                , cd_usuario_avaliador
                , grau_escolaridade
                , tipo_promocao
                , avaliador_responsavel_comite
            )
            VALUES
            (
                  {cd_avaliacao_capa}
                , {cd_usuario_avaliado}
                , {dt_periodo}
                , '{status}'
                , CURRENT_TIMESTAMP
                , {cd_usuario_avaliador}
                , {grau_escolaridade}
                , '{tipo_promocao}'
                , '{avaliador_responsavel_comite}'
            )
        ";

        // create
        $this->dal->createQuery( $sql );

        // parse
        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$entidade->get_cd_avaliacao_capa() );
        $this->dal->setAttribute( "{cd_usuario_avaliado}", (int)$entidade->get_cd_usuario_avaliado() );
        $this->dal->setAttribute( "{dt_periodo}", $entidade->get_dt_periodo() );
        $this->dal->setAttribute( "{status}", $entidade->get_status() );
        $this->dal->setAttribute( "{cd_usuario_avaliador}", (int)$entidade->get_cd_usuario_avaliador() );
        $this->dal->setAttribute( "{grau_escolaridade}", $entidade->get_grau_escolaridade() );
        $this->dal->setAttribute( "{tipo_promocao}", $entidade->get_tipo_promocao() );
        $this->dal->setAttribute( "{avaliador_responsavel_comite}", $entidade->get_avaliador_responsavel_comite() );

        // execute
        $result = $this->dal->executeQuery();

        // error
        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }

        return $bReturn;
    }

    public function update( entity_projetos_avaliacao_capa_extended $entidade )
    {
        $bReturn = false;
        $query = "

                 UPDATE projetos.avaliacao_capa
                    SET grau_escolaridade={grau_escolaridade}
                        {set_extended_tipo_promocao}
                        {set_extended_media_geral}
                        {set_extended_status}
                  WHERE cd_avaliacao_capa = {cd_avaliacao_capa}

        ";

        // Arranjo de variáveis opcionais
        if($entidade->get_tipo_promocao()=='null')
        {
            $query = str_replace( "{set_extended_tipo_promocao}", ", tipo_promocao=null", $query );
        }
        elseif( $entidade->get_tipo_promocao()!="" )
        {
            $query = str_replace( "{set_extended_tipo_promocao}", ", tipo_promocao='{tipo_promocao}'", $query );
        }
        else
        {
            $query = str_replace( "{set_extended_tipo_promocao}", "", $query );
        }

        if($entidade->get_media_geral()=='null')
        {
            $query = str_replace( "{set_extended_media_geral}", ", media_geral=null", $query );
        }
        elseif( $entidade->get_media_geral()!="" )
        {
            $query = str_replace( "{set_extended_media_geral}", ", media_geral={media_geral}", $query );
        }
        else
        {
            $query = str_replace( "{set_extended_media_geral}", "", $query );
        }

        if($entidade->get_status()=='null')
        {
            $query = str_replace( "{set_extended_status}", ", status=null", $query );
        }
        elseif( $entidade->get_status()!="" )
        {
            $query = str_replace( "{set_extended_status}", ", status='{status}'", $query );
        }
        else
        {
            $query = str_replace( "{set_extended_status}", "", $query );
        }

        // criação da query na camada DAL
        $this->dal->createQuery($query);

        // Substituições das variáveis
        if ($entidade->get_grau_escolaridade()=="")
        {
            $this->dal->setAttribute( "{grau_escolaridade}", "grau_escolaridade" );
		}
        else
        {
            $this->dal->setAttribute( "{grau_escolaridade}", $entidade->get_grau_escolaridade() );
        }

        $this->dal->setAttribute( "{tipo_promocao}", $entidade->get_tipo_promocao() );
        $this->dal->setAttribute( "{grau_escolaridade}", $entidade->get_grau_escolaridade() );
        $this->dal->setAttribute( "{status}", $entidade->get_status() );
        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$entidade->get_cd_avaliacao_capa() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.update() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    public function publicar( entity_projetos_avaliacao_capa_extended $entidade )
    {
        $bReturn = false;
        $this->dal->createQuery("

                 UPDATE projetos.avaliacao_capa
                    SET dt_publicacao=CURRENT_TIMESTAMP
                      , status='{status}'
                      , media_geral = {media_geral}
                  WHERE cd_avaliacao_capa = {cd_avaliacao_capa} 

        ");

        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$entidade->get_cd_avaliacao_capa() );
        $this->dal->setAttribute( "{status}", $entidade->get_status() );
        $this->dal->setAttribute( "{media_geral}", $entidade->get_media_geral() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.publicar() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }

        return $bReturn;
    }

    public function encaminhar_ao_comite( $cd_avaliacao_capa )
    {
        // LISTA COMITE
        $this->dal->createQuery("

			SELECT * 
			FROM projetos.avaliacao_comite 
			WHERE cd_avaliacao_capa={cd_avaliacao_capa} 
			AND dt_exclusao IS NULL

		");
        $this->dal->setAttribute("{cd_avaliacao_capa}", intval($cd_avaliacao_capa));
        $query_1 = $this->dal->getResultset();

		// CARREGA AVALIAÇÃO DO SUPERIOR
    	$this->dal->createQuery("

			SELECT * 
			FROM projetos.avaliacao 
			WHERE cd_avaliacao_capa={cd_avaliacao_capa} 
			AND tipo='S'

		");
    	$this->dal->setAttribute("{cd_avaliacao_capa}", intval($cd_avaliacao_capa));
    	$query_2 = $this->dal->getResultset();
    	$item_2 = pg_fetch_array($query_2);

    	// CARREGA COMPETENCIAS INSTITUCIONAIS
    	$this->dal->createQuery("

			SELECT * 
			FROM projetos.avaliacoes_comp_inst 
			WHERE cd_avaliacao={cd_avaliacao}

		");
    	$this->dal->setAttribute("{cd_avaliacao}", intval($item_2['cd_avaliacao']));
    	$query_3 = $this->dal->getResultset();
    	while($item_3 = pg_fetch_array($query_3)){$coll[]=$item_3;}

		// comite
        while ($item_1 = pg_fetch_array($query_1))
        {
        	$this->dal->createQuery("

				SELECT count(*) 
				FROM projetos.avaliacao 
				WHERE tipo='C' 
				AND cd_usuario_avaliador={cd_usuario_avaliador} 
				AND cd_avaliacao_capa={cd_avaliacao_capa}

			");
			$this->dal->setAttribute('{cd_usuario_avaliador}', intval($item_1['cd_usuario_avaliador']));
        	$this->dal->setAttribute('{cd_avaliacao_capa}', intval($cd_avaliacao_capa));
        	
        	$quantos = $this->dal->getScalar();
        	
        	// se ainda não existe a avaliação para esse integrante do comite então cria.
        	if(intval($quantos)==0)
        	{
	        	// inserir uma avaliação para o integrante do comite
				$new_id = getNextval("projetos", "avaliacao", "cd_avaliacao", $this->db);
				$this->dal->createQuery("
	
					INSERT INTO projetos.avaliacao( 
						cd_avaliacao, cd_usuario_avaliador, tipo, dt_criacao, cd_avaliacao_capa 
					) VALUES ( 
						{cd_avaliacao}, {cd_usuario_avaliador}, '{tipo}', current_timestamp, {cd_avaliacao_capa} 
					);
	
				");
				
				//echo "avaliação $new_id criada!".br(2);
				
				$this->dal->setAttribute('{cd_avaliacao}', intval($new_id));
				$this->dal->setAttribute('{cd_usuario_avaliador}', intval($item_1['cd_usuario_avaliador']));
				$this->dal->setAttribute('{tipo}', 'C');
				$this->dal->setAttribute('{cd_avaliacao_capa}', intval($cd_avaliacao_capa));
				
				$this->dal->executeQuery();
	
	        	// copiar as competencias institucionais para avaliação
	    		foreach($coll as $item_3)
	    		{
	    			$this->dal->createQuery("
	
						INSERT INTO projetos.avaliacoes_comp_inst(
							cd_avaliacao,cd_comp_inst,grau
						) VALUES (
							{cd_avaliacao}, {cd_comp_inst}, {grau}
						);
	
					");
					$this->dal->setAttribute('{cd_avaliacao}', intval($new_id));
					$this->dal->setAttribute('{cd_comp_inst}', $item_3['cd_comp_inst']);
					$this->dal->setAttribute('{grau}', $item_3['grau']);
					
					$this->dal->executeQuery();
	    		}
        	}
        	else
        	{
        		//echo "usuário do comite já possui avaliação criada para essa capa".br(2);
        	}
        }

		// TUDO CERTO? ENTÃO ALTERAR A CAPA DA AVALIAÇÃO COM NOVO STATUS
		$bReturn = false;
        $this->dal->createQuery("

                 UPDATE projetos.avaliacao_capa
                    SET status='S'
                  WHERE cd_avaliacao_capa = '{cd_avaliacao_capa}' 

        ");
        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$cd_avaliacao_capa );
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.encaminhar_ao_comite() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
        	// Gravar histórico
        	$this->salvar_historico( array(
        		'cd_avaliacao_capa'=>$cd_avaliacao_capa
        		, 'usuario'=>$_SESSION['U']
        		, 'mensagem'=>'Avaliação encaminhada ao comite (status alterado para S)'
        	));

            $bReturn = true;
        }

        return $bReturn;
    }
    
    public function encaminhar_ao_comite_view()
    {
    	// não iniciadas pelo comitê
    	
        $this->dal->createQuery( "

			SELECT u.nome, c.cd_avaliacao_capa, avaliado.nome as nome_avaliado
			FROM projetos.avaliacao_comite com 
			JOIN projetos.avaliacao_capa c ON c.cd_avaliacao_capa=com.cd_avaliacao_capa
			JOIN projetos.usuarios_controledi u ON u.codigo=com.cd_usuario_avaliador
			JOIN projetos.usuarios_controledi avaliado ON avaliado.codigo=c.cd_usuario_avaliado
			WHERE c.dt_periodo=2010 AND c.status='S'
			AND com.dt_exclusao IS NULL
			AND com.cd_usuario_avaliador NOT IN 
			(
				SELECT a.cd_usuario_avaliador
				FROM projetos.avaliacao a JOIN projetos.avaliacao_capa c ON c.cd_avaliacao_capa=a.cd_avaliacao_capa
				WHERE c.dt_periodo=2010
				AND a.tipo='C' AND a.cd_avaliacao_capa=com.cd_avaliacao_capa
			)

		" );
		
		$query_1 = $this->dal->getResultset();
		
		while( $item = pg_fetch_array($query_1) )
		{
			//$this->encaminhar_ao_comite( $item['cd_avaliacao_capa'] );
			
			/*echo 
				"<b>Capa:</b> ".$item['cd_avaliacao_capa'].br()
				."<b>Avaliador:</b> ".$item['nome'].br()
				.'<b>Avaliado:</b> '.$item['nome_avaliado'].''.br()
				.'<b>Avaliação criada com sucesso!</b>'.br(2)
				;*/
		}
    }
    
    public function encaminhar_ao_administrador( $cd_avaliacao_capa )
    {
        $bReturn = false;
        $this->dal->createQuery("

                 UPDATE projetos.avaliacao_capa
                    SET status='E'
                  WHERE cd_avaliacao_capa = '{cd_avaliacao_capa}' 

        ");

        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$cd_avaliacao_capa );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.encaminhar_ao_administrador() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    public function insert_integrante_comite(entity_projetos_avaliacao_comite & $entidade)
    {
        $bReturn = false;

        $entidade->set_cd_avaliacao_comite( getNextval("projetos", "avaliacao_comite", "cd_avaliacao_comite", $this->db) );
        $sql = "

            INSERT INTO projetos.avaliacao_comite
            (
                  cd_avaliacao_comite
                , cd_avaliacao_capa
                , cd_usuario_avaliador
                , fl_responsavel
            )
             VALUES (
                  {cd_avaliacao_comite}
                , {cd_avaliacao_capa}
                , {cd_usuario_avaliador}
                , '{fl_responsavel}'
            )

        ";

        // create
        $this->dal->createQuery( $sql );

        // parse
        $this->dal->setAttribute( "{cd_avaliacao_comite}", (int)$entidade->get_cd_avaliacao_comite() );
        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$entidade->get_cd_avaliacao_capa() );
        $this->dal->setAttribute( "{cd_usuario_avaliador}", (int)$entidade->get_cd_usuario_avaliador() );
        $this->dal->setAttribute( "{fl_responsavel}", $entidade->get_fl_responsavel() );

        // execute
        $result = $this->dal->executeQuery();

        // error
        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.insert_integrante_comite() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    public function delete_integrante_comite($cd_avaliacao_comite)
    {
        $bReturn = false;
        $this->dal->createQuery("

                 DELETE FROM projetos.avaliacao_comite
                 WHERE cd_avaliacao_comite = {cd_avaliacao_comite} 

        ");

        $this->dal->setAttribute( "{cd_avaliacao_comite}", (int)$cd_avaliacao_comite );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.delete_integrante_comite() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    /**
     * Definição do responsável pelo comite
     * 
     * @param int $cd_avaliacao_comite Código do integrante do comite da avaliação que foi indicado como responsável. Esse parametro originalmente era único e obrigatório, por compatibilidade segue obrigatório mas só será usado se a $origem for "comite"
     * 
     * @param string $origem "comite" ou "superior". Indica se o integrante do comitê é o superior imediato ou é literalmente um dos indicados do comite
     * 
     * @param int @cd_capa Código da capa de avaliação que será usada caso a $origem seja "superior"
     * 
     * @return boolean Sucesso ou Falha para true ou false
     */
    public function definir_responsavel_comite($cd_avaliacao_comite, $origem="comite", $cd_capa=0)
    {
        $bReturn = false;
        
        if($origem=="comite")
        {
	        $this->dal->createQuery("
	
	            UPDATE projetos.avaliacao_comite 
	            SET fl_responsavel = 'N' 
	            WHERE cd_avaliacao_capa in (
	                                SELECT cd_avaliacao_capa 
	                                FROM projetos.avaliacao_comite 
	                                WHERE cd_avaliacao_comite = {cd_avaliacao_comite}
	                                );
	
				UPDATE projetos.avaliacao_capa 
	            SET avaliador_responsavel_comite = 'N' 
	            WHERE cd_avaliacao_capa in (
	                                SELECT cd_avaliacao_capa 
	                                FROM projetos.avaliacao_comite 
	                                WHERE cd_avaliacao_comite = {cd_avaliacao_comite}
	                                );

	            UPDATE projetos.avaliacao_comite 
	            SET fl_responsavel = 'S'
	            WHERE cd_avaliacao_comite = {cd_avaliacao_comite};
	
	        ");
        	$this->dal->setAttribute( "{cd_avaliacao_comite}", (int)$cd_avaliacao_comite );
        }
        elseif($origem=="superior")
        {
	        $this->dal->createQuery("
	
	            UPDATE projetos.avaliacao_comite 
	            SET fl_responsavel = 'N' 
	            WHERE cd_avaliacao_capa = {cd_avaliacao_capa};
	
				UPDATE projetos.avaliacao_capa 
	            SET avaliador_responsavel_comite = 'N' 
	            WHERE cd_avaliacao_capa = {cd_avaliacao_capa};

	            UPDATE projetos.avaliacao_capa
	            SET avaliador_responsavel_comite = 'S'
	            WHERE cd_avaliacao_capa = {cd_avaliacao_capa};
	
	        ");
	        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$cd_capa );
        }

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.definir_responsavel_comite() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    /**
     * Reabre a avaliação para o superior
     */
    public function reabrir_avaliacao( $cd_avaliacao_capa )
    {
    	$bReturn = false;
        $this->dal->createQuery("

            UPDATE projetos.avaliacao_capa
               SET status='F'
                 , dt_publicacao=null
                 , media_geral=0
             WHERE cd_avaliacao_capa = {cd_avaliacao_capa};

        ");

        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$cd_avaliacao_capa );
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.reabrir_avaliacao() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
        	// gravar histórico
        	$this->salvar_historico( array(
        		'cd_avaliacao_capa'=>$cd_avaliacao_capa
        		, 'usuario'=>$_SESSION['U']
        		, 'mensagem'=>'Avaliação reaberta (status alterado para F, data de publicação anulada, media geral zerada)'
        	));
        	
            $bReturn = true;
        }

        return $bReturn;
    }

	/**
     * Encerra a avaliação
     * O Status C é descrito como "C - FECHADO COMITE (PUBLICADO)" mas também é usado para identificar
     * que a avaliação foi fechada, chegou ao seu estágio final, independente do comite ter participado ou não do 
     * processo.
     */
    public function encerrar_avaliacao( $cd_avaliacao_capa )
    {
    	$bReturn = false;
        $this->dal->createQuery("

            UPDATE projetos.avaliacao_capa
               SET status         = 'C',
                   dt_publicacao  = CURRENT_TIMESTAMP,
                   media_geral    = projetos.avaliacao_nota(cd_avaliacao_capa)
             WHERE cd_avaliacao_capa = {cd_avaliacao_capa};

        ");

        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$cd_avaliacao_capa );
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.encerrar_avaliacao() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
        	// gravar histórico
        	$this->salvar_historico( array(
        		'cd_avaliacao_capa'=>$cd_avaliacao_capa
        		, 'usuario'=>$_SESSION['U']
        		, 'mensagem'=>'Avaliação encerrada (status alterado para C, data de publicação gravada, media gera zerada)'
        	));
        	
            $bReturn = true;
        }

        return $bReturn;
    }

	/**
     * Excluir avaliação
     * Apaga completamente (DELETE) uma avaliação. Comando usado a critério do administrador do sistema.
     * Só será permitida exclusãode avaliações ainda não fechadas pelo superior.
     */
    public function excluir_avaliacao( $cd_avaliacao_capa )
    {
    	$bReturn = false;
        $this->dal->createQuery("

            DELETE FROM projetos.avaliacoes_comp_espec WHERE cd_avaliacao IN (
				SELECT DISTINCT ava.cd_avaliacao 
				FROM projetos.avaliacao ava 
				WHERE cd_avaliacao_capa = {cd_avaliacao_capa}
			);
			DELETE FROM projetos.avaliacoes_comp_inst WHERE cd_avaliacao IN (
				SELECT DISTINCT ava.cd_avaliacao 
				FROM projetos.avaliacao ava 
				WHERE cd_avaliacao_capa = {cd_avaliacao_capa}
			);
			DELETE FROM projetos.avaliacoes_responsabilidades WHERE cd_avaliacao IN (
				SELECT DISTINCT ava.cd_avaliacao 
				FROM projetos.avaliacao ava 
				WHERE cd_avaliacao_capa = {cd_avaliacao_capa}
			);
			DELETE FROM projetos.avaliacao_aspecto WHERE cd_avaliacao IN (
				SELECT DISTINCT ava.cd_avaliacao 
				FROM projetos.avaliacao ava 
				WHERE cd_avaliacao_capa = {cd_avaliacao_capa}
			);
			
			DELETE FROM projetos.avaliacao_comite WHERE cd_avaliacao_capa = {cd_avaliacao_capa};

			DELETE FROM projetos.avaliacao WHERE cd_avaliacao_capa = {cd_avaliacao_capa};

			DELETE FROM projetos.avaliacao_capa WHERE cd_avaliacao_capa = {cd_avaliacao_capa};

        ");

        $this->dal->setAttribute( "{cd_avaliacao_capa}", intval($cd_avaliacao_capa) );
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_projetos_avaliacao_capa.excluir_avaliacao() ao executar comando SQL.' . $this->dal->getMessage() . '');
        }
        else
        {
        	// gravar histórico
        	$this->salvar_historico( array(
        		'cd_avaliacao_capa'=>$cd_avaliacao_capa
        		, 'usuario'=>$_SESSION['U']
        		, 'mensagem'=>'Avaliação excluída'
        	));

            $bReturn = true;
        }

        return $bReturn;
    }

    public function salvar_historico( $values = array() )
    {
    	$bReturn = false;
        $this->dal->createQuery("

        	INSERT INTO projetos.avaliacao_capa_historico(
		             dt_criacao, usuario, mensagem, cd_avaliacao_capa)
		    VALUES ( current_timestamp, '{usuario}', '{mensagem}', {cd_avaliacao_capa} );

        ");

        $this->dal->setAttribute( "{usuario}", $values['usuario'] );
        $this->dal->setAttribute( "{mensagem}", $values['mensagem'] );
        $this->dal->setAttribute( "{cd_avaliacao_capa}", (int)$values['cd_avaliacao_capa'] );
        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            // throw new Exception('Erro em ADO_projetos_avaliacao_capa.salvar_historico() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }

        return $bReturn;
    }
}
?>