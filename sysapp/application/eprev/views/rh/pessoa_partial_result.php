			<!-- BARRA DE PAGINAÇÃO -->
			<div id="pagination_bar" class="pagination-bar-top">
				<?= $this->pagination->create_links() ?>
			</div>
			<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
		    	<thead>
				<tr>
					<td><b>Cód</b></td>
					<td><b>Nome</b></td>
					<td><b>Divisão</b></td>
					<td><b>Nome Usual</b></td>
					<td><b>Usuário</b></td>
					<td><b>Papel</b></td>
				</tr>
		    	</thead>
				<tbody>
					<? foreach( $usuarios as $usuario ): ?>
						<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
						<td align="center"><?= $usuario->codigo ?></td>
						<td align="center"><?= $usuario->nome ?></td>
						<td align="center"><?= $usuario->divisao ?></td>
						<td align="center"><?= $usuario->guerra ?></td>
						<td align="center"><?= $usuario->usuario ?></td>
						<td align="center">
							<?
							switch( $usuario->tipo )
							{
								case 'D' : echo 'Diretoria Executiva'; break;
								case 'G' : echo 'Gerente'; break;
								case 'U' : echo 'Colaborador'; break;
								case 'N' : echo 'Colaborador'; break;
								case 'P' : echo 'Prestador de Serviços'; break;
								case 'E' : echo 'Estagiário'; break;
							}
							?>
						</td>
						</tr>
					<? endforeach; ?>
				</tbody>
			</table>
			<!-- BARRA DE PAGINAÇÃO -->
			<div id="pagination_bar" class="pagination-bar-bottom">
				<?= $this->pagination->create_links(); ?>
				<br />
			</div>