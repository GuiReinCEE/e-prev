<?php
class documento_recebido
{
	/**
	 * Enviar o protocolo para determinado funcionсrio
	 *
	 * @param 	int 	$cd_documento_recebido
	 * @param 	int 	$cd_usuario_envio
	 * @return bool
	 */
	static function enviar_protocolo($cd_documento_recebido, $cd_usuario_envio, $cd_usuario_destino, $redirecionamento=FALSE)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		if($redirecionamento)
		{
			$sql_extra = ", dt_redirecionamento=CURRENT_TIMESTAMP ";
		}

		$db->setSQL( "

			UPDATE

				projetos.documento_recebido

			SET

				dt_envio = CURRENT_TIMESTAMP
				, cd_usuario_envio = {cd_usuario_envio}
				, cd_usuario_destino = {cd_usuario_destino}
				$sql_extra

			WHERE

				cd_documento_recebido = {cd_documento_recebido}

		" );

		$db->setParameter( "{cd_documento_recebido}", intval($cd_documento_recebido) );
		$db->setParameter( "{cd_usuario_envio}", intval($cd_usuario_envio) );
		$db->setParameter( "{cd_usuario_destino}", intval($cd_usuario_destino) );

		$ret = $db->execute();
		//  --

		$db->setSQL( "

			INSERT INTO 

				projetos.documento_recebido_historico

				(
	            cd_documento_recebido, cd_documento_recebido_tipo, 
	            cd_usuario_cadastro, dt_envio, dt_cadastro, nr_ano, nr_contador, 
	            dt_ok, cd_usuario_envio, cd_usuario_ok, dt_alteracao, cd_usuario_destino, dt_redirecionamento, observacao_ok
	        	)

		    SELECT 

		    	cd_documento_recebido, cd_documento_recebido_tipo, 
	            cd_usuario_cadastro, dt_envio, dt_cadastro, nr_ano, nr_contador, 
	            dt_ok, cd_usuario_envio, cd_usuario_ok, CURRENT_TIMESTAMP AS dt_alteracao, cd_usuario_destino, dt_redirecionamento, observacao_ok 

			FROM 

				projetos.documento_recebido

			WHERE

				cd_documento_recebido = {cd_documento_recebido};

		" );

		$db->setParameter( "{cd_documento_recebido}", intval($cd_documento_recebido) );

		$ret = $db->execute();

		return $ret;
	}
	
	static function receber_protocolo($cd_documento_recebido, $cd_usuario_ok, $observacao_ok)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			UPDATE

				projetos.documento_recebido

			SET

				dt_ok = CURRENT_TIMESTAMP
				, cd_usuario_ok = {cd_usuario_ok}
				, observacao_ok = '{observacao_ok}'

			WHERE

				cd_documento_recebido = {cd_documento_recebido}

		" );

		$db->setParameter( "{cd_documento_recebido}", intval($cd_documento_recebido) );
		$db->setParameter( "{cd_usuario_ok}", intval($cd_usuario_ok) );
		$db->setParameter( "{observacao_ok}", $observacao_ok );

		$ret = $db->execute();
		//  --

		$db->setSQL( "

			INSERT INTO 

				projetos.documento_recebido_historico

				(
	            cd_documento_recebido, cd_documento_recebido_tipo, 
	            cd_usuario_cadastro, dt_envio, dt_cadastro, nr_ano, nr_contador, 
	            dt_ok, cd_usuario_envio, cd_usuario_ok, dt_alteracao, cd_usuario_destino, dt_redirecionamento
	        	)

		    SELECT 

		    	cd_documento_recebido, cd_documento_recebido_tipo, 
	            cd_usuario_cadastro, dt_envio, dt_cadastro, nr_ano, nr_contador, 
	            dt_ok, cd_usuario_envio, cd_usuario_ok, CURRENT_TIMESTAMP AS dt_alteracao, cd_usuario_destino, dt_redirecionamento 

			FROM 

				projetos.documento_recebido

			WHERE

				cd_documento_recebido = {cd_documento_recebido};

		" );

		$db->setParameter( "{cd_documento_recebido}", intval($cd_documento_recebido) );

		$ret = $db->execute();

		return $ret;
	}
	
	static function redirecionar_protocolo($cd_documento_recebido, $cd_usuario_redirecionamento)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			UPDATE

				projetos.documento_recebido

			SET

				dt_redirecionamento = CURRENT_TIMESTAMP
				, cd_usuario_destino = {cd_usuario_destino}

			WHERE

				cd_documento_recebido = {cd_documento_recebido}

		" );

		$db->setParameter( "{cd_documento_recebido}", intval($cd_documento_recebido) );
		$db->setParameter( "{cd_usuario_destino}", intval($cd_usuario_redirecionamento) );

		$ret = $db->execute();
		//  --

		$db->setSQL( "

			INSERT INTO 

				projetos.documento_recebido_historico

				(
	            cd_documento_recebido, cd_documento_recebido_tipo, 
	            cd_usuario_cadastro, dt_envio, dt_cadastro, nr_ano, nr_contador, 
	            dt_ok, cd_usuario_envio, cd_usuario_ok, dt_alteracao, cd_usuario_destino, dt_redirecionamento
	        	)

		    SELECT 

		    	cd_documento_recebido, cd_documento_recebido_tipo, 
	            cd_usuario_cadastro, dt_envio, dt_cadastro, nr_ano, nr_contador, 
	            dt_ok, cd_usuario_envio, cd_usuario_ok, CURRENT_TIMESTAMP AS dt_alteracao, cd_usuario_destino, dt_redirecionamento 

			FROM 

				projetos.documento_recebido

			WHERE

				cd_documento_recebido = {cd_documento_recebido};

		" );

		$db->setParameter( "{cd_documento_recebido}", intval($cd_documento_recebido) );

		$ret = $db->execute();

		return $ret;
	}
	
	static function select_2()
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			/* documento_recebido::select_2 */

			SELECT distinct usuario_cadastro.codigo, usuario_cadastro.divisao, usuario_cadastro.guerra
			FROM projetos.documento_recebido a 
			JOIN projetos.usuarios_controledi usuario_cadastro
			ON a.cd_usuario_cadastro=usuario_cadastro.codigo
			
			JOIN projetos.documento_recebido_item b ON b.cd_documento_recebido=a.cd_documento_recebido
			
			ORDER BY usuario_cadastro.guerra

		" );
		
		$ret = $db->get(true);

		return $ret;
	}

