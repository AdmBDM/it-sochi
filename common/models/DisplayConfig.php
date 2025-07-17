<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $screen_id
 * @property string $layout_order
 * @property string|null $duration_map
 * @property bool $enabled
 * @property string $updated_at
 */
class DisplayConfig extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%display_config}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['screen_id', 'layout_order'], 'required'],
            [['screen_id'], 'string', 'max' => 64],
            [['layout_order', 'duration_map'], 'string'],
            [['enabled'], 'boolean'],
            [['updated_at'], 'safe'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'screen_id' => 'ID экрана',
            'layout_order' => 'Порядок блоков (JSON)',
            'duration_map' => 'Длительности блоков (JSON)',
            'enabled' => 'Включено',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return array
     */
    public function getBlockOrder(): array
    {
        return json_decode($this->layout_order, true) ?: [];
    }

    /**
     * @return array
     */
    public function getDurations(): array
    {
        return json_decode($this->duration_map, true) ?: [];
    }
}
