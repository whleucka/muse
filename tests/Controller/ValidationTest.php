<?php

declare(strict_types=1);

use Nebula\Framework\Controller\Controller;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class ValidationTest extends TestCase
{
	public function testValidateRequestString(): void
	{
		$payload = ["age" => "foo"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest(["age" => ["string"]]);
		$this->assertSame($payload, $data);

		$payload = ["age" => 37];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest(["age" => ["string"]]);
		$this->assertFalse($data);
	}

	public function testValidateRequestNumeric(): void
	{
		$payload = ["age" => 37];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest(["age" => ["numeric"]]);
		$this->assertSame($payload, $data);

		$payload = ["age" => "foo"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest(["age" => ["numeric"]]);
		$this->assertFalse($data);
	}

	public function testValidateRequestRequired(): void
	{
		$controller = new Controller(new Request());
		$data = $controller->validateRequest(["age" => ["required"]]);
		$this->assertFalse($data);

		$payload = ["age" => 37];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest(["age" => ["required"]]);
		$this->assertSame($payload, $data);
	}

	public function testValidateRequestArray(): void
	{
		$payload = ["collection" => [1, 2, 3]];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"collection" => ["array"]
		]);
		$this->assertSame($payload, $data);

		$payload = ["age" => 37];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest(["age" => ["array"]]);
		$this->assertFalse($data);
	}

	public function testValidateRequestEmail(): void
	{
		$payload = ["email" => "test@test.com"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"email" => ["email"]
		]);
		$this->assertSame($payload, $data);

		$payload = ["email" => "test@test"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"email" => ["email"]
		]);
		$this->assertFalse($data);

		$payload = ["email" => "test"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"email" => ["email"]
		]);
		$this->assertFalse($data);

		$payload = ["email" => null];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"email" => ["required", "email"]
		]);
		$this->assertFalse($data);
	}

	public function testValidateRequestMatch(): void
	{
		$payload = ["password" => "test123", "password_match" => "test123"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"password" => ["required"],
			"password_match" => ["required", "match|password"]
		]);
		$this->assertSame($payload, $data);

		$payload = ["password" => "test12", "password_match" => "test123"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"password" => ["required"],
			"password_match" => ["required", "match|password"]
		]);
		$this->assertFalse($data);
	}

	public function testValidateRequestMax(): void
	{
		$payload = ["number" => 4];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"number" => ["max|5"]
		]);

		$this->assertSame($payload, $data);
		$payload = ["number" => 5];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"number" => ["max|5"]
		]);
		$this->assertSame($payload, $data);

		$payload = ["number" => 5];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"number" => ["max|4"]
		]);
		$this->assertFalse($data);
	}

	public function testValidateRequestMin(): void
	{
		$payload = ["number" => 10];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"number" => ["min|5"]
		]);
		$this->assertSame($payload, $data);

		$payload = ["number" => 5];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"number" => ["min|5"]
		]);
		$this->assertSame($payload, $data);

		$payload = ["number" => 3];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"number" => ["min|4"]
		]);
		$this->assertFalse($data);
	}

	public function testValidateRequestMaxLength(): void
	{
		$payload = ["string" => "abcd"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"string" => ["maxlength|4"]
		]);
		$this->assertSame($payload, $data);

		$payload = ["string" => "abcd"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"string" => ["maxlength|3"]
		]);
		$this->assertFalse($data);

		$payload = ["string" => "abcd"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"string" => ["maxlength|5"]
		]);
		$this->assertSame($payload, $data);
	}

	public function testValidateRequestMinLength(): void
	{
		$payload = ["string" => "abcd"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"string" => ["minlength|4"]
		]);
		$this->assertSame($payload, $data);

		$payload = ["string" => "abcd"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"string" => ["minlength|3"]
		]);
		$this->assertSame($payload, $data);

		$payload = ["string" => "abcd"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"string" => ["minlength|5"]
		]);
		$this->assertFalse($data);
	}

	public function testValidateRequestSymbol(): void
	{
		$payload = ["string" => "qrstuv"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"string" => ["symbol"]
		]);
		$this->assertFalse($data);

		$payload = ["string" => "qrs&uv"];
		$controller = new Controller(new Request(request: $payload));
		$data = $controller->validateRequest([
			"string" => ["symbol"]
		]);
		$this->assertSame($payload, $data);
	}
}
