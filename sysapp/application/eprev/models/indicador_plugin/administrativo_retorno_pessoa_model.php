<?php
class Administrativo_retorno_pessoa_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function get_valores($args=array())
	{
		$qr_sql = "
			SELECT nr_valor_1 AS nr_despesa,
				   nr_valor_2 AS nr_receita
			  FROM indicador_pga.despesa_sobre_receita
		     WHERE TO_CHAR(dt_referencia,'YYYY') = ".str_escape($args['nr_ano'])."
		       AND TO_CHAR(dt_referencia,'MM') = ".str_escape($args['nr_mes']).";";
			
		return $this->db->query($qr_sql)->row_array();	
	}	

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
		   SELECT cd_administrativo_retorno_pessoa,
				  cd_indicador_tabela,
				  TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
				  TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
				  TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				  dt_referencia,
				  nr_pessoa,
				  nr_receita,
				  nr_despesa,
				  nr_diferenca,
				  nr_resultado,
				  nr_resultado_percentual,
				  nr_meta,
				  ds_observacao,
				  fl_media
			 FROM indicador_plugin.administrativo_retorno_pessoa 
		    WHERE dt_exclusao IS NULL
		      AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		    ORDER BY dt_referencia ASC";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   nr_pessoa,
				   cd_indicador_tabela 
			  FROM indicador_plugin.administrativo_retorno_pessoa 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_administrativo_retorno_pessoa)
	{
		$qr_sql = " 
			SELECT cd_administrativo_retorno_pessoa,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   cd_indicador_tabela,
				   nr_pessoa,
				   nr_receita,
				   nr_despesa,
				   nr_meta,
				   ds_observacao
			  FROM indicador_plugin.administrativo_retorno_pessoa 
			 WHERE cd_administrativo_retorno_pessoa = ".intval($cd_administrativo_retorno_pessoa).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		if(intval($args['cd_administrativo_retorno_pessoa']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.administrativo_retorno_pessoa
				     (
				       cd_indicador_tabela, 
                       dt_referencia, 
					   nr_pessoa, 
                       nr_receita, 
					   nr_despesa, 
					   nr_diferenca,
					   nr_resultado,
					   nr_resultado_percentual,
					   nr_meta,
                       ds_observacao,
                       fl_media, 
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
                VALUES 
				     (
				       ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ".(trim($args['nr_pessoa']) != '' ? floatval($args['nr_pessoa']) : "DEFAULT").",
					   ".(trim($args['nr_receita']) != '' ? floatval($args['nr_receita']) : "DEFAULT").",
					   ".(trim($args['nr_despesa']) != '' ? floatval($args['nr_despesa']) : "DEFAULT").",
					   ".(trim($args['nr_diferenca']) != '' ? floatval($args['nr_diferenca']) : "DEFAULT").",
					   ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",
					   ".(trim($args['nr_resultado_percentual']) != '' ? floatval($args['nr_resultado_percentual']) : "DEFAULT").",
					   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					   ".(trim($args['ds_observacao']) != '' ?  str_escape($args['ds_observacao']) : "DEFAULT").",
					   ".(trim($args['fl_media']) != '' ?  str_escape($args['fl_media']) : "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.administrativo_retorno_pessoa
				   SET dt_referencia           = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       nr_pessoa               = ".(trim($args['nr_pessoa']) != '' ? floatval($args['nr_pessoa']) : "DEFAULT").",
                       nr_receita              = ".(trim($args['nr_receita']) != '' ? floatval($args['nr_receita']) : "DEFAULT").",
					   nr_despesa              = ".(trim($args['nr_despesa']) != '' ? floatval($args['nr_despesa']) : "DEFAULT").",
					   nr_diferenca            = ".(trim($args['nr_diferenca']) != '' ? floatval($args['nr_diferenca']) : "DEFAULT").",
					   nr_resultado            = ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",
					   nr_resultado_percentual = ".(trim($args['nr_resultado_percentual']) != '' ? floatval($args['nr_resultado_percentual']) : "DEFAULT").",
					   nr_meta                 = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
                       ds_observacao           = ".(trim($args['ds_observacao']) != '' ?  str_escape($args['ds_observacao']) : "DEFAULT").",
					   cd_usuario_alteracao    = ".intval($args['cd_usuario']).",
					   dt_alteracao            = CURRENT_TIMESTAMP
				 WHERE cd_administrativo_retorno_pessoa = ".intval($args['cd_administrativo_retorno_pessoa']).";";
		}
	
		$this->db->query($qr_sql);		
	}

	public function excluir($cd_administrativo_retorno_pessoa, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.administrativo_retorno_pessoa 
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			   	   cd_usuario_exclusao = ".intval($cd_usuario)." 
			 WHERE cd_administrativo_retorno_pessoa = ".intval($cd_administrativo_retorno_pessoa).";"; 
		
		$this->db->query($qr_sql);
	}

	public function fechar_periodo($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($cd_usuario)." 
		     WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";

		$this->db->query($qr_sql);
	}

}