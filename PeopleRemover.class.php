<?php
/**
 * 123PeopleRemover class
 * 
 * @TODO Ajouter des commentaires
 * @TODO Test-unitaire
 * @TODO Sortir le "printOut" & "printOurError" ...
 * @author Francois-Guillaume Ribreau
 *
 */
class PeopleRemover{
	
	private $url
			, $callback
			, $httpClient // [HttpClient]
			, $htmlSource
			, $VERSION
			, $PROD
			, $SERVER123PEOPLE;
	
	function __construct($http_get_url = null, $http_get_callback){
		//Old static var
		$this->VERSION = 2;
		$this->PROD = true;
		$this->SERVER123PEOPLE = 'www.123people.';//[fr | us | de | it | br | etc...]
		
		$this->url = $http_get_url;
		$this->callback = $http_get_callback;
		$httpClient = null;
		$htmlSource = null;
	}
	
	public function run(){
		
		if(empty($this->callback)){
			$this->callback = 'defaultCallback';
			echo $this->printOutError(0, 'No parameter "callback" specified');
			return;
		}
		
		if(empty($this->url)){
			echo $this->printOutError(1, 'No parameter "url" specified');
			return;
		}
		
		if(!$this->isGoodUrl()){
			echo $this->printOutError(2, 'Bad url value');
			return;
		}

		return $this->httpClientInit() && $this->httpClientGet() && $this->httpClientParse();
	}
	
	/**
	 * isGoodUrl
	 * Test si $http_get_url est valide
	 */
	public function isGoodUrl(){
		return strpos($this->url, 'http://www.123people.') == 0 && strpos($this->url, '/s/') != false;
	}
	
	public function getTLD(){
		$p = strlen('http://www.123people.');
		return substr($this->url, $p, strpos($this->url, '/', $p)-$p);
	}
	
	
	public function httpClientInit(){
		$this->httpClient = new HttpClient($this->SERVER123PEOPLE.$this->getTLD());
		$this->httpClient->setDebug(!$this->PROD);
		return true;
	}
	
	/**
	 * httpClientGet
	 * Récupère le code html de la page
	 **/
	public function httpClientGet(){
		if($this->PROD){
			if (!$this->httpClient->get($this->getRequestUrl())) {
			    die('An error occurred: '.$this->httpClient->getError());
			}
			
			$this->htmlSource = $this->httpClient->getContent();
		} else {
			$this->htmlSource = file_get_contents('tmp.html');
		}

		if(empty($this->htmlSource)){
			echo $this->printOutError(3, 'The server returned an empty source');
			return false;
		}
		
		return true;
	}
	
	public function getRequestUrl(){
		$url = parse_url($this->url);
		return $url['path'];
	}
	
	/**
	 * httpClientParse
	 * Ici se trouve la partie... "sale"
	 * Je suis ouvert à toute participation !
	 */
	public function httpClientParse(){
		//Get all links in $links[]
		if(!preg_match_all("(((f|ht){1}tp://)[-a-zA-Z0-9@:%_;\+.|~#?&//=]+)", $this->htmlSource, $links) 
		|| count($links) == 0
		|| count($links[0]) == 0){
			echo $this->printOutError(4, 'No links');
			return false;
		}

		function isBadLink($v){
			return stripos($v, '123people') === FALSE 
			&& stripos($v, 'commander.1and1.fr') === FALSE
			&& stripos($v, 'w3.org/TR') === FALSE
			&& stripos($v, 'yahooapis.com') === FALSE
			&& stripos($v, 'w3.org/1999') === FALSE
			&& stripos($v, 'intelius.com') === FALSE // World-US
			&& stripos($v, 'united-domains.de') === FALSE // Austria
			&& stripos($v, 'amazon.de/dp/') === FALSE // De
			&& substr_count($v,'.') >= 2;
		}

		//Quick & dirty -__-'
		$links = array_values(array_unique( array_filter($links[0], 'isBadLink')));

		if(count($links) == 0){
			echo $this->printOutError(5, 'There are no links :s');
			
			echo '<textarea>'.$this->htmlSource.'</textarea>';
			return false;
		}
		
		echo $this->printOut(array('links' => $links));
		return true;
	}

	/**
	 * printOut
	 * Retourne un JSONP
	 */
	private function printOut($obj){
		header('Content-type: application/json');
		return $this->callback.'('.json_encode(array('version' => $this->VERSION, 'content' => $obj)).');';
	}
	
	/**
	 * printOutError
	 * Retourne un JSONP d'erreur
	 */
	private function printOutError($id, $message){
		return $this->printOut(array('errId' => $id, 'errMsg' => $message));
	}
}


?>