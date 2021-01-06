<?php


namespace Kong95\Traits;

use Yii;
use Kong95\Kernel\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use Kong95\Kernel\SoftDeleteBehavior;

trait ActiveRecordTrait
{

    static $create_at = 'create_at';
    static $update_at = 'update_at';
    static $create_by = 'create_user_id';
    static $update_by = 'update_user_id';


    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [self::$create_at, self::$update_at],
                    ActiveRecord::EVENT_BEFORE_UPDATE => self::$update_at,
                    ActiveRecord::EVENT_BEFORE_DELETE => self::$update_at,
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => [
                        self::$create_by,
                        self::$update_by
                    ],
                    ActiveRecord::EVENT_BEFORE_UPDATE => self::$update_by,
                    ActiveRecord::EVENT_BEFORE_DELETE => self::$update_by,
                ],
                'value' => function ($event) {
                    return self::getUserId();
                },
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'status' => ActiveRecord::DELETE,
                    self::$update_at => date('Y-m-d H:i:s'),
                    self::$update_by => self::getUserId()
                ],
                // mutate native `delete()` method.
                'replaceRegularDelete' => true,
            ],
        ];
    }


    /**
     * @param string|null $alias
     * @param bool $isCondition
     * @return \yii\db\ActiveQuery
     */
    public static function findAlias(string $alias = null, bool $isCondition = true)
    {
        $alias = empty($alias) ? (static::tableName()) : $alias;
        $query = parent::find()->alias($alias);
        $isCondition && $query->where(['<>',$alias.'.status',static::DELETE]);
        return $query;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public static function find()
    {
        return static::findAlias();
    }


    /**
     * @param $attributes
     * @param string $condition
     * @param array $params
     * @return int
     */
    public static function updateAll($attributes, $condition = '', $params = [])
    {
        $attributes[self::$update_at] = date('Y-m-d H:i:s');
        $attributes[self::$update_by] = self::getUserId();

        $condition = [
            'AND',
            $condition,
            ['<>', 'status', ActiveRecord::DELETE],
        ];
        return parent::updateAll($attributes, $condition, $params);
    }


    /**
     * @param null $condition
     * @param array $params
     * @return int
     */
    public static function deleteAll($condition = null, $params = [])
    {
        $attributes = ['status' => ActiveRecord::DELETE];
        return static::updateAll($attributes, $condition, $params);
    }


    /**
     * @param $counters
     * @param string $condition
     * @param array $params
     * @return int
     */
    public static function updateAllCounters($counters, $condition = '', $params = [])
    {
        $condition = [
            'AND',
            $condition,
            ['<>', 'status', ActiveRecord::DELETE]
        ];
        return parent::updateAllCounters($counters, $condition, $params);
    }


    protected static function getUserId()
    {
        return Yii::$app->user->id ?? '';
    }
}