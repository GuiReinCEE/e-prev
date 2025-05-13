<?php
class cpuscanner_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
	
    public function checkEquipamento(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT CASE WHEN COUNT(*) > 0 THEN 'S' ELSE 'N' END AS fl_equipamento
                      FROM projetos.equipamentos
					 WHERE codigo_patrimonio = ".intval($args['nr_patrimonio'])."
                  ";

        $result = $this->db->query($qr_sql);
    }

    public function setTipoEquipamento($args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET tipo_equipamento  = ".intval($args['tp_equipamento'])."
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio'])."
                  ";

        $this->db->query($qr_sql);
    }	
	
    public function insereEquipamento($args=array())
    {
        $qr_sql = "
					INSERT INTO projetos.equipamentos (codigo_patrimonio, tipo_equipamento) VALUES (".intval($args['nr_patrimonio']).", ".intval($args['tp_equipamento']).")
                  ";

        $this->db->query($qr_sql);
    }	
	
    public function setUsuarioEprev(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.usuarios_controledi 
					   SET estacao_trabalho = NULL
					 WHERE estacao_trabalho = '".trim($args['nr_ip'])."';
					
					UPDATE projetos.usuarios_controledi 
					   SET np_computador              = ".intval($args['nr_patrimonio']).", 
					       estacao_trabalho           = '".trim($args['nr_ip'])."', 
						   dt_hora_scanner_computador = CURRENT_TIMESTAMP 
				     WHERE REPLACE(REPLACE(UPPER(usuario),'ç','C'),'Ç','C') = REPLACE(REPLACE(UPPER('".trim($args['usuario'])."'),'ç','C'),'Ç','C');
                  ";

        $result = $this->db->query($qr_sql);
    }	
	
    public function setUsuario(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET usuario     = (funcoes.get_usuario(LOWER(REPLACE(REPLACE(UPPER('".trim($args['usuario'])."'),'ç','C'),'Ç','C')))),
					       ds_usuario  = (LOWER(REPLACE(REPLACE(UPPER('".trim($args['usuario'])."'),'ç','C'),'Ç','C'))),
					       cod_divisao = (funcoes.get_usuario_area(usuario::INTEGER))
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);        

        $result = $this->db->query($qr_sql);
    }	
	
    public function setCPUScanner(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET versao_cpuscanner = '".trim($args['versao'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }

    public function setIP(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET ip = '".trim($args['nr_ip'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	
	
    public function setExecucao(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET ultima_atualizacao = CURRENT_TIMESTAMP 
					 WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $this->db->query($qr_sql);
    }	

    public function setSituacao(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET situacao = 'SIT1' 
					 WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $this->db->query($qr_sql);
    }	
    
    public function setComputador(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET identif_rede    = '".trim($args['computador'])."',
					       nome_computador = '".trim($args['computador'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }
	
    public function setMacAddress(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET mac_address = '".trim($args['mac_address'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }
	
    public function setProcessador(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET processador_nome = '".trim($args['processador'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	
	
    public function setVersaoIExplorer(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET versao_explorer = '".trim($args['versao_explorer'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }

    public function setVersaoFirefox(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET versao_firefox = '".trim($args['versao_firefox'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }

    public function setVersaoChrome(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET versao_chrome = '".trim($args['versao_chrome'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }
	
    public function setVersaoDotNet(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET versao_dotnet = '".trim($args['versao_dotnet'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	
	
    public function setDataInstallOS(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET dt_instalacao_os = TO_TIMESTAMP('".trim($args['dt_install_win'])."','DD/MM/YYYY HH24:MI:SS')
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	

    public function setTipoOS(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET sistema_operacional_tipo = '".trim($args['tipo_win'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }
	
    public function setAtalhos(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET atalhos = '".str_replace("`","",str_replace("´","",str_replace("'","",trim($args['atalhos']))))."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	

    public function setQTMonitor(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET qt_monitor = '".intval($args['qt_monitor'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	
	
    public function setResolucaoMonitor(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET monitor_resolucao = '".trim($args['monitor_resolucao'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	

    public function setVersaoFreePDF(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET versao_freepdf = '".trim($args['versao_freepdf'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	
	
    public function setVersaoJava(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET versao_java = '".trim($args['versao_java'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	

    public function setVersaoWinrar(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET versao_winrar = '".trim($args['versao_winrar'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }		

    public function setVersaoOffice(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET versao_office = '".trim($args['versao_office'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	

    public function setDriveList(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET lista_unidade = '".trim($args['drive_lista'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }

    public function setCompartilhamentoList(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET lista_compartilhamento = '".trim($args['compartilhamento_lista'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }

    public function setDispositivoAudio(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET lista_dispositivo_som = '".trim($args['audio_lista'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	

    public function setMemoriaRAM(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET memoria_ram = ".intval($args['memoria_ram'])."
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }

    public function setVersaoSO(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET sistema_operacional = '".trim($args['sistema_operacional'])."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }

    public function setDriveSize(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET tipo_a = '".trim($args['tipo_a'])."',
					       tipo_b = '".trim($args['tipo_b'])."',
					       tipo_c = '".trim($args['tipo_c'])."',
					       tipo_d = '".trim($args['tipo_d'])."',
					       tipo_e = '".trim($args['tipo_e'])."',
					       tipo_f = '".trim($args['tipo_f'])."',
					       tipo_g = '".trim($args['tipo_g'])."',
					       tipo_h = '".trim($args['tipo_h'])."',					   
					       espaco_total_a = ".intval($args['espaco_total_a']).",
					       espaco_total_b = ".intval($args['espaco_total_b']).",
					       espaco_total_c = ".intval($args['espaco_total_c']).",
					       espaco_total_d = ".intval($args['espaco_total_d']).",
					       espaco_total_e = ".intval($args['espaco_total_e']).",
					       espaco_total_f = ".intval($args['espaco_total_f']).",
					       espaco_total_g = ".intval($args['espaco_total_g']).",
					       espaco_total_h = ".intval($args['espaco_total_h']).",
					       espaco_livre_a = ".intval($args['espaco_livre_a']).",
					       espaco_livre_b = ".intval($args['espaco_livre_b']).",
					       espaco_livre_c = ".intval($args['espaco_livre_c']).",
					       espaco_livre_d = ".intval($args['espaco_livre_d']).",
					       espaco_livre_e = ".intval($args['espaco_livre_e']).",
					       espaco_livre_f = ".intval($args['espaco_livre_f']).",
					       espaco_livre_g = ".intval($args['espaco_livre_g']).",
					       espaco_livre_h = ".intval($args['espaco_livre_h']).",	
					       espaco_usado_a = ".intval($args['espaco_usado_a']).",
					       espaco_usado_b = ".intval($args['espaco_usado_b']).",
					       espaco_usado_c = ".intval($args['espaco_usado_c']).",
					       espaco_usado_d = ".intval($args['espaco_usado_d']).",
					       espaco_usado_e = ".intval($args['espaco_usado_e']).",
					       espaco_usado_f = ".intval($args['espaco_usado_f']).",
					       espaco_usado_g = ".intval($args['espaco_usado_g']).",
					       espaco_usado_h = ".intval($args['espaco_usado_h'])."
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	
	
    public function setProgramasInstalados(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET programas_instalados = '".trim(str_replace("'","`",$args['programas_instalados']))."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	
	
    public function setImpressora(&$result,$args=array())
    {
        $qr_sql = "
					UPDATE projetos.equipamentos 
					   SET impressora = '".trim(str_replace("'","`",$args['impressoras']))."'
			         WHERE codigo_patrimonio = ".intval($args['nr_patrimonio']);

        $result = $this->db->query($qr_sql);
    }	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	



	
}
?>