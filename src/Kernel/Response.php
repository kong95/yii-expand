<?php


namespace Kong95\Yii\Kernel;


class Response
{


    /**
     *
     * config/main.php
     * 'components'=>[
     *      'response'=>[
     *          'class' => 'yii\web\Response',
     *          'charset' => 'UTF-8',
     *          'on beforeSend' => function ($event) {
     *              \Kong95\Kernel\Response::api($event)
     *          }
     *      ]
     * ]
     *
     * @param $event
     */
    public static function api($event)
    {
        /** @var \yii\web\Response  $response*/
        $response = $event->sender;
        $response->format=\yii\web\Response::FORMAT_JSON;

        if($response->isOk){
            $data['code'] = $response->statusCode;
            $data['message'] = $response->statusText;
            is_null($response->data) || $data['data'] = $response->data;
        }else{
            $data['code'] = $response->data['code'];
            $data['message'] = $response->data['message'];
            YII_DEBUG && $data['data'] = $response->data;
        }
        $response->statusCode = 200;
        $response->data = $data;
    }
}