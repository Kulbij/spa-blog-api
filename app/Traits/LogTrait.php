<?php

namespace App\Traits;

use App\Services\LogService;

/**
 * Trait LogTrait
 *
 * @package App\Traits
 */
trait LogTrait
{
    /**
     * @var string
     */
    private string $typeInfo = 'info';

    /**
     * @var string
     */
    private string $typeError = 'error';

    /**
     * @var string
     */
    private string $typeWarning = 'warning';

    /**
     * @var string
     */
    private string $code;

    /**
     * @param  string  $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @param  string  $message
     * @param  string  $action
     * @param  array  $data
     */
    public function info(string $message, string $action, array $data): void
    {
        $this->log($this->typeInfo, $message, $action, $data);
    }

    /**
     * @param  string  $message
     * @param  string  $action
     * @param  array  $data
     */
    public function exception(string $message, string $action, array $data): void
    {
        $this->log($this->typeError, $message, $action, $data);
    }

    /**
     * @param  string  $message
     * @param  string  $action
     * @param  array  $data
     */
    public function warning(string $message, string $action, array $data): void
    {
        $this->log($this->typeWarning, $message, $action, $data);
    }

    /**
     * @param  string  $type
     * @param  string  $message
     * @param  string  $action
     * @param  array  $data
     */
    private function log(string $type, string $message, string $action, array $data): void
    {
        LogService::$type(
            $message,
            __CLASS__ . ':' . $action,
            $type .'_'. $this->code .'_'. strtoupper($action),
            array_merge(['manager' => auth()->user()->id], $data)
        );
    }
}
