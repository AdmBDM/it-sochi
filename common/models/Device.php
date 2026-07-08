<?php

namespace common\models;

use common\models\PrinterPageCounter;
use common\models\PrinterRepair;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "devices".
 *
 * @property int $id
 * @property int $model_id
 * @property int $status_id
 * @property int $workplace_id
 * @property string|null $serial_number
 * @property string|null $inventory_number
 * @property string|null $name
 * @property string|null $comment
 * @property string|null $mac_address
 * @property string|null $printer_metrics
 * @property string $created_at
 * @property string $updated_at
 *
 * @property DeviceModel $model
 * @property DeviceStatus $status
 * @property Workplace $workplace
 * @property Movement[] $movements
 * @property DiscoveredPrinter[] $discoveredPrinters
 */
class Device extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'devices';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['model_id', 'status_id', 'workplace_id'], 'required'],
            [['model_id', 'status_id', 'workplace_id'], 'integer'],
            [['comment', 'printer_metrics'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['serial_number', 'inventory_number', 'name'], 'string', 'max' => 255],
            [['mac_address'], 'string', 'max' => 17],
            [['mac_address'], 'match', 'pattern' => '/^([0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/', 'message' => 'Неверный формат MAC-адреса'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'model_id' => 'Модель устройства',
            'status_id' => 'Статус',
            'workplace_id' => 'Рабочее место',
            'serial_number' => 'Серийный номер',
            'inventory_number' => 'Инвентарный номер',
            'name' => 'Название',
            'comment' => 'Комментарий',
            'mac_address' => 'MAC-адрес',
            'created_at' => 'Создано',
            'updated_at' => 'Обновлено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getModel(): ActiveQuery
    {
        return $this->hasOne(DeviceModel::class, ['id' => 'model_id']);
    }

    public function getStatus(): ActiveQuery
    {
        return $this->hasOne(DeviceStatus::class, ['id' => 'status_id']);
    }

    public function getWorkplace(): ActiveQuery
    {
        return $this->hasOne(Workplace::class, ['id' => 'workplace_id']);
    }

    public function getMovements(): ActiveQuery
    {
        return $this->hasMany(Movement::class, ['device_id' => 'id']);
    }

    /**
     * Связанные discovered_printers
     * @return ActiveQuery
     */
    public function getDiscoveredPrinters(): ActiveQuery
    {
        return $this->hasMany(DiscoveredPrinter::class, ['matched_device_id' => 'id']);
    }

    // Дополнительные геттеры для вложенных данных

    /**
     * @return mixed|null
     */
    public function getBrand(): mixed
    {
        return $this->model ? $this->model->brand : null;
    }

    /**
     * @return mixed|null
     */
    public function getType(): mixed
    {
//        return $this->model ? $this->type->name : null;
        return $this->model ? $this->model->type : null;
    }

    /**
     * @return Employee|null
     */
    public function getEmployee(): ?Employee
    {
        return $this->workplace ? $this->workplace->employee : null;
    }

    /**
     * @return Location|null
     */
    public function getLocation(): ?Location
    {
        return $this->workplace ? $this->workplace->location : null;
    }

    /**
     * Возвращает информацию по устройству в читабельном виде
     * @param bool $isStr
     *
     * @return array|string
     */
    public function getDeviceDataStr(bool $isStr = true): array|string
    {
        $type  = $this->model->type->name ?? '';
        $brand = $this->model->brand->name ?? '';
        $model = $this->model->name ?? '';
        $employee = $this->workplace->employee->famIO ?? '';

        if ($isStr) return trim("Тип: {$type}, Бренд: {$brand}, Модель: {$model}, Сотрудник: {$employee}");
        return ["Сотрудник" => $employee, "Тип" => $type, "Бренд" => $brand, "Модель" => $model];
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        $type  = $this->model->type->name ?? '';
        $brand = $this->model->brand->name ?? '';
        $model = $this->model->name ?? '';
        $sn = $this->serial_number ?? '';
        return trim("{$type} {$brand} {$model}, s/n: {$sn}");
    }

    /**
     * @return bool
     */
    public function getIsPrinter(): bool
    {
        return $this->model->type->name === 'Принтер' || $this->model->type->name === 'МФУ';
    }

    /**
     * @return array
     */
    public function getPrinterStats(): array
    {
        if (!$this->getIsPrinter()) return [];

        $metrics = json_decode($this->printer_metrics ?? '{}', true);

        return [
            'total_pages' => $metrics['total_pages'] ?? null,
            'last_reading' => $metrics['last_reading_at'] ?? null,
            'monthly_load' => PrinterPageCounter::getMonthlyLoad($this->id),
            'repairs_count' => $this->getPrinterRepairs()->count(),
            'repairs_cost' => $this->getPrinterRepairs()->sum('cost') ?: 0,
            'cartridge_changes' => $this->getCartridgeReplacements()->count(),
            'last_repair' => $this->getPrinterRepairs()->orderBy('started_at DESC')->one(),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPrinterRepairs(): ActiveQuery
    {
        return $this->hasMany(PrinterRepair::class, ['device_id' => 'id'])
            ->orderBy(['started_at' => SORT_DESC]);
    }
}
