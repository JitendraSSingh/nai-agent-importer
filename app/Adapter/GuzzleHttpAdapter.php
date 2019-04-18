<?php

namespace App\Adapter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Exceptions\HttpException;

class GuzzleHttpAdapter implements HttpClientAdapterInterface
{
	protected $client;
	protected $response;

	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	public function get($url)
	{
		try{
			$this->response = $this->client->request('GET',$url);
		}catch(RequestException $re){
			var_dump($re->getMessage());
			$this->response = $re->getResponse();
			$this->handleError();
		}

		return $this->response->getBody();
	}

	public function handleError()
	{
		//$body = (string)$this->response->getBody();
		//$code = (int)$this->response->getStatusCode();
		var_dump($this->response);
		throw new HttpException("Error Processing Request", $code);	
	}
}