<?php
class cpuscanner extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		$this->load->model('projetos/cpuscanner_model');
    }
	
	function setArquivo()
    {
		$file = fopen('./up/aniversario/teste.pdf', 'w'); 
		fwrite($file, base64_decode($_POST['arquivo']));
		fclose($file);		
		echo "OK";
    }	
	
	function getArquivo()
    {
		$arq = './up/aniversario/teste.pdf';
		$file = fopen($arq, 'r'); 
		#$ar_ret['arquivo'] = base64_encode(fread($file, filesize($arq)));
		$data = utf8_encode(base64_encode(fread($file, filesize($arq))));
		fclose($file);		
		echo $data;
		#echo json_encode($ar_ret);
    }	
	

	function equipamento($nr_patrimonio = 0, $tp_equipamento = 0)
    {
		$result = null;
		$data   = array();
		$args   = array();		
		$ar_ret['ERRO']       = 0;
		$ar_ret['PATRIMONIO'] = intval($nr_patrimonio);
		
		if(intval($nr_patrimonio) > 0)
		{
			$args['nr_patrimonio']  = intval($nr_patrimonio);
			$args['tp_equipamento'] = (intval($tp_equipamento) > 0 ? intval($tp_equipamento) : 1);
			$this->cpuscanner_model->checkEquipamento($result, $args);
			$ar_check = $result->row_array();

			if($ar_check['fl_equipamento'] == 'N')
			{
				$this->cpuscanner_model->insereEquipamento($args);
			}
			else
			{
				$this->cpuscanner_model->setTipoEquipamento($args);
			}
		}
		else
		{
			$ar_ret['ERRO']       = 1;
			$ar_ret['PATRIMONIO'] = intval($nr_patrimonio);			
		}

		echo json_encode($ar_ret);
    }

	function setUsuarioEprev()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['nr_ip']         = utf8_decode($this->input->post('nr_ip', true)); 
		$args['usuario']       = utf8_decode($this->input->post('usuario', true)); 
		
		$this->cpuscanner_model->setUsuarioEprev($result, $args);
		
		echo json_encode($args);
		/*
		''ATUALIZA USUARIO
        vf_patrimonio = pegaPatrimonio()
        'MsgBox vf_patrimonio
        If vf_patrimonio <> "" Then
               vl_sql = "UPDATE projetos.usuarios_controledi SET np_computador = " & vf_patrimonio & ", estacao_trabalho = '" & vl_ip & "', dt_hora_scanner_computador = CURRENT_TIMESTAMP WHERE REPLACE(REPLACE(UPPER(usuario),'ç','C'),'Ç','C') = REPLACE(REPLACE(UPPER('" & vg_usuario & "'),'ç','C'),'Ç','C')"
               vl_rs.Open vl_sql, dbPg
        End If
		*/
	}
	
	function setUsuario()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['usuario']       = utf8_decode($this->input->post('usuario', true)); 
		
		$this->cpuscanner_model->setUsuario($result, $args);
		
		echo json_encode($args);
	}	
	
	function setCPUScanner()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['versao']        = utf8_decode($this->input->post('versao', true)); 
		
		$this->cpuscanner_model->setCPUScanner($result, $args);
		
		echo json_encode($args);
		/*
        ''ATUALIZA VERSAO CPUSCANNER
        vf_patrimonio = pegaPatrimonio()
        'MsgBox vf_patrimonio
        If vf_patrimonio <> "" Then
               vl_sql = "UPDATE projetos.equipamentos SET versao_cpuscanner = TRIM('" & App.Major & "." & App.Minor & "." & App.Revision & "') 
			   WHERE codigo_patrimonio = " & vf_patrimonio
               vl_rs.Open vl_sql, dbPg
        End If
		*/
	}
	
	function setIP()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['nr_ip']         = utf8_decode($this->input->post('nr_ip', true)); 
		
		$this->cpuscanner_model->setIP($result, $args);
		
		echo json_encode($args);
	}	
	
	function setExecucao()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$this->cpuscanner_model->setExecucao($result, $args);
		
		echo json_encode($args);
		/*
        ''ATUALIZA EXECUCAO
        vf_patrimonio = pegaPatrimonio()
        'MsgBox vf_patrimonio
        If vf_patrimonio <> "" Then
               vl_sql = "UPDATE projetos.equipamentos SET ultima_atualizacao = CURRENT_TIMESTAMP WHERE codigo_patrimonio = " & vf_patrimonio
               vl_rs.Open vl_sql, dbPg
        End If
		*/
	}	
	
	function setSituacao()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$this->cpuscanner_model->setSituacao($result, $args);
		
		echo json_encode($args);
		/*
        ''ATUALIZA SITUACAO
        If vf_patrimonio <> "" Then
               vl_sql = "UPDATE projetos.equipamentos SET situacao = 'SIT1' WHERE codigo_patrimonio = " & vf_patrimonio
               vl_rs.Open vl_sql, dbPg
        End If
		*/
	}

	function setComputador()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['computador']    = utf8_decode($this->input->post('computador', true)); 
		
		$this->cpuscanner_model->setComputador($result, $args);
		
		echo json_encode($args);
	}

	function setMacAddress()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['mac_address']   = utf8_decode($this->input->post('mac_address', true)); 
		
		$this->cpuscanner_model->setMacAddress($result, $args);
		
		echo json_encode($args);
		
		/*
        ''ATUALIZA MAC_ADDRESS
        vf_mac_address = GetMACs_IfTable2()
        'MsgBox vf_mac_address
        If vf_mac_address <> "" Then
           vl_sql = "UPDATE projetos.equipamentos SET mac_address = TRIM('" & vf_mac_address & "') WHERE codigo_patrimonio = " & vf_patrimonio
           vl_rs.Open vl_sql, dbPg
        End If		
		*/
	}

	function setProcessador()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['processador']   = utf8_decode($this->input->post('processador', true)); 
		
		$this->cpuscanner_model->setProcessador($result, $args);
		
		echo json_encode($args);
		
		/*
         ''ATUALIZA PROCESSADOR
        Dim v_proc As Variant
        v_proc = pegaProcessador()
        'MsgBox v_proc
        dsProc.Text = v_proc
        If v_proc <> "" Then
           vl_sql = "UPDATE projetos.equipamentos SET processador_nome = TRIM('" & Replace(dsProc.Text, "  ", " ") & "') WHERE codigo_patrimonio = " & vf_patrimonio
           vl_rs.Open vl_sql, dbPg
        End If	
		*/
	}	
	
	function setVersaoIExplorer()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio']   = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['versao_explorer'] = utf8_decode($this->input->post('versao_explorer', true)); 
		
		$this->cpuscanner_model->setVersaoIExplorer($result, $args);
		
		echo json_encode($args);
		
		/*
        ''ATUALIZA VERSAO INTERNET EXPLORER
        Dim v_explorer As Variant
        'v_explorer = GetProductVersion("C:\Arquivos de programas\Internet Explorer\iexplorer.exe")
        'MsgBox v_proc
        'VExplorer.Text = v_explorer
        'If v_explorer <> "" Then
        '   vl_sql = "UPDATE projetos.equipamentos SET versao_explorer = TRIM('" & Replace(VExplorer.Text, "  ", " ") & "') WHERE codigo_patrimonio = " & vf_patrimonio
        '   vl_rs.Open vl_sql, dbPg
        'Else
            v_explorer = getVersaoIExplorer
            VExplorer.Text = v_explorer
            If v_explorer <> "" Then
                vl_sql = "UPDATE projetos.equipamentos SET versao_explorer = TRIM('" & Replace(VExplorer.Text, "  ", " ") & "') WHERE codigo_patrimonio = " & vf_patrimonio
                vl_rs.Open vl_sql, dbPg
            End If
      
        'End If
		*/
	}	
	
	function setVersaoFirefox()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio']  = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['versao_firefox'] = utf8_decode($this->input->post('versao_firefox', true)); 
		
		$this->cpuscanner_model->setVersaoFirefox($result, $args);
		
		echo json_encode($args);
		
		/*
        ''ATUALIZA VERSAO FIREFOX
        Dim v_firefox As Variant
        v_firefox = getVersaoFirefox 'GetProductVersion("C:\Arquivos de programas\Mozilla Firefox\firefox.exe")
        'MsgBox v_proc
        VFirefox.Text = v_firefox
        If v_firefox <> "" Then
           vl_sql = "UPDATE projetos.equipamentos SET versao_firefox = TRIM('" & Replace(VFirefox.Text, "  ", " ") & "') WHERE codigo_patrimonio = " & vf_patrimonio
           vl_rs.Open vl_sql, dbPg
        End If
		*/
	}	
	
	function setVersaoChrome()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['versao_chrome'] = utf8_decode($this->input->post('versao_chrome', true)); 
		
		$this->cpuscanner_model->setVersaoChrome($result, $args);
		
		echo json_encode($args);
		
		/*
        ''ATUALIZA VERSAO CHROME
        Dim v_chrome As Variant
        v_chrome = getVersaoChrome
        'MsgBox v_proc
        VChrome.Text = v_chrome
        If v_chrome <> "" Then
           vl_sql = "UPDATE projetos.equipamentos SET versao_chrome = TRIM('" & Replace(VChrome.Text, "  ", " ") & "') WHERE codigo_patrimonio = " & vf_patrimonio
           vl_rs.Open vl_sql, dbPg
        End If
		*/
	}	
	
	function setVersaoDotNet()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['versao_dotnet'] = utf8_decode($this->input->post('versao_dotnet', true)); 
		
		$this->cpuscanner_model->setVersaoDotNet($result, $args);
		
		echo json_encode($args);
		
		/*
        ''ATUALIZA VERSAO DOTNET
        Dim v_dotnet As Variant
        v_dotnet = getVersaoDotNet
        VDotNet.Text = v_dotnet
        If v_dotnet <> "" Then
           vl_sql = "UPDATE projetos.equipamentos SET versao_dotnet = TRIM('" & Replace(VDotNet.Text, "  ", " ") & "') WHERE codigo_patrimonio = " & vf_patrimonio
           vl_rs.Open vl_sql, dbPg
        End If
		*/
	}

	function setDataInstallOS()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio']  = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['dt_install_win'] = utf8_decode($this->input->post('dt_install_win', true)); 
		
		$this->cpuscanner_model->setDataInstallOS($result, $args);
		
		echo json_encode($args);
		
		/*
        ''ATUALIZA DATA DE INSTALAÇÃO SISTEMA OPERACIONAL
        Dim v_data_os As Variant
        v_data_os = getDataInstallOS
        VDataOS.Text = v_data_os
        If v_data_os <> "" Then
           vl_sql = "UPDATE projetos.equipamentos SET dt_instalacao_os = TO_TIMESTAMP('" & Replace(VDataOS.Text, "  ", " ") & "','DD/MM/YYYY HH24:MI:SS') WHERE codigo_patrimonio = " & vf_patrimonio
           vl_rs.Open vl_sql, dbPg
        End If
		*/
	}	
	
	function setTipoOS()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['tipo_win']      = utf8_decode($this->input->post('tipo_win', true)); 
		
		$this->cpuscanner_model->setTipoOS($result, $args);
		
		echo json_encode($args);
		
		/*
        ''ATUALIZA TIPO SISTEMA OPERACIONAL
        Dim v_tipo_os As Variant
        v_tipo_os = getTipoOS
        VTipoOS.Text = v_tipo_os
        If v_tipo_os <> "" Then
           vl_sql = "UPDATE projetos.equipamentos SET sistema_operacional_tipo = TRIM('" & Replace(VTipoOS.Text, "  ", " ") & "') WHERE codigo_patrimonio = " & vf_patrimonio
           vl_rs.Open vl_sql, dbPg
        End If
		*/
	}		
	
	function setAtalhos()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['atalhos']       = utf8_decode($this->input->post('atalhos', true)); 
		
		$this->cpuscanner_model->setAtalhos($result, $args);
		
		echo json_encode($args);
		
		/*
        ''ATUALIZA ATALHOS DESKTOP
        vf_patrimonio = pegaPatrimonio()
        dsProc.Text = v_proc
        If vf_patrimonio <> "" Then
           vl_sql = "UPDATE projetos.equipamentos SET atalhos = E'" & Replace(getDesktop(), "'", "") & "' WHERE codigo_patrimonio = " & vf_patrimonio
           vl_rs.Open vl_sql, dbPg
        End If
		*/
	}	
	
	function setQTMonitor()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['qt_monitor']    = utf8_decode($this->input->post('qt_monitor', true)); 
		
		$this->cpuscanner_model->setQTMonitor($result, $args);
		
		echo json_encode($args);
	}	
	
	function setResolucaoMonitor()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio']     = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['monitor_resolucao'] = utf8_decode($this->input->post('monitor_resolucao', true)); 
		
		$this->cpuscanner_model->setResolucaoMonitor($result, $args);
		
		echo json_encode($args);
	}	
	
	function setVersaoFreePDF()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio']  = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['versao_freepdf'] = utf8_decode($this->input->post('versao_freepdf', true)); 
		
		$this->cpuscanner_model->setVersaoFreePDF($result, $args);
		
		echo json_encode($args);
	}	
	
	function setVersaoJava()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['versao_java']   = utf8_decode($this->input->post('versao_java', true)); 
		
		$this->cpuscanner_model->setVersaoJava($result, $args);
		
		echo json_encode($args);
	}	
	
	function setVersaoWinrar()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['versao_winrar'] = utf8_decode($this->input->post('versao_winrar', true)); 
		
		$this->cpuscanner_model->setVersaoWinrar($result, $args);
		
		echo json_encode($args);
	}
	
	function setVersaoOffice()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['versao_office'] = utf8_decode($this->input->post('versao_office', true)); 
		
		$this->cpuscanner_model->setVersaoOffice($result, $args);
		
		echo json_encode($args);
	}	
	
	function setDriveList()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['drive_lista']   = utf8_decode($this->input->post('drive_lista', true)); 
		
		$this->cpuscanner_model->setDriveList($result, $args);
		
		echo json_encode($args);
	}	
		
	function setCompartilhamentoList()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio']          = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['compartilhamento_lista'] = utf8_decode($this->input->post('compartilhamento_lista', true)); 
		
		$this->cpuscanner_model->setCompartilhamentoList($result, $args);
		
		echo json_encode($args);
	}	
	
	function setDispositivoAudio()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['audio_lista']   = utf8_decode($this->input->post('audio_lista', true)); 
		
		$this->cpuscanner_model->setDispositivoAudio($result, $args);
		
		echo json_encode($args);
	}

	function setMemoriaRAM()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['memoria_ram']   = utf8_decode($this->input->post('memoria_ram', true)); 
		
		$this->cpuscanner_model->setMemoriaRAM($result, $args);
		
		echo json_encode($args);
	}

	function setVersaoSO()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio']       = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['sistema_operacional'] = utf8_decode($this->input->post('sistema_operacional', true)); 
		
		$this->cpuscanner_model->setVersaoSO($result, $args);
		
		echo json_encode($args);
	}

	function setDriveSize()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio']  = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['tipo_a']         = utf8_decode($this->input->post('tipo_a', true)); 
		$args['tipo_b']         = utf8_decode($this->input->post('tipo_b', true)); 
		$args['tipo_c']         = utf8_decode($this->input->post('tipo_c', true)); 
		$args['tipo_d']         = utf8_decode($this->input->post('tipo_d', true)); 
		$args['tipo_e']         = utf8_decode($this->input->post('tipo_e', true)); 
		$args['tipo_f']         = utf8_decode($this->input->post('tipo_f', true)); 
		$args['tipo_g']         = utf8_decode($this->input->post('tipo_g', true)); 
		$args['tipo_h']         = utf8_decode($this->input->post('tipo_h', true));		
		$args['espaco_total_a'] = utf8_decode($this->input->post('espaco_total_a', true)); 
		$args['espaco_total_b'] = utf8_decode($this->input->post('espaco_total_b', true)); 
		$args['espaco_total_c'] = utf8_decode($this->input->post('espaco_total_c', true)); 
		$args['espaco_total_d'] = utf8_decode($this->input->post('espaco_total_d', true)); 
		$args['espaco_total_e'] = utf8_decode($this->input->post('espaco_total_e', true)); 
		$args['espaco_total_f'] = utf8_decode($this->input->post('espaco_total_f', true)); 
		$args['espaco_total_g'] = utf8_decode($this->input->post('espaco_total_g', true)); 
		$args['espaco_total_h'] = utf8_decode($this->input->post('espaco_total_h', true)); 
		$args['espaco_livre_a'] = utf8_decode($this->input->post('espaco_livre_a', true)); 
		$args['espaco_livre_b'] = utf8_decode($this->input->post('espaco_livre_b', true)); 
		$args['espaco_livre_c'] = utf8_decode($this->input->post('espaco_livre_c', true)); 
		$args['espaco_livre_d'] = utf8_decode($this->input->post('espaco_livre_d', true)); 
		$args['espaco_livre_e'] = utf8_decode($this->input->post('espaco_livre_e', true)); 
		$args['espaco_livre_f'] = utf8_decode($this->input->post('espaco_livre_f', true)); 
		$args['espaco_livre_g'] = utf8_decode($this->input->post('espaco_livre_g', true)); 
		$args['espaco_livre_h'] = utf8_decode($this->input->post('espaco_livre_h', true)); 		
		$args['espaco_usado_a'] = utf8_decode($this->input->post('espaco_usado_a', true)); 
		$args['espaco_usado_b'] = utf8_decode($this->input->post('espaco_usado_b', true)); 
		$args['espaco_usado_c'] = utf8_decode($this->input->post('espaco_usado_c', true)); 
		$args['espaco_usado_d'] = utf8_decode($this->input->post('espaco_usado_d', true)); 
		$args['espaco_usado_e'] = utf8_decode($this->input->post('espaco_usado_e', true)); 
		$args['espaco_usado_f'] = utf8_decode($this->input->post('espaco_usado_f', true)); 
		$args['espaco_usado_g'] = utf8_decode($this->input->post('espaco_usado_g', true)); 
		$args['espaco_usado_h'] = utf8_decode($this->input->post('espaco_usado_h', true)); 		
		
		$this->cpuscanner_model->setDriveSize($result, $args);
		
		echo json_encode($args);
	}

	function setProgramasInstalados()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio']        = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['programas_instalados'] = utf8_decode($this->input->post('programas_instalados', true)); 
		
		$this->cpuscanner_model->setProgramasInstalados($result, $args);
		
		echo json_encode($args);
	}
	
	function setImpressora()
    {
		$result = null;
		$data   = array();
		$args   = array();		
		
		$args['nr_patrimonio'] = utf8_decode($this->input->post('nr_patrimonio', true)); 
		$args['impressoras']   = utf8_decode($this->input->post('impressoras', true)); 
		
		$this->cpuscanner_model->setImpressora($result, $args);
		
		echo json_encode($args);
	}	
	
	function getProgramasExterno()
    {
		$ar_ret = array();		
		
		#$ar_ret['ar_prog'][0]['programa']      = "//srvseguranca/Executaveis/bloqueiaWin/bloqueiaWin.exe";
		#$ar_ret['ar_prog'][0]['programa_nome'] = "Controle de Intervalo";
		#$ar_ret['ar_prog'][3]['programa']      = "//srvseguranca/Executaveis/videoFamilia/familiavideo.exe";
		#$ar_ret['ar_prog'][3]['programa_nome'] = "Video Vendas";	


		$ar_ret['ar_prog'][0]['programa']      = "//srvseguranca/Executaveis/MensagemX/MensagemX.exe";
		$ar_ret['ar_prog'][0]['programa_nome'] = "Mensagens";

		$ar_ret['ar_prog'][1]['programa']      = "//srvseguranca/Executaveis/vendasFamilia/vendasFamilia.exe";
		$ar_ret['ar_prog'][1]['programa_nome'] = "Vendas";	

		$ar_ret['ar_prog'][2]['programa']      = "//srvseguranca/Executaveis/pendenciaAviso/pendenciaAviso.exe";
		$ar_ret['ar_prog'][2]['programa_nome'] = "Aviso de Pendencias";		
		
		#$ar_ret['ar_prog'][3]['programa']      = "//srvseguranca/Executaveis/voip/VOIPAtualizaContato.exe";
		#$ar_ret['ar_prog'][3]['programa_nome'] = "Atualizar Ramais";		
		
		$ar_ret['qt_prog'] = count($ar_ret['ar_prog']);
		

		echo json_encode($ar_ret,JSON_HEX_QUOT);
	}	
}
?>