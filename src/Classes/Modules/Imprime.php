<?php

/*#######################################################
|														|
| Arquivo com a classe das impressões					|
|														|
| Esta classe herda as variáveis e métodos da classe	|
| Crud. A documentação dos métodos está na classe pai 	|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Modules;

/*### CLASSE DAS IMPRESSÕES (HERDA CRUD) ###*/
class Imprime extends \Database\Crud {

	protected $DynamicTable;
	protected $Path;
	protected $PDFClass;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];

		//ARMAZENA AS VARIÁVEIS DOS CAMINHOS DAS PASTAS E HTTP
		$this->Path = array("AbsolutePath" => $container['AbsolutePath'],
							"RelativePath" => $container['RelativePath'],
							"HttpReferer" => $container['HttpReferer']);

		//CHAMADA PARA CLASSE PDF
		$this->PDFClass = $container["PDF"];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/





	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/





	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/
	public function MaskInsertUpdateValues () {

	}
	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/



	/*### MONTA O SQL QUE SERÁ EXECUTADO, ARMAZENANDO NA SQLAction ###*/
	public function BuildSqlAction() {

		/*### SE A ORIGEM FOR O PRÉ-CADASTRO ###*/
		if($this->Request["origem"]=="pre-cadastro")
			{
				if($this->Request["AtendimentoSemSenha"]==1) {

					$SQLAction = "SELECT DISTINCT
											    PESS.Pess_id,
											    PESS.Pess_Nome,
												PESS.Pess_CPF,
											    EVEN.Even_id,
											    EVEN.Even_Titulo,
											    CONCAT(1,LPAD(EVEN.Even_id,4,'0'),LPAD(PESS.Pess_id,7,'0')) AS Pess_Barcode
											FROM
												Pessoas PESS

											INNER JOIN
												(SELECT Eventos.Even_id,
														Eventos.Even_Titulo,
														".$this->PrimaryKey." AS Pessoas_Pess_id
													FROM
														Eventos
													WHERE
														Eventos.Even_id = ".$this->Request['Even_id']."
														AND Eventos.Even_Delete = 0) EVEN ON EVEN.Pessoas_Pess_id = PESS.Pess_id

											WHERE
											    PESS.Pess_id = ".$this->PrimaryKey."
											    AND PESS.Pess_Delete = 0";
				}
				else {

					$SQLAction = "SELECT DISTINCT
											    PESS.Pess_id,
											    PESS.Pess_Nome,
												PESS.Pess_CPF,
											    SERV.Serv_id,
											    SERV.Serv_Titulo,
												PARC.Parc_id,
											    PARC.Parc_Razao_Nome,
											    EVEN.Even_id,
											    EVEN.Even_Titulo,
											    ATEN.Aten_id,
											    ATEN.Aten_Pid,
											    ATEN.Aten_Data,
											    ATEN.Aten_SenhaNumero,
											    ATEN.Aten_Status,
											    STATUS_ATEN.TabeDinaValo_Descricao AS Aten_Status_Nome,
											    CONCAT(1,LPAD(EVEN.Even_id,4,'0'),LPAD(PESS.Pess_id,7,'0')) AS Pess_Barcode,
											    CONCAT(2,LPAD(ATEN.Aten_id,11,'0')) AS Aten_Barcode
											FROM
											    Atendimentos ATEN

											INNER JOIN
												ParceirosServicos PARCSERV ON PARCSERV.ParcServ_id = ATEN.ParceirosServicos_ParcServ_id
																			AND PARCSERV.ParcServ_Delete = 0

											INNER JOIN
												Servicos SERV ON SERV.Serv_id = PARCSERV.Servicos_Serv_id
																AND SERV.Serv_Delete = 0

											INNER JOIN
												Pessoas PESS ON PESS.Pess_id = ATEN.Pessoas_Pess_id
																AND PESS.Pess_Delete = 0

											INNER JOIN
												Parceiros PARC ON PARC.Parc_id = PARCSERV.Parceiros_Parc_id
																AND PARC.Parc_Delete = 0

											INNER JOIN
												Eventos EVEN ON EVEN.Even_id = PARCSERV.Eventos_Even_id
																AND EVEN.Even_Delete = 0

											INNER JOIN
												TabelasDinamicasValores STATUS_ATEN ON STATUS_ATEN.TabeDinaValo_Codigo = ATEN.Aten_Status
																					AND STATUS_ATEN.TabeDinaValo_Tabela = 'STATUS_ATENDIMENTOS'
																					AND STATUS_ATEN.TabeDinaValo_Delete = 0

											WHERE
											    ATEN.Pessoas_Pess_id = '".$this->PrimaryKey."'
											    AND EVEN.Even_id = '".$this->Request['Even_id']."'
											    AND ATEN.Aten_Delete = 0
											    AND ATEN.Aten_Status NOT IN (99)
											    AND SERV.Serv_Senha = 1

											ORDER BY
												Aten_Data,
											    Parc_Razao_Nome,
											    Serv_Titulo";
				}

				$this->setSQLAction($SQLAction);
			}
		/*### SE A ORIGEM FOR O PRÉ-CADASTRO ###*/








		/*### SE A ORIGEM FOR O ATENDIMENTO ###*/
		elseif($this->Request["origem"]=="atendimento")
			{
				$SQLAction = "SELECT
									Pessoas.Pess_id,
									Pessoas.Pess_Nome,
									Pessoas.Pess_CPF,
									Pessoas.Pess_RG,
									Pessoas.Pess_DtNascimento,
									SEXO.TabeDinaValo_Descricao AS Pess_Sexo,
									Pessoas.Pess_Endereco_Logradouro,
									Pessoas.Pess_Endereco_Numero,
									Pessoas.Pess_Endereco_Complemento,
									Pessoas.Pess_Endereco_Bairro,
									Pessoas.Pess_Endereco_CEP,
									Pessoas.Pess_Endereco_Cidade,
									Pessoas.Pess_Endereco_UF,
									Pessoas.Pess_Celular,
									Pessoas.Pess_Telefone,
									Pessoas.Pess_Email,
									Parceiros.Parc_Razao_Nome,
									Servicos.Serv_Titulo,
									Eventos.Even_Titulo,
									Atendimentos.Aten_id,
									Atendimentos.Aten_Data,
									Atendimentos.Aten_Hora_Inicio,
									Atendimentos.Aten_Hora_Atend,
									Atendimentos.Aten_Hora_Fim,
									Atendimentos.Aten_SenhaNumero,
								    ParceirosFichas.ParcFich_Titulo,
								    ParceirosFichasPerguntas.ParcFichPerg_id,
								    ParceirosFichasPerguntas.ParcFichPerg_Sequencia,
								    ParceirosFichasPerguntas.ParcFichPerg_Pergunta,
								    ParceirosFichasPerguntas.ParcFichPerg_Tipo,
								    (CASE
								    	WHEN AtendimentosRespostas.AtenResp_Resposta IS NOT NULL AND ParceirosFichasPerguntas.ParcFichPerg_Tipo = 'sim-nao' AND AtendimentosRespostas.AtenResp_Resposta = 0
								    		THEN 'NÃO'
								    	WHEN AtendimentosRespostas.AtenResp_Resposta IS NOT NULL AND ParceirosFichasPerguntas.ParcFichPerg_Tipo = 'sim-nao' AND AtendimentosRespostas.AtenResp_Resposta = 1
								    		THEN 'SIM'
								    	WHEN AtendimentosRespostas.AtenResp_Resposta IS NOT NULL AND ParceirosFichasPerguntas.ParcFichPerg_Tipo IN ('alternativas','multipla')
								    		THEN ParcFichPergOpcoes.PFPOpc_Opcao
								    	WHEN AtendimentosRespostas.AtenResp_Resposta IS NOT NULL AND ParceirosFichasPerguntas.ParcFichPerg_Tipo = 'condicional'
								    		THEN
								    			CASE
								    				WHEN ParcFichPergOpcoes.PFPOpc_Condicao <> ''
														THEN CONCAT(ParcFichPergOpcoes.PFPOpc_Opcao, ' - ', ParcFichPergOpcoes.PFPOpc_Condicao, ' R: ', AtendimentosRespostas.AtenResp_Condicao)
													ELSE ParcFichPergOpcoes.PFPOpc_Opcao
								    			END
								    	ELSE AtendimentosRespostas.AtenResp_Resposta
								    END) AS Resposta,
								    CONCAT(1,LPAD(Eventos.Even_id,4,'0'),LPAD(Pessoas.Pess_id,7,'0')) AS Pess_Barcode,
								    CONCAT(2,LPAD(Atendimentos.Aten_id,11,'0')) AS Aten_Barcode
								FROM
									Atendimentos

								INNER JOIN
									Pessoas ON Pessoas.Pess_id = Atendimentos.Pessoas_Pess_id
											AND Pessoas.Pess_Delete = 0

								INNER JOIN
									ParceirosServicos ON ParceirosServicos.ParcServ_id = Atendimentos.ParceirosServicos_ParcServ_id
														AND ParceirosServicos.ParcServ_Delete = 0

								INNER JOIN
									Servicos ON Servicos.Serv_id = ParceirosServicos.Servicos_Serv_id
													AND Servicos.Serv_Delete = 0

								INNER JOIN
									Parceiros ON Parceiros.Parc_id = ParceirosServicos.Parceiros_Parc_id
													AND Parceiros.Parc_Delete = 0

								INNER JOIN
									TabelasDinamicasValores SEXO ON SEXO.TabeDinaValo_Codigo = Pessoas.Pess_Sexo
																	AND SEXO.TabeDinaValo_Tabela = 'SEXO'
																	AND SEXO.TabeDinaValo_Delete = 0

								INNER JOIN
									Eventos ON Eventos.Even_id = ParceirosServicos.Eventos_Even_id
													AND Eventos.Even_Delete = 0

								LEFT JOIN
								    ParceirosFichas ON ParceirosFichas.ParcFich_id = Atendimentos.ParceirosFichas_ParcFich_id
													AND ParceirosFichas.ParcFich_Delete = 0

								LEFT JOIN
								    ParceirosFichasPerguntas ON ParceirosFichasPerguntas.ParceirosFichas_ParcFich_id = ParceirosFichas.ParcFich_id
																AND ParceirosFichasPerguntas.ParcFichPerg_Delete = 0

								LEFT JOIN
									AtendimentosRespostas ON AtendimentosRespostas.Atendimentos_Aten_id = Atendimentos.Aten_id
															AND AtendimentosRespostas.ParceirosFichasPerguntas_ParcFichPerg_id = ParceirosFichasPerguntas.ParcFichPerg_id
															AND AtendimentosRespostas.AtenResp_Delete = 0

								LEFT JOIN
									ParcFichPergOpcoes ON ParcFichPergOpcoes.ParceirosFichasPerguntas_ParcFichPerg_id = AtendimentosRespostas.ParceirosFichasPerguntas_ParcFichPerg_id
														AND ParcFichPergOpcoes.PFPOpc_id = AtendimentosRespostas.AtenResp_Resposta

								WHERE
								    Atendimentos.Aten_id = '".$this->PrimaryKey."'
								    AND Atendimentos.Aten_Delete = 0
								    AND Atendimentos.Aten_Status NOT IN (99)

								ORDER BY
									ParcFichPerg_Sequencia,
									PFPOpc_Sequencia";

				$this->setSQLAction($SQLAction);
			}
		/*### SE A ORIGEM FOR O ATENDIMENTO ###*/









		/*### SE A ORIGEM FOR A FICHA DE ATENDIMENTO ###*/
		elseif($this->Request["origem"]=="parceiros-fichas")
			{
				$SQLAction = "SELECT DISTINCT
									Parceiros.Parc_Razao_Nome,
									Servicos.Serv_Titulo,
								    ParceirosFichas.ParcFich_Titulo,
								    ParceirosFichasPerguntas.ParcFichPerg_id,
								    ParceirosFichasPerguntas.ParcFichPerg_Sequencia,
								    ParceirosFichasPerguntas.ParcFichPerg_Pergunta,
								    ParceirosFichasPerguntas.ParcFichPerg_Tipo,
								    ParcFichPergOpcoes.PFPOpc_Opcao,
								    ParcFichPergOpcoes.PFPOpc_Condicao
								FROM
								    ParceirosFichasPerguntas

								INNER JOIN
								    ParceirosFichas ON ParceirosFichas.ParcFich_id = ParceirosFichasPerguntas.ParceirosFichas_ParcFich_id
													AND ParceirosFichas.ParcFich_Delete = 0

								LEFT JOIN
									ParcFichPergOpcoes ON ParcFichPergOpcoes.ParceirosFichasPerguntas_ParcFichPerg_id = ParceirosFichasPerguntas.ParcFichPerg_id
														AND ParcFichPergOpcoes.PFPOpc_Delete = 0

								INNER JOIN
									Servicos ON Servicos.Serv_id = ParceirosFichas.Servicos_Serv_id
													AND Servicos.Serv_Delete = 0

								INNER JOIN
									Parceiros ON Parceiros.Parc_id = ParceirosFichas.Parceiros_Parc_id
													AND Parceiros.Parc_Delete = 0

								WHERE
								    ParceirosFichas.ParcFich_id = '".$this->PrimaryKey."'
								    AND ParceirosFichasPerguntas.ParcFichPerg_Delete = 0

								ORDER BY
									ParcFichPerg_Sequencia,
									PFPOpc_Sequencia";

				$this->setSQLAction($SQLAction);
			}
		/*### SE A ORIGEM FOR A FICHA DE ATENDIMENTO ###*/









	}
	/*### MONTA O SQL QUE SERÁ EXECUTADO, ARMAZENANDO NA SQLAction ###*/



	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/
	public function AfterExecuteAction () {

	}
	/*### EXECUTA AÇÕES DEPOIS DO MÉTODO ExecuteAction ###*/


	/*### RETORNA O RESULTADO DA ExecuteAction CONFORME A ACTION ###*/
	public function ReturnAction() {

		//RETORNA O TOKEN
		$response['token'] = $this->TokenClass->getToken();

		$ResultSQLAction = $this->ResultSQLAction->fetchAll(\PDO::FETCH_OBJ);







		/*### LAYOUT DE IMPRESSÃO DO PRÉ-CADASTRO ###*/
		if($this->Request["origem"]=="pre-cadastro") {

			$response["logo"] = "logo-topo.png";

			foreach ($ResultSQLAction as $key => $AtenObject) {

				$response["Pess_id"] = $AtenObject->pess_id;
				$response["Pessoa"] = $AtenObject->pess_nome;
				$response["CPF"] = $this->MaskValue->Cpf($AtenObject->pess_cpf,'add');
				$response["PessoaBarcode"] = $AtenObject->pess_barcode;
				$response["Evento"] = $AtenObject->even_titulo;
				$response["Parceiro"][$AtenObject->aten_id] = $AtenObject->parc_razao_nome;
				$response["Servico"][$AtenObject->aten_id] = $AtenObject->serv_titulo;
				$response["Senha"][$AtenObject->aten_id] = str_pad($AtenObject->aten_senhanumero,3,0,STR_PAD_LEFT);
				$response["Data"][$AtenObject->aten_id] = $this->MaskValue->Data($AtenObject->aten_data,'US2BR_TIME');
				$response["Status"][$AtenObject->aten_id] = $AtenObject->aten_status;
				$response["StatusNome"][$AtenObject->aten_id] = $AtenObject->aten_status_nome;
				$response["AtenBarcode"][$AtenObject->aten_id] = $AtenObject->aten_barcode;
			}

			$response["html"] = file_get_contents("../../lib/html-templates/pre-cadastro.html");

			$response["html"] = str_replace("%%logo%%","http://".$this->Path["HttpReferer"]."/images/".$response["logo"],$response["html"]);
			$response["html"] = str_replace("%%Evento%%","<strong>".$response["Evento"]."</strong>",$response["html"]);

			$response["html"] = str_replace("%%DadosPessoa%%","<strong>Código: </strong>".$response["Pess_id"]
																."<br><strong>Nome: </strong>".$response["Pessoa"]
																."<br><strong>CPF: </strong>".$response["CPF"]
																."<br><img src='src/Controller/Controller.php?Route=Barcode&Type=ean13&Return=image&Value=".$response["PessoaBarcode"]."&Token=".$response['token']."'/>",$response["html"]);
			if($this->Request["AtendimentoSemSenha"]<>1) {

				foreach ($response["Parceiro"] as $Aten_id => $Parceiro) {
					$DadosAtendimentos .= "<p>";
					$DadosAtendimentos .= "<br><span class='AtenParceiro'><strong>Parceiro: </strong>".$Parceiro."</span>";
					$DadosAtendimentos .= "<br><span class='AtenServico'><strong>Serviço: </strong>".$response["Servico"][$Aten_id]."</span>";
					$DadosAtendimentos .= "<br><span class='AtenData'><strong>Data/Hora: </strong>".$response["Data"][$Aten_id]."</span>";
					if($response["Status"][$Aten_id]>0) $DadosAtendimentos .= "<br><span class='AtenStatus'><strong>".$response["StatusNome"][$Aten_id]."</strong></span>";
					$DadosAtendimentos .= "<br><span class='AtenSenha'><strong>Senha: </strong>".$response["Senha"][$Aten_id]."</span>";
					$DadosAtendimentos .= "<br><img src='src/Controller/Controller.php?Route=Barcode&Type=ean13&Return=image&Value=".$response["AtenBarcode"][$Aten_id]."&Token=".$response['token']."'/>";
					$DadosAtendimentos .= "<p>";
				}

			}

			$response["html"] = str_replace("%%DadosAtendimentos%%",$DadosAtendimentos,$response["html"]);

			$response["html"] = str_replace("%%Rodape%%","Emitido em ".$this->Date["NowBR"]." por ".$this->TokenClass->getClaim("UserData")->Usua_PrimeiroUltimoNome,$response["html"]);
		}
		/*### LAYOUT DE IMPRESSÃO DO PRÉ-CADASTRO ###*/














		/*### LAYOUT DE IMPRESSÃO DO ATENDIMENTO E FICHA DE ATENDIMENTO ###*/
		elseif(($this->Request["origem"]=="atendimento")||($this->Request["origem"]=="parceiros-fichas")) {

			$response["logo"] = "logo-topo.png";

			foreach ($ResultSQLAction as $key => $AtenObject) {

				if($this->Request["origem"]=="atendimento") {

					$response["Pess_id"] = $AtenObject->pess_id;
					$response["Pess_Nome"] = $AtenObject->pess_nome;
					$response["Pess_CPF"] = $this->MaskValue->Cpf($AtenObject->pess_cpf,'add');
					$response["Pess_RG"] = $AtenObject->pess_rg;
					$response["Pess_DtNascimento"] = $this->MaskValue->Data($AtenObject->pess_dtnascimento,'US2BR');
					$response["Pess_Sexo"] = $AtenObject->pess_sexo;
					$response["Pess_Endereco_Logradouro"] = $AtenObject->pess_endereco_logradouro;
					$response["Pess_Endereco_Numero"] = $AtenObject->pess_endereco_numero;
					$response["Pess_Endereco_Complemento"] = $AtenObject->pess_endereco_complemento;
					$response["Pess_Endereco_Bairro"] = $AtenObject->pess_endereco_bairro;
					$response["Pess_Endereco_CEP"] = $this->MaskValue->Cep($AtenObject->pess_endereco_cep,'add');
					$response["Pess_Endereco_Cidade"] = $AtenObject->pess_endereco_cidade;
					$response["Pess_Endereco_UF"] = $AtenObject->pess_endereco_uf;
					$response["Cidade_UF"] = $response["Pess_Endereco_Cidade"]."/".$response["Pess_Endereco_UF"];
					$response["Pess_Telefone"] = $this->MaskValue->Telefone($AtenObject->pess_telefone,'add'); ;
					$response["Pess_Celular"] = $this->MaskValue->Telefone($AtenObject->pess_celular,'add'); ;
					$response["Pess_Email"] = $AtenObject->pess_email;
					$response["Even_Titulo"] = $AtenObject->even_titulo;
					$response["Aten_id"] = $AtenObject->aten_id;
					$response["Aten_Data"] = $this->MaskValue->Data($AtenObject->aten_data,'US2BR');
					$response["Aten_Hora_Inicio"] = $AtenObject->aten_hora_inicio;
					$response["Aten_Hora_Atend"] = $AtenObject->aten_hora_atend;
					$response["Aten_Hora_Fim"] = $AtenObject->aten_hora_fim;
					$response["Aten_SenhaNumero"] = str_pad($AtenObject->aten_senhanumero,3,0,STR_PAD_LEFT);

					$response["PessoaBarcode"] = $AtenObject->pess_barcode;
					$response["AtenBarcode"] = $AtenObject->aten_barcode;
				}

				$response["Parc_Razao_Nome"] = $AtenObject->parc_razao_nome;
				$response["Serv_Titulo"] = $AtenObject->serv_titulo;

				$response["ParcFich_Titulo"] = $AtenObject->parcfich_titulo;

				$response["Questionario"][$AtenObject->parcfichperg_id]["ParcFichPerg_id"] = $AtenObject->parcfichperg_id;
				$response["Questionario"][$AtenObject->parcfichperg_id]["ParcFichPerg_Sequencia"] = str_pad($AtenObject->parcfichperg_sequencia,2,0,STR_PAD_LEFT);
				$response["Questionario"][$AtenObject->parcfichperg_id]["ParcFichPerg_Pergunta"] = $AtenObject->parcfichperg_pergunta;
				$response["Questionario"][$AtenObject->parcfichperg_id]["ParcFichPerg_Tipo"] = $AtenObject->parcfichperg_tipo;

				if($this->Request["origem"]=="atendimento") {

					$response["Questionario"][$AtenObject->parcfichperg_id]["Resposta"][] = $AtenObject->resposta;
				}
				elseif($this->Request["origem"]=="parceiros-fichas") {

					$response["Even_Titulo"] = "EVENTO: ________________________________________";

					$response["Questionario"][$AtenObject->parcfichperg_id]["Opcoes"][] = $AtenObject->pfpopc_opcao;
					$response["Questionario"][$AtenObject->parcfichperg_id]["Condicoes"][] = $AtenObject->pfpopc_condicao;
				}

			}

			$response["Content"] = file_get_contents("../../lib/html-templates/ficha-atendimento.html");

			$Endereco = $response["Pess_Endereco_Logradouro"];
			if($response["Pess_Endereco_Numero"]<>"") $Endereco .= ", ".$response["Pess_Endereco_Numero"];
			if($response["Pess_Endereco_Complemento"]<>"") $Endereco .= " - ".$response["Pess_Endereco_Complemento"];
			if($response["Pess_Endereco_Bairro"]<>"") $Endereco .= " - ".$response["Pess_Endereco_Bairro"];

			$response["Content"] = str_replace("%%DadosPessoa%%","<div class='row' style='font-size: 11px;'>"
																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Código: </span>".$response["Pess_id"]."</div>"
																."<div class='col-xs-9 paddingficha'><span style='font-weight:bold;'>Nome: </span>".$response["Pess_Nome"]."</div>"
																."</div>"

																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-3 paddingficha'><span style='font-weight:bold;'>CPF: </span>".$response["Pess_CPF"]."</div>"
																."<div class='col-xs-3 paddingficha'><span style='font-weight:bold;'>RG: </span>".$response["Pess_RG"]."</div>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Dt. Nasc: </span>".$response["Pess_DtNascimento"]."</div>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Sexo: </span>".$response["Pess_Sexo"]."</div>"
																."</div>"

																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-11 paddingficha'><span style='font-weight:bold;'>Endereço: </span>".$Endereco."</div>"
																."</div>"

																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-1 paddingficha'><span style='font-weight:bold;'>CEP: </span>".$response["Pess_Endereco_CEP"]."</div>"
																."<div class='col-xs-4 paddingficha'><span style='font-weight:bold;'>Cidade/UF: </span>".$response["Cidade_UF"]."</div>"
																."<div class='col-xs-3 paddingficha'><span style='font-weight:bold;'>Telefone: </span>".$response["Pess_Telefone"]."</div>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Celular: </span>".$response["Pess_Celular"]."</div>"
																."</div>"

																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-11 paddingficha'><span style='font-weight:bold;'>E-mail: </span>".$response["Pess_Email"]."</div>"
																."</div>"
																."</div>",$response["Content"]);

			$response["Content"] = str_replace("%%DadosAtendimento%%","<div class='row' style='font-size: 11px;'>"
																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-4 paddingficha'><span style='font-weight:bold;'>Parceiro: </span>".$response["Parc_Razao_Nome"]."</div>"
																."<div class='col-xs-4 paddingficha'><span style='font-weight:bold;'>Serviço: </span>".$response["Serv_Titulo"]."</div>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Senha: </span>".$response["Aten_SenhaNumero"]."</div>"
																."</div>"

																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Data: </span>".$response["Aten_Data"]."</div>"
																."<div class='col-xs-3 paddingficha'><span style='font-weight:bold;'>Pré-atendimento: </span>".$response["Aten_Hora_Inicio"]."</div>"
																."<div class='col-xs-3 paddingficha'><span style='font-weight:bold;'>Atendimento: </span>".$response["Aten_Hora_Atend"]."</div>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Saída: </span>".$response["Aten_Hora_Fim"]."</div>"
																."</div>"
																."</div>",$response["Content"]);


			if(count($response["Questionario"])>0) {

				foreach ($response["Questionario"] as $ParcFichPerg_id => $DadosQuestionario) {
					// print_r($DadosQuestionario);
					if($DadosQuestionario["ParcFichPerg_Sequencia"]>0) {

						$FichaAtendimento .= "<div class='row' style='font-size:11px; margin-bottom:15px; '>";
						$FichaAtendimento .= "<div style='font-size:12px; font-weight: bold; margin-bottom:6px;'>".$DadosQuestionario["ParcFichPerg_Sequencia"]." - ".$DadosQuestionario["ParcFichPerg_Pergunta"]."</div>";
						if(($DadosQuestionario["ParcFichPerg_Tipo"]=="alternativas")||
							($DadosQuestionario["ParcFichPerg_Tipo"]=="multipla")||
							($DadosQuestionario["ParcFichPerg_Tipo"]=="condicional")) {

							if($this->Request["origem"]=="atendimento") {

								foreach ($DadosQuestionario["Resposta"] as $key => $Reposta) {

									$Respostas[$ParcFichPerg_id] .= "<img src='http://".$this->Path["HttpReferer"]."/images/box-checked.png' width='18' height='15' style='padding-left:20px;' /> ".$Reposta;
								}
							}
							elseif($this->Request["origem"]=="parceiros-fichas") {

								foreach ($DadosQuestionario["Opcoes"] as $key => $Opcoes) {

									if($DadosQuestionario["Condicoes"][$key]<>"") {

										$Respostas[$ParcFichPerg_id] .= "<div style='width:100%; padding-top:6px; padding-bottom:6px;' class='borderbottom'><img src='http://".$this->Path["HttpReferer"]."/images/box-unchecked.png' width='18' height='15' style='padding-left:20px;' />".$Opcoes." - ".$DadosQuestionario["Condicoes"][$key].":</div>";
									}
									else {

										$Respostas[$ParcFichPerg_id] .= "<img src='http://".$this->Path["HttpReferer"]."/images/box-unchecked.png' width='18' height='15' style='padding-left:20px;' /> ".$Opcoes;
									}
								}
							}

							$FichaAtendimento .= "R: ".$Respostas[$ParcFichPerg_id];

						}
						else {

							if($this->Request["origem"]=="atendimento") {

								$FichaAtendimento .= "R: ".$DadosQuestionario["Resposta"][0];
							}
							elseif($this->Request["origem"]=="parceiros-fichas") {

								if($DadosQuestionario["ParcFichPerg_Tipo"]=="sim-nao") {

									$FichaAtendimento .= "R: <img src='http://".$this->Path["HttpReferer"]."/images/box-unchecked.png' width='18' height='15' style='padding-left:20px;' /> SIM";
									$FichaAtendimento .= "<img src='http://".$this->Path["HttpReferer"]."/images/box-unchecked.png' width='18' height='15' style='padding-left:20px;' /> NÃO";
								}
								else {

									$FichaAtendimento .= "<div style='width:100%' class='borderbottom'>";
									$FichaAtendimento .= "<div class='col-xs-11 paddingficha'>R:</div>";
									$FichaAtendimento .= "</div>";
									if($DadosQuestionario["ParcFichPerg_Tipo"]=="texto-longo") $FichaAtendimento .= "<div style='width:100%' class='borderbottom'><div class='col-xs-11 paddingficha'>&nbsp;</div></div>
																													<div style='width:100%' class='borderbottom'><div class='col-xs-11 paddingficha'>&nbsp;</div></div>
																													<div style='width:100%' class='borderbottom'><div class='col-xs-11 paddingficha'>&nbsp;</div></div>
																													<div style='width:100%' class='borderbottom'><div class='col-xs-11 paddingficha'>&nbsp;</div></div>";
								}

							}
						}

						$FichaAtendimento .= "</div>";
					}
					}
			}

			$response["Content"] = str_replace("%%TituloFicha%%",strtoupper($response["ParcFich_Titulo"]),$response["Content"]);

			$response["Content"] = str_replace("%%FichaAtendimento%%",$FichaAtendimento,$response["Content"]);

			if($this->Request["origem"]=="atendimento") {

				$response["Header"] = "<div class='col-xs-3'><img src='http://".$this->Path["HttpReferer"]."/images/".$response["logo"]."'></div><div class='col-xs-6' style='text-align:center;'><h3>".$response["Even_Titulo"]."</h3><strong>FICHA INDIVIDUAL ATENDIMENTO<strong></div><div class='col-xs-2' style='float:right;'><img src='http://".$this->Path["HttpReferer"]."/src/Controller/Controller.php?Route=Barcode&Type=ean13&Return=image&Value=".$response["AtenBarcode"]."&Token=".$response['token']."' style='margin-top:-60px;'/></div>";
			}
			elseif($this->Request["origem"]=="parceiros-fichas") {

				$response["Header"] = "<div class='col-xs-3'><img src='http://".$this->Path["HttpReferer"]."/images/".$response["logo"]."'></div><div class='col-xs-8' style='text-align:center;'><h3>".$response["Even_Titulo"]."</h3><strong>FICHA INDIVIDUAL ATENDIMENTO<strong></div>";
			}


			$response["Footer"] = "<div class='col-xs-10'>Emitido em ".$this->Date["NowBR"]." por ".$this->TokenClass->getClaim("UserData")->Usua_PrimeiroUltimoNome."</div><div class='col-xs-1' style='tex-align:right;'>{PAGENO}/{nbpg}</div>";

			$response["Filename"] = md5(uniqid(microtime(),1)).getmypid();

			$response["Output"] = "file";

			$PDF = $this->PDFClass;

			if($this->Request["origem"]=="parceiros-fichas") {

				$PDF->setType($this->Request["origem"]);
			}

			$PDF->setOutput($response["Output"]);
			$PDF->setFilename($response["Filename"]);
			$PDF->setHeader($response['Header']);
			$PDF->setFooter($response["Footer"]);
			$PDF->setContent($response["Content"]);
			$PDF->GeneratePDF();

		}
		/*### LAYOUT DE IMPRESSÃO DO ATENDIMENTO E FICHA DE ATENDIMENTO ###*/
















		/*### LAYOUT DE IMPRESSÃO DO HISTÓRICO PÓS-MULTIAÇÃO ###*/
		elseif($this->Request["origem"]=="pos-multiacao") {

			$response["logo"] = "logo-topo.png";

			foreach ($ResultSQLAction as $key => $PosMObject) {

					$response["Pess_id"] = $PosMObject->pess_id;
					$response["Pess_Nome"] = $PosMObject->pess_nome;
					$response["Pess_CPF"] = $this->MaskValue->Cpf($PosMObject->pess_cpf,'add');
					$response["Pess_RG"] = $PosMObject->pess_rg;
					$response["Pess_DtNascimento"] = $this->MaskValue->Data($PosMObject->pess_dtnascimento,'US2BR');
					$response["Pess_Sexo"] = $PosMObject->pess_sexo;
					$response["Pess_Endereco_Logradouro"] = $PosMObject->pess_endereco_logradouro;
					$response["Pess_Endereco_Numero"] = $PosMObject->pess_endereco_numero;
					$response["Pess_Endereco_Complemento"] = $PosMObject->pess_endereco_complemento;
					$response["Pess_Endereco_Bairro"] = $PosMObject->pess_endereco_bairro;
					$response["Pess_Endereco_CEP"] = $this->MaskValue->Cep($PosMObject->pess_endereco_cep,'add');
					$response["Pess_Endereco_Cidade"] = $PosMObject->pess_endereco_cidade;
					$response["Pess_Endereco_UF"] = $PosMObject->pess_endereco_uf;
					$response["Cidade_UF"] = $response["Pess_Endereco_Cidade"]."/".$response["Pess_Endereco_UF"];
					$response["Pess_Telefone"] = $this->MaskValue->Telefone($PosMObject->pess_telefone,'add'); ;
					$response["Pess_Celular"] = $this->MaskValue->Telefone($PosMObject->pess_celular,'add'); ;
					$response["Pess_Email"] = $PosMObject->pess_email;
					$response["Even_Titulo"] = $PosMObject->even_titulo;
					$response["Aten_id"] = $PosMObject->aten_id;
					$response["Aten_Data"] = $this->MaskValue->Data($PosMObject->aten_data,'US2BR');
					$response["Aten_Hora_Inicio"] = $PosMObject->aten_hora_inicio;
					$response["Aten_Hora_Atend"] = $PosMObject->aten_hora_atend;
					$response["Aten_Hora_Fim"] = $PosMObject->aten_hora_fim;
					$response["Aten_SenhaNumero"] = str_pad($PosMObject->aten_senhanumero,3,0,STR_PAD_LEFT);

					$response["PessoaBarcode"] = $PosMObject->pess_barcode;
					$response["AtenBarcode"] = $PosMObject->aten_barcode;

					$response["Parc_Razao_Nome"] = $PosMObject->parc_razao_nome;
					$response["Serv_Titulo"] = $PosMObject->serv_titulo;

					$response["Historico"][$PosMObject->posm_id]["Data"] = $this->MaskValue->Data($PosMObject->posm_data,'US2BR');
					$response["Historico"][$PosMObject->posm_id]["TipoProcedimento"] = $PosMObject->posm_tipoprocedimento;
					$response["Historico"][$PosMObject->posm_id]["Historico"] = $PosMObject->posm_historico;
			}


			$response["Content"] = file_get_contents("../../lib/html-templates/atendimento-pos-multiacao.html");

			$Endereco = $response["Pess_Endereco_Logradouro"];
			if($response["Pess_Endereco_Numero"]<>"") $Endereco .= ", ".$response["Pess_Endereco_Numero"];
			if($response["Pess_Endereco_Complemento"]<>"") $Endereco .= " - ".$response["Pess_Endereco_Complemento"];
			if($response["Pess_Endereco_Bairro"]<>"") $Endereco .= " - ".$response["Pess_Endereco_Bairro"];

			$response["Content"] = str_replace("%%DadosPessoa%%","<div class='row' style='font-size: 11px;'>"
																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Código: </span>".$response["Pess_id"]."</div>"
																."<div class='col-xs-9 paddingficha'><span style='font-weight:bold;'>Nome: </span>".$response["Pess_Nome"]."</div>"
																."</div>"

																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-3 paddingficha'><span style='font-weight:bold;'>CPF: </span>".$response["Pess_CPF"]."</div>"
																."<div class='col-xs-3 paddingficha'><span style='font-weight:bold;'>RG: </span>".$response["Pess_RG"]."</div>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Dt. Nasc: </span>".$response["Pess_DtNascimento"]."</div>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Sexo: </span>".$response["Pess_Sexo"]."</div>"
																."</div>"

																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-11 paddingficha'><span style='font-weight:bold;'>Endereço: </span>".$Endereco."</div>"
																."</div>"

																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-1 paddingficha'><span style='font-weight:bold;'>CEP: </span>".$response["Pess_Endereco_CEP"]."</div>"
																."<div class='col-xs-4 paddingficha'><span style='font-weight:bold;'>Cidade/UF: </span>".$response["Cidade_UF"]."</div>"
																."<div class='col-xs-3 paddingficha'><span style='font-weight:bold;'>Telefone: </span>".$response["Pess_Telefone"]."</div>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Celular: </span>".$response["Pess_Celular"]."</div>"
																."</div>"

																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-11 paddingficha'><span style='font-weight:bold;'>E-mail: </span>".$response["Pess_Email"]."</div>"
																."</div>"
																."</div>",$response["Content"]);

			$response["Content"] = str_replace("%%DadosAtendimento%%","<div class='row' style='font-size: 11px;'>"
																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-4 paddingficha'><span style='font-weight:bold;'>Parceiro: </span>".$response["Parc_Razao_Nome"]."</div>"
																."<div class='col-xs-4 paddingficha'><span style='font-weight:bold;'>Serviço: </span>".$response["Serv_Titulo"]."</div>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Senha: </span>".$response["Aten_SenhaNumero"]."</div>"
																."</div>"

																."<div style='width:100%' class='borderbottom'>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Data: </span>".$response["Aten_Data"]."</div>"
																."<div class='col-xs-3 paddingficha'><span style='font-weight:bold;'>Pré-atendimento: </span>".$response["Aten_Hora_Inicio"]."</div>"
																."<div class='col-xs-3 paddingficha'><span style='font-weight:bold;'>Atendimento: </span>".$response["Aten_Hora_Atend"]."</div>"
																."<div class='col-xs-2 paddingficha'><span style='font-weight:bold;'>Saída: </span>".$response["Aten_Hora_Fim"]."</div>"
																."</div>"
																."</div>",$response["Content"]);

			if(count($response["Historico"])>0) {

				foreach ($response["Historico"] as $PosM_id => $DadosPosM) {

						$Historico .= "<div class='row' style='font-size:11px; margin-bottom:15px; '>";
						$Historico .= "<div style='font-size:12px; font-weight: bold; margin-bottom:6px;'>".$DadosPosM["Data"]." - ".$DadosPosM["TipoProcedimento"]."</div>";
						$Historico .= $DadosPosM["Historico"];
						$Historico .= "<hr>";
						$Historico .= "</div>";

				}
			}

			$response["Content"] = str_replace("%%HistPosMultiacao%%",$Historico,$response["Content"]);

			$response["Header"] = "<div class='col-xs-3'><img src='http://".$this->Path["HttpReferer"]."/images/".$response["logo"]."'></div><div class='col-xs-6' style='text-align:center;'><h3>".$response["Even_Titulo"]."</h3><strong>HISTÓRICO DE ATENDIMENTO PÓS-MULTIAÇÃO<strong></div><div class='col-xs-2' style='float:right;'><img src='http://".$this->Path["HttpReferer"]."/src/Controller/Controller.php?Route=Barcode&Type=ean13&Return=image&Value=".$response["AtenBarcode"]."&Token=".$response['token']."' style='margin-top:-60px;'/></div>";

			$response["Footer"] = "<div class='col-xs-10'>Emitido em ".$this->Date["NowBR"]." por ".$this->TokenClass->getClaim("UserData")->Usua_PrimeiroUltimoNome."</div><div class='col-xs-1' style='tex-align:right;'>{PAGENO}/{nbpg}</div>";

			$response["Filename"] = md5(uniqid(microtime(),1)).getmypid();

			$response["Output"] = "file";

			$PDF = $this->PDFClass;

			$PDF->setOutput($response["Output"]);
			$PDF->setFilename($response["Filename"]);
			$PDF->setHeader($response['Header']);
			$PDF->setFooter($response["Footer"]);
			$PDF->setContent($response["Content"]);
			$PDF->GeneratePDF();

		}
		/*### LAYOUT DE IMPRESSÃO DO HISTÓRICO PÓS-MULTIAÇÃO ###*/



		//RETORNA OK NA AÇÃO
		$response[$this->Action] = "OK";

		//RETORNA O SQLAction
		$response["SQLAction"] = $this->SQLAction;

		$this->setResponseAction($response);
		echo json_encode($this->ResponseAction);

	}
	/*### RETORNA O RESULTADO DA ExecuteAction CONFORME A ACTION ###*/


}
/*### CLASSE DAS IMPRESSÕES (HERDA CRUD) ###*/
