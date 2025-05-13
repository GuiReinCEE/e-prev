<?php
class Cadastro_colaborador_model extends Model
{
	function __construct()
  	{
    	parent::Model();
  	}

  	public function get_gerencia()
  	{
  		$qr_sql = "
  			SELECT codigo AS value,
  			       nome AS text
  			  FROM funcoes.get_gerencias_vigente();";

  		return $this->db->query($qr_sql)->result_array();
  	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cc.cd_cadastro_colaborador,
				   cc.ds_nome,
				   TO_CHAR(cc.dt_enviado,'DD/MM/YYYY HH24:MI:SS') AS dt_enviado,
				   TO_CHAR(cc.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
				   cc.fl_tipo,
				   CASE WHEN cc.fl_tipo = 'C' THEN 'Colaborador'
				        WHEN cc.fl_tipo = 'D' THEN 'Diretoria Executiva'
						WHEN cc.fl_tipo = 'P' THEN 'Prestador de Serviço'
						WHEN cc.fl_tipo = 'E' THEN 'Estagiário'
						ElSE ''
				   END AS ds_tipo,
				   CASE WHEN cc.fl_tipo = 'C' THEN 'label label-info'
				   		WHEN cc.fl_tipo = 'D' THEN 'label label-success'
			            WHEN cc.fl_tipo = 'P' THEN 'label label-warning' 
			            WHEN cc.fl_tipo = 'E' THEN 'label label-inverse' 
			            ElSE ''
			       END AS ds_class_tipo,
				   cc.cd_gerencia,
				   TO_CHAR(cc.dt_admissao,'DD/MM/YYYY') AS dt_admissao,
				   cc.cd_cargo,
				   CASE WHEN cc.dt_enviado IS NULL THEN 'Aguardando Solicitação'
						WHEN cc.dt_enviado IS NOT NULL AND cc.dt_liberado_infra IS NULL THEN 'Usuário Solicitado'
						WHEN cc.dt_liberado_infra IS NOT NULL AND cc.dt_liberado_eletro IS NULL THEN 'Liberado pela Infra'
						WHEN cc.dt_liberado_eletro IS NOT NULL AND cc.dt_liberado_eprev IS NULL THEN 'Liberado pelo Eletro'
						WHEN cc.dt_liberado_eprev IS NOT NULL THEN 'Liberado'
						ELSE ''
				   END AS ds_status,
				   CASE WHEN cc.dt_enviado IS NULL THEN 'label label-important'
						WHEN cc.dt_enviado IS NOT NULL AND cc.dt_liberado_infra IS NULL THEN 'label label-warning'
						WHEN cc.dt_liberado_infra IS NOT NULL AND cc.dt_liberado_eletro IS NULL THEN 'label label-info'
						WHEN cc.dt_liberado_eletro IS NOT NULL AND cc.dt_liberado_eprev IS NULL THEN 'label label-inverse'
						WHEN cc.dt_liberado_eprev IS NOT NULL THEN 'label label-success'
						ELSE ''
				   END AS ds_status_label,
				   cc.ds_usuario,
				   c.nome_cargo,
				   cc.nr_ramal,
				   cc.cd_usuario
			  FROM projetos.cadastro_colaborador cc
			  LEFT JOIN projetos.cargos c
			    ON c.cd_cargo = cc.cd_cargo
			 WHERE dt_exclusao IS NULL
			 ".(trim($args['ds_nome']) != '' ? "AND cc.ds_nome = ".str_escape($args['ds_nome']) : "")."	
			 ".(trim($args['cd_gerencia']) != '' ? "AND cc.cd_gerencia = ".str_escape($args['cd_gerencia']) : "")."			   
			 ".(trim($args['fl_tipo']) != '' ? "AND cc.fl_tipo = ".str_escape($args['fl_tipo']) : "")."
	         ".(trim($args['fl_status']) == 'C' ? "AND cc.dt_enviado IS NULL" : "")."
		     ".(trim($args['fl_status']) == 'U' ? "AND cc.dt_enviado IS NOT NULL AND dt_liberado_infra IS NULL" : "")."
		     ".(trim($args['fl_status']) == 'I' ? "AND cc.dt_liberado_infra IS NOT NULL AND dt_liberado_eletro IS NULL" : "")."
		     ".(trim($args['fl_status']) == 'E' ? "AND cc.dt_liberado_eletro IS NOT NULL AND dt_liberado_eprev IS NULL" : "")."
		     ".(trim($args['fl_status']) == 'L' ? "AND cc.dt_liberado_eprev IS NOT NULL" : "").";";
			 
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega($cd_cadastro_colaborador)
	{
		$qr_sql = "
			SELECT cc.cd_cadastro_colaborador, 
			       cc.ds_nome,
				   TO_CHAR(cc.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
				   cc.fl_tipo, 
				   CASE WHEN cc.fl_tipo = 'C' THEN 'Colaborador'
				        WHEN cc.fl_tipo = 'D' THEN 'Diretoria Executiva'
						WHEN cc.fl_tipo = 'P' THEN 'Prestador de Serviço'
						WHEN cc.fl_tipo = 'E' THEN 'Estagiário'
						ElSE ''
				   END AS ds_tipo,
				   cc.cd_gerencia,
				   TO_CHAR(cc.dt_admissao,'DD/MM/YYYY') AS dt_admissao,
				   cc.cd_cargo,
				   cc.ds_observacao,
				   TO_CHAR(cc.dt_enviado,'DD/MM/YYYY HH24:MI:SS') AS dt_enviado,
				   cc.cd_usuario_enviado,
				   cc.ds_usuario,
				   cc.cd_usuario_liberado_infra,
				   TO_CHAR(cc.dt_liberado_infra,'DD/MM/YYYY HH24:MI:SS') AS dt_liberado_infra,
				   cc.cd_usuario_liberado_eletro,
				   TO_CHAR(cc.dt_liberado_eletro,'DD/MM/YYYY HH24:MI:SS') AS dt_liberado_eletro,
				   cc.cd_usuario_liberado_eprev,
				   TO_CHAR(cc.dt_liberado_eprev,'DD/MM/YYYY HH24:MI:SS') AS dt_liberado_eprev,
				   cc.senha_rede,
				   cc.senha_eletro,
				   c.nome_cargo,
				   funcoes.get_usuario_nome(cc.cd_usuario_enviado) AS ds_nome_enviado,
				   funcoes.get_usuario_nome(cc.cd_usuario_liberado_infra) AS ds_nome_infra,
				   funcoes.get_usuario_nome(cc.cd_usuario_liberado_eletro) AS ds_nome_eletro,
				   funcoes.get_usuario_nome(cc.cd_usuario_liberado_eprev) AS ds_nome_eprev,
				   d.nome AS ds_nome_gerencia,
				   cc.cd_usuario AS cd_usuario_colaborador,
				   cc.nr_ramal,
				   cc.fl_usuario_sa,
				   cc.cd_usuario
			  FROM projetos.cadastro_colaborador cc
		      LEFT JOIN projetos.cargos c
			    ON c.cd_cargo = cc.cd_cargo
			  JOIN projetos.divisoes d
			    ON d.codigo = cc.cd_gerencia
			 WHERE cc.cd_cadastro_colaborador = ".intval($cd_cadastro_colaborador).";";
		
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_cadastro_colaborador = intval($this->db->get_new_id('projetos.cadastro_colaborador', 'cd_cadastro_colaborador'));

		$qr_sql = "
			INSERT INTO projetos.cadastro_colaborador
			     (
					cd_cadastro_colaborador, 
					ds_nome,
					dt_nascimento,
					fl_tipo, 
					cd_gerencia,
					dt_admissao,
					cd_cargo,
					ds_observacao,
					cd_usuario_inclusao,
					cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_cadastro_colaborador).",
                    ".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").",
					".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".$args['dt_nascimento']."', 'DD/MM/YYYY')" : "DEAFULT").",
					".(trim($args['fl_tipo']) != '' ? str_escape($args['fl_tipo']) : "DEFAULT").",
					".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
					".(trim($args['dt_admissao']) != '' ? "TO_DATE('".$args['dt_admissao']."', 'DD/MM/YYYY')"  : "DEAFULT").",
					".(trim($args['cd_cargo']) != '' ? intval($args['cd_cargo']) : "DEFAULT").",
					".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql);
		
		return $cd_cadastro_colaborador;
	}

	public function atualizar($cd_cadastro_colaborador, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.cadastro_colaborador
               SET ds_nome		 		= ".(trim($args['ds_nome']) != '' ? str_escape($args['ds_nome']) : "DEFAULT").",
				   dt_nascimento 		= ".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".$args['dt_nascimento']."', 'DD/MM/YYYY')" : "DEAFULT").",
				   fl_tipo		 		= ".(trim($args['fl_tipo']) != '' ? str_escape($args['fl_tipo']) : "DEFAULT").",
				   cd_gerencia	 		= ".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
				   dt_admissao	 		= ".(trim($args['dt_admissao']) != '' ? "TO_DATE('".$args['dt_admissao']."', 'DD/MM/YYYY')" : "DEAFULT").",
				   cd_cargo		 		= ".(trim($args['cd_cargo']) != '' ? intval($args['cd_cargo']) : "DEAFULT").",
				   ds_observacao 		= ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
				   ds_usuario			= ".(trim($args['ds_usuario']) != '' ? str_escape($args['ds_usuario']) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_cadastro_colaborador = ".intval($cd_cadastro_colaborador).";";

        $this->db->query($qr_sql);  
	}
	
