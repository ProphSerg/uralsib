<?php

use kartik\grid\GridView;
use yii\helpers\Html;

use app\assets\ClipboardAsset;
ClipboardAsset::Instantiate($this, '.btnClip');

$this->title = 'Зарегистрированные терминалы';

echo GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	#'filterRowOptions' => ['class' => 'atmTableFilter'],
	#'pjax' => true,
	'hover' => true,
	'condensed' => true,
	'floatHeader' => true,
	'tableOptions' => ['class' => 'posTable'],
#'striped' => false,
#'headerRowOptions' => ['class' => 'kartik-sheet-style atmTable'],
#'filterRowOptions' => ['class' => 'kartik-sheet-style'],
	'columns' => [
		[
			'class' => \kartik\grid\ExpandRowColumn::className(),
			'width' => '50px',
			'value' => function ($model, $key, $index, $column) {
				return GridView::ROW_COLLAPSED;
			},
			'detailUrl' => 'register-detail',
			/*
			 'detail' => function ($model, $key, $index, $column) {
			 
				return Yii::$app->controller->renderPartial('_register-detail', ['model' => $model]);
			},
			 * 
			 */
				'headerOptions' => ['class' => 'kartik-sheet-style'],
				'expandOneOnly' => true,
			],
			[
				'attribute' => 'DateReg',
				'width' => '70px',
			],
			[
				'attribute' => 'TerminalID',
				'width' => '70px',
			],
			[
				'attribute' => 'Name',
			],
			[
				'attribute' => 'Address',
			],
		],
	]);

#var_dump($dataProvider->query);
