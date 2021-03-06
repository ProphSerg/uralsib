<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Залитые Hypercom';

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    #'filterRowOptions' => ['class' => 'atmTableFilter'],
    #'pjax' => true,
    'hover' => true,
    #'condensed' => true,
    'floatHeader' => true,
    'tableOptions' => ['class' => 'posTable'],
#'striped' => false,
#'headerRowOptions' => ['class' => 'kartik-sheet-style atmTable'],
#'filterRowOptions' => ['class' => 'kartik-sheet-style'],
    'columns' => [
        [
            'attribute' => 'KCV',
            'width' => '50px',
        ],
        [
            'attribute' => 'Serial',
            'width' => '70px',
        ],
        [
            'attribute' => 'DateEnter',
            'format' => ['date', 'php:d/m/Y H:i:s'],
            'width' => '150px',
        ],
        [
            'attribute' => 'reg.FullDesc',
            'label' => 'ТСП (Дата регистрации, Номер терминала, Название, Адрес)',
            'format' => 'raw',
            'value' => function($data) {
                $str = '';
                $i = 0;
                $tt = [];
                foreach ($data['reg'] as $item) {
                    $str .= '<p>' . implode('. ', [
                                Yii::$app->formatter->asDatetime($item['DateReg'], 'php:d/m/Y H:i'),
                                '<b>' . $item['TerminalID'] . "</b>",
                                $item['Name'],
                                $item['Address'],
                    ]);
                }
                return $str;
            }
        ]
    ],
]);

#var_dump($dataProvider->query);
