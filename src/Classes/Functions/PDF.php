<?php

/*#######################################################
|														|
| Arquivo com a classe de geração do PDF				|
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

namespace Functions;



/*### CLASSE DE GERAÇÃO DE PDF (HERDA CRUD) ###*/
class PDF {

	protected $Filename;
	protected $Type;
	protected $Output;
	protected $Header;
	protected $Footer;
	protected $Content;
	protected $Path;
	protected $Orientation;
	protected $Margin;
	protected $AutoTopMargin;
	protected $mPDFDocument;


	/*### CONSTRUTOR ###*/
	public function __construct($container) {

		//ARMAZENA AS VARIÁVEIS DOS CAMINHOS DAS PASTAS E HTTP
		$this->Path = array("AbsolutePath" => $container['AbsolutePath'],
							"RelativePath" => $container['RelativePath'],
							"HttpReferer" => $container['HttpReferer']);

	}
	/*### CONSTRUTOR ###*/





	/*### ARMAZENA O TIPO DE ARQUIVO ###*/
	public function SetFilename($Filename) {

		$this->Filename = $Filename;
		return $this->Filename;
	}
	/*### ARMAZENA O TIPO DE ARQUIVO ###*/




	/*### ARMAZENA O TIPO DE ARQUIVO ###*/
	public function SetOutput($Output) {

		$this->Output = $Output;
		return $this->Output;
	}
	/*### ARMAZENA O TIPO DE ARQUIVO ###*/




	/*### ARMAZENA O TIPO DE ARQUIVO ###*/
	public function SetType($Type) {

		$this->Type = $Type;
		return $this->Type;
	}
	/*### ARMAZENA O TIPO DE ARQUIVO ###*/




	/*### ARMAZENA O CABEÇALHO ###*/
	public function SetHeader($Header) {

		$this->Header = $Header;
		return $this->Header;
	}
	/*### ARMAZENA O CABEÇALHO ###*/




	/*### ARMAZENA A ORIENTAÇÃO DA PÁGINA ###*/
	public function SetOrientation($Orientation) {

		if($Orientation=="") $this->Orientation = "P";
		else $this->Orientation = $Orientation;

		return $this->Orientation;
	}
	/*### ARMAZENA A ORIENTAÇÃO DA PÁGINA ###*/




	/*### ARMAZENA A ORIENTAÇÃO DA PÁGINA ###*/
	public function SetMargin($Margin) {

		if($Margin=="stretch") $this->AutoTopMargin = "stretch";
		else $this->AutoTopMargin = false;

		$this->Margin = $Margin;

		return $this->Margin;
	}
	/*### ARMAZENA A ORIENTAÇÃO DA PÁGINA ###*/



	/*### ARMAZENA O RODAPÉ ###*/
	public function SetFooter($Footer) {

		$this->Footer = $Footer;
		return $this->Footer;
	}
	/*### ARMAZENA O RODAPÉ ###*/



	/*### ARMAZENA O CONTEÚDO DO ARQUIVO ###*/
	public function SetContent($Content) {

		$this->Content = $Content;
		return $this->Content;
	}
	/*### ARMAZENA O CONTEÚDO DO ARQUIVO ###*/



	public function GeneratePDF() {



		$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8',
								'format' => [210,297],
								'setAutoTopMargin' => $this->AutoTopMargin,
								'bleedMargin' => 0,
								'orientation' => $this->Orientation]);

		if($this->Margin=="zero") {

			$mpdf->SetMargins(0);
		}


		$CSSBootstrap = file_get_contents($this->Path["AbsolutePath"].'/lib/smartadmin/css/bootstrap.min.css');

		$CSSPage = file_get_contents($this->Path["AbsolutePath"].'/lib/css/page.css');

		if($this->Type=="parceiros-fichas") {

			$CSSFicha = " .borderbottom { border-bottom: 1px solid #000; } .paddingficha { padding-top:8px; paddind-bottom:8px; } ";
		}

		$mpdf->WriteHTML($CSSBootstrap.$CSSFicha,1);

		$mpdf->SetHTMLHeader($this->Header);
		$mpdf->SetHTMLFooter($this->Footer);

		$mpdf->WriteHTML($this->Content,2);


		/*### APAGA OS ARQUIVOS DO DIRETÓRIO TEMPORÁRIO ###*/
		// echo "<br>hash: ".$this->hash;
		$directoryIterator = new \DirectoryIterator($this->Path["AbsolutePath"]."/tmp");
		foreach($directoryIterator as $ArquivoPDF)
			{
				// echo "<br><br>".$ArquivoPDF->__toString();
				// echo "<br>".$ArquivoPDF->getType();
				// echo "<br>".$ArquivoPDF->getExtension();
				// echo "<br>".$ArquivoPDF->getPathname();
				// echo "<br>".$ArquivoPDF->getCTime();
				// echo "<br>".(time() - $ArquivoPDF->getCTime());

				if(strtolower($ArquivoPDF->getExtension())=="pdf") {

					if((time() - $ArquivoPDF->getCTime())>=300) @unlink($ArquivoPDF->getPathname());
				}
			}
		/*### APAGA OS ARQUIVOS DO DIRETÓRIO TEMPORÁRIO ###*/

		// echo "<pre>"; print_r($mpdf); echo "</pre>";
		// exit;

		if($this->Output=="file") {

			$mpdf->Output($this->Path["AbsolutePath"].'/tmp/'.$this->Filename.'.pdf',\Mpdf\Output\Destination::FILE);
		}
		else {

			$mpdf->Output($this->Filename,\Mpdf\Output\Destination::INLINE);
		}


	}



}
/*### CLASSE DE GERAÇÃO DE PDF (HERDA CRUD) ###*/
