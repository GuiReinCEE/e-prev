<?php
class tarefa_checklist
{
	/**
	 * Listar perguntas relacionada a checklist de testes de tarefas
	 * filtrando pelo tipo da pergunta
	 * 
	 * @param enum_projetos_tarefa_checklist_tipo $cd_tarefa_checklist_tipo
	 * @param int $cd_tarefa_checklist_grupo código do grupo da pergunta
	 * 
	 * @return array(array()) 
	 * ------------------------------------
	 * SELECT

				ptc.cd_tarefa_checklist
				, ptcp.cd_tarefa_checklist_pergunta
				, ptcp.ds_pergunta

			FROM

				projetos.tarefa_checklist ptc

				JOIN projetos.tarefa_checklist_pergunta ptcp
				ON ptc.cd_tarefa_checklist=ptcp.cd_tarefa_checklist

			WHERE 

				ptcp.fl_ativo = 'S'
				AND ptc.fl_ativo = 'S'
				AND ptc.cd_tarefa_checklist_tipo = {cd_tarefa_checklist_tipo}
				AND ( ptcp.cd_tarefa_checklist_grupo = {cd_tarefa_checklist_grupo} OR 0 = {cd_tarefa_checklist_grupo} )

			ORDER BY

				ptcp.nr_ordem

			;
	 * ------------------------------------
	 */
	public static function select_1($cd_tarefa_checklist_tipo, $cd_tarefa_checklist_grupo=0)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT

				ptc.cd_tarefa_checklist
				, ptcp.cd_tarefa_checklist_pergunta
				, ptcp.ds_pergunta

			FROM

				projetos.tarefa_checklist ptc

				JOIN projetos.tarefa_checklist_pergunta ptcp
				ON ptc.cd_tarefa_checklist=ptcp.cd_tarefa_checklist

			WHERE 

				ptcp.fl_ativo = 'S'
				AND ptc.fl_ativo = 'S'
				AND ptc.cd_tarefa_checklist_tipo = {cd_tarefa_checklist_tipo}
				AND ( ptcp.cd_tarefa_checklist_grupo = {cd_tarefa_checklist_grupo} OR 0 = {cd_tarefa_checklist_grupo} )

			ORDER BY

				ptcp.nr_ordem

			;

		" );

		$db->setParameter("{cd_tarefa_checklist_tipo}", intval($cd_tarefa_checklist_tipo));
		$db->setParameter("{cd_tarefa_checklist_grupo}", intval($cd_tarefa_checklist_grupo));

		$r = $db->get(true);
		$collection = array();

		foreach( $r as $item )
		{
			$collection[sizeof($collection)] = $item;
		}

		return $collection;
	}
	
	/**
	 * Carregar resposta de Tarefa/Pergunta informada por parametros
	 *
	 * @param int $cd_tarefas
	 * @param int $cd_tarefa_checklist_pergunta
	 */
	static function select_2($cd_tarefas, $cd_tarefa_checklist_pergunta)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT

				*

			FROM

				projetos.tarefa_checklist_resposta

			WHERE 

				cd_tarefas = {cd_tarefas}
				AND cd_tarefa_checklist_pergunta = {cd_tarefa_checklist_pergunta}

			;

		" );

		$db->setParameter("{cd_tarefas}", intval($cd_tarefas));
		$db->setParameter("{cd_tarefa_checklist_pergunta}", intval($cd_tarefa_checklist_pergunta));

		$r = $db->get();
		$collection = array();

		foreach( $r as $item )
		{
			$collection[sizeof($collection)] = $item;
		}

		return $collection;
	}
	
	/**
	 * Lista todos os grupos existentes para o tipo de checklist informado
	 *
	 * @param int $cd_tarefa_checklist_tipo
	 * @return array(array())
	 * 
	 * ---------------
	 * 	    SELECT

				grupo.*
			
			FROM
			
				projetos.tarefa_checklist_grupo grupo
			
			WHERE 
			
				grupo.cd_tarefa_checklist_tipo = {cd_tarefa_checklist_tipo}
			
			ORDER BY
			
				grupo.nr_ordem
			
			;
	 * 
	 * ---------------
	 */
	static function select_3($cd_tarefa_checklist_tipo)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			SELECT

				grupo.*
			
			FROM
			
				projetos.tarefa_checklist_grupo grupo
			
			WHERE 
			
				grupo.cd_tarefa_checklist_tipo = {cd_tarefa_checklist_tipo}
			
			ORDER BY
			
				grupo.nr_ordem
			
			;

		" );

		$db->setParameter("{cd_tarefa_checklist_tipo}", intval($cd_tarefa_checklist_tipo));

		$r = $db->get();
		$collection = array();

		foreach( $r as $item )
		{
			$collection[sizeof($collection)] = $item;
		}

		return $collection;
	}
	
	/**
	 * Inserir uma resposta para pergunta de checklist relacionada a tarefas
	 *
	 * @param int $cd_tarefas
	 * @param int $cd_tarefa_checklist_pergunta
	 * @param string $fl_resposta "S/N/''"
	 * @param string $fl_especialista "S/N"
	 */
	static function inserir_resposta( $cd_tarefas, $cd_tarefa_checklist_pergunta, $fl_resposta, $fl_especialista )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			INSERT INTO projetos.tarefa_checklist_resposta
			(
	            fl_resposta
	            , fl_especialista
	            , cd_tarefa_checklist_pergunta
	            , cd_tarefas
            )
   			VALUES
   			(
	   			'{fl_resposta}'
	   			, '{fl_especialista}'
	   			, {cd_tarefa_checklist_pergunta}
	   			, {cd_tarefas}
   			);
		

		" );

		$db->setParameter("{fl_resposta}", $fl_resposta);
		$db->setParameter("{fl_especialista}", $fl_especialista);
		$db->setParameter("{cd_tarefa_checklist_pergunta}", intval($cd_tarefa_checklist_pergunta));
		$db->setParameter("{cd_tarefas}", intval($cd_tarefas));

		$r = $db->execute();
		
		if($db->haveError())
		{
			//throw new Exception( $db->getMessage() );
			throw new Exception( "{Falha em tarefa_checklist::inserir_resposta()}" );
			return false;
		}
	}
	
	/**
	 * Excluir todas respostas relacionadas a uma determinada tarefa
	 *
	 * @param int $cd_tarefas
	 * @return unknown
	 */
	static function excluir_resposta( $cd_tarefas )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			DELETE FROM projetos.tarefa_checklist_resposta
			WHERE cd_tarefas = {cd_tarefas}
			;

		" );

		$db->setParameter("{cd_tarefas}", intval($cd_tarefas));

		$r = $db->execute();
		
		if($db->haveError())
		{
			//throw new Exception( $db->getMessage() );
			throw new Exception( "{Falha em tarefa_checklist::excluir_resposta()}" );
			return false;
		}
	}
}
?>