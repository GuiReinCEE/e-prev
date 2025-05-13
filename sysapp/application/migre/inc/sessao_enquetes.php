<? 
	session_start();
//   if ( (!session_is_registered("CNPJ")) and (!session_is_registered("RE")) ) {
	if (!session_is_registered("RE")) {
		header('Location: auto_atendimento_erros.php?c=sessaoexpirou');
		exit;
	}
?>