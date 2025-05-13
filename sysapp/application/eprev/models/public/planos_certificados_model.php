<?php
class planos_certificados_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_plano, 
				   nome_certificado, 
				   TO_CHAR(dt_inicio,'DD/MM/YYYY') AS dt_inicio, 
				   TO_CHAR(dt_final,'DD/MM/YYYY') AS dt_final, 
				   versao_certificado
			  FROM public.planos_certificados 
			 WHERE cd_plano = ".intval($args['cd_plano'])." 
			 ORDER BY cd_plano, versao_certificado DESC";
			
		$result = $this->db->query($qr_sql);
	}
	
	function planos(&$result, $args=array())
	{
		$qr_sql = "
			SELECT descricao AS text,
			       cd_plano AS value
			  FROM public.planos
			  ORDER BY descricao";
			  
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT p.cd_plano, 
			       p.descricao, 
				   pc.nome_certificado, 
				   pc.cd_spc, 
				   pos_imagem, 
				   pc.largura_imagem, 
				   pc.coluna_1, 
				   pc.coluna_2, 
				   TO_CHAR(pc.dt_aprovacao_spc, 'DD/MM/YYYY') AS dt_aprovacao_spc,
				   pc.versao_certificado, 
				   TO_CHAR(pc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio, 
				   TO_CHAR(pc.dt_final, 'DD/MM/YYYY') AS dt_final,
				   pc.nr_largura_logo,
				   pc.nr_altura_logo,
				   pc.nr_x_logo,
				   pc.nr_fonte_verso,
				   pc.nr_altura_linha_verso,
				   pc.presidente_nome,
				   pc.presidente_assinatura
		      FROM planos_certificados pc
			  JOIN planos p
			    ON p.cd_plano = pc.cd_plano
		     WHERE p.cd_plano            = ".intval($args['cd_plano'])."
			   AND pc.versao_certificado = ".intval($args['versao_certificado']).";";
		
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE planos_certificados
			   SET nome_certificado      = ".(trim($args['nome_certificado']) != '' ? "'".trim($args['nome_certificado'])."'" : "DEFAULT").",
			       cd_spc                = ".(trim($args['cd_spc']) != '' ? "'".trim($args['cd_spc'])."'" : "DEFAULT").",
				   pos_imagem            = ".(trim($args['pos_imagem']) != '' ? intval($args['pos_imagem']) : "DEFAULT").",
				   largura_imagem        = ".(trim($args['largura_imagem']) != '' ? intval($args['largura_imagem']) : "DEFAULT").",
				   coluna_1              = ".(trim($args['coluna_1']) != '' ? "'".trim($args['coluna_1'])."'" : "DEFAULT").",
				   coluna_2              = ".(trim($args['coluna_2']) != '' ? "'".trim($args['coluna_2'])."'" : "DEFAULT").",
				   dt_inicio             = ".(trim($args['dt_inicio']) != '' ? "TO_DATE('".$args['dt_inicio']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   dt_final              = ".(trim($args['dt_final']) != '' ? "TO_DATE('".$args['dt_final']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   dt_aprovacao_spc      = ".(trim($args['dt_aprovacao_spc']) != '' ? "TO_DATE('".$args['dt_aprovacao_spc']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   nr_largura_logo       = ".(trim($args['nr_largura_logo']) != '' ? intval($args['nr_largura_logo']) : "DEFAULT").",
				   nr_altura_logo        = ".(trim($args['nr_altura_logo']) != '' ? intval($args['nr_altura_logo']) : "DEFAULT").",
				   nr_x_logo             = ".(trim($args['nr_x_logo']) != '' ? intval($args['nr_x_logo']) : "DEFAULT").",
				   nr_fonte_verso        = ".(trim($args['nr_fonte_verso']) != '' ? app_decimal_para_db($args['nr_fonte_verso']) : "DEFAULT").",
				   nr_altura_linha_verso = ".(trim($args['nr_altura_linha_verso']) != '' ? app_decimal_para_db($args['nr_altura_linha_verso']) : "DEFAULT").",
				   presidente_nome       = ".(trim($args['presidente_nome']) != '' ? "'".trim($args['presidente_nome'])."'" : "DEFAULT").",
				   presidente_assinatura = ".(trim($args['presidente_assinatura']) != '' ? "'".trim($args['presidente_assinatura'])."'" : "DEFAULT")."
			 WHERE cd_plano           = ".intval($args['cd_plano'])."
			   AND versao_certificado = ".intval($args['versao_certificado']).";";
			   
			   
		
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_certificado(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(pc.dt_aprovacao_spc,'DD/MM/YYYY') AS dt_aprovacao_spc, 
				   pc.cd_spc AS cd_plano_spc, 
				   pc.nome_certificado AS nome_plano_certificado, 
				   pc.pos_imagem, 
				   pc.largura_imagem, 
				   pc.coluna_1, 
				   pc.coluna_2,
				   pc.cd_plano,
			       pc.nr_fonte_verso,			
                   pc.nr_altura_linha_verso,
				   pc.nr_altura_logo,
				   pc.nr_largura_logo
			  FROM public.planos_certificados pc 
			 WHERE pc.dt_final IS NULL
			   AND pc.cd_plano = CASE WHEN ".intval($args['cd_plano'])." = 1 AND ".intval($args['versao_certificado'])." = 3 THEN 3 ELSE ".intval($args['cd_plano'])." END";
			   
		$result = $this->db->query($qr_sql);
	}
	
}
?>