	static function select_3()
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			/* documento_recebido::select_3 */

			SELECT DISTINCT usuario_ok.codigo, usuario_ok.divisao, usuario_ok.guerra 
			FROM projetos.documento_recebido a 
			JOIN projetos.usuarios_controledi usuario_ok
			ON a.cd_usuario_ok=usuario_ok.codigo

			JOIN projetos.documento_recebido_item b ON b.cd_documento_recebido=a.cd_documento_recebido

			ORDER BY usuario_ok.guerra

		" );

		$ret = $db->get(true);

		return $ret;
	}

	static function select_1($cd_usuario=0)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			SELECT DISTINCT 

				dr.cd_documento_recebido
				, dr.nr_ano
				, dr.nr_contador
				, dr.cd_usuario_destino
				, to_char(dr.dt_redirecionamento, 'DD/MM/YYYY') AS dt_redirecionamento

				, to_char( dr.dt_cadastro, 'DD/MM/YYYY' ) as dt_cadastro
				, usuario_cadastro.divisao as divisao_usuario_cadastro
				, usuario_cadastro.guerra as guerra_usuario_cadastro

				, to_char( dr.dt_envio, 'DD/MM/YYYY' ) as dt_envio
				, usuario_envio.divisao as divisao_usuario_envio
				, usuario_envio.guerra as guerra_usuario_envio

				, to_char( dr.dt_ok, 'DD/MM/YYYY' ) as dt_ok
				, usuario_ok.divisao as divisao_usuario_ok
				, usuario_ok.guerra as guerra_usuario_ok

				, usuario_destino.divisao as divisao_usuario_destino
				, usuario_destino.guerra as guerra_usuario_destino

			FROM

				projetos.documento_recebido dr

				LEFT JOIN projetos.documento_recebido_item dri
				ON dr.cd_documento_recebido=dri.cd_documento_recebido

				LEFT JOIN projetos.usuarios_controledi usuario_cadastro
				ON usuario_cadastro.codigo = dr.cd_usuario_cadastro

				LEFT JOIN projetos.usuarios_controledi usuario_envio
				ON usuario_envio.codigo = dr.cd_usuario_envio

				LEFT JOIN projetos.usuarios_controledi usuario_ok
				ON usuario_ok.codigo = dr.cd_usuario_ok

				LEFT JOIN projetos.usuarios_controledi usuario_destino
				ON usuario_destino.codigo = dr.cd_usuario_destino

			WHERE

				(dr.dt_envio IS NULL OR dr.dt_ok IS NULL)
				AND 
				(
					usuario_cadastro.codigo={cd_usuario} 
					OR usuario_ok.codigo={cd_usuario} 
					OR usuario_destino.codigo={cd_usuario} 
					OR 0={cd_usuario}
				)
				;

		" );
		
		$db->setParameter("{cd_usuario}", (int)$cd_usuario);

		$ret = $db->get(true);

		return $ret;
	}

	/**
	 * documento_protocolo::protocolo_ja_confirmado()
	 * 
	 * Retorna true ou false se existir ou nуo existir confirmaчуo (recebimento e indexaчуo) 
	 * 
	 * @return bool
	 */
	public static function protocolo_ja_confirmado( $cd_documento_protocolo )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			SELECT COUNT(*) as quantos
			  FROM projetos.documento_protocolo 
			 WHERE dt_exclusao IS NULL 
			   AND dt_indexacao IS NOT NULL 
			   AND cd_documento_protocolo = {cd_documento_protocolo}

		" );

		$db->setParameter( "{cd_documento_protocolo}", intval($cd_documento_protocolo) );

		$ret = $db->get();

		return ( $ret[0]['quantos']>0 );
	}

	public static function confirmar_indexacao( $cd_documento_protocolo )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			UPDATE projetos.documento_protocolo
			   SET dt_indexacao = CURRENT_TIMESTAMP
				   , cd_usuario_indexacao = {cd_usuario_indexacao}
			 WHERE cd_documento_protocolo = {cd_documento_protocolo}

		" );

		$db->setParameter( "{cd_documento_protocolo}", intval($cd_documento_protocolo) );
		$db->setParameter( "{cd_usuario_indexacao}", intval($_SESSION['Z']) );

		$ret = $db->execute();

		return $ret;
	}

	public static function carregar( $cd_documento_recebido )
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			SELECT 
				a.cd_documento_recebido
				,a.nr_ano
				,a.nr_contador
				,a.cd_documento_recebido_tipo
				,a.cd_usuario_destino
				,e.ds_tipo
				,to_char(a.dt_ok,'DD/MM/YYYY') as dt_ok
				,to_char(a.dt_envio,'DD/MM/YYYY') as dt_envio
				,to_char(a.dt_cadastro,'DD/MM/YYYY') as dt_cadastro
				,b.guerra as guerra_usuario_cadastro
				,c.guerra as guerra_usuario_envio
				,d.guerra as guerra_usuario_ok
				,f.guerra as guerra_usuario_destino
			FROM 
				projetos.documento_recebido a
				
				JOIN projetos.documento_recebido_tipo e
				ON e.cd_documento_recebido_tipo=a.cd_documento_recebido_tipo

				LEFT JOIN projetos.usuarios_controledi b
				ON a.cd_usuario_cadastro=b.codigo

				LEFT JOIN projetos.usuarios_controledi c
				ON c.codigo=a.cd_usuario_envio

				LEFT JOIN projetos.usuarios_controledi d
				ON d.codigo=a.cd_usuario_ok
				
				LEFT JOIN projetos.usuarios_controledi f
				ON f.codigo=a.cd_usuario_destino
			WHERE
				cd_documento_recebido = {cd_documento_recebido}
				;

		" );

		$db->setParameter( "{cd_documento_recebido}", intval($cd_documento_recebido) );

		$ret = $db->get(true);

		return $ret[0];
	}

	public static function inserir( $cd_documento_recebido_tipo, & $new_row )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL( "

			INSERT INTO projetos.documento_recebido
        	(
	            cd_documento_recebido
	            , cd_documento_recebido_tipo
	            , cd_usuario_cadastro
	            , dt_envio
	            , dt_cadastro
	            , nr_ano
	            , nr_contador
	            , dt_ok
	            , cd_usuario_envio
	            , cd_usuario_ok
			)
		    VALUES 
		    (
		    	{cd_documento_recebido}
	            , {cd_documento_recebido_tipo}
	            , {cd_usuario_cadastro}
	            , NULL
	            , CURRENT_TIMESTAMP
	            , EXTRACT( 'year' from CURRENT_TIMESTAMP )
	            , (SELECT CASE WHEN max(nr_contador) is null THEN 1 ELSE MAX(nr_contador)+1 END FROM projetos.documento_recebido WHERE nr_ano = EXTRACT( 'year' from CURRENT_TIMESTAMP ) )
	            , NULL
	            , NULL
	            , NULL
			);

		");

		$cd_documento_recebido = $db->newId('projetos.documento_recebido.cd_documento_recebido');

        $db->setParameter( "{cd_documento_recebido}", intval($cd_documento_recebido) );
        $db->setParameter( "{cd_documento_recebido_tipo}", intval($cd_documento_recebido_tipo) );
        $db->setParameter( "{cd_usuario_cadastro}", intval($_SESSION['Z']) );

        $db->execute();
		if( $db->haveError() )
        {
        	// echo 'Problemas';
	        return false;
        }
        else
        {
			$new_row = documento_recebido::carregar($cd_documento_recebido);
        	return true;
        }
	}
}
?>