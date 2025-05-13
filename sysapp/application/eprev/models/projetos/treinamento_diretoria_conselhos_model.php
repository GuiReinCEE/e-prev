<?php
class Treinamento_diretoria_conselhos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
    
    public function get_uf()
	{
        $qr_sql = "
			SELECT cd_uf AS value,
				   ds_uf AS text
			  FROM geografico.uf
			 ORDER BY text;";
        
        return $this->db->query($qr_sql)->result_array();
    }
	
    function get_gerencias()
	{
        $qr_sql = "
			SELECT codigo AS value,
			       nome AS text
			  FROM projetos.divisoes
			 ORDER BY text;";
			 
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function get_tipo()
	{
        $qr_sql = "
            SELECT cd_treinamento_colaborador_tipo AS value,
                   ds_treinamento_colaborador_tipo AS text
              FROM projetos.treinamento_colaborador_tipo
             WHERE dt_exclusao IS NULL;";
        
        return $this->db->query($qr_sql)->result_array();
    }
	
    public function listar($args = array())
	{
        $qr_sql = "
            SELECT funcoes.nr_treinamento_colaborador(tdc.nr_ano, tdc.nr_numero) AS nr_numero,
				   tdc.cd_treinamento_diretoria_conselhos,
                   tdc.ds_nome,
                   tdc.ds_promotor,
                   tdc.ds_cidade,
                   tdc.ds_uf,
                   TO_CHAR(tdc.dt_inicio,'DD/MM/YYYY') AS dt_inicio,
                   TO_CHAR(tdc.dt_final,'DD/MM/YYYY') AS dt_final,
                   tcp.ds_treinamento_colaborador_tipo,
                   tdc.nr_carga_horaria,
                   (SELECT COUNT(*)
                      FROM projetos.treinamento_diretoria_conselhos_item tcdi
                     WHERE tcdi.cd_treinamento_diretoria_conselhos = tdc.cd_treinamento_diretoria_conselhos
                       AND tcdi.dt_exclusao IS NULL) AS tl_colaborador
              FROM projetos.treinamento_diretoria_conselhos tdc
              LEFT JOIN projetos.treinamento_colaborador_tipo tcp
                ON tcp.cd_treinamento_colaborador_tipo = tdc.cd_treinamento_colaborador_tipo
             WHERE tdc.dt_exclusao IS NULL
               ".((trim($args['cd_empresa']) != '' AND trim($args['cd_registro_empregado']) != '' AND trim($args['seq_dependencia']) != '') ? 
                "AND 0 < (SELECT COUNT(*)
                            FROM projetos.treinamento_diretoria_conselhos_item tcdi
                           WHERE tcdi.cd_treinamento_diretoria_conselhos = tdc.cd_treinamento_diretoria_conselhos
                             AND tcdi.cd_empresa            		     = ".intval($args['cd_empresa'])."
                             AND tcdi.cd_registro_empregado 			 = ".intval($args['cd_registro_empregado'])."
                             AND tcdi.seq_dependencia       			 = ".intval($args['seq_dependencia'])."
                             AND tcdi.dt_exclusao IS NULL)" : '')."
               ".((trim($args['ds_nome_colaborador']) != '') ? 
                "AND 0 < (SELECT COUNT(*)
                            FROM projetos.treinamento_diretoria_conselhos_item tcdi
                           WHERE tcdi.cd_treinamento_diretoria_conselhos = tdc.cd_treinamento_diretoria_conselhos
                             AND UPPER(funcoes.remove_acento(tcdi.ds_nome)) LIKE UPPER(funcoes.remove_acento('%".str_replace(" ","%",trim($args['ds_nome_colaborador']))."%'))
                             AND tcdi.dt_exclusao IS NULL)" : '')."
               ".(((trim($args['dt_inicio_ini']) != "") AND (trim($args['dt_inicio_fim']) != "")) ? " AND DATE_TRUNC('day', tdc.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_final_ini']) != "") AND (trim($args['dt_final_fim']) != "")) ? " AND DATE_TRUNC('day', tdc.dt_final) BETWEEN TO_DATE('".$args['dt_final_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_final_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['nr_numero']) != '' ? "AND tdc.nr_numero = ".intval($args['nr_numero']) : '' )."
               ".(trim($args['nr_ano']) != '' ? "AND tdc.nr_ano = ".intval($args['nr_ano']) : '' )."
               ".(trim($args['cd_treinamento_colaborador_tipo']) != '' ? "AND tdc.cd_treinamento_colaborador_tipo = ".intval($args['cd_treinamento_colaborador_tipo']) : '' )."
			  ORDER BY nr_numero;
                ";
		#echo '<PRE style="text-align: left;">'.print_r($args,true)."<BR>".$qr_sql.'</PRE>';
				
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function carrega($cd_treinamento_diretoria_conselhos)
    {
        $qr_sql = "
            SELECT funcoes.nr_treinamento_colaborador(nr_ano, nr_numero) AS nr_numero,
				   cd_treinamento_diretoria_conselhos,
                   ds_nome,
                   ds_promotor,
                   ds_endereco,
                   ds_cidade,
                   ds_uf,
                   TO_CHAR(dt_inicio,'DD/MM/YYYY') AS dt_inicio,
                   TO_CHAR(hr_inicio,'HH24:MI:SS') AS hr_inicio,
                   TO_CHAR(dt_final,'DD/MM/YYYY') AS dt_final,
                   TO_CHAR(hr_final,'HH24:MI:SS') AS hr_final,
                   nr_carga_horaria,
                   vl_unitario,
                   cd_treinamento_colaborador_tipo,
				   TO_CHAR(dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao
              FROM projetos.treinamento_diretoria_conselhos
             WHERE cd_treinamento_diretoria_conselhos = ".intval($cd_treinamento_diretoria_conselhos).";";

        return $this->db->query($qr_sql)->row_array();
    }
    
    public function salvar($args = array())
    {
		$cd_treinamento_diretoria_conselhos = intval($this->db->get_new_id('projetos.treinamento_diretoria_conselhos', 'cd_treinamento_diretoria_conselhos'));

        $qr_sql = "
			INSERT INTO projetos.treinamento_diretoria_conselhos
				 (
					 cd_treinamento_diretoria_conselhos,
					 ds_nome,
					 ds_promotor,
					 ds_endereco,
					 ds_cidade,
					 ds_uf,
					 dt_inicio,
					 hr_inicio,
					 dt_final,
					 hr_final,
					 nr_carga_horaria,
					 vl_unitario,
					 cd_treinamento_colaborador_tipo,
					 cd_usuario_inclusao,
					 cd_usuario_alteracao
				 )
			VALUES 
				 (
					 ".$cd_treinamento_diretoria_conselhos.",
					 ".str_escape($args['ds_nome']).",
					 ".str_escape($args['ds_promotor']).",
					 ".(trim($args['ds_endereco']) == '' ? "DEFAULT"  : str_escape($args['ds_endereco'])).",
					 ".(trim($args['ds_cidade']) == '' ? "DEFAULT"  : str_escape($args['ds_cidade'])).",
					 ".(trim($args['ds_uf']) == '' ? "DEFAULT"  : str_escape($args['ds_uf'])).",
					 ".(trim($args['dt_inicio']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')").",
					 ".(trim($args['hr_inicio']) == "" ? "DEFAULT" : "CAST('".$args['hr_inicio']."' AS TIME)").",
					 ".(trim($args['dt_final']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_final']."','DD/MM/YYYY')").",
					 ".(trim($args['hr_final']) == "" ? "DEFAULT" : "CAST('".$args['hr_final']."' AS TIME)").",
					 ".(trim($args['nr_carga_horaria']) == "" ? "DEFAULT" : floatval($args['nr_carga_horaria'])).",
					 ".(trim($args['vl_unitario']) == '' ? "DEFAULT"  : floatval($args['vl_unitario'])).",
					 ".(trim($args['cd_treinamento_colaborador_tipo']) == '' ? "DEFAULT"  : intval($args['cd_treinamento_colaborador_tipo'])).",
					 ".intval($args['cd_usuario']).",
					 ".intval($args['cd_usuario'])."
				 );";
				 
		$this->db->query($qr_sql);
		
		return $cd_treinamento_diretoria_conselhos;
    }
    
	public function atualizar($cd_treinamento_diretoria_conselhos, $args = array())
	{
		$qr_sql = "	
			UPDATE projetos.treinamento_diretoria_conselhos
			   SET ds_nome                         = ".str_escape($args['ds_nome']).",
				   ds_promotor                     = ".str_escape($args['ds_promotor']).",
				   ds_endereco                     = ".(trim($args['ds_endereco']) == '' ? "DEFAULT"  : str_escape($args['ds_endereco'])).",
				   ds_cidade                       = ".(trim($args['ds_cidade']) == '' ? "DEFAULT"  : str_escape($args['ds_cidade'])).",
				   ds_uf                           = ".(trim($args['ds_uf']) == '' ? "DEFAULT"  : str_escape($args['ds_uf'])).",
				   dt_inicio                       = ".(trim($args['dt_inicio']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_inicio']."','DD/MM/YYYY')").",
				   hr_inicio                       = ".(trim($args['hr_inicio']) == "" ? "DEFAULT" : "CAST('".$args['hr_inicio']."' AS TIME)").",
				   dt_final                        = ".(trim($args['dt_final']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_final']."','DD/MM/YYYY')").",   
				   hr_final                        = ".(trim($args['hr_final']) == "" ? "DEFAULT" : "CAST('".$args['hr_final']."' AS TIME)").",
				   nr_carga_horaria                = ".(trim($args['nr_carga_horaria']) == "" ? "DEFAULT" : floatval($args['nr_carga_horaria'])).",
				   vl_unitario                     = ".(trim($args['vl_unitario']) == '' ? "DEFAULT"  : floatval($args['vl_unitario'])).",
				   cd_treinamento_colaborador_tipo = ".(trim($args['cd_treinamento_colaborador_tipo']) == '' ? "DEFAULT"  : intval($args['cd_treinamento_colaborador_tipo'])).",
				   dt_alteracao					   = CURRENT_TIMESTAMP,
				   cd_usuario_alteracao			   =  ".intval($args['cd_usuario'])."
			 WHERE cd_treinamento_diretoria_conselhos = ".intval($cd_treinamento_diretoria_conselhos).";";
					  
		$this->db->query($qr_sql);
	}
	
    public function excluir($cd_treinamento_diretoria_conselhos, $cd_usuario)
    {
        $qr_sql = "
			UPDATE projetos.treinamento_diretoria_conselhos
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			WHERE cd_treinamento_diretoria_conselhos = ".intval($cd_treinamento_diretoria_conselhos).";";
				
        $this->db->query($qr_sql);
    }	
	
    public function colaboradores($cd_treinamento_diretoria_conselhos)
    {	
        $qr_sql = "
			SELECT tdci.cd_treinamento_diretoria_conselhos_item,
				   tdci.cd_treinamento_diretoria_conselhos,
			 	   tdci.cd_empresa,
				   tdci.cd_registro_empregado,
				   tdci.seq_dependencia,
				   tdci.ds_nome,
				   tdci.cd_gerencia,
				   tdci.ds_centro_custo,
				   tdci.arquivo,
				   tdci.arquivo_nome,
				   d.nome AS area_gerencia
			  FROM projetos.treinamento_diretoria_conselhos_item tdci
			  LEFT JOIN projetos.divisoes d
			    ON d.codigo = tdci.cd_gerencia
			 WHERE tdci.cd_treinamento_diretoria_conselhos = ".intval($cd_treinamento_diretoria_conselhos)."
			   AND tdci.dt_exclusao IS NULL;";
					
        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_colaborador($cd_treinamento_diretoria_conselhos_item)
    {	
        $qr_sql = "
			SELECT tdci.cd_treinamento_diretoria_conselhos_item,
				   tdci.cd_treinamento_diretoria_conselhos,
			 	   tdci.cd_empresa,
				   tdci.cd_registro_empregado,
				   tdci.seq_dependencia,
				   tdci.ds_nome,
				   tdci.cd_gerencia,
				   tdci.ds_centro_custo,
				   tdci.arquivo,
				   tdci.arquivo_nome
			  FROM projetos.treinamento_diretoria_conselhos_item tdci
			 WHERE tdci.cd_treinamento_diretoria_conselhos_item = ".intval($cd_treinamento_diretoria_conselhos_item)."
			   AND tdci.dt_exclusao IS NULL;";
					
        return $this->db->query($qr_sql)->row_array();
    }
	
    public function salvar_colaborador($args = array())
    {
        $qr_sql = "
			INSERT INTO projetos.treinamento_diretoria_conselhos_item
				 (
				    cd_treinamento_diretoria_conselhos,
					cd_empresa,
					cd_registro_empregado,
					seq_dependencia,
					ds_nome,
					cd_gerencia,
					ds_centro_custo,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
			VALUES
				 (
					".intval($args['cd_treinamento_diretoria_conselhos']).",
					".(trim($args['cd_empresa'])            == '' ? "DEFAULT"  : intval($args['cd_empresa'])).", 
					".(trim($args['cd_registro_empregado']) == '' ? "DEFAULT"  : intval($args['cd_registro_empregado'])).", 
					".(trim($args['seq_dependencia'])       == '' ? "DEFAULT"  : intval($args['seq_dependencia'])).", 
					UPPER(funcoes.remove_acento('".trim($args['ds_nome'])."')),
					".(trim($args['cd_gerencia']) == '' ? "DEFAULT"  : "'".trim($args['cd_gerencia'])."'").",   
					".(trim($args['ds_centro_custo']) == '' ? "DEFAULT"  : "'".trim($args['ds_centro_custo'])."'").",  
					".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
					".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
					".intval($args['usuario'])."
				 )";
        
            $this->db->query($qr_sql);
    }

    public function atualizar_colaborador($cd_treinamento_diretoria_conselhos_item, $args = array())
    {
    	$qr_sql = "
    		UPDATE projetos.treinamento_diretoria_conselhos_item
    		   SET cd_empresa 			 = ".(trim($args['cd_empresa']) == '' ? "DEFAULT"  : intval($args['cd_empresa'])).",
				   cd_registro_empregado = ".(trim($args['cd_registro_empregado']) == '' ? "DEFAULT"  : intval($args['cd_registro_empregado'])).",
				   seq_dependencia 		 = ".(trim($args['seq_dependencia'])       == '' ? "DEFAULT"  : intval($args['seq_dependencia'])).",
				   ds_nome 				 = ".(trim($args['ds_nome']) != '' ? "UPPER(funcoes.remove_acento('".trim($args['ds_nome'])."'))" : "DEFAULT").",
				   cd_gerencia 			 = ".(trim($args['cd_gerencia']) == '' ? "DEFAULT"  : "'".trim($args['cd_gerencia'])."'").",
				   ds_centro_custo 		 = ".(trim($args['ds_centro_custo']) == '' ? "DEFAULT"  : "'".trim($args['ds_centro_custo'])."'").",  
				   arquivo 				 = ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
				   arquivo_nome 		 = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
				   cd_usuario_alteracao  = ".intval($args['usuario']).",
				   dt_alteracao 		 = CURRENT_TIMESTAMP
    		 WHERE cd_treinamento_diretoria_conselhos_item = ".intval($cd_treinamento_diretoria_conselhos_item).";";

    	$this->db->query($qr_sql);
    }
    
    public function excluir_colaborador($cd_treinamento_diretoria_conselhos_item, $cd_usuario)
    {
		$qr_sql = "
			UPDATE projetos.treinamento_diretoria_conselhos_item
               SET dt_exclusao         = CURRENT_TIMESTAMP,
                   cd_usuario_exclusao = ".intval($cd_usuario)."
             WHERE cd_treinamento_diretoria_conselhos_item = ".intval($cd_treinamento_diretoria_conselhos_item).";";
					
        $this->db->query($qr_sql);
    }
	
	public function agenda_listar($cd_treinamento_diretoria_conselhos)
	{
        $qr_sql = "
			SELECT tdca.cd_treinamento_diretoria_conselhos_agenda,
				   TO_CHAR(tdca.dt_agenda_ini,'DD/MM/YYYY') AS dt_agenda,
				   TO_CHAR(tdca.dt_agenda_ini,'HH24:MI') AS hr_ini,
				   TO_CHAR(tdca.dt_agenda_fim,'HH24:MI') AS hr_fim
			  FROM projetos.treinamento_diretoria_conselhos_agenda tdca
			  JOIN projetos.treinamento_diretoria_conselhos tdc
			    ON tdc.cd_treinamento_diretoria_conselhos = tdca.cd_treinamento_diretoria_conselhos
             WHERE tdca.dt_exclusao IS NULL
			   AND tdc.cd_treinamento_diretoria_conselhos = ".intval($cd_treinamento_diretoria_conselhos).";";
				
        return $this->db->query($qr_sql)->result_array();
    }
	
    public function agenda_salvar($args = array())
    {
        $qr_sql = "
			INSERT INTO projetos.treinamento_diretoria_conselhos_agenda
                 (
					cd_treinamento_diretoria_conselhos,
					dt_agenda_ini,
					dt_agenda_fim,
					cd_usuario_inclusao,
					cd_usuario_alteracao
                 )
			VALUES
                 (
					(SELECT cd_treinamento_diretoria_conselhos 
					   FROM projetos.treinamento_diretoria_conselhos 
				      WHERE cd_treinamento_diretoria_conselhos = ".intval($args['cd_treinamento_diretoria_conselhos'])."),
					".(trim($args['dt_agenda']) != ''? "TO_TIMESTAMP('".$args['dt_agenda']." ".$args['hr_ini']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
					".(trim($args['dt_agenda']) != ''? "TO_TIMESTAMP('".$args['dt_agenda']." ".$args['hr_fim']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
					".intval($args['usuario']).",
					".intval($args['usuario'])."
                 );";
        
        $this->db->query($qr_sql);
    }	
	
    public function agenda_excluir($cd_treinamento_diretoria_conselhos_agenda, $cd_usuario)
    {
        $qr_sql = "
			UPDATE projetos.treinamento_diretoria_conselhos_agenda
			   SET dt_exclusao 		   = CURRENT_TIMESTAMP,
				   cd_usuario_exclusao = ".intval($cd_usuario)."
			 WHERE cd_treinamento_diretoria_conselhos_agenda = ".intval($cd_treinamento_diretoria_conselhos_agenda).";";
        
        $this->db->query($qr_sql);
    }	

    public function agenda_atualizar($cd_treinamento_diretoria_conselhos, $cd_usuario)
    {
        $qr_sql = "
			UPDATE projetos.treinamento_diretoria_conselhos_agenda AS tdca
			   SET dt_alteracao         = CURRENT_TIMESTAMP,
			       cd_usuario_alteracao = ".intval($cd_usuario)."
			 WHERE tdca.cd_treinamento_diretoria_conselhos = (SELECT tdc.cd_treinamento_diretoria_conselhos 
																FROM projetos.treinamento_diretoria_conselhos tdc
															   WHERE tdc.dt_final > CURRENT_DATE
																 AND tdc.dt_exclusao IS NULL
																 AND tdc.cd_treinamento_diretoria_conselhos = ".intval($cd_treinamento_diretoria_conselhos).");";

        $this->db->query($qr_sql);
    }	
}
