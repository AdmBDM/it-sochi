<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Модель для таблицы `discovered_printers`.
 * Хранит данные о принтерах, обнаруженных через SNMP (сетевые) и WMI (локальные).
 *
 * @property int $id
 * @property string $ip              Уникальный идентификатор: IP для сетевых, IP:hash для локальных
 * @property string|null $snmp_name  Имя устройства из SNMP (для сетевых) или имя PC (для локальных)
 * @property string|null $snmp_descr Описание из SNMP / WMI-драйвер / доп. информация
 * @property string|null $guessed_model Угаданная модель по OID или драйвер из WMI
 * @property string|null $host        Имя хоста (PC), на котором установлен локальный принтер
 * @property string|null $local_name  Локальное имя принтера в Windows (WMI)
 * @property string|null $connection_type Тип подключения: USB001, LPT1, FILE: и т.д. (WMI)
 * @property string|null $discovered_at Время первого обнаружения (timestamp от агента)
 * @property string $source           Источник обнаружения: 'snmp' | 'wmi'
 * @property bool $is_local          true = локальный принтер (WMI), false = сетевой (SNMP)
 * @property int|null $matched_device_id Ссылка на таблицу devices (ручное сопоставление)
 * @property string|null $last_seen_at Время последнего обнаружения
 * @property string|null $mac_address  MAC-адрес (для сетевых принтеров)
 * @property string|null $serial_snmp  Серийный номер из SNMP
 * @property string|null $last_ip      Последний известный IP (для сетевых)
 */

class DiscoveredPrinter extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%discovered_printers}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['ip', 'source'], 'required'],
            [['ip'], 'string', 'max' => 15],
            [['ip'], 'unique'],
            [['snmp_name', 'host', 'local_name', 'connection_type', 'guessed_model'], 'string', 'max' => 255],
            [['snmp_descr'], 'string'],
            [['source'], 'string', 'max' => 20],
            [['source'], 'in', 'range' => ['snmp', 'wmi']],
            [['is_local'], 'boolean'],
            [['is_local'], 'default', 'value' => false],
            [['discovered_at', 'last_seen_at'], 'safe'],
            [['matched_device_id'], 'integer'],
            [['matched_device_id'], 'exist', 'skipOnError' => true, 'targetClass' => Device::class, 'targetAttribute' => ['matched_device_id' => 'id']],
            [['mac_address'], 'string', 'max' => 17],
            [['serial_snmp'], 'string', 'max' => 100],
            [['last_ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'ip' => 'IP / Уникальный ключ',
            'snmp_name' => 'Имя (SNMP/WMI)',
            'snmp_descr' => 'Описание',
            'guessed_model' => 'Модель / Драйвер',
            'host' => 'Хост (PC)',
            'local_name' => 'Локальное имя принтера',
            'connection_type' => 'Порт подключения',
            'discovered_at' => 'Первое обнаружение',
            'source' => 'Источник',
            'is_local' => 'Локальный',
            'matched_device_id' => 'Сопоставленное устройство',
            'last_seen_at' => 'Последнее обнаружение',
            'mac_address' => 'MAC-адрес',
            'serial_snmp' => 'Серийный номер',
            'last_ip' => 'Последний IP',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getDevice(): ActiveQuery
    {
        return $this->hasOne(Device::class, ['id' => 'matched_device_id']);
    }

    /**
     * Автосопоставление по IP
     *
     * @return bool
     * @throws Exception
     */
    public function tryMatchByIp(): bool
    {
        $device = Device::find()
            ->where(['snmp_ip' => $this->ip])
            ->orWhere(['LIKE', 'comment', $this->ip]) // fallback
            ->one();

        if ($device) {
            $this->matched_device_id = $device->id;
            return $this->save(false, ['matched_device_id']);
        }
        return false;
    }
}
