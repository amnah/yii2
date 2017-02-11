<?php

namespace app\components;

use Yii;

class BaseModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'value' => [$this, 'getTimestampValue'],
            ],
        ];
    }

    /**
     * Get value for TimestampBehavior
     * @return string
     */
    public function getTimestampValue()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Load post data into model
     * @param string $formName
     * @return bool
     * @see \yii\base\Model::load()
     */
    public function loadPost($formName = null)
    {
        return $this->load(Yii::$app->request->post(), $formName);
    }

    /**
     * Load post data into model and validate
     * Returns null if no post data is loaded. Otherwise returns validation result
     * @param string $formName
     * @param array $attributeNames
     * @param bool $clearErrors
     * @return bool|null
     */
    public function loadPostAndValidate($formName = null, $attributeNames = null, $clearErrors = true)
    {
        if (!$this->loadPost($formName)) {
            return null;
        }
        return $this->validate($attributeNames, $clearErrors);
    }

    /**
     * Load post data into model and save (with validation)
     * Returns null if no post data is loaded. Otherwise returns save result
     * @param string $formName
     * @param array $attributeNames
     * @return bool|null
     */
    public function loadPostAndSave($formName = "", $attributeNames = null)
    {
        if (!$this->loadPost($formName)) {
            return null;
        }
        return $this->save(true, $attributeNames);
    }
}