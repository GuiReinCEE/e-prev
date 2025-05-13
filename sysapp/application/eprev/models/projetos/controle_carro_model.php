<?php
class Controle_carro_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_destino()
	{		
		$qr_sql = "
			SELECT cd_controle_carro_destino AS value,
			       ds_controle_carro_destino AS text
			  FROM projetos.controle_carro_destino
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_controle_carro_destino;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_motivo()
	{		
		$qr_sql = "
			SELECT cd_controle_carro_motivo AS value,
			       ds_controle_carro_motivo AS text
			  FROM projetos.controle_carro_motivo
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_controle_carro_motivo;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_motorista()
	{		
		$qr_sql = "
			SELECT cd_controle_carro_motorista AS value,
			       UPPER(ds_controle_carro_motorista) AS text
			  FROM projetos.controle_carro_motorista
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_controle_carro_motorista;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_veiculo()
	{		
		$qr_sql = "
			SELECT cd_controle_carro_veiculo AS value,
			       ds_controle_carro_veiculo AS text
			  FROM projetos.controle_carro_veiculo
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_controle_carro_veiculo;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar($cd_controle_carro_veiculo, $args = array())
	{
		$qr_sql = "
			SELECT c.cd_controle_carro,
			       UPPER(ma.ds_controle_carro_motorista) AS ds_controle_carro_motorista,
			       d.ds_controle_carro_destino,
			       m.ds_controle_carro_motivo,
			       v.ds_controle_carro_veiculo,
			       TO_CHAR(c.dt_saida, 'DD/MM/YYYY HH24:MI') AS dt_saida,
			       TO_CHAR(c.dt_retorno, 'DD/MM/YYYY HH24:MI') AS dt_retorno,
			       c.nr_km_saida,
			       c.nr_km_retorno,
			       (c.nr_km_retorno - c.nr_km_saida) AS nr_km_rodado
			  FROM projetos.controle_carro c
			  JOIN projetos.controle_carro_destino d
			    ON d.cd_controle_carro_destino = c.cd_controle_carro_destino
			  JOIN projetos.controle_carro_motivo m
			    ON m.cd_controle_carro_motivo = c.cd_controle_carro_motivo
			  JOIN projetos.controle_carro_motorista ma
			    ON ma.cd_controle_carro_motorista = c.cd_controle_carro_motorista
			  JOIN projetos.controle_carro_veiculo v
			    ON v.cd_controle_carro_veiculo = c.cd_controle_carro_veiculo
			 WHERE c.dt_exclusao IS NULL
			   AND c.cd_controle_carro_veiculo = ".intval($cd_controle_carro_veiculo)."
			   ".(((trim($args['dt_saida_ini']) != '') AND (trim($args['dt_saida_fim']) != '')) ? "AND DATE_TRUNC('day', c.dt_saida) BETWEEN TO_DATE('".$args['dt_saida_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_saida_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_retorno_ini']) != '') AND (trim($args['dt_retorno_fim']) != '')) ? "AND DATE_TRUNC('day', c.dt_retorno) BETWEEN TO_DATE('".$args['dt_retorno_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_retorno_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(intval($args['cd_controle_carro_destino']) > 0 ? "AND c.cd_controle_carro_destino = ".intval($args['cd_controle_carro_destino']) : "")."
			   ".(intval($args['cd_controle_carro_motivo']) > 0 ? "AND c.cd_controle_carro_motivo = ".intval($args['cd_controle_carro_motivo']) : "")."
			   ".(intval($args['cd_controle_carro_motorista']) > 0 ? "AND c.cd_controle_carro_motorista = ".intval($args['cd_controle_carro_motorista']) : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_controle_carro)
	{		
		$qr_sql = "
			SELECT c.cd_controle_carro,
			       c.nr_km_saida,
			       TO_CHAR(c.dt_saida, 'DD/MM/YYYY') AS dt_saida,
			       TO_CHAR(c.dt_saida, 'HH24:MI') AS hr_saida,
			       c.cd_controle_carro_destino,
			       c.cd_controle_carro_motivo,
			       c.cd_controle_carro_veiculo,
			       c.nr_km_retorno,
			       TO_CHAR(c.dt_retorno, 'DD/MM/YYYY') AS dt_retorno,
			       TO_CHAR(c.dt_retorno, 'HH24:MI') AS hr_retorno,
			       c.cd_controle_carro_motorista,
			       c.ds_observacao,
			       UPPER(ma.ds_controle_carro_motorista) AS ds_controle_carro_motorista,
			       d.ds_controle_carro_destino,
			       m.ds_controle_carro_motivo,
			       v.ds_controle_carro_veiculo
			  FROM projetos.controle_carro c
			  JOIN projetos.controle_carro_destino d
			    ON d.cd_controle_carro_destino = c.cd_controle_carro_destino
			  JOIN projetos.controle_carro_motivo m
			    ON m.cd_controle_carro_motivo = c.cd_controle_carro_motivo
			  JOIN projetos.controle_carro_motorista ma
			    ON ma.cd_controle_carro_motorista = c.cd_controle_carro_motorista
			  JOIN projetos.controle_carro_veiculo v
			    ON v.cd_controle_carro_veiculo = c.cd_controle_carro_veiculo
			 WHERE cd_controle_carro = ".intval($cd_controle_carro).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_controle_carro = intval($this->db->get_new_id('projetos.controle_carro', 'cd_controle_carro'));
		
		$qr_sql = "
			INSERT INTO projetos.controle_carro
			     (
                   cd_controle_carro, 
                   nr_km_saida, 
                   dt_saida, 
                   cd_controle_carro_destino, 
                   cd_controle_carro_motivo, 
                   nr_km_retorno, 
                   dt_retorno, 
                   cd_controle_carro_motorista, 
                   cd_controle_carro_veiculo,
                   ds_observacao,
                   cd_gerencia,
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
			VALUES
			     (
			     	".intval($cd_controle_carro).",
			     	".(trim($args['nr_km_saida']) != '' ? intval($args['nr_km_saida']) : "DEFAULT").",
			     	".(trim($args['dt_saida']) != '' ? "TO_TIMESTAMP('".trim($args['dt_saida'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
			     	".(trim($args['cd_controle_carro_destino']) != '' ? intval($args['cd_controle_carro_destino']) : "DEFAULT").",
			     	".(trim($args['cd_controle_carro_motivo']) != '' ? intval($args['cd_controle_carro_motivo']) : "DEFAULT").",
			     	".(trim($args['nr_km_retorno']) != '' ? intval($args['nr_km_retorno']) : "DEFAULT").",
			     	".(trim($args['dt_retorno']) != '' ? "TO_TIMESTAMP('".trim($args['dt_retorno'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
			     	".(trim($args['cd_controle_carro_motorista']) != '' ? intval($args['cd_controle_carro_motorista']) : "DEFAULT").",
			     	".(trim($args['cd_controle_carro_veiculo']) != '' ? intval($args['cd_controle_carro_veiculo']) : "DEFAULT").",
			     	".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
			     	'".trim($args['cd_gerencia'])."',
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 );";
		
		$this->db->query($qr_sql);

		return $cd_controle_carro;
	}

	public function atualizar($cd_controle_carro, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.controle_carro
			   SET nr_km_saida                 = ".(trim($args['nr_km_saida']) != '' ? intval($args['nr_km_saida']) : "DEFAULT").",
                   dt_saida                    = ".(trim($args['dt_saida']) != '' ? "TO_TIMESTAMP('".trim($args['dt_saida'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   cd_controle_carro_destino   = ".(trim($args['cd_controle_carro_destino']) != '' ? intval($args['cd_controle_carro_destino']) : "DEFAULT").",
                   cd_controle_carro_motivo    = ".(trim($args['cd_controle_carro_motivo']) != '' ? intval($args['cd_controle_carro_motivo']) : "DEFAULT").",
                   nr_km_retorno               = ".(trim($args['nr_km_retorno']) != '' ? intval($args['nr_km_retorno']) : "DEFAULT").",
                   dt_retorno                  = ".(trim($args['dt_retorno']) != '' ? "TO_TIMESTAMP('".trim($args['dt_retorno'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                   cd_controle_carro_motorista = ".(trim($args['cd_controle_carro_motorista']) != '' ? intval($args['cd_controle_carro_motorista']) : "DEFAULT").",
                   cd_controle_carro_veiculo   = ".(trim($args['cd_controle_carro_veiculo']) != '' ? intval($args['cd_controle_carro_veiculo']) : "DEFAULT").",
                   ds_observacao               = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
				   cd_usuario_alteracao        = ".intval($args['cd_usuario']).",
				   dt_alteracao                = CURRENT_TIMESTAMP
			 WHERE cd_controle_carro = ".intval($cd_controle_carro).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_controle_carro, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.controle_carro
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_controle_carro = ".intval($cd_controle_carro).";";
			 
		$this->db->query($qr_sql);
	}

	public function abastecimento($cd_controle_carro_abastecimento)
	{
		$qr_sql = "
			SELECT cd_controle_carro_abastecimento,
			       cd_controle_carro,
			       nr_km,
			       TO_CHAR(dt_abastecimento, 'DD/MM/YYYY') AS dt_abastecimento,
			       TO_CHAR(dt_abastecimento, 'HH24:MI') AS hr_abastecimento,
			       nr_valor,
			       nr_litro
			  FROM projetos.controle_carro_abastecimento
			 WHERE cd_controle_carro_abastecimento = ".intval($cd_controle_carro_abastecimento).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function abastecimento_listar($cd_controle_carro)
	{
		$qr_sql = "
			SELECT cd_controle_carro_abastecimento,
			       cd_controle_carro,
			       nr_km,
			       TO_CHAR(dt_abastecimento, 'DD/MM/YYYY HH24:MI') AS dt_abastecimento,
			       nr_valor,
			       nr_litro
			  FROM projetos.controle_carro_abastecimento
			 WHERE cd_controle_carro = ".intval($cd_controle_carro)."
			   AND dt_exclusao       IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function abastecimento_salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.controle_carro_abastecimento
			     (
                   cd_controle_carro, 
                   nr_km, 
                   nr_valor, 
        		   nr_litro, 
        		   dt_abastecimento,
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
			VALUES
			     (
			     	".intval($args['cd_controle_carro']).",
			     	".(trim($args['nr_km']) != '' ? intval($args['nr_km']) : "DEFAULT").",
			     	".(trim($args['nr_valor']) != '' ? app_decimal_para_db($args['nr_valor']) : "DEFAULT").",
			     	".(trim($args['nr_litro']) != '' ? app_decimal_para_db($args['nr_litro']) : "DEFAULT").",
			     	".(trim($args['dt_abastecimento']) != '' ? "TO_TIMESTAMP('".trim($args['dt_abastecimento'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);
	}

	public function abastecimento_atualizar($cd_controle_carro_abastecimento, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.controle_carro_abastecimento
			   SET nr_km                = ".(trim($args['nr_km']) != '' ? intval($args['nr_km']) : "DEFAULT").",
                   nr_valor             = ".(trim($args['nr_valor']) != '' ? app_decimal_para_db($args['nr_valor']) : "DEFAULT").",
        		   nr_litro             = ".(trim($args['nr_litro']) != '' ? app_decimal_para_db($args['nr_litro']) : "DEFAULT").",
        		   dt_abastecimento     = ".(trim($args['dt_abastecimento']) != '' ? "TO_TIMESTAMP('".trim($args['dt_abastecimento'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_controle_carro_abastecimento = ".intval($cd_controle_carro_abastecimento).";";

		$this->db->query($qr_sql);
	}

	public function abastecimento_excluir($cd_controle_carro_abastecimento, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.controle_carro_abastecimento
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_controle_carro_abastecimento = ".intval($cd_controle_carro_abastecimento).";";
			 
		$this->db->query($qr_sql);
	}
}