<?php
class Pos_Venda_Participante_Acompanhamento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT pvpa.cd_pos_venda_participante_acompanhamento, 
					       pvpa.cd_pos_venda_participante, 
                           pvpa.acompanhamento, 
						   TO_CHAR(pvpa.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   pvpa.cd_usuario_inclusao,
						   uc.nome
                      FROM projetos.pos_venda_participante_acompanhamento pvpa
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = pvpa.cd_usuario_inclusao
					 WHERE pvpa.dt_exclusao IS NULL
					   AND pvpa.cd_pos_venda_participante = ".intval($args['cd_pos_venda_participante'])."
					 ORDER BY pvpa.dt_inclusao DESC
		          ";
		$result = $this->db->query($qr_sql);
	}
	
	function acompanhamentoSalvar(&$result, $args=array())
	{
		if(intval($args['cd_pos_venda_participante_acompanhamento']) > 0)
		{
			##UPDATE
			$retorno = intval($args['cd_pos_venda_participante_acompanhamento']);
		}
		else
		{
			###INSERT
			$new_id = intval($this->db->get_new_id("projetos.pos_venda_participante_acompanhamento", "cd_pos_venda_participante_acompanhamento"));
			$qr_sql = " 
						INSERT INTO projetos.pos_venda_participante_acompanhamento
						     (
                               cd_pos_venda_participante_acompanhamento, 
							   cd_pos_venda_participante, 
                               acompanhamento, 
							   cd_usuario_inclusao
							 )
                        VALUES 
						     (
							   ".$new_id.",
							   ".$args['cd_pos_venda_participante'].",
							   '".$args['acompanhamento']."',
							   ".$args['cd_usuario']."
							 );		
					  ";
			$this->db->query($qr_sql);	
			$retorno = intval($new_id);
		}
		
		#echo "<pre>$qr_sql</pre>";exit;
		
		return $retorno;
	}	
	
}
?>