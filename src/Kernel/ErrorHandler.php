<?php


namespace Kong95\Kernel;


use Yii;
use Kong95\Exception\Exception;
use yii\web\HttpException;

class ErrorHandler extends \yii\web\ErrorHandler
{

    protected function convertExceptionToArray($exception)
    {
        $instanced = $exception instanceof Exception;
        if (YII_DEBUG || $instanced) {
            $array = [
                'name' => $instanced ? $exception->getName() : 'Exception',
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ];
        } else {
            $message = $exception->getMessage();
            $trace = $exception->getTraceAsString();
            Yii::error($message . PHP_EOL . $trace);

            $array = [
                'name' => 'Exception',
                'message' => $exception->getMessage(),
                'code' => 500
            ];
        }

        if ($exception instanceof HttpException) {
            $array['status'] = $exception->statusCode;
        }

        if (YII_DEBUG) {
            $array['type'] = get_class($exception);
            $array['file'] = $exception->getFile();
            $array['line'] = $exception->getLine();
            $array['stack-trace'] = explode("\n", $exception->getTraceAsString());
            if ($exception instanceof \yii\db\Exception) {
                $array['error-info'] = $exception->errorInfo;
            }
        }

        if (($prev = $exception->getPrevious()) !== null) {
            $array['previous'] = $this->convertExceptionToArray($prev);
        }
        Yii::debug($array);
        return $array;
    }

}