	public function atualizar_usuario($args = array())
	{
		$qr_sql = "
			UPDATE projetos.cadastro_colaborador
               SET ds_usuario			= ".(trim($args['ds_usuario']) != '' ? str_escape($args['ds_usuario']) : "DEFAULT").",
				   senha_rede			= ".(trim($args['senha_rede']) != '' ? str_escape($args['senha_rede']) : "DEFAULT").",
				   nr_ramal             = ".(trim($args['nr_ramal']) != '' ? intval($args['nr_ramal']) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   fl_usuario_sa		= ".(trim($args['fl_usuario_sa']) != '' ? str_escape($args['fl_usuario_sa']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_cadastro_colaborador = ".intval($args['cd_cadastro_colaborador']).";";

        $this->db->query($qr_sql);  
	}
	
	public function atualizar_usuario_infra($args = array())
	{
		$qr_sql = "
			UPDATE projetos.cadastro_colaborador
               SET senha_eletro 		= ".(trim($args['senha_eletro']) != '' ? str_escape($args['senha_eletro']) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_cadastro_colaborador = ".intval($args['cd_cadastro_colaborador']).";";

        $this->db->query($qr_sql);  
	}
	
	public function atualizar_usuario_eletro($args = array())
	{
		$qr_sql = "
			UPDATE projetos.cadastro_colaborador
               SET cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_cadastro_colaborador = ".intval($args['cd_cadastro_colaborador']).";";

        $this->db->query($qr_sql);  
	}

	public function solicitar_usuario($cd_cadastro_colaborador, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.cadastro_colaborador
               SET cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP,
				   cd_usuario_enviado	= ".intval($cd_usuario).",
				   dt_enviado 			= CURRENT_TIMESTAMP
             WHERE cd_cadastro_colaborador = ".intval($cd_cadastro_colaborador).";";
			 
        $this->db->query($qr_sql);  
	}
	
	public function liberar_usuario_rede($cd_cadastro_colaborador, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.cadastro_colaborador
               SET cd_usuario_alteracao      = ".intval($cd_usuario).",
                   dt_alteracao         	 = CURRENT_TIMESTAMP,
				   cd_usuario_liberado_infra = ".intval($cd_usuario).",
				   dt_liberado_infra 	     = CURRENT_TIMESTAMP
             WHERE cd_cadastro_colaborador = ".intval($cd_cadastro_colaborador).";";
			 
        $this->db->query($qr_sql);  
	}
	
	public function liberar_usuario_eletro($cd_cadastro_colaborador, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.cadastro_colaborador
               SET cd_usuario_alteracao       = ".intval($cd_usuario).",
                   dt_alteracao         	  = CURRENT_TIMESTAMP,
				   cd_usuario_liberado_eletro = ".intval($cd_usuario).",
				   dt_liberado_eletro 	      = CURRENT_TIMESTAMP
             WHERE cd_cadastro_colaborador = ".intval($cd_cadastro_colaborador).";";
			 
        $this->db->query($qr_sql);  
	}

	public function liberar_usuario_eprev($cd_cadastro_colaborador, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.cadastro_colaborador
               SET cd_usuario_alteracao      = ".intval($cd_usuario).",
                   dt_alteracao              = CURRENT_TIMESTAMP,
				   cd_usuario_liberado_eprev = ".intval($cd_usuario).",
				   dt_liberado_eprev	     = CURRENT_TIMESTAMP
             WHERE cd_cadastro_colaborador = ".intval($cd_cadastro_colaborador).";";
			 
        $this->db->query($qr_sql);  
	}

	public function get_cargo()
    {
        $qr_sql = "
            SELECT cd_cargo AS value, 
                   nome_cargo AS text 
              FROM projetos.cargos 
             WHERE cd_familia IS NOT NULL
             ORDER BY nome_cargo;";

        return $this->db->query($qr_sql)->result_array();
    }
}