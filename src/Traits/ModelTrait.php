<?php


namespace Kong95\Yii\Traits;


trait ModelTrait
{

    /**
     * @return string
     */
    public function getFirstError(): string
    {
        $errors = array_values($this->firstErrors);
        return reset($errors);
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function isPhone($attribute, $params)
    {
        $message = $attribute . '格式错误.';
        $regex = "/^1[3456789]{1}\d{9}$/";
        $match = preg_match($regex, $this->$attribute);
        $match === 0 and $this->addError($attribute, $message);
    }
}