<?php
class t_eventos_institucionais_inscricao
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
			FROM projetos.eventos_institucionais_inscricao 
			WHERE cd_eventos_institucionais_inscricao={cd_eventos_institucionais_inscricao}
		" );

		$db->setParameter( "{cd_eventos_institucionais_inscricao}", $cd_pk );

		$r = $db->get();

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection[0];
	}

	/**
	 * Inserir dados na tabela cenario.cenario_publicacao
	 * 
	 * @param array $dados Campos da base com valores para persistir
	 * 
	 * @return int Nova sequence gerada
	 */
	public static function insert($dados = array())
	{

		$sql = "
			INSERT INTO projetos.eventos_institucionais_inscricao
			(
				cd_eventos_institucionais_inscricao
				, cd_eventos_institucionais
				, cd_empresa
				, cd_registro_empregado
				, seq_dependencia
				, nome
				, telefone
				, email
				, observacao
				, dt_cadastro
				, cadastro_por
				, tipo
				, endereco
				, cidade
				, cep
				, uf
				, fl_desclassificado
				, empresa
			)
			 VALUES
			(
				{cd_eventos_institucionais_inscricao}
				, {cd_eventos_institucionais}
				, {cd_empresa}
				, {cd_registro_empregado}
				, {seq_dependencia}
				, UPPER(funcoes.remove_acento('{nome}'))
				, '{telefone}'
				, '{email}'
				, '{observacao}'
				, CURRENT_TIMESTAMP
				, '{cadastro_por}'
				, {tipo}
				, {endereco}
				, {cidade}
				, {cep}
				, {uf}
				, {fl_desclassificado}
				, {empresa}
			);
	
		";
		
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL("
					SELECT email_texto,
                           email_assunto
					  FROM projetos.eventos_institucionais
				     WHERE cd_evento = ".intval($dados['cd_eventos_institucionais']).";
		            ");
		$ar_email = $db->get();		
		$ar_email = $ar_email[0];
		
		if((trim($dados['email']) != "") and (trim($ar_email['email_texto']) != "") and (trim($ar_email['email_assunto']) != ""))
		{
			$sql.= "
				INSERT INTO projetos.envia_emails 
					 ( 
					   dt_envio,
					   de,
					   para,
					   cc,
					   cco,
					   assunto,
					   texto,
					   cd_empresa, 
					   cd_registro_empregado, 
					   seq_dependencia,					   
					   cd_evento
					 ) 
				VALUES
					 ( 
					   CURRENT_TIMESTAMP,
					   'Fundação CEEE',
					   '".$dados['email']."',
					   '',
					   '',
					   '".trim($ar_email['email_assunto'])."',
					   '".trim($ar_email['email_texto'])."',
					   ".(trim($dados['cd_empresa'])            == "" ? 'DEFAULT' : $dados['cd_empresa']).", 
					   ".(trim($dados['cd_registro_empregado']) == "" ? 'DEFAULT' : $dados['cd_registro_empregado']).", 
					   ".(trim($dados['seq_dependencia'])       == "" ? 'DEFAULT' : $dados['seq_dependencia']).",					   
					   59
					 );
				   ";
		}	
		
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL($sql);

		$newId = $db->newId("projetos.eventos_institucionais_inscricao.cd_eventos_institucionais_inscricao");
		$db->setParameter( "{cd_eventos_institucionais_inscricao}", $newId );
		$db->setParameter( "{cd_eventos_institucionais}", $dados['cd_eventos_institucionais'] );
		$db->setParameter( "{cd_empresa}", $dados['cd_empresa'], array('use_null'=>true) );
		$db->setParameter( "{cd_registro_empregado}", $dados['cd_registro_empregado'], array('use_null'=>true) );
		$db->setParameter( "{seq_dependencia}", $dados['seq_dependencia'], array('use_null'=>true) );
		$db->setParameter( "{nome}", $dados['nome'] );
		$db->setParameter( "{telefone}", $dados['telefone'] );
		$db->setParameter( "{email}", $dados['email'] );
		$db->setParameter( "{observacao}", $dados['observacao'] );
		$db->setParameter( "{cadastro_por}", $dados['cadastro_por'] );
		$db->setParameter( "{tipo}", $dados['tipo'], array('use_null'=>true) );
		$db->setParameter( "{endereco}", $dados['endereco'], array('use_null'=>true) );
		$db->setParameter( "{cidade}", $dados['cidade'], array('use_null'=>true) );
		$db->setParameter( "{cep}", $dados['cep'], array('use_null'=>true) );
		$db->setParameter( "{uf}", $dados['uf'], array('use_null'=>true) );
		$db->setParameter( "{fl_desclassificado}", $dados['fl_desclassificado'], array('use_null'=>true) );
		$db->setParameter( "{empresa}", $dados['empresa'], array('use_null'=>true) );

		$db->execute();

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
				, endereco = '{endereco}'
				, cidade = '{cidade}'
				, cep = '{cep}'
				, uf = '{uf}'
				, fl_desclassificado = '{fl_desclassificado}'
				, fl_selecionado = '{fl_selecionado}'
				, ds_motivo = '{ds_motivo}'
				, empresa = '{empresa}'
			WHERE cd_eventos_institucionais_inscricao = {cd_eventos_institucionais_inscricao};

		");

		$db->setParameter( "{cd_eventos_institucionais_inscricao}", $dados['cd_eventos_institucionais_inscricao'] );
		$db->setParameter( "{cd_eventos_institucionais}", $dados['cd_eventos_institucionais'] );
		$db->setParameter( "{cd_empresa}", $dados['cd_empresa'], array('use_null'=>true) );
		$db->setParameter( "{cd_registro_empregado}", $dados['cd_registro_empregado'], array('use_null'=>true) );
		$db->setParameter( "{seq_dependencia}", $dados['seq_dependencia'], array('use_null'=>true) );
		$db->setParameter( "{nome}", $dados['nome'] );
		$db->setParameter( "{telefone}", $dados['telefone'] );
		$db->setParameter( "{email}", $dados['email'] );
		$db->setParameter( "{observacao}", $dados['observacao'] );
		$db->setParameter( "{tipo}", $dados['tipo'] );
		$db->setParameter( "{endereco}", $dados['endereco'] );
		$db->setParameter( "{cidade}", $dados['cidade'] );
		$db->setParameter( "{cep}", $dados['cep'] );
		$db->setParameter( "{uf}", $dados['uf'] );
		$db->setParameter( "{fl_desclassificado}", $dados['fl_desclassificado'] );
		$db->setParameter( "{fl_selecionado}", $dados['fl_selecionado'] );
		$db->setParameter( "{ds_motivo}", $dados['ds_motivo'] );
		$db->setParameter( "{empresa}", $dados['empresa'] );
		
		$db->execute();

		// echo $db->getMessage();exit;
		
		if($db->haveError())
		{
			throw new Exception( $db->getMessage() );
			return false; 
		}
		
		return true;
	}

	/**
	 * Atualizar dados na tabela
	 * 
	 * @param array $dados Campos da base com valores para persistir
	 * 
	 * @return boolean Sucesso ou falha para true ou false
	 */
	public static function selecionar($cd_eventos_institucionais_inscricao, $fl_selecionado)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL("

			UPDATE projetos.eventos_institucionais_inscricao
			SET fl_selecionado = '{fl_selecionado}'
			WHERE cd_eventos_institucionais_inscricao = {cd_eventos_institucionais_inscricao};

		");

		$db->setParameter( "{cd_eventos_institucionais_inscricao}", $cd_eventos_institucionais_inscricao );
		$db->setParameter( "{fl_selecionado}", $fl_selecionado );

		$db->execute();

		if($db->haveError())
		{
			throw new Exception( $db->getMessage() );
			return false; 
		}

		return true;
	}

	public static function delete($cd_pk)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL("

			UPDATE projetos.eventos_institucionais_inscricao
			SET dt_exclusao = CURRENT_TIMESTAMP
			WHERE cd_eventos_institucionais_inscricao = {cd_eventos_institucionais_inscricao};

		");

		$db->setParameter( "{cd_eventos_institucionais_inscricao}", $cd_pk );

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