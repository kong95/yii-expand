<?php


namespace Kong95\Yii\Kernel;


use Yii;
use Kong95\Yii\Traits\ActiveRecordTrait;

abstract class ActiveRecord extends \yii\db\ActiveRecord
{
    use ActiveRecordTrait;

    //启用
    const ENABLE=1;
    //禁用
    const DISABLE=0;
    //删除
    const DELETE=-1;

}