<?php
class aniversario_cartao_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function usuario(&$result, $args=array())
    {
		$qr_sql = "
					SELECT TO_CHAR(dt_nascimento,'DD/MM/') || TO_CHAR(CURRENT_DATE,'YYYY') AS dt_nascimento,
					       uc.nome,
						   uc.divisao
					  FROM projetos.usuarios_controledi uc
					 WHERE codigo = ".intval($args['cd_usuario'])."
			      ";
			
		$result = $this->db->query($qr_sql);
	}
}
?>