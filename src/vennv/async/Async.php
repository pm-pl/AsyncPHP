<?php

namespace vennv\async;

final class Async {

    private static mixed $value = null;
    private static string $error = "";
    private static bool $hasError = false;
    private static array $awaiting = [];
    private static array $listTerminated = [];

    public const SUCCESS = "success";
    public const ERROR = "error";

    public static function create(callable $callable) : Async {

        try {

            $fiber = new \Fiber($callable);
            $fiber->start();

            self::$value = $fiber->getReturn();

        } catch (\Throwable $e) {
            self::$hasError = true;
            self::$error = $e->getMessage();
        }

        self::run();

        return new self();
    }

    private static function addWait(Await $await) : bool {
        try {
            self::$awaiting[] = $await;
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private static function dropAwaits() : bool {
        try {
            foreach (self::$listTerminated as $index) {
                unset(self::$awaiting[$index]);
            }
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private static function addTerminated(int $index) : bool {
        try {
            self::$listTerminated[] = $index;
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private static function processFiber(?\Fiber $fiber, int $index) : bool {
        if (!is_null($fiber)) {
            if ($fiber->isSuspended() && !$fiber->isTerminated()) {
                $fiber->resume();
            } elseif ($fiber->isTerminated()) {
                self::addTerminated($index);
            }
            return true;
        }
        return false;
    }

    public static function await(callable $callable) : mixed {

        $fiber = new \Fiber($callable);

        $await = new Await(
            \Fiber::getCurrent(),
            $fiber
        );

        self::addWait($await);

        $fiber->start();

        while (!$fiber->isTerminated()) {

            $fiber->resume();

            if (!$fiber->isTerminated()) {
                \Fiber::suspend();
            } else {
                break;
            }
        }

        return $fiber->getReturn();
    }

    private static function run() : void {

        while (count(self::$awaiting) > 0) {

            foreach (self::$awaiting as $index => $data) {

                $parent = $data->getCurrent();
                $fiber = $data->getFiber();

                if (!self::processFiber($parent, $index)) {
                    self::processFiber($fiber, $index);
                }

            }

            self::dropAwaits();

            self::$awaiting = array_values(self::$awaiting);
        }

    }

    public static function awaitAll(array $await) : array {

        $result = [];

        foreach ($await as $value) {
            $result[] = self::await($value);
        }

        return $result;
    }

    public static function awaitAny(array $await) : mixed {

        $result = null;

        foreach ($await as $value) {
            $result = self::await($value);
            if (!is_null($result)) {
                break;
            }
        }

        return $result;
    }

    public static function hasError() : bool {
        return self::$hasError;
    }

    public static function getError() : string {
        return self::$error;
    }

    public static function getValue() : mixed {
        return self::$value;
    }

    public function fThen(array $callable) : Async {

        global $error;
        $error = self::$hasError;

        if ($error) {
            if (isset($callable[self::ERROR])) {
                $callable[self::ERROR](self::$error);
            }
        } else {
            if (isset($callable[self::SUCCESS])) {
                $callable[self::SUCCESS](self::$value);
            }
        }

        return $this;
    }

}