<?php

/*#######################################################
|														|
| Arquivo com a classe que gera, valida e interpreta o	|
| token JWT 											|
|														|
| Data de criação: 03/02/2018							|
| Autor: Guilherme Moreira de Castro					|
| Cliente: Portabilis Tecnologia						|
| E-mail: guicastro@gmail.com							|
|														|
#######################################################*/

namespace Functions;

/*### FUNÇÃO QUE GERA, VALIDA E INTERPRETA O TOKEN DO JWT ###*/
class TokenClass {

	private $JWTBuilder;
	private $JWTParser;
	private $JWTValidationData;
	private $JWTSha256;
	private $Key;
	private $TokenObject;
	private $Token;
	private $Date;
	private $DateMK;

	/*### CONSTRUTOR, OBRIGATÓRIO CHAMAR A CLASSE JWT ###*/
	public function __construct($container) {

		$this->JWTBuilder = new \Lcobucci\JWT\Builder;
		$this->JWTParser = new \Lcobucci\JWT\Parser;
		$this->JWTValidationData = new \Lcobucci\JWT\ValidationData;
		$this->JWTSha256 = new \Lcobucci\JWT\Signer\Hmac\Sha256;
		$this->Key = $container['DefuseKey'];
		$this->Date = $container["Date"];
		$this->DateMK = $container["DateMK"];
	}
	/*### CONSTRUTOR, OBRIGATÓRIO CHAMAR A CLASSE JWT ###*/

	/*### ARMAZENA O TOKEN EM FORMATO DE STRING ###*/
	public function setToken($token) {

		$this->Token = $token;
		return $this->Token;
	}
	/*### ARMAZENA O TOKEN EM FORMATO DE STRING ###*/


	/*### RETORNA A STRING DO TOKEN ###*/
	public function getToken() {

		return $this->Token;
	}
	/*### RETORNA A STRING DO TOKEN ###*/


	/*### ARMAZENA O OBJETO JWT DO TOKEN ###*/
	public function setTokenObject($TokenObject) {

		$this->TokenObject = $TokenObject;
		return $this->TokenObject;
	}
	/*### ARMAZENA O OBJETO JWT DO TOKEN ###*/


	/*### RETORNA O OBJETO JWT DO TOKEN ###*/
	public function getTokenObject() {

		return $this->TokenObject;
	}
	/*### RETORNA O OBJETO JWT DO TOKEN ###*/


	/*### TRATA E RETORNA O TOKEN PARA ENVIO VIA JSON ###*/
	public function sendToken() {

		$TokenObject = $this->getTokenObject()->getToken();
		$this->setToken($TokenObject->__toString());

		return $this->Token;
	}
	/*### TRATA E RETORNA O TOKEN PARA ENVIO VIA JSON ###*/



	/*### RECEBE E TRATA A STRING DO TOKEN DE ACESSO ###*/
	public function receiveToken() {

		$parseToken = ($this->JWTParser);
		$TokenObject = $parseToken->parse($this->Token);

		$this->setTokenObject($TokenObject);
	}
	/*### RECEBE E TRATA A STRING DO TOKEN DE ACESSO ###*/



	/*### CRIA O TOKEN ###*/
	public function CreateToken($UserData, $Data) {

		//CHAVE 256 BITS PARA ASSINATURA DO TOKEN
		$signer = ($this->JWTSha256);

		//INICIA O BUILDER
		$TokenObject = ($this->JWTBuilder);

		//DEFINE O EMISSOR
		$TokenObject->setIssuer($container['HttpReferer']);

		//DEFINE O RECEPTOR
		$TokenObject->setAudience($container['HttpReferer']);

		//DEFINE O ID DO TOKEN
		$TokenObject->setId(base64_encode(mcrypt_create_iv(32)), true);

		//DEFINE A DATA DE CRIAÇÃO DO TOKEN
		$TokenObject->setIssuedAt(time());

		//DEFINE O TEMPO DE EXPIRAÇÃO DO TOKEN
		$TokenObject->setExpiration(time() + $this->DateMK["HourMK"]*12);

		//ARMAZENA O OBJETO DE DADOS DO USUÁRIO (USERDATA)
		$TokenObject->set('UserData', $UserData);

		//ARMAZENA O OBJETO DE DADOS GENÉRICOS (DATA)
		$TokenObject->set('Data', $Data);

		//ASSINA O TOKEN
		$TokenObject->sign($signer, $this->Key);

		//ARMAZENA O OBJETO DO TOKEN
		$this->setTokenObject($TokenObject);
	}
	/*### CRIA O TOKEN ###*/



	/*### RETORNA O CABEÇALHO DO OBJETO DO TOKEN ###*/
	public function getHeaders() {

		$TokenObject = $this->getTokenObject();

		return $TokenObject->getHeaders();

	}
	/*### RETORNA O CABEÇALHO DO OBJETO DO TOKEN ###*/


	/*### RETORNA AS VARIÁVEIS (CLAIMS) DO OBJETO DO TOKEN ###*/
	public function getClaims() {

		$TokenObject = $this->getTokenObject();

		return $TokenObject->getClaims();

	}
	/*### RETORNA AS VARIÁVEIS (CLAIMS) DO OBJETO DO TOKEN ###*/



	/*### RETORNA UMA VARIÁVEL ESPECÍFICA (CLAIS) DO OBJETO DO TOKEN ###*/
	public function getClaim($claim) {

		$TokenObject = $this->getTokenObject();

		return $TokenObject->getClaim($claim);

	}
	/*### RETORNA UMA VARIÁVEL ESPECÍFICA (CLAIS) DO OBJETO DO TOKEN ###*/



	/*### VALIDA O TOKEN ###*/
	public function ValidateToken() {

		//INSTANCIA A CHASE 256 BITS
		$Signer = ($this->JWTSha256);

		//INSTANCIA O VALIDADOR
		$Validation = ($this->JWTValidationData);

		//RETORNA O OBJETO DO TOKEN
		$TokenObject = $this->getTokenObject();

		//ADICIONA NA VALIDAÇÃO O EMISSOR
		$Validation->setIssuer($container['HttpReferer']);

		//ADICIONA NA VALIDAÇÃO O RECEPTOR
		$Validation->setAudience($container['HttpReferer']);

		//ADICIONA NA VALIDAÇÃO A DATA E HORA ATUAIS
		$Validation->setCurrentTime(time());

		/*### VERIFICA SE A ASSINATURA ESTÁ CORRETA, SE O TOKEN NÃO SOFREU ALTERAÇÕES E SE ESTÁ DENTRO DO PRAZO DE VALIDADE, RETORNANDO TRUE SE OK ###*/
		if(($TokenObject->verify($Signer, $this->Key)==true)&&($TokenObject->validate($Validation)==true)) return true;
		else return false;
		/*### VERIFICA SE A ASSINATURA ESTÁ CORRETA, SE O TOKEN NÃO SOFREU ALTERAÇÕES E SE ESTÁ DENTRO DO PRAZO DE VALIDADE, RETORNANDO TRUE SE OK ###*/
	}
	/*### VALIDA O TOKEN ###*/


}
/*### FUNÇÃO QUE GERA, VALIDA E INTERPRETA O TOKEN DO JWT ###*/