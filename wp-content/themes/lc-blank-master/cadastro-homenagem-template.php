<?php
/*
Template Name: Cadastro de Homenagem
*/
require_once('homenagem.php');

get_header(); ?>
<script type="text/javascript" src="../wp-content/themes/lc-blank-master/smartcrop.js"></script>

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<?php
		the_content();

		// Formulário para cadastro de homenagem
		if($_SERVER["REQUEST_METHOD"] == "POST") {
			$camposForm = [
				'nome',
				'data_nascimento',
				'data_falecimento',
				'texto_tributo',
				'epigrafe',
				'bairro',
				'nome_homenageador',
				'email_homenageador',
				'relacionamento',
			];
			$foto = $_FILES['foto'];
			$sqlData = [];
			$sql = "INSERT INTO homenagens (";
			foreach ($camposForm as $key => $coluna) {
				// $sql .= $coluna.',';
				$sqlData[$coluna] = $_POST[$coluna];
			}
			$sql = rtrim($sql, ',');
			$sql .= ') VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
			
			// EXTCODE
			global $wpdb;
			$wpdb->show_errors(); 
			// $data = array('nome' => 'Fulano', 'email_homenageador' => 'fljr@gm.ail');
			// var_dump($data);
			// echo "<br>DATA ^^^... SQLDATA vvv<br><br>";
			// var_dump($sqlData);
			
			$wpdb->insert('homenagens',$sqlData);
			$idHomenagem = $wpdb->insert_id;
			
			// ENVIO DE FOTO
			$target_dir = get_template_directory()."/../../uploads/2020/fotos/";			
			$target_file = $target_dir . $idHomenagem;
			$uploadOk = 1;
			$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

						// Check if image file is a actual image or fake image
			if(isset($_POST["submit"])) {
				$check = getimagesize($_FILES["foto"]["tmp_name"]);
				if($check !== false) {
					echo "Arquivo é uma imagem - " . $check["mime"] . ".";
					$uploadOk = 1;
				} else {
					echo "O arquivo não é uma imagem.";
					$uploadOk = 0;
				}
			}

						// Check if file already exists
			if (file_exists($target_file)) {
				echo "Desculpe, não foi possível enviar. Tente enviar outra foto. Se o erro persistir, por favor, envie um e-mail para rmgomes@prefeitura.sp.gov.br e informe este erro.";
				$uploadOk = 0;
			}

						// Check file size
			if ($_FILES["foto"]["size"] > 9000000) {
				echo "Desculpe, o arquivo é muito grande. Por favor, selecione outro arquivo.";
				$uploadOk = 0;
			}
						// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0)
			{
				echo "Desculpe, ocorreu uma falha no envio. Tente novamente com outro arquivo.";
						// if everything is ok, try to upload file
			} else {
				// Envia arquivo e recorta imagem
				function fn_resize($image_resource_id,$width,$height)
				{
					$target_width =480;
					$target_height =640;
					
					$target_layer=imagecreatetruecolor($target_width,$target_height);
					// imagecopyresampled($target_layer,$image_resource_id,0,0,0,0,$target_width,$target_height, $width,$height);
					imagecopyresampled($target_layer,$image_resource_id,0,0,$_POST['crop-x'],$_POST['crop-y'],$target_width,$target_height, $_POST['crop-w'], $_POST['crop-h']);
					return $target_layer;
				}
				
				$file = $_FILES['foto']['tmp_name']; 
				$source_properties = getimagesize($file);
				$image_type = $source_properties[2]; 
				if( $image_type == IMAGETYPE_JPEG ) {
					$image_resource_id = imagecreatefromjpeg($file);  
					$target_layer = fn_resize($image_resource_id,$source_properties[0],$source_properties[1]);
					imagejpeg($target_layer,$target_file . "_c.jpg");
				}
				elseif( $image_type == IMAGETYPE_GIF )  {  
					$image_resource_id = imagecreatefromgif($file);
					$target_layer = fn_resize($image_resource_id,$source_properties[0],$source_properties[1]);
					imagegif($target_layer,$target_file . "_c.gif");
				}
				elseif( $image_type == IMAGETYPE_PNG ) {
					$image_resource_id = imagecreatefrompng($file); 
					$target_layer = fn_resize($image_resource_id,$source_properties[0],$source_properties[1]);
					imagepng($target_layer, $target_file . "_c.png");
				}

				echo "<script>window.alert('Sua homenagem foi enviada com sucesso. Após breve análise, ela será inserida no nosso mural.');
				window.location.replace('https://aultimahomenagem.prefeitura.sp.gov.br');</script>";

				/*if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
					echo "The file ". basename( $_FILES["foto"]["name"]). " has been uploaded.";
				} else {
					echo "Sorry, there was an error uploading your file.";
				}
				*/
			}
			// FIM ENVIO DE FOTO
		}
		?>
		<br>
		<br>
		<form method="post" class="form-homenagem" action="<?php echo get_permalink();?>" enctype="multipart/form-data">
			<label for="nome">Nome completo do homenageado</label><br>
			<input type="text" id="nome" name="nome" placeholder="Ex: Maria Oliveira da Silva" required>
			<br><br>
			<label for="data_nascimento">Data de nascimento</label><br>
			<input type="date" id="data_nascimento" name="data_nascimento" required>
			<br><br>
			<label for="data_falecimento">Data de falecimento</label><br>
			<input type="date" id="data_falecimento" name="data_falecimento" required>
			<br><br>
			<label for="bairro">Bairro em que morava</label><br>
			<input type="text" id="bairro" name="bairro" placeholder="Ex: São Mateus" required>
			<br><br>
			<label for="texto_tributo">Texto tributo<br>
				<span>
					Conte sobre como era a vida do homenageado. Qual era a profissão, hobbies e bairro em que morava. Como era a família? Era casado(a) e tinha filhos? Como você acha que esta pessoa gostaria de ser lembrada? Use quantas palavras achar necessário
				</span>
			</label><br>
			<textarea id="texto_tributo" name="texto_tributo" rows="10" required></textarea>
			<br><br>
			<label for="epigrafe">Epígrafe (110 caracteres)<br>
				<span>
					Uma frase que essa pessoa falava. Um fato interessante sobre ela. Algo que faça as pessoas imaginarem e se conectarem com o homenageado.
				</span>
			</label><br>
			<input type="text" name="epigrafe" id="epigrafe" maxlength="110" required>
			<br><br>

			<label for="foto">Foto</label><br>
			<input type="file" id="foto" name="foto" accept="image/*" required>
			<div id="preview-cropper">
				<img id="preview" src="https://via.placeholder.com/240x320" height="320" style="max-width: unset;" alt="Retrato">
				<input type="hidden" name="crop-x" id="crop-x" value="0">
				<input type="hidden" name="crop-y" id="crop-y" value="0">
				<input type="hidden" name="crop-w" id="crop-w" value="0">
				<input type="hidden" name="crop-h" id="crop-h" value="0">				
			</div>

			<br><br>
			<p>É muito importante sabermos quem prestou a homenagem. Essas informações são sigilosas e não serão divulgadas. Por favor, nos informe:</p>
			<br><br>
			<label for="nome_homenageador">Seu nome</label><br>
			<input type="text" name="nome_homenageador" id="nome_homenageador" placeholder="Ex: Pedro da Silva" required>
			<br><br>
			<label for="email_homenageador">E-mail</label><br>
			<input type="email" name="email_homenageador" id="email_homenageador" placeholder="Ex: pedro.silva@email.com.br" required>
			<br><br>
			<label for="relacionamento">O que você é da pessoa homenageada?<br>
				<span>Exemplos: grau de parentesco (filho, filha, esposa, genro, neta).</span>
			</label><br>
			<input type="text" name="relacionamento" id="relacionamento" placeholder="Ex: Neto" required>
			<br><br>
			<input type="checkbox" class="form-checkbox" name="confirma" id="confirma" required>
			<label for="confirma">Confirmo que tenho autorização para publicar essa homenagem</label>
			<br><br>
			<div class="button-area"><button type="submit" id="submit-bt-overlay"></button><button type="submit">Enviar homenagem</button></div>
		</form>

		<script type="text/javascript">
			const $ = jQuery;
			var imagem = document.querySelector('#preview');
			const minWidth = 240;
			const minHeight = minWidth / 0.75;
			const loadingIcon = "../wp-content/themes/lc-blank-master/loading.gif";

			imagem.style.opacity = 0;

			function readURL(input) {
			  if (input.files && input.files[0]) {
			  	var reader = new FileReader();
			    
			    reader.onload = function(e) {
			    	var anterior = $('#preview').attr('src');
			    	try {
			    		$('#preview').attr('src', e.target.result);
			    	}
			    	catch {
			    		console.error("Erro ao atualizar imagem;");
			    	}
			    	finally {
			    		window.setTimeout(() => {
				    		imagem = document.querySelector('#preview');
				    		imagem.style.opacity = 1;
					      
			    			if (imagem.naturalWidth < minWidth) {
					      	$('#preview').attr('src', anterior);
					      	window.alert("A imagem enviada é muito pequena. Por favor, escolha uma imagem com maior resolução.");
					      	console.error("Imagem muito pequena.");
					      	return;
					      }

	      	      smartcrop.crop(imagem, { width: minWidth, height: minHeight }).then(function(result) {
	      	      	atualizaCrop(result.topCrop.x, result.topCrop.y, result.topCrop.width, result.topCrop.height);
	      				});
				      }, 500);
			    	}
			      /*
			      */
			      // console.log("Imagem: ",imagem);
			      
						
			    }
			    
			    reader.readAsDataURL(input.files[0]); // converte para string base64
			  }
			}

			$("#foto").change(function() {
			  readURL(this);
			});

			// Make the DIV element draggable:
			dragElement(document.getElementById("preview"));

			function dragElement(elmnt) {
			  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
			  if (document.getElementById("preview-cropper")) {
			    document.getElementById("preview-cropper").onmousedown = dragMouseDown;
			  } else {
			    elmnt.onmousedown = dragMouseDown;
			  }

			  function dragMouseDown(e) {
			    e = e || window.event;
			    e.preventDefault();
			    pos3 = e.clientX;
			    pos4 = e.clientY;
			    document.onmouseup = closeDragElement;
			    document.onmousemove = elementDrag;
			  }

			  function elementDrag(e) {
			    e = e || window.event;
			    e.preventDefault();
			    pos1 = pos3 - e.clientX;
			    pos2 = pos4 - e.clientY;
			    pos3 = e.clientX;
			    pos4 = e.clientY;
			    let marginTop = (elmnt.offsetTop - pos4 - e.screenY)*(-0.75);
			    elmnt.style['margin-top'] = (marginTop < 0 ? marginTop : 0) + "px";
			    elmnt.style['margin-left'] = (elmnt.offsetLeft - pos1 < 0 ? elmnt.offsetLeft - pos1 : 0) + "px";
			  }

			  function closeDragElement() {
			    document.onmouseup = null;
			    document.onmousemove = null;
			  }
			}

			function zoom(zoomIn = true) {
				let img = document.querySelector('#preview');
				const zoomStep = 50;
				const minHeight = 320;
				const maxHeight = 3000;

				if(zoomIn) {
					if (img.height + zoomStep > maxHeight)
						return
					img.height += zoomStep;
				}
				else {
					if(img.height - zoomStep < minHeight)
						return
					img.height -= zoomStep;
				}
			}

			function atualizaCrop(cx, cy, cw, ch) {
				document.querySelector('#crop-x').value = cx;
				document.querySelector('#crop-y').value = cy;
				document.querySelector('#crop-w').value = cw;
				document.querySelector('#crop-h').value = ch;
				return;
				const img = document.querySelector('#preview');
				const fatorEscala = img.naturalHeight / img.height;
				var srcWidth = minWidth;
				var srcHeight = minHeight;
				var marginLeft = Math.abs(parseInt(img.style["margin-left"]));
				var marginTop = Math.abs(parseInt(img.style["margin-top"]));

				marginLeft = marginLeft > 0 ? marginLeft*fatorEscala : 0;
				marginTop = marginTop > 0 ? marginTop*fatorEscala : 0;

				// srcWidth *= fatorEscala;
				// srcHeight *= fatorEscala;

				document.querySelector('#left-margin').value = marginLeft.toString();
				document.querySelector('#top-margin').value = marginTop.toString();
			}
			
		</script>

		<style type="text/css">
			.btn-zoom {
				display: inline-block;
		    width: 1em;
		    height: 1em;
		    font-weight: bold;
		    background-color: #944576;
		    color: #ffffff;
		    text-align: center;
		    vertical-align: middle;
		    line-height: 1em;
		    margin: 2px;
		    border-radius: 5px;
		    cursor: pointer;
			}
			.unselectable {
		    -webkit-touch-callout: none;
		    -webkit-user-select: none;
		    -khtml-user-select: none;
		    -moz-user-select: none;
		    -ms-user-select: none;
		    user-select: none;
			}
			#preview-cropper {
				/*width: 240px;*/
				height: 320px;
				/*overflow: hidden;*/
				/*border: 1px solid gray;*/
			}
			#epigrafe {
				width: 90em;
				max-width: calc(100% - 20px);
			}
		</style>

	<?php endwhile; ?>
	<?php endif; ?>

<?php get_footer(); ?>
