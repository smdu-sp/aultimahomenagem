<?php
/*
Template Name: Painel administrativo
*/
require_once('homenagem.php');

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$moderador = wp_get_current_user();
	moderaHomenagem($_POST['id'], $moderador->data->user_login, $_POST['arquivar']);
	return 200;
}

get_header(); 
if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<?php
		the_content();

		$vue = get_site_url() == 'https://aultimahomenagem.prefeitura.sp.gov.br' ? 'vue.min.js' : 'vue.js';

		$localhosting = get_site_url() == 'http://localhost/www/aultimahomenagem';
		// include local
			echo "<script type='text/javascript' src='../wp-content/themes/lc-blank-master/{$vue}'></script>";
			echo "<script type='text/javascript' src='../wp-content/themes/lc-blank-master/axios.min.js'></script>";

 ?>
 <!-- BOOTSTRAP -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


<div id="app">
	<transition name="fade">
		<div id="loading" class="alert alert-warning" v-if="loading">Carregando lista de homenagens...</div>
	</transition>
	<!-- TÍTULO DA TABELA HOMENAGENS: PENDENTES, APROVADAS, ARQUIVADAS -->
	<div class="align-center pb-4">
		<h1>Lista de homenagens {{ publicadas == 0 ? 'pendentes' : '' }}{{ publicadas == 1 ? 'publicadas' : '' }}{{ publicadas == 2 ? 'arquivadas' : '' }}</h1>
		<br>
		<button class="btn btn-lg btn-warning" :disabled="publicadas == 0" @click="publicadas = 0">Pendentes</button>
		<button class="btn btn-lg btn-success" :disabled="publicadas == 1" @click="publicadas = 1">Publicadas</button>
		<button class="btn btn-lg btn-danger" :disabled="publicadas == 2" @click="publicadas = 2">Arquivadas</button>
	</div>
	<transition name="fade">		
		<table class="table table-striped" v-if="!loading">
			<tr>
				<th>Foto</th>
				<th v-for="(prop, index) in homenagens[0]" v-if="index !== 'foto_url'" scope="col">{{index}}</th>
				<th>Aprovar</th>
			</tr>
			<tr v-for="(homenagem, index) in homenagens" v-if="homenagem.publicada == publicadas">
				<td style="width: 120px; min-width: 120px"><img :src="'../wp-content/uploads/2020/fotos/'+homenagem.id+'_c.jpg'" width="120" height="160"></td>
				<td v-for="(prop, index) in homenagem" v-if="index !== 'foto_url'">{{prop}}</td>
				<td>
					<button class="btn btn-success" @click="moderaHomenagem(homenagem.id)">&#10004;</button>
					<button class="btn btn-danger" @click="moderaHomenagem(homenagem.id, true)">&times;</button>
				</td>
			</tr>
		</table>
	</transition>
</div>

<script type="text/javascript">
	const listaHomenagens = "<?php echo $localhosting ? './lista-homenagens/' : '/lista-homenagens/'; ?>";

	var app = new Vue({
		el: '#app',
		data: {
			paginaHomenagem: './homenagem/?id=',
			fotoSrc: './wp-content/uploads/2020/fotos/',
			homenagens: [],
			homenagemAtual: '',
			loading: true,
			publicadas: 0
		},
		methods: {
			selecionaHomenagem: function(event, homenagem) {
				this.hover = true
				this.homenagemAtual = homenagem
			},
			moderaHomenagem: function(id, arquivar = false) {				
				let formData = new FormData
				formData.append('id', id)
				formData.append('arquivar', arquivar ? '1' : '0')

				axios
					.post("<?=get_permalink()?>", formData)
					.then(response => {
						console.log(response.data)
					})
					.catch(error => {
					console.error("ERRO AO MODERAR HOMENAGEM")
					console.log(error)
				})
				.finally(() => {
					for(i in this.homenagens) {
						if (this.homenagens[i].id === id) {
							this.homenagens[i].publicada = arquivar ? "2" : "1"
							return
						}
					}
				})

			}
		},
		mounted() {
			axios
				.get(listaHomenagens)
				.then(response => {
					this.homenagens = response.data.homenagens
				})
				.catch(error => {
					console.error("ERRO AO OBTER HOMENAGENS")
					console.log(error)
				})
				.finally(() => this.loading = false)
		}
	})
</script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<style type="text/css">
	#app {
		margin: auto;
		/*max-width: 960px;*/
		font-size: 16px;
	}
	#app td,th {
		font-size: 12px;
		/*padding: 1em;*/
	}
	#loading {
		margin: auto;
	}
	.fade-enter-active, .fade-leave-active {
	  transition: opacity .5s;
	}
	.fade-enter, .fade-leave-to /* .fade-leave-active em versões anteriores a 2.1.8 */ {
	  opacity: 0;
	}
	h1 {
		font-family: 'GeosansLight';
	}
</style>
<?php endwhile; ?>
	<?php endif; ?>
<?php 
get_footer();
?>
