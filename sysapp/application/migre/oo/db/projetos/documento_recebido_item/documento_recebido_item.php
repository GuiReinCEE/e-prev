<?php
class documento_recebido_item
{
	static function select_1($cd_documento_recebido)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			/* documento_recebido_item::select_1(); */
			SELECT DISTINCT 

				dri.cd_documento_recebido_item
				, dri.nr_folha
				, dri.cd_empresa
				, dri.cd_registro_empregado
				, dri.seq_dependencia
				, to_char( dri.dt_cadastro , 'DD/MM/YYYY' ) AS dt_cadastro

				, documento.cd_tipo_doc
				, documento.nome_documento

				, dri.arquivo
				, dri.nome

			FROM

				projetos.documento_recebido_item dri

				LEFT JOIN public.tipo_documentos documento
				ON documento.cd_tipo_doc=dri.cd_tipo_doc

			WHERE

				cd_documento_recebido = {cd_documento_recebido}
				AND dri.dt_exclusao IS NULL
				;

		" );

		$db->setParameter( "{cd_documento_recebido}", $cd_documento_recebido );

		$collection = $db->get(true);

		return $collection;
	}

	/**
	 * documento_protocolo_item::salvar_dados_da_indexacao
	 * 
	 * Gravar informaчѕes de indexaчуo do item protocolado 
	 * (tabela documento_protocolo_item)
	 * 
	 * @param int $cd_documento_protocolo_item chave primсria da tabela
	 * @param array() $dados informaчѕes a serem salvas, informar array como segue
	 * 					$dados['dt_indexacao'] = 'valor';
	 * 					$dados['ds_observacao_indexacao'] = 'valor';
	 * 
	 * @return bool
	 */
	public static function salvar_dados_da_indexacao( $cd_documento_protocolo_item, $dados = array() )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			UPDATE projetos.documento_protocolo_item
			SET dt_indexacao = TO_DATE( '{dt_indexacao}', 'DD/MM/YYYY' )
			, ds_observacao_indexacao = '{ds_observacao_indexacao}'
			WHERE cd_documento_protocolo_item = {cd_documento_protocolo_item};

		" );

		$db->setParameter( "{cd_documento_protocolo_item}", $cd_documento_protocolo_item );
		$db->setParameter( "{dt_indexacao}", $dados['dt_indexacao'] );
		$db->setParameter( "{ds_observacao_indexacao}", $dados['ds_observacao_indexacao'] );

		$ret = $db->execute();

		return $ret;
	}

	public static function carregar( $cd_documento_protocolo_item )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			SELECT cd_documento_protocolo_item, cd_documento_protocolo, cd_tipo_doc, 
			       cd_empresa, cd_registro_empregado, seq_dependencia, dt_cadastro, 
			       cd_usuario_cadastro, dt_exclusao, cd_usuario_exclusao, descricao, 
			       fl_recebido, observacao, ds_processo, ds_observacao_indexacao, 
			       TO_CHAR(dt_indexacao, 'DD/MM/YYYY') AS dt_indexacao
			FROM projetos.documento_protocolo_item 
			WHERE dt_exclusao IS NULL AND cd_documento_protocolo_item = {cd_documento_protocolo_item}

		" );

		$db->setParameter( "{cd_documento_protocolo_item}", $cd_documento_protocolo_item );

		$collection = $db->get();
		$item = $collection[0];

		return $item;
	}

	public static function select_2($filtro = array())
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		// Montar filtros
		$where_ext = "";
		$sep = " AND ";

		if($filtro['ano']!="")
		{
			$where_ext .= $sep . " a.nr_ano = " . intval( $filtro['ano'] );
			$sep = " AND ";
		}
		if($filtro['contador']!="")
		{
			$where_ext .= $sep . " a.nr_contador = " . intval( $filtro['contador'] );
			$sep = " AND ";
		}
		if($filtro['cd_empresa']!="")
		{
			$where_ext .= $sep . " b.cd_empresa = " . intval( $filtro['cd_empresa'] );
			$sep = " AND ";
		}
		if($filtro['cd_registro_empregado']!="")
		{
			$where_ext .= $sep . " b.cd_registro_empregado = " . intval( $filtro['cd_registro_empregado'] );
			$sep = " AND ";
		}
		if($filtro['seq_dependencia']!="")
		{
			$where_ext .= $sep . " b.seq_dependencia = " . intval( $filtro['seq_dependencia'] );
			$sep = " AND ";
		}
		if($filtro['cd_tipo_doc']!='')
		{
			if( is_numeric($filtro['cd_tipo_doc']) )
			{
				$where_ext .= $sep . " b.cd_tipo_doc = " . intval( $filtro['cd_tipo_doc'] );
			}
			else
			{
				$where_ext .= $sep . " d.nome_documento LIKE UPPER('%" . $db->escape( $filtro['cd_tipo_doc'] ) . "%') ";
			}
			$sep = " AND ";
		}

		if($filtro['dt_envio_inicio']!="" && $filtro['dt_envio_fim']!="")
		{
			$where_ext .= $sep . " DATE_TRUNC('day', a.dt_envio) BETWEEN TO_DATE('".$filtro['dt_envio_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$filtro['dt_envio_fim']."', 'DD/MM/YYYY') ";
			$sep = " AND ";
		}

		if($filtro['dt_ok_inicio']!="" && $filtro['dt_ok_fim']!="")
		{
			$where_ext .= $sep . " DATE_TRUNC('day', a.dt_ok) BETWEEN TO_DATE('".$filtro['dt_ok_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$filtro['dt_ok_fim']."', 'DD/MM/YYYY') ";
			$sep = " AND ";
		}
		
		if((int)$filtro['cd_usuario_envio']>0)
		{
			$where_ext .= $sep . " a.cd_usuario_cadastro=".(int)$filtro['cd_usuario_envio'];
			$sep = " AND ";
		}
		
		if((int)$filtro['cd_usuario_destino']>0)
		{
			$where_ext .= $sep . " a.cd_usuario_ok=".(int)$filtro['cd_usuario_destino'];
			$sep = " AND ";
		}

		$db->setSQL( "

			/* documento_recebido_item::select_2(); */
		
			SELECT

				a.nr_ano
				, a.nr_contador
				, a.cd_documento_recebido
				, a.cd_usuario_cadastro
				, TO_CHAR(a.dt_envio, 'DD/MM/YYYY') as dt_envio
				, a.cd_usuario_envio
				, TO_CHAR(a.dt_ok, 'DD/MM/YYYY') as dt_ok
				, a.cd_usuario_ok
				, to_char(a.dt_redirecionamento, 'DD/MM/YYYY') AS dt_redirecionamento
				, b.cd_tipo_doc
				, b.cd_empresa
				, b.cd_registro_empregado
				, b.seq_dependencia
				, b.nr_folha
				, coalesce(c.nome, b.nome) as nome_participante
				, d.nome_documento
				, e.divisao as divisao_usuario_destino
				, e.guerra as guerra_usuario_destino
				, b.arquivo

			FROM

				projetos.documento_recebido a

				JOIN projetos.documento_recebido_item b
				ON a.cd_documento_recebido=b.cd_documento_recebido

				LEFT JOIN public.participantes c
				ON b.cd_empresa=c.cd_empresa 
				AND b.cd_registro_empregado=c.cd_registro_empregado 
				AND b.seq_dependencia=c.seq_dependencia

				LEFT JOIN public.tipo_documentos d
				ON b.cd_tipo_doc=d.cd_tipo_doc
				
				LEFT JOIN projetos.usuarios_controledi e
				ON e.codigo=a.cd_usuario_destino

			WHERE

				b.dt_exclusao IS NULL
				$where_ext

			ORDER BY

				a.nr_ano, a.nr_contador

		" );

		$r = $db->get(true);
		$collection = array();

		foreach( $r as $item )
		{
			$collection[sizeof($collection)] = $item;
		}

		return $collection;
	}

	public function reiniciar_itens_get_sql($cd_documento_protocolo)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			UPDATE projetos.documento_protocolo_item
			SET fl_recebido = 'N'
				, dt_indexacao = null
				, motivo_devolucao=''
				, dt_devolucao=null
				, cd_usuario_devolucao=null
			WHERE cd_documento_protocolo = {cd_documento_protocolo}
			;

		" );

		$db->setParameter( "{cd_documento_protocolo}", $cd_documento_protocolo );

		return $db->getSQL();
	}

	public function receber_item_get_sql( $cd_documento_recebido_item )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$devolucao = "";
		$indexacao = "";

		$db->setSQL( "

			UPDATE projetos.documento_recebido_item
			SET dt_recebimento = 'CURRENT_TIMESTAMP'
			,cd_usuario_recebimento = {cd_usuario_recebimento}
			WHERE cd_documento_recebido_item = {cd_documento_recebido_item};

		" );

		$db->setParameter( "{cd_documento_recebido_item}", $cd_documento_recebido_item );
		$db->setParameter( "{fl_recebido}", $fl_recebido );

		return $db->getSQL();
	}

	public function executar_sql($s)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL($s);
		$db->execute();
	}

	public function consultar_total_devolvidos( $cd_documento_protocolo )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			SELECT count(*) as quantos
			FROM projetos.documento_protocolo_item 
			WHERE cd_documento_protocolo = {cd_documento_protocolo}
			AND dt_devolucao IS NOT NULL

		" );

		$db->setParameter( "{cd_documento_protocolo}", $cd_documento_protocolo );

		$collection = $db->get(true);
		$item = $collection[0];

		return $item['quantos'];
	}

	public static function inserir(& $args)
	{
		// CONSISTENCIAS

		if( trim($args['cd_empresa'])=='' ) { $args['cd_empresa']='null'; } else { $args['cd_empresa']=intval($args['cd_empresa']); }
		if( trim($args['cd_registro_empregado'])=='' ) { $args['cd_registro_empregado']='null'; } else { $args['cd_registro_empregado']=intval($args['cd_registro_empregado']); }
		if( trim($args['seq_dependencia'])=='' ) { $args['seq_dependencia']='null'; } else { $args['seq_dependencia']=intval($args['seq_dependencia']); }

		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			INSERT INTO projetos.documento_recebido_item
			(
	            cd_documento_recebido_item
	            , cd_documento_recebido
	            , cd_empresa
	            , cd_registro_empregado
	            , seq_dependencia
	            , ds_observacao
	            , nr_folha
	            , cd_tipo_doc
	            , dt_cadastro
	            , cd_usuario_cadastro
				, arquivo
				, arquivo_nome
				, nome
	        )
		    VALUES 
		    (
		    	{cd_documento_recebido_item}
	            , {cd_documento_recebido}
	            , {cd_empresa}
	            , {cd_registro_empregado}
	            , {seq_dependencia}
	            , '{ds_observacao}'
	            , {nr_folha}
	            , {cd_tipo_doc}
	            , CURRENT_TIMESTAMP
	            , {cd_usuario_cadastro}
	            , '{arquivo}'
	            , '{arquivo_nome}'
	            , '{nome}'
			);

		" );

		$args["cd_documento_recebido_item"] = $db->newId("projetos.documento_recebido_item.cd_documento_recebido_item");
		$db->setParameter( "{cd_documento_recebido_item}", (int)$args["cd_documento_recebido_item"] );
		$db->setParameter( "{cd_documento_recebido}", (int)$args['cd_documento_recebido'] );
		$db->setParameter( "{cd_tipo_doc}", (int)$args['cd_tipo_doc'] );
		$db->setParameter( "{cd_empresa}", $args['cd_empresa'] );
		$db->setParameter( "{cd_registro_empregado}", $args['cd_registro_empregado'] );
		$db->setParameter( "{seq_dependencia}", $args['seq_dependencia'] );
		$db->setParameter( "{ds_observacao}", $args['ds_observacao'] );
		$db->setParameter( "{cd_usuario_cadastro}", (int)$args['cd_usuario_cadastro'] );
		$db->setParameter( "{nr_folha}", (int)$args['nr_folha'] );
		$db->setParameter( "{arquivo}", $args['arquivo'] );
		$db->setParameter( "{arquivo_nome}", $args['arquivo_nome'] );
		$db->setParameter( "{nome}", $args['nome'] );

		$db->execute();

		return true;
	}
	
	static function excluir($cd_documento_recebido_item, $cd_usuario_exclusao)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			UPDATE 
				projetos.documento_recebido_item
			SET 
				dt_exclusao = CURRENT_TIMESTAMP
				, cd_usuario_exclusao = {cd_usuario_exclusao}
			WHERE 
				cd_documento_recebido_item = {cd_documento_recebido_item}
			;

		" );

		$db->setParameter( "{cd_documento_recebido_item}", (int)$cd_documento_recebido_item );
		$db->setParameter( "{cd_usuario_exclusao}", (int)$cd_usuario_exclusao );

		$db->execute();

		return true;
	}
}
?>