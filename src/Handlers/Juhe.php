<?php

namespace AnomalyLab\LuminousSMS\Handlers;

use AnomalyLab\LuminousSMS\Exceptions\HandlerBadException;
use AnomalyLab\LuminousSMS\Contracts\MessagerInterface;
use AnomalyLab\LuminousSMS\Support\Arrays;

/**
 *	Class Juhe
 *
 *	@link			https://anomaly.ink
 *	@author			Anomaly lab, Inc <support@anomaly.ink>
 *	@author			Bill Li <bill@anomaly.ink>
 *	@package		AnomalyLab\LuminousSMS\Handlers\Juhe
 */
class Juhe extends Handler
{
	protected const REQUEST_URL = 'http://v.juhe.cn/sms/send';

	protected const REQUEST_FORMAT = 'json';

	/**
	 *	The handler name.
	 *
	 *	@var		string
	 */
	protected $name = 'juhe';

	/**
	 *	Remove Juhe SMS push does not support methods.
	 *
	 *	@var		array
	 */
	protected $removeMethods = ['voice', 'text_many', 'template_id'];

	/**
	 *	Seed message.
	 *
	 *	The current drive service providers to implement push information content.
	 *
	 *	@param		\AnomalyLab\LuminousSMS\Contracts\MessagerInterface		$messager
	 *
	 *	@return		array
	 *
	 *	@throws		\AnomalyLab\LuminousSMS\Exceptions\HandlerBadException;
	 */
	public function send(MessagerInterface $messager) : array
	{
		$params = [
			'mobile'	=> $messager->getMobilePhone(),
			'tpl_id'	=> $messager->getTemplate(),
			'tpl_value'	=> $this->formatTemplateData($messager->getData()),
			'dtype'		=> static::REQUEST_FORMAT,
			'key'		=> Arrays::get($this->config, 'app_key'),
		];

		$result = $this->get(static::REQUEST_URL, $params);

		if ( $result['error_code'] )
		{
			throw new HandlerBadException($result['reason'], $result['error_code'], $result);
		}

		return $result;
	}

	/**
	 *	Format the template data.
	 *
	 *	@param		array		$data
	 *
	 *	@return		string
	 */
	protected function formatTemplateData(array $data) : string
	{
		$formatted = [];

		foreach ( $data as $key => $value )
		{
			$formatted[sprintf('#%s#', trim($key, '#'))] = $value;
		}

		return http_build_query($formatted);
	}
}