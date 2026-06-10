<?php
// common/modules/api/controllers/AgentController.php

namespace common\modules\api\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\models\DiscoveredPrinter;

class AgentController extends Controller  // ← было BaseAgentController, стало AgentController
{
    public $enableCsrfValidation = false;

    public function behaviors(): array
    {
        return [
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function beforeAction($action): bool
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $key = Yii::$app->request->headers->get('X-Agent-Key');
        $expected = Yii::$app->params['agentApiKey'] ?? null;

        if (!$expected || $key !== $expected) {
            Yii::$app->response->statusCode = 403;
            Yii::$app->response->data = ['status' => 'error', 'message' => 'Invalid key'];
            return false;
        }

        return parent::beforeAction($action);
    }

    public function actionReport(): array
    {
        $raw = Yii::$app->request->getRawBody();
        $data = json_decode($raw, true);

        if (!$data || empty($data['printers'])) {
            return ['status' => 'ok', 'received' => 0];
        }

        $received = 0;
        foreach ($data['printers'] as $printer) {
            $model = DiscoveredPrinter::findOne([
                'host' => $data['hostname'],
                'local_name' => $printer['name'],
            ]) ?? new DiscoveredPrinter();

            $model->host = $data['hostname'];
            $model->host_ip = $data['ip'];
            $model->local_name = $printer['name'];
            $model->connection_type = $printer['port'];
            $model->driver_name = $printer['driver'] ?? null;
            $model->printer_type = $this->classifyPrinterType($printer);
            $model->description = $printer['description'] ?? null;
            $model->discovered_at = $data['timestamp'];
            $model->last_seen_at = date('Y-m-d H:i:s');
            $model->source = 'wmi';
            $model->is_local = true;
            $model->ip = $data['ip'] . ':' . md5($printer['name']);

            if ($model->save(false)) {
                $received++;
            }
        }

        return ['status' => 'ok', 'received' => $received];
    }

    private function classifyPrinterType(array $printer): string
    {
        if (!empty($printer['is_receipt'])) return 'receipt';
        if (!empty($printer['is_label'])) return 'label';
        if (preg_match('/zebra|tsc|godex|argox|brother ql|dymo|xprinter/i', $printer['driver'])) return 'label';
        if (preg_match('/receipt|pos|thermal|escpos|tm-|star|citizen|bixolon/i', $printer['driver'])) return 'receipt';
        return 'local';
    }
}