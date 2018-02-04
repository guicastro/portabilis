<?php

/*#######################################################
|														|
| Arquivo com a classe do cadastro de Alunos			|
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

/*### CLASSE DO CADASTRO DE PESSOA (HERDA CRUD) ###*/
class Aluno extends \Database\Crud {

	protected $DynamicTable;

	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/
	public function __construct($container) {

		parent::__construct($container['Connect'], $container);

		//CHAMADA PARA CLASSE TABELADINAMICA
		$this->DynamicTable = $container['DynamicTable'];
	}
	/*### CONSTRUTOR, COM AS CHAMADAS OBRIGATÓRIAS (USANDO O MESMO CONSTRUTORA DA CRUD) ###*/









	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/
	public function MaskResultSQLAction () {

		if($this->Action=="selecionar")
			{
				$ResultSQLAction = $this->ResultSQLAction;

				if(count($ResultSQLAction)>0) {

					foreach ($ResultSQLAction as $key => $DataObject) {

						if(is_numeric($key)) {

							$ResultSQLAction[$key]->alun_cpf = $this->MaskValue->Cpf($DataObject->alun_cpf,'add');
							$ResultSQLAction[$key]->alun_reccreatedon = $this->MaskValue->Data($DataObject->alun_reccreatedon,'US2BR_TIME');
							$ResultSQLAction[$key]->alun_recmodifiedon = $this->MaskValue->Data($DataObject->alun_recmodifiedon,'US2BR_TIME');

							$ResultSQLAction[$key]->alun_dtnascimento = $this->MaskValue->Data($DataObject->alun_dtnascimento,'US2BR');
							$ResultSQLAction[$key]->alun_endereco_cep = $this->MaskValue->Cep($DataObject->alun_endereco_cep,'add');
							$ResultSQLAction[$key]->alun_celular = $this->MaskValue->Telefone($DataObject->alun_celular,'add');
							$ResultSQLAction[$key]->alun_telefone = $this->MaskValue->Telefone($DataObject->alun_telefone,'add');

							$ResultSQLAction[$key]->alun_cpf_antigo = $this->MaskValue->Cpf($DataObject->alun_cpf,'remove');
						}

					}
				}

				// if(count($ResultSQLAction)>1) $ResultSQLAction
				$this->setResultSQLAction($ResultSQLAction);
			}
	}
	/*### EXECUTA AS MÁSCARAS DE VALORES E DADOS NO OBJETO DE DADOS DO SELECT ###*/







	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/
	public function MaskInsertUpdateValues () {

		$Request = $this->Request;

		if(($this->Action=="inserir")||($this->Action=="alterar"))
			{
				$Request["alun_cpf"] = $this->MaskValue->Cpf($Request["alun_cpf"],'remove');
				$Request["alun_cpf_antigo"] = $this->MaskValue->Cpf($Request["alun_cpf_antigo"],'remove');
				$Request["alun_dtnascimento"] = $this->MaskValue->Data($Request["alun_dtnascimento"],'BR2US');
				$Request["alun_endereco_cep"] = $this->MaskValue->Cep($Request["alun_endereco_cep"],'remove');
				$Request["alun_celular"] = $this->MaskValue->Telefone($Request["alun_celular"],'remove');
				$Request["alun_telefone"] = $this->MaskValue->Telefone($Request["alun_telefone"],'remove');

				$this->setRequest($Request);
			}

	}
	/*### EXECUTA AS AÇÕES DE MÁSCARA DE VALORES NOS DADOS DE UPDATE OU INSERT ###*/








	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/
	public function BeforeExecuteAction () {


		if(($this->Action=="inserir")||($this->Action=="alterar"))
			{

				/*### VERIFICA SE O CPF DIGITADO JÁ EXISTE NO CADASTRO ###*/
				if($this->Request["alun_cpf"]<>$this->Request["alun_cpf_antigo"])
					{
						$this->BeforeExecuteAction = false;

						//VALIDA O CPF
						$ValidateCPF = $this->MaskValue->ValidarCPF($this->Request["alun_cpf"],'validate');

						/*### SE O CPF FOR INVÁLIDO RETORNA MENSAGEM ###*/
						if($ValidateCPF==false) {

							$this->ErrorBeforeExecuteAction = "<p>O CPF digitado não é válido.</p>";
						}
						/*### SE O CPF FOR INVÁLIDO RETORNA MENSAGEM ###*/


						/*### SENÃO, CHECA SE NÃO É DUPLICADO ###*/
						else {

							$SQLChecaCPF = "SELECT COUNT(*) AS CPF FROM ".$this->ModuleDefs->Table." WHERE ".$this->ModuleDefs->Prefix."CPF = '".$this->Request["alun_cpf"]."' AND ".$this->ModuleDefs->Prefix."Delete = 0";
							$ExecuteChecaCPF = $this->db->query($SQLChecaCPF);
							$ResultChecaCPF = $ExecuteChecaCPF->fetchAll(\PDO::FETCH_OBJ);
							if($ResultChecaCPF[0]->cpf > 0) {

								$this->ErrorBeforeExecuteAction = "<p>O CPF digitado já existe na base de dados.</p>";
							} else {

								$this->BeforeExecuteAction = true;
							}
						}
						/*### SENÃO, CHECA SE NÃO É DUPLICADO ###*/

					}
				/*### VERIFICA SE O CPF DIGITADO JÁ EXISTE NO CADASTRO ###*/
			}

	}
	/*### EXECUTA AÇÕES NECESSÁRIAS ANTES DE EXECUTAR A QUERY DA SQLAction ###*/





}
/*### CLASSE DO CADASTRO DE PESSOA (HERDA CRUD) ###*/
