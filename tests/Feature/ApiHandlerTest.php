<?php


namespace Feature;

use Application\Modules\ApiHandler;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;

/**
 * @internal
 */
final class ApiHandlerTest extends CIUnitTestCase
{
	public function test_api_handler()
	{
		$dispatch = ApiHandler::dispatch('www.google.co.uk', ['data' => null]);

		dd($dispatch);
	}
}
