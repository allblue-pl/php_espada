<?php namespace E;
defined('_ESPADA') or die(NO_ACCESS);


class Exception {
	static private array $OnErrorListeners = [];

	static public function AddOnErrorListener(callable $exception_listener): void {
		self::$OnErrorListeners[] = $exception_listener;
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

        /* @phpstan-ignore booleanNot.alwaysFalse */
		if (!EDEBUG)
			die (INTERNAL_ERROR_MESSAGE);

		echo '<b>Exception:</b> ' . $e->getMessage() . '<br /><br />'."\n\n";

		$backtrace_array = $e->getTrace();

		array_unshift($backtrace_array, [
			'file' => $e->getFile(),
			'line' => $e->getLine()
		]);

		foreach ($backtrace_array as $backtrace_line) {
			if (isset($backtrace_line['file']))
				echo '<b>' . $backtrace_line['file'] . ':' . $backtrace_line['line'] . '</b><br />'."\n";
			else
				echo '<b>Unknown</b><br />' . "\n";

			if (isset($backtrace_line['function'])) {
				echo "\t" . '&nbsp;&nbsp;&nbsp;' . $backtrace_line['function'] .
						'<br />' . "\n";
			} else
				echo "\t" . '&nbsp;&nbsp;&nbsp; Unknown <br />';
		}

		die();
	}

	static public function NotifyListeners(\Throwable $e): void {
		foreach (self::$OnErrorListeners as $on_error_listener)
			$on_error_listener($e);
	}

	static public function RemoveOnErrorListener(callable $exception_listener): void {
		$index = array_search($exception_listener, self::$OnErrorListeners);
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
