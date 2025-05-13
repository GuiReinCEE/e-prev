<?php
class t_mensagem_estacao
{
	/**
	 * Query para tabela e relacionamento filtrada pela gerencia e pela data atual
	 *
	 * @param $gerencia Gerência a ser encontrada
	 *
	 * @return array(array) Resultado da consulta
	 */
	public static function select_1( $gerencia )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "
			SELECT *
			FROM projetos.mensagem_estacao estacao
			JOIN projetos.mensagem_estacao_gerencia gerencia ON estacao.cd_mensagem_estacao=gerencia.cd_mensagem_estacao
			WHERE (gerencia='ALL' OR gerencia = '{gerencia}') AND dt_inicial = CURRENT_DATE
		" );

		$db->setParameter( "{gerencia}", $gerencia );

		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}
	
	/**
	 * Query para tabela projetos.mensagem_estacao filtrada por data inicial
	 *
	 * @param array() Data Inicial da mensagem, ou seja, data do agendamento e codigo da mensagem
	 *
	 * @return boolean True ou False para Existe ou Não existe registros para data informada
	 */
	public static function existe_na_data( $filtro )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "
			SELECT count(*) as qtd
			FROM projetos.mensagem_estacao estacao
			WHERE dt_inicial = '{dt_inicial}'
			AND NOT cd_mensagem_estacao={cd_mensagem_estacao}
		" );

		$db->setParameter( "{dt_inicial}", $filtro['dt_inicial'], array('is_date'=>true) );
		$db->setParameter( "{cd_mensagem_estacao}", $filtro['cd_mensagem_estacao'] );

		$r = $db->get();
		
		$ret = ($r[0]['qtd']>"0");

		return $ret;
	}

	/**
	 * Query para tabela filtrada pela PK
	 *
	 * @param $cd_pk Valor da chave primária a ser procurada
	 *
	 * @return array
	 */
	public static function select_pk( $cd_pk )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "
			SELECT cd_mensagem_estacao, nome, arquivo, url, to_char(dt_inicial, 'DD/MM/YYYY') as dt_inicial, dt_cadastro, cd_usuario
			FROM projetos.mensagem_estacao estacao
			WHERE cd_mensagem_estacao = {cd_mensagem_estacao}
		" );

		$db->setParameter( "{cd_mensagem_estacao}", $cd_pk );

		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection[0];
	}
	
	/**
	 * Inserir dados na tabela projetos.mensagem_estacao
	 * e na tabela projetos.mensagem_estacao_gerencia
	 * 
	 * @param array $dados Campos da base com valores para persistir
	 * 
	 * @return int Nova sequence gerada
	 */
	public static function insert($dados = array())
	{

		$db = new postgres();
		$db = DBFactory::createObject();
		
		#echo "<PRE>".print_r($dados,true)."</PRE>"; exit;
		
		
		// gerencias (tabela relacionada)
		$inserts_gerencias = "INSERT INTO projetos.mensagem_estacao_gerencia (cd_mensagem_estacao, gerencia) VALUES ";
		$sep = "";
		if(sizeof($dados['gerencias'])>0)
		{
			foreach($dados['gerencias'] as $gerencia)
			{
				$inserts_gerencias .= $sep . " ({cd_mensagem_estacao}, '" . $db->escape($gerencia) . "') ";
				$sep = ", ";
			}
		}
		else
		{
			$inserts_gerencias .= " ({cd_mensagem_estacao}, 'ALL') ";
		}
		
		$db->setSQL("
			INSERT INTO projetos.mensagem_estacao
			(
				cd_mensagem_estacao
				, nome
				, arquivo
				, url
				, dt_inicial
				, dt_cadastro
				, cd_usuario
			)
			 VALUES
			(
				{cd_mensagem_estacao}
				, '{nome}'
				, '{arquivo}'
				, '{url}'
				, {dt_inicial}
				, CURRENT_TIMESTAMP
				, {cd_usuario}
			);
			
			$inserts_gerencias
		");

		$newId = $db->newId("projetos.mensagem_estacao.cd_mensagem_estacao");
		$db->setParameter( "{cd_mensagem_estacao}", $newId );
		$db->setParameter( "{nome}", $dados['nome'] );
		$db->setParameter( "{arquivo}", $dados['arquivo'] );
		$db->setParameter( "{url}", $dados['url'] );
		$db->setParameter( "{dt_inicial}", $dados['dt_inicial'], array('is_date'=>true, 'use_null'=>true) );
		$db->setParameter( "{cd_usuario}", $dados['cd_usuario'] );
		
		$db->setParameter( "{gerencia}", "ALL" );

		$db->execute();

		if($db->haveError())
		{
			throw new Exception( $db->getMessage() );
			return false; 
		}

		return $newId;
	}
	
	/**
	 * Atualizar dados na tabela projetos.mensagem_estacao
	 * e na tabela projetos.mensagem_estacao_gerencia
	 * 
	 * @param array $dados Campos da base com valores para persistir
	 * 
	 * @return boolean true ou false para Sucesso ou falha 
	 */
	public static function update($dados = array())
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		
		// gerencias (tabela relacionada)
		$inserts_gerencias = "INSERT INTO projetos.mensagem_estacao_gerencia (cd_mensagem_estacao, gerencia) VALUES ";
		$sep = "";
		if(sizeof($dados['gerencias'])>0)
		{
			foreach($dados['gerencias'] as $gerencia)
			{
				$inserts_gerencias .= $sep . " ({cd_mensagem_estacao}, '" . $db->escape($gerencia) . "') ";
				$sep = ", ";
			}
		}
		else
		{
			$inserts_gerencias .= " ({cd_mensagem_estacao}, 'ALL') ";
		}
		
		if($dados["arquivo"]=="")
		{
			$db->setSQL("
	
				UPDATE projetos.mensagem_estacao
				SET nome = '{nome}',
				    url = '{url}',
					dt_inicial = '{dt_inicial}'
				WHERE cd_mensagem_estacao = {cd_mensagem_estacao};
				
				DELETE FROM projetos.mensagem_estacao_gerencia WHERE cd_mensagem_estacao={cd_mensagem_estacao};
				
				$inserts_gerencias
	
			");
		}
		else
		{
			$db->setSQL("

				UPDATE projetos.mensagem_estacao
				SET nome = '{nome}',
					url = '{url}',
					dt_inicial = '{dt_inicial}',
					arquivo = '{arquivo}'
				WHERE cd_mensagem_estacao = {cd_mensagem_estacao};
				
				DELETE FROM projetos.mensagem_estacao_gerencia WHERE cd_mensagem_estacao={cd_mensagem_estacao};
				
				$inserts_gerencias

			");
		}

		$db->setParameter( "{nome}", $dados['nome'] );
		$db->setParameter( "{url}", $dados['url'] );
		$db->setParameter( "{arquivo}", $dados['arquivo'] );
		$db->setParameter( "{dt_inicial}", $dados['dt_inicial'], array('is_date'=>true) );
		
		$db->setParameter( "{cd_mensagem_estacao}", $dados['cd_mensagem_estacao'] );

		$db->execute();

		if($db->haveError())
		{
			throw new Exception( $db->getMessage() );
			return false; 
		}

		return true;
	}
	
	public static function delete($pk)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL("

			DELETE FROM projetos.mensagem_estacao_gerencia WHERE cd_mensagem_estacao = {cd_mensagem_estacao};
			DELETE FROM projetos.mensagem_estacao WHERE cd_mensagem_estacao = {cd_mensagem_estacao};

		");

		$db->setParameter( "{cd_mensagem_estacao}", $pk );

		$db->execute();

		if($db->haveError())
		{
			throw new Exception( $db->getMessage() );
			return false; 
		}
		
		return true;
	}
}
?>