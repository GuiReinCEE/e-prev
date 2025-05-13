<?php
class xlite extends Controller
{
    function __construct()
    {
        parent::Controller();
		
		#$this->load->model('projetos/cpuscanner_model');
    }

	function getVersaoXlite()
    {
		/*
    'X-LITE 4.6 (WIN 7)
    Public exe_xlite_7_46 As String = "C:\Program Files (x86)\CounterPath\X-Lite\X-Lite.exe"
    Public arq_conf_client_7_46 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\4.6\default_user\lista_config.txt"
    Public arq_bd_7_46 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\4.6\default_user\contacts.db"

    'X-LITE 4.8 (WIN 7)
    Public exe_xlite_7_48 As String = "C:\Program Files (x86)\CounterPath\X-Lite\X-Lite.exe"
    Public arq_conf_client_7_48 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\4.8\default_user\lista_config.txt"
    Public arq_bd_7_48 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\4.8\default_user\contacts.db"

    'X-LITE 5.0 (WIN 7)
    Public exe_xlite_7_50 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath\X-Lite\Current\X-Lite.exe"
    Public arq_conf_client_7_50 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\5.0\default_user\lista_config.txt"
    Public arq_bd_7_50 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\5.0\default_user\contacts.db"

    'X-LITE 5.4 (WIN 7)
    Public exe_xlite_7_54 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath\X-Lite\Current\X-Lite.exe"
    Public arq_conf_client_7_54 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\5.4\default_user\lista_config.txt"
    Public arq_bd_7_54 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\5.4\default_user\contacts.db"

    'X-LITE 5.6 (WIN 7)
    Public exe_xlite_7_56 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath\X-Lite\Current\X-Lite.exe"
    Public arq_conf_client_7_56 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\5.6\default_user\lista_config.txt"
    Public arq_bd_7_56 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\5.6\default_user\contacts.db"


    'X-LITE (WIN 7)
    Public exe_xlite_7 As String = "C:\Program Files (x86)\CounterPath\X-Lite\X-Lite.exe"
    Public arq_conf_client_7 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\default_user\lista_config.txt"
    Public arq_bd_7 As String = "C:\Users\" + Environment.UserName + "\AppData\Local\CounterPath Corporation\X-Lite\default_user\contacts.db"



    'BRIA 3 (WIN 7)
    Public exe_bria3_7 As String = "C:\Program Files (x86)\CounterPath\Bria 3\Bria3.exe"
    Public arq_conf_client_bria3_7 As String = "C:\Users\" + Environment.UserName + "\AppData\Roaming\CounterPath Corporation\Bria 3\default_user\lista_config.txt"
    Public arq_bd_bria3_7 As String = "C:\Users\" + Environment.UserName + "\AppData\Roaming\CounterPath Corporation\Bria 3\default_user\contacts.db"

    'BRIA 4 (WIN 7)
    Public exe_bria4_7 As String = "C:\Program Files (x86)\CounterPath\Bria 4\Bria4.exe"
    Public arq_conf_client_bria4_7 As String = "C:\Users\" + Environment.UserName + "\AppData\Roaming\CounterPath Corporation\Bria\4.0\default_user\lista_config.txt"
    Public arq_bd_bria4_7 As String = "C:\Users\" + Environment.UserName + "\AppData\Roaming\CounterPath Corporation\Bria\4.0\default_user\contacts.db"




		*/
		
		$ar_ret = array();		

		$ar_ret['ar_config'][] = array
		(
			'tipo_win'   => "XP",
			'tipo'       => 'XLITE' ,
			'executavel' => "C:\Arquivos de programas\CounterPath\X-Lite\X-Lite.exe",
			'arq_config' => "{userprofile}\Configurações locais\Dados de aplicativos\CounterPath Corporation\X-Lite\default_user\lista_config.txt",
			'arq_bd'     => "{userprofile}\Configurações locais\Dados de aplicativos\CounterPath Corporation\X-Lite\default_user\contacts.db"
		);
		
		$ar_ret['ar_config'][] = array
		(
			'tipo'       => "WIN",
			'executavel' => "C:\Arquivos de programas\CounterPath\X-Lite\X-Lite.exe",
			'arq_config' => "{userprofile}\Configurações locais\Dados de aplicativos\CounterPath Corporation\X-Lite\default_user\lista_config.txt",
			'arq_bd'     => "{userprofile}\Configurações locais\Dados de aplicativos\CounterPath Corporation\X-Lite\default_user\contacts.db"
		);
		
		
		$ar_ret['qt_config'] = count($ar_ret['ar_config']);
		

		echo json_encode($ar_ret,JSON_HEX_QUOT);
	}	
}
?>