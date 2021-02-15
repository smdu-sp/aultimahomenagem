<?php
/*
Template Name: Homenagem
*/
require_once('homenagem.php');

get_header(); ?>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<?php
		the_content();
		// Obtenção de dados do banco e armazenamento em json
		$id = $_GET['id'];
		$homenagem = getHomenagem($id, is_user_logged_in());
		if (!$homenagem) {
			echo "Homenagem não encontrada.";
		}
		else{
			echo "<script>const homenagem = ".json_encode($homenagem).";</script>";
			?>
			<script>
				console.log("Script iniciado.");

				const nascimento = new Date(homenagem.data_nascimento);
				const falecimento = new Date(homenagem.data_falecimento);
				const divConteudo = document.querySelector('.conteudo-homenagem');
				const nome = "<h1>"+homenagem.nome+"</h1>";
				const anosBairro = "<h2>"+nascimento.getUTCFullYear()+" - "+falecimento.getUTCFullYear()+" | "+homenagem.bairro+"</h2>";
				const epigrafe = "<h3>&ldquo;"+homenagem.epigrafe+"&rdquo;</h3>";
				// const foto = "<img src='"+homenagem.foto_url+"' title='Retrato'>";
				// Obtém foto a partir do ID do registro
				const foto = "<img src='../wp-content/uploads/2020/fotos/"+homenagem.id+"_c.jpg' title='Retrato' width='340' height='420'>";
				const textoTributo = "<p>"+homenagem.texto_tributo.replace(/\\/g,'').replace(new RegExp('\r?\n','g'), '<br>')+"</p>";
				const conteudoFinal = nome+anosBairro+epigrafe+"<div class='colunas-foto'>"+foto+textoTributo+"</div>";

				divConteudo.innerHTML = conteudoFinal;
			</script>
			<?php
		}		
		?>

	<?php endwhile; ?>
	<?php endif; ?>

<?php get_footer(); ?>
