<?php

namespace AnomalyLab\LuminousSMS\Handlers;

use AnomalyLab\LuminousSMS\Exceptions\HandlerBadException;
use AnomalyLab\LuminousSMS\Contracts\MessagerInterface;
use AnomalyLab\LuminousSMS\Support\Configure;

/**
 *	Class Yunpian
 *
 *	@link			https://anomaly.ink
 *	@author			Anomaly lab, Inc <support@anomaly.ink>
 *	@author			Bill Li <bill@anomaly.ink>
 *	@package		AnomalyLab\LuminousSMS\Handlers\Qclod
 */
class Yunpian extends Handler
{
	protected const REQUEST_TEMPLATE = 'https://%s.yunpian.com/%s/%s/%s.%s';

	protected const REQUEST_VERSION = 'v2';

	protected const REQUEST_FORMAT = 'json';

	/**
	 *	The handler name.
	 *
	 *	@var		string
	 */
	protected $name = 'yunpian';

	/**
	 *	Seed message.
	 *
	 *	The current drive service providers to implement push information content.
	 *
	 *	@param		int|string		$to
	 *	@param		\AnomalyLab\LuminousSMS\Contracts\MessagerInterface		$messager
	 *
	 *	@return		array
	 *
	 *	@throws		\AnomalyLab\LuminousSMS\Exceptions\HandlerBadException;
	 */
	public function send(MessagerInterface $messager) : array
	{
		$result = $this->post(
			//	Build request url.
			sprintf(self::ENDPOINT_TEMPLATE, 'sms', self::ENDPOINT_VERSION, 'sms', 'single_send', self::ENDPOINT_FORMAT),
			[
				'apikey'	=> Configure::get($this->config, 'api_key'),
				'mobile'	=> $messager->getMobilePhone(),
				'text'		=> $message->getContent(),
			]
		);

		if ( $result['code'] )
		{
			throw new HandlerBadException($result['msg'], $result['code'], $result);
		}

		return $result;
	}
}