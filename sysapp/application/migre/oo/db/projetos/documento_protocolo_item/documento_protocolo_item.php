<?php
class documento_protocolo_item
{
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
			 ORDER BY cd_documento_protocolo_item

		" );

		$db->setParameter( "{cd_documento_protocolo_item}", $cd_documento_protocolo_item );

		$collection = $db->get();
		$item = $collection[0];

		return $item;
	}

	public static function select_01($filtro = array())
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		// Montar filtros
		$where_ext = "";
		$sep = " AND ";

		if($filtro['ano']!="")
		{
			$where_ext .= $sep . " dp.ano = " . $db->escape( $filtro['ano'] );
			$sep = " AND ";
		}
		if($filtro['contador']!="")
		{
			$where_ext .= $sep . " dp.contador = " . $db->escape( $filtro['contador'] );
			$sep = " AND ";
		}
		if($filtro['cd_empresa']!="")
		{
			$where_ext .= $sep . " dpi.cd_empresa = " . $db->escape( $filtro['cd_empresa'] );
			$sep = " AND ";
		}
		if($filtro['cd_registro_empregado']!="")
		{
			$where_ext .= $sep . " dpi.cd_registro_empregado = " . $db->escape( $filtro['cd_registro_empregado'] );
			$sep = " AND ";
		}
		if($filtro['seq_dependencia']!="")
		{
			$where_ext .= $sep . " dpi.seq_dependencia = " . $db->escape( $filtro['seq_dependencia'] );
			$sep = " AND ";
		}
		if($filtro['cd_tipo_doc']!='')
		{
			if( is_numeric($filtro['cd_tipo_doc']) )
			{
				$where_ext .= $sep . " dpi.cd_tipo_doc = " . $db->escape( $filtro['cd_tipo_doc'] );
			}
			else
			{
				$where_ext .= $sep . " ptd.nome_documento LIKE UPPER('%" . $db->escape( $filtro['cd_tipo_doc'] ) . "%') ";
			}
			$sep = " AND ";
		}

		if($filtro['ds_processo']!="")
		{
			$where_ext .= $sep . " dpi.ds_processo = '" . $db->escape( $filtro['ds_processo'] ) . "' ";
			$sep = " AND ";
		}

		if($filtro['dt_envio_inicio']!="" && $filtro['dt_envio_fim']!="")
		{
			$where_ext .= $sep . " DATE_TRUNC('day', dp.dt_envio) BETWEEN TO_DATE('".$filtro['dt_envio_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$filtro['dt_envio_fim']."', 'DD/MM/YYYY') ";
			$sep = " AND ";
		}

		if($filtro['dt_indexacao_inicio']!="" && $filtro['dt_indexacao_fim']!="")
		{
			$where_ext .= $sep . " DATE_TRUNC('day', dpi.dt_indexacao) BETWEEN TO_DATE('".$filtro['dt_indexacao_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$filtro['dt_indexacao_fim']."', 'DD/MM/YYYY') ";
			$sep = " AND ";
		}

		if($filtro['dt_ok_inicio']!="" && $filtro['dt_ok_fim']!="")
		{
			$where_ext .= $sep . " DATE_TRUNC('day', dp.dt_ok) BETWEEN TO_DATE('".$filtro['dt_ok_inicio']."', 'DD/MM/YYYY') AND TO_DATE('".$filtro['dt_ok_fim']."', 'DD/MM/YYYY') ";
			$sep = " AND ";
		}
		
		if($filtro['apenas_devolvidos']=="S")
		{
			$where_ext .= $sep . " dt_devolucao IS NOT NULL ";
			$sep = " AND ";
		}

		$db->setSQL( "

			SELECT

				dp.ano
				, dp.contador
				, dp.cd_documento_protocolo
				, dp.cd_usuario_cadastro
				, TO_CHAR(dp.dt_envio, 'DD/MM/YYYY') as dt_envio
			    , dp.cd_usuario_envio
			    , TO_CHAR(dp.dt_ok, 'DD/MM/YYYY') as dt_ok
			    , dp.cd_usuario_ok
			    , dp.ordem_itens
			    , TO_CHAR(dpi.dt_indexacao, 'DD/MM/YYYY') AS dt_indexacao
			    , dpi.cd_tipo_doc
			    , dpi.cd_empresa
			    , dpi.cd_registro_empregado
			    , dpi.seq_dependencia
			    , dpi.ds_processo, dpi.nr_folha
		       	, to_char(dpi.dt_devolucao, 'DD/MM/YYYY') AS dt_devolucao
		       	, dpi.motivo_devolucao
		       	, dpi.ds_observacao_indexacao
		       	, pp.nome as nome_participante
		       	, ptd.nome_documento
				, dpi.arquivo

			FROM

				projetos.documento_protocolo dp

				JOIN projetos.documento_protocolo_item dpi
				ON dp.cd_documento_protocolo=dpi.cd_documento_protocolo

				LEFT JOIN public.participantes pp
				ON pp.cd_empresa=dpi.cd_empresa 
				AND pp.cd_registro_empregado=dpi.cd_registro_empregado 
				AND pp.seq_dependencia=dpi.seq_dependencia

				LEFT JOIN public.tipo_documentos ptd
				ON ptd.cd_tipo_doc=dpi.cd_tipo_doc

			WHERE 

				dp.dt_exclusao IS NULL AND dpi.dt_exclusao IS NULL
				$where_ext

			ORDER BY

				dp.ano, dp.contador, dpi.documento_protocolo_item

			-- LIMIT 10

		" );

		$r = $db->get();
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

	public function salvar_item_get_sql( $cd_documento_protocolo_item, $fl_recebido, $dt_indexacao, $ds_observacao_indexacao, $dt_devolucao, $motivo_devolucao )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$devolucao = "";
		$indexacao = "";

		if($dt_devolucao!="")
		{
			$devolucao .= " , dt_devolucao = TO_DATE( '{dt_devolucao}', 'DD/MM/YYYY' ) ";
			$devolucao .= " , cd_usuario_devolucao = " . $_SESSION['Z'];
		}
		else
		{
			$devolucao .= " , dt_devolucao = null ";
			$devolucao .= " , cd_usuario_devolucao = null";
		}

		if($dt_indexacao!="")
		{
			$indexacao .= " , dt_indexacao = TO_DATE('{dt_indexacao}', 'DD/MM/YYYY') ";
		}
		else
		{
			$indexacao .= " , dt_indexacao = null ";
		}

		$db->setSQL( "

			UPDATE projetos.documento_protocolo_item
			   SET fl_recebido = '{fl_recebido}'
			       $indexacao
			       $devolucao
			       , ds_observacao_indexacao = '{ds_observacao_indexacao}'
			       , motivo_devolucao = '{motivo_devolucao}'
			 WHERE cd_documento_protocolo_item = {cd_documento_protocolo_item}
			;

		" );

		$db->setParameter( "{cd_documento_protocolo_item}", $cd_documento_protocolo_item );
		$db->setParameter( "{fl_recebido}", $fl_recebido );
		$db->setParameter( "{dt_indexacao}", $dt_indexacao );
		$db->setParameter( "{ds_observacao_indexacao}", $ds_observacao_indexacao );
		$db->setParameter( "{dt_devolucao}", $dt_devolucao );
		$db->setParameter( "{motivo_devolucao}", $motivo_devolucao );

		return $db->getSQL();
	}

	public function executar_sql($s)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL($s);
		$db->execute();
	}

	public function consultar_total_por_indexacao( $dt_indexacao )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			SELECT count(*) as quantos
			FROM projetos.documento_protocolo_item 
			WHERE dt_indexacao = to_date( '{dt_indexacao}', 'DD/MM/YYYY' )

		" );

		$db->setParameter( "{dt_indexacao}", $dt_indexacao );

		//echo "teste";exit;
		$collection = $db->get();
		$item = $collection[0];
		
		return $item['quantos'];
	}

	public function consultar_total_indexados( $cd_documento_protocolo )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			SELECT count(*) as quantos
			FROM projetos.documento_protocolo_item 
			WHERE cd_documento_protocolo = {cd_documento_protocolo}
			AND fl_recebido = 'S'

		" );

		$db->setParameter( "{cd_documento_protocolo}", $cd_documento_protocolo );

		$collection = $db->get();
		$item = $collection[0];

		return $item['quantos'];
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

		$collection = $db->get();
		$item = $collection[0];
		
		return $item['quantos'];
	}
}
?>