<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();

	include_once('auto_atendimento_monta_sessao.php');

	$qr_sql = "
		INSERT INTO public.log_acessos_usuario 
			 (
			   sid,
			   hora,
			   pagina
			 ) 
		VALUES
			 (
			   ".$_SESSION['SID'].",
			   CURRENT_TIMESTAMP,
			   'AUTORIZACAO_DESCONTO_ENTIDADES_GRAVA'
			 );";
	@pg_query($db,$qr_sql);

	$list = $_POST['arr'];
	
	#echo "<PRE>"; print_r($_POST); EXIT;

	foreach ($list as $key => $item) 
	{
		if(trim($_POST[$item.'_alt']) == 'S')
		{
			$array = explode('_', $item);

			$cod_recolhimento = $array[0];
			$cd_verba         = $array[1];
			$fl_opcao         = $_POST[$item];

			/*
			echo 'CD RECOLHIMENTO : '.$array[0]."<br/>";
			echo 'CD VERBA : '.$array[1]."<br/>";
			
			echo 'SELEÇÂO : '.$_POST[$item]."<br/>";

			echo 'RECOLHIMENTO : '.$_POST[$array[0].'_recolhimento']."<br/>";
			echo 'VERBA : '.$_POST[$array[1].'_verba'] ."<br/>";
			
			echo $item." - ".$_POST[$item]."<br/>";
			*/

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/set_autorizacao_verba");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&cod_recolhimento=".$cod_recolhimento."&cd_verba=".$cd_verba."&fl_opcao=".$fl_opcao);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$_RETORNO = curl_exec($ch);
			curl_close ($ch);

			#print_r($_RETORNO); echo "<HR>";
			
			$FL_RETORNO = TRUE;
			$_RETORNO = json_decode($_RETORNO, TRUE);
			if (!(json_last_error() === JSON_ERROR_NONE))
			{
				switch (json_last_error()) 
				{
					case JSON_ERROR_NONE:
						$FL_RETORNO = TRUE;
					break;
						default:
						$FL_RETORNO = FALSE;
					break;
				}
			}

			if($FL_RETORNO)
			{
				if(intval($_RETORNO['error']['status']) == 1)
				{
					echo "
						<script>
							alert('Desculpe, mas não foi possível efetuar sua solicitação.\\n\\nTente novamente mais tarde.\\n\\nObrigado');
							document.location.href = 'auto_atendimento_desconto_entidades.php';
						</script>";
					exit;
				}
			}
		}
	}

	echo "
		<script>
			alert('Alterações efetuadas.\\n\\nObrigado');
			document.location.href = 'auto_atendimento_desconto_entidades.php';
		</script>";
	exit;