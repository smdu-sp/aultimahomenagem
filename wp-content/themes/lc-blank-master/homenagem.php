<?php

	/**
	 * Homenagem
	 */
	class Homenagem
	{
		
		function __construct($id, $nome, $data_nascimento, $data_falecimento, $texto_tributo, $epigrafe, $bairro, $foto_url, $nome_homenageador, $email_homenageador, $relacionamento, $publicada, $servidor)
		{
			$this->id = $id;
			$this->nome = $nome;
			$this->data_nascimento = $data_nascimento;
			$this->data_falecimento = $data_falecimento;
			$this->texto_tributo = $texto_tributo;
			$this->epigrafe = $epigrafe;
			$this->bairro = $bairro;
			$this->foto_url = $foto_url;
			$this->nome_homenageador = $nome_homenageador;
			$this->email_homenageador = $email_homenageador;
			$this->relacionamento = $relacionamento;
			$this->publicada = $publicada;
			$this->servidor = $servidor;
		}
	}

	$publicFields = "id, nome, data_nascimento, data_falecimento, texto_tributo, epigrafe, bairro, foto_url, publicada";

	$exemplo = new Homenagem(0,"Maria de Oliveira (exemplo)", "1964-01-01", "2020-01-01", "O que a vida lhe dava de oportunidades, ele agarrava. A luta não lhe\nintimidava. Ele era um homem muito bom, correto. Fazia de tudo para ter o pão de cada dia. Todos o admiravam pelas suas batalhas vencidas. A vida dele era a de poder lutar com o que ele tinha em mãos.\nEle era especial, tinha um jeito muito engraçado por conta de ser \"gordinho\". Uma frase muito engraçada que ele dizia: \"O que que eu fiz pra Eu?\". E saia, cantando pela casa. Aberal nasceu em Pariquera-açu (SP) e faleceu em São Paulo (SP) aos 55 anos vítima do coronavírus.", "O que a vida lhe dava de oportunidades, ele agarrava. A luta não lhe intimidava.", "Sé", "https://aultimahomenagem.prefeitura.sp.gov.br/wp-content/uploads/2020/05/exemplo_foto.jpg", "Fulano de Tal", "fulano@detal.com.br", "Filho", "1", 0);

	function getHomenagens(bool $privados = false, bool $servidores = false)
	{
		global $wpdb;
		global $publicFields;
		$fields = $publicFields;
		$where = "publicada='1' AND servidor='0'";
		if ($servidores) {
			$fields = "*";
			$where = "servidor > 0";
		}
		else if ($privados) {
			$fields = "*";
			$where = "servidor='0'";
		}

		$sql = "SELECT {$fields} FROM homenagens WHERE {$where};";
		$results = $wpdb->get_results($sql, OBJECT);
		return $results;
	}

	function getHomenagem($id, bool $privados = false)
	{
		if (is_null($id)) {			
			return $GLOBALS['exemplo'];
		}
		global $wpdb;
		global $publicFields;
		$fields = $publicFields;
		$where = "publicada=1";

		if ($privados) {
			$fields = "*";
			$where = "1";
		}

		$sql = "SELECT {$fields} FROM homenagens WHERE id={$id} AND {$where};";
		$result = $wpdb->get_results($sql, OBJECT);
		
		if(sizeof($result) == 0)
			return false;
		$result = $result[0];
		$homenagem = new Homenagem(
			$result->id,
			$result->nome,
			$result->data_nascimento,
			$result->data_falecimento,
			$result->texto_tributo,
			$result->epigrafe,
			$result->bairro,
			$result->foto_url,
			$result->nome_homenageador,
			$result->email_homenageador,
			$result->relacionamento,
			$result->publicada,
			$result->servidor
		);
		return $homenagem;
	}

	function moderaHomenagem($id, $moderador, $arquivar = false)
	{
		global $wpdb;

		$acao = $arquivar ? "Arquivou" : "Aprovou";
		$registro = "{$acao} homenagem {$id}";
		$log = array('usuario' => $moderador, 'registro' => $registro);
		$wpdb->insert('logs', $log);

		$valor = $arquivar ? 2 : 1;
		$dados = array('publicada' => $valor);

		$filtroWhere = array('id' => $id);
		$wpdb->update('homenagens', $dados, $filtroWhere);

		if($wpdb->last_error !== '') {
			echo "ERRO: <br><br>";
			echo $wpdb->print_error();
		}
		else
			echo "1";
	}
?>