<?php

namespace AnomalyLab\LuminousSMS\Traits;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 *	Trait HttpRequest
 *
 *	@link			https://anomaly.ink
 *	@author			Anomaly lab, Inc <support@anomaly.ink>
 *	@author			Bill Li <bill@anomaly.ink>
 *	@package		AnomalyLab\LuminousSMS\Traits\HttpRequest
 */
trait HttpRequest
{
	/**
	 *	Make a get request.
	 *
	 *	@param		string		$endpoint
	 *	@param		array		$query
	 *	@param		array		$headers
	 *
	 *	@return		mixed
	 */
	protected function get(string $endpoint, array $query = [], array $headers = [])
	{
		return $this->request(
			'get',
			$endpoint,
			[
				'headers'	=> $headers,
				'query'		=> $query,
			]
		);
	}

	/**
	 *	Make a post request.
	 *
	 *	@param		string		$endpoint
	 *	@param		array		$params
	 *	@param		array		$headers
	 *
	 *	@return		mixed
	 */
	protected function post(string $endpoint, array $params = [], array $headers = [])
	{
		return $this->request(
			'post',
			$endpoint,
			[
				'headers'		=> $headers,
				'form_params'	=> $params
			]
		);
	}
	/**
	 *	Make a http request.
	 *
	 *	@param		string		$method
	 *	@param		string		$endpoint
	 *	@param		array		$options		http://docs.guzzlephp.org/en/latest/request-options.html
	 *
	 *	@return		mixed
	 */
	protected function request(string $method, string $endpoint, array $options = [])
	{
		return $this->unwrapResponse(
			$this
				->getHttpClient($this->getBasicOptions())
				->{$method}($endpoint, $options)
		);
	}

	/**
	 *	Return base Guzzle options.
	 *
	 *	@return		array
	 */
	protected function getBasicOptions() : array
	{
		return [
			'base_uri'	=> method_exists($this, 'getBaseUri') ? $this->getBaseUri() : '',
			'timeout'	=> $this->getTimeout(),
		];
	}

	/**
	 *	Return http client.
	 *
	 *	@param		array		$options
	 *
	 *	@return		\GuzzleHttp\Client
	 *
	 */
	protected function getHttpClient(array $options = []) : Client
	{
		return new Client($options);
	}

	/**
	 *	Convert response contents to json.
	 *
	 *	@param		\Psr\Http\Message\ResponseInterface		$response
	 *
	 *	@return		mixed
	 */
	protected function unwrapResponse(ResponseInterface $response)
	{
		$contentType = $response->getHeaderLine('Content-Type');
		$contents = $response->getBody()->getContents();

		if ( false !== stripos($contentType, 'json') || stripos($contentType, 'javascript') )
		{
			return json_decode($contents, true);
		}
		elseif ( false !== stripos($contentType, 'xml') )
		{
			return json_decode(json_encode(simplexml_load_string($contents)), true);
		}

		return $contents;
	}
}