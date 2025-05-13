<?php
class Contrato_resultado_resposta_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT ds_contrato_formulario_grupo, ds_contrato_formulario_pergunta, cd_divisao, ds_resposta, count(*) as quantos
		FROM consultas.contrato_resultado_resposta WHERE cd_contrato_avaliacao={cd_contrato_avaliacao}
		GROUP BY ds_contrato_formulario_grupo, ds_contrato_formulario_pergunta, cd_divisao, ds_resposta, grupo_ordem, pergunta_ordem, resposta_ordem
		ORDER BY grupo_ordem, pergunta_ordem, cd_divisao, resposta_ordem
		;
		";

		// parse query ...
		esc( "{cd_contrato_avaliacao}", $args["avaliacao"], $sql, "int" );


		// echo "<pre>$sql</pre>";

		// return result ...
		$result = $this->db->query($sql);
	}

	function listar_test( &$result, $args=array() )
	{
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 1', 'quantos'=>'15' );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 2', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 3', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 4', 'quantos'=>'15'  );

		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 1', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 2', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 3', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 4', 'quantos'=>'15'  );

		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 1', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 2', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 3', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 4', 'quantos'=>'15'  );

		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 1', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 2', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 3', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 1grupo 1grupo 1grupo 1grupo 1grupo 1', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 4', 'quantos'=>'15'  );

		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 1', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 2', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 3', 'quantos'=>'15'  );

		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 1', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 2', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 1pergunta 1pergunta 1pergunta 1pergunta 1', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 3', 'quantos'=>'15'  );

		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 1', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 2', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GI', 'ds_resposta'=>'resposta 3', 'quantos'=>'15'  );

		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 1', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 2', 'quantos'=>'15'  );
		$collection_final[] = array( 'ds_contrato_formulario_grupo'=>'grupo 2', 'ds_contrato_formulario_pergunta'=>'pergunta 2', 'cd_divisao'=>'GA', 'ds_resposta'=>'resposta 3', 'quantos'=>'15'  );
		$result = $collection_final;
	}
}
