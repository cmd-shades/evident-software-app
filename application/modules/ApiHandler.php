<?php


namespace Application\Modules;

use App\Libraries\Ssid_common;
use Application\Modules\Web\Models\Webapp_model;

final class ApiHandler
{
	/**
	 * @var \App\Libraries\Ssid_common $ssid_common
	 */
	private $ssid_common;

	public function __construct()
	{
		$this->ssid_common = new Ssid_common();
	}


	public static function dispatch($url_endpoint = false, $data = false, $options = false, $is_get_method = false)
	{
//		return (new Webapp_model())->api_dispatcher($url_endpoint, $data, $options, $is_get_method);

		return (new self())->handleRequest($url_endpoint, $data, $options, $is_get_method);
	}


	/* Dispatch an api request locally */
	/**
	 * @param $url_endpoint
	 * @param $data
	 * @param array|false $headers
	 * @param $is_get_method
	 *
	 * @return false|mixed
	 */
	private function handleRequest($url_endpoint = false, $data = false, $headers = false, $is_get_method = false): mixed
	{
		if(empty($url_endpoint)) {
			throw new \InvalidArgumentException('Invalid endpoint.');
		}

		if(empty($data)) {
			throw new \InvalidArgumentException('Invalid payload.');
		}

		$options = false;

		if($is_get_method){
			if(is_array($headers) && false !== $headers){
				$options = array_merge($headers, ['method' => 'GET']) ;
			} else {
				$options = ['method' => 'GET'];
			}
		}

		$postData = $this->preparePostBodyData($data);

		return $this->request($url_endpoint, $postData, $options);
	}

	private function preparePostBodyData($payload)
	{
		return $this->ssid_common->_prepare_curl_post_data($payload);
	}

	private function request($url, $body, $headers)
	{
		$response = $this->ssid_common->doCurl($url, $body, $headers);

		if(isset($response->message) && false === empty($response->message))
		{
			$message = $response->message;
			if ($message === 'Expired token') {
				// $this->session->set_flashdata('message', 'Your token has expired. Please login.');
				redirect('webapp/user/login', 'refresh');
			}

		}

		return $response;
	}
}
