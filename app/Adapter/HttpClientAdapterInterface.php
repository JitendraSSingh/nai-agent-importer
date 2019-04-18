<?php

namespace App\Adapter;

use App\Exceptions\HttpException;

interface HttpClientAdapterInterface
{
	/**
	 * @param  string $url
	 * 
	 * @throws HttpException
	 * 
	 * @return string
	 */
	public function get($url);
}