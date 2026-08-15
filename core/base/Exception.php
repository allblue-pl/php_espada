<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);

/**
 * @phpstan-type T_OnErrorListener callable(\Throwable $e): void
 */
class Exception {
    /** @var list<T_OnErrorListener> */
	static private array $OnErrorListeners = [];

	static public function AddOnErrorListener(callable $exceptionListener): void {
		self::$OnErrorListeners[] = $exceptionListener;
	}

    static public function ClearErrorListeners(): void {
        self::$OnErrorListeners = [];
    }

	static public function ErrorHandler(int $errno, string $errstr, string $errfile,
			int $errline): bool {
		throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
	}

	static public function ExceptionHandler(\Throwable $e): void {
		self::NotifyListeners($e);

		if (!EDEBUG)
			die (INTERNAL_ERROR_MESSAGE);

		echo '<b>Exception:</b> ' . $e->getMessage() . '<br /><br />'."\n\n";

        /** @var array<array{
            file?: string,
            function?: string,
            line: string,
        }> */
		$backtraceArray = $e->getTrace();

		array_unshift($backtraceArray, [
			'file' => $e->getFile(),
			'line' => $e->getLine()
		]);

		foreach ($backtraceArray as $backtraceLine) {
			if (isset($backtraceLine['file']))
				echo '<b>' . $backtraceLine['file'] . ':' . $backtraceLine['line'] . '</b><br />'."\n";
			else
				echo '<b>Unknown</b><br />' . "\n";

			if (isset($backtraceLine['function'])) {
				echo "\t" . '&nbsp;&nbsp;&nbsp;' . $backtraceLine['function'] .
						'<br />' . "\n";
			} else
				echo "\t" . '&nbsp;&nbsp;&nbsp; Unknown <br />';
		}

		die();
	}

	static public function NotifyListeners(\Throwable $e): void {
		foreach (self::$OnErrorListeners as $onErrorListener)
			$onErrorListener($e);
	}

	static public function RemoveOnErrorListener(callable $exceptionListener): void {
		$index = array_search($exceptionListener, self::$OnErrorListeners);
		if ($index === false)
			throw new \Exception('`exception_listener` not in listeners array.');

		array_splice(self::$OnErrorListeners, $index, 1);
	}

	// static public function ShutdownHandler()
	// {
	// 	$error = error_get_last();
	//
	// 	if ($error === null)
	// 		return;
	//
	// 	throw new \Exception($error['message'], $error['type'], 0,
	// 			$error['file'], $error['line']);
	// }

}
