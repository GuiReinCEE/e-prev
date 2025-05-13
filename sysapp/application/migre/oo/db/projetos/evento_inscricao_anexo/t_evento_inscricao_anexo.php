<?php
class t_evento_inscricao_anexo
{
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
			SELECT * 
			FROM projetos.evento_inscricao_anexo 
			WHERE cd_evento_inscricao_anexo={cd_evento_inscricao_anexo}
		" );

		$db->setParameter( "{cd_evento_inscricao_anexo}", $cd_pk );

		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection[0];
	}

	/**
	 * Query para selecionar todos os anexos de determinada inscrição
	 *
	 * @param $cd Valor da chave primária da inscrição para busca de anexos
	 *
	 * @return array
	 */
	public static function select_por_inscricao( $cd )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "
			SELECT * 
			FROM projetos.evento_inscricao_anexo 
			WHERE cd_eventos_institucionais_inscricao={cd_eventos_institucionais_inscricao}
		" );

		$db->setParameter( "{cd_eventos_institucionais_inscricao}", $cd );

		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}

	/**
	 * Inserir dados na tabela
	 * 
	 * @param array $dados Campos da base com valores para persistir
	 * 
	 * @return int Nova sequence gerada
	 */
	public static function insert($dados = array())
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL("
			INSERT INTO projetos.evento_inscricao_anexo
			(
				cd_evento_inscricao_anexo
				, cd_eventos_institucionais_inscricao
				, anexo
			)
			 VALUES
			(
				{cd_evento_inscricao_anexo}
				, {cd_eventos_institucionais_inscricao}
				, '{anexo}'
			)
		");

		$newId = $db->newId("projetos.evento_inscricao_anexo.cd_evento_inscricao_anexo");
		$db->setParameter( "{cd_evento_inscricao_anexo}", $newId );
		$db->setParameter( "{cd_eventos_institucionais_inscricao}", $dados['cd_eventos_institucionais_inscricao'] );
		$db->setParameter( "{anexo}", $dados['anexo'] );

		$db->execute();

		// echo $db->getMessage();

		if($db->haveError())
		{
			throw new Exception( $db->getMessage() );
			return false; 
		}
		
		return $newId;
	}

	/**
	 * Atualizar dados na tabela
	 * 
	 * @param array $dados Campos da base com valores para persistir
	 * 
	 * @return boolean Sucesso ou falha para true ou false
	 */
	public static function update($dados = array())
	{
		return false;
		
		/*
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL("

			UPDATE projetos.eventos_institucionais_inscricao
			SET cd_eventos_institucionais = {cd_eventos_institucionais}
				, cd_empresa = {cd_empresa}
				, cd_registro_empregado = {cd_registro_empregado}
				, seq_dependencia = {seq_dependencia}
				, nome = '{nome}'
				, telefone = '{telefone}'
				, email = '{email}'
				, observacao = '{observacao}'
				, tipo = '{tipo}'
			WHERE cd_eventos_institucionais_inscricao = {cd_eventos_institucionais_inscricao};

		");
		
		$db->setParameter( "{cd_eventos_institucionais_inscricao}", $dados['cd_eventos_institucionais_inscricao'] );
		$db->setParameter( "{cd_eventos_institucionais}", $dados['cd_eventos_institucionais'] );
		$db->setParameter( "{cd_empresa}", $dados['cd_empresa'] );
		$db->setParameter( "{cd_registro_empregado}", $dados['cd_registro_empregado'] );
		$db->setParameter( "{seq_dependencia}", $dados['seq_dependencia'] );
		$db->setParameter( "{nome}", $dados['nome'] );
		$db->setParameter( "{telefone}", $dados['telefone'] );
		$db->setParameter( "{email}", $dados['email'] );
		$db->setParameter( "{observacao}", $dados['observacao'] );
		$db->setParameter( "{tipo}", $dados['tipo'] );

		$db->execute();
		
		// echo $db->getMessage();
		
		if($db->haveError())
		{
			throw new Exception( $db->getMessage() );
			return false; 
		}
		return true;
		*/
		
	}
}
?>