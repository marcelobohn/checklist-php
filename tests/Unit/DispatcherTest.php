<?php

namespace Tests\Unit;

use App\Dispatcher;
use PHPUnit\Framework\TestCase;

/**
 * Dispatcher de ações (src/Dispatcher.php) — o "qual ?acao= roda qual handler".
 */
class DispatcherTest extends TestCase
{
	public function testExecutaOHandlerDaAcaoCasada(): void
	{
		$chamou = null;
		(new Dispatcher())
			->on('grava', function () use (&$chamou) { $chamou = 'grava'; })
			->on('apaga', function () use (&$chamou) { $chamou = 'apaga'; })
			->run('apaga');

		$this->assertSame('apaga', $chamou);
	}

	public function testAcaoDesconhecidaNaoFazNada(): void
	{
		$chamou = false;
		(new Dispatcher())
			->on('grava', function () use (&$chamou) { $chamou = true; })
			->run('inexistente');

		$this->assertFalse($chamou);
	}

	public function testAcaoNulaNaoFazNada(): void
	{
		$chamou = false;
		(new Dispatcher())
			->on('grava', function () use (&$chamou) { $chamou = true; })
			->run(null);

		$this->assertFalse($chamou);
	}

	public function testExecutaApenasOHandlerCorrespondente(): void
	{
		$executados = array();
		(new Dispatcher())
			->on('grava', function () use (&$executados) { $executados[] = 'grava'; })
			->on('apaga', function () use (&$executados) { $executados[] = 'apaga'; })
			->run('grava');

		$this->assertSame(array('grava'), $executados);
	}

	public function testOnEhEncadeavel(): void
	{
		$d = new Dispatcher();
		$this->assertSame($d, $d->on('grava', function () {}));
	}

	public function testUltimoHandlerRegistradoVencePraMesmaAcao(): void
	{
		$chamou = null;
		(new Dispatcher())
			->on('grava', function () use (&$chamou) { $chamou = 'primeiro'; })
			->on('grava', function () use (&$chamou) { $chamou = 'segundo'; })
			->run('grava');

		$this->assertSame('segundo', $chamou);
	}
}
