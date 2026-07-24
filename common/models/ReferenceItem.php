<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%reference_items}}".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int|null $type_id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $sort_order
 * @property bool $is_active
 * @property bool $is_deleted
 * @property string|null $legacy_table
 * @property int|null $legacy_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ReferenceItem|null $parent
 * @property ReferenceItem[] $children
 * @property ReferenceItem|null $type
 */
class ReferenceItem extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%reference_items}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['code', 'name'], 'required'],
            [['parent_id', 'type_id', 'sort_order', 'legacy_id'], 'integer'],
            [['description'], 'string'],
            [['is_active', 'is_deleted'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
            [['legacy_table'], 'string', 'max' => 100],
            [['code'], 'unique'],
            [
                ['parent_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => self::class,
                'targetAttribute' => ['parent_id' => 'id'],
            ],
            [
                ['type_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => self::class,
                'targetAttribute' => ['type_id' => 'id'],
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Родитель',
            'type_id' => 'Тип',
            'code' => 'Код',
            'name' => 'Наименование',
            'description' => 'Описание',
            'sort_order' => 'Порядок сортировки',
            'is_active' => 'Активна',
            'is_deleted' => 'Удалена',
            'legacy_table' => 'Исходная таблица',
            'legacy_id' => 'Исходный ID',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * Родительский элемент.
     *
     * @return ActiveQuery
     */
    public function getParent(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'parent_id']);
    }

    /**
     * Дочерние элементы.
     *
     * @return ActiveQuery
     */
    public function getChildren(): ActiveQuery
    {
        return $this->hasMany(self::class, ['parent_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'name' => SORT_ASC]);
    }

    /**
     * Тип элемента.
     *
     * @return ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'type_id']);
    }

    /**
     * Возвращает корневые элементы дерева.
     *
     * @return self[]
     */
    public static function getRootNodes(): array
    {
        return static::find()
            ->where([
                'parent_id' => null,
                'is_deleted' => false,
            ])
            ->orderBy([
                'sort_order' => SORT_ASC,
                'name' => SORT_ASC,
            ])
            ->all();
    }

    /**
     * Возвращает дерево элементов, начиная с указанного родителя.
     *
     * @param int|null $parentId
     *
     * @return self[]
     */
    public static function getTree(?int $parentId = null): array
    {
        return static::find()
            ->where([
                'parent_id' => $parentId,
                'is_deleted' => false,
            ])
            ->orderBy([
                'sort_order' => SORT_ASC,
                'name' => SORT_ASC,
            ])
            ->all();
    }

    /**
     * Возвращает все активные элементы классификатора.
     *
     * @return self[]
     */
    public static function getAllActive(): array
    {
        return static::find()
            ->where(['is_deleted' => false])
            ->orderBy([
                'sort_order' => SORT_ASC,
                'name' => SORT_ASC,
            ])
            ->all();
    }

    /**
     * Группирует элементы классификатора по родительскому идентификатору.
     *
     * @return array<int|null, self[]>
     */
    public static function getGroupedTree(): array
    {
        $grouped = [];

        foreach (static::getAllActive() as $item) {
            $grouped[$item->parent_id][] = $item;
        }

        return $grouped;
    }

    /**
     * Проверяет наличие дочерних элементов.
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * Возвращает цепочку родителей текущего элемента.
     *
     * Первый элемент массива — корень дерева,
     * последний — текущий элемент.
     *
     * @return self[]
     */
    public function getPath(): array
    {
        $path = [];
        $current = $this;

        while ($current !== null) {
            array_unshift($path, $current);
            $current = $current->parent;
        }

        return $path;
    }

}
