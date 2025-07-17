<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\WorkerSchedule;

class XmlParserController extends Controller
{
    private array $sources = [
        'abs-wrk-mo' => '\\\\abs-wrk-mo\\Monitor\\data.xml',
        'geely2vid' => '\\\\Geely2vid\\Monitor\\data.xml',
        'servviewge' => '\\\\Servviewge\\Monitor\\data.xml',
    ];

    public function actionImport()
    {
        foreach ($this->sources as $sourceId => $path) {
            echo "Обработка: {$sourceId} ({$path})\n";

            if (!file_exists($path)) {
                echo "⚠️  Файл не найден: $path\n";
                continue;
            }

            $xml = simplexml_load_file($path);
            if (!$xml) {
                echo "⚠️  Ошибка парсинга XML: $path\n";
                continue;
            }

            // Удаляем старые записи для этого источника (пока это отключаем)
//            WorkerSchedule::deleteAll(['source_id' => $sourceId]);

            foreach ($xml->worker as $worker) {
                $name = (string)$worker->personality->name;
                $avatar = (string)$worker->personality->avatar;

                foreach ($worker->orders->order as $order) {
                    $model = new WorkerSchedule([
                        'source_id' => $sourceId,
                        'worker_name' => $name,
                        'avatar' => $avatar,
                        'car_number' => (string)$order->{'car-number'},
                        'car_region' => (string)$order->{'car-region'},
                        'car_model' => (string)$order->{'car-model'},
                        'start_repair' => trim((string)$order->{'start-repair'}),
                        'finish_repair' => trim((string)$order->{'finish-repair'}),
                        'condition' => (string)$order->condition,
                    ]);
                    if (!$model->save()) {
                        echo "❌ Ошибка сохранения: " . json_encode($model->errors) . "\n";
                    }
                }
            }

            echo "✅ Импорт из '{$sourceId}' завершён.\n";
        }
    }
}
