			<!-- BARRA DE PAGINAÇÃO -->
			Quantidade: <?php echo $quantos; ?>
			<div id="pagination_bar" class="pagination-bar-top">
				<?= $this->pagination->create_links() ?>
			</div>
			<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
		    	<thead>
				<tr>
					<td><b>Código</b></td>
					<td><b>Data de solicitação</b></td>
					<td><b>Situação</b></td>
					<td><b>De</b></td>
					<td><b>Destinos</b></td>
					<td><b>RE</b></td>
					<td><b>Assunto</b></td>
					<td><b>Data de envio</b></td>
				</tr>
		    	</thead>
				<tbody>
					<? foreach( $itens as $item ): ?>
						<?php $link = base_url() . "../controle_projetos/cad_envia_emails.php?op=A&c=$item->cd_email"; ?>
						<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
							<td align="center"><?php echo anchor($link, $item->cd_email, array("target"=>"_blank")); ?></td>
							<td align="center"><?php echo $item->dt_envio; ?></td>
							<td align="center"><?php if( $item->dt_retorno!="" ) echo "<font color='red'><b>retornou</b></font>"; else if( $item->dt_email_enviado!="" ) echo "enviado"; else echo "aguarda envio"; ?></td>
							<td align="center"><?php echo $item->de; ?></td>
							<td align="center"><?php echo $item->para . ' ' . $item->cc; ?></td>
							<td align="center"><?php echo $item->cd_registro_empregado; ?></td>
							<td align="center"><?php echo $item->assunto; ?></td>
							<td align="center"><?php echo $item->dt_email_enviado; ?></td>
						</tr>
					<? endforeach; ?>
				</tbody>
			</table>
			<!-- BARRA DE PAGINAÇÃO -->
			<div id="pagination_bar" class="pagination-bar-bottom">
				<?= $this->pagination->create_links(); ?>
				<br />
			</div>