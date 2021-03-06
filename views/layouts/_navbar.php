<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
#use app\common\myNavBar as NavBar;
use app\common\myMenuHelper;

NavBar::begin([
	'brandLabel' => 'УРАЛСИБ',
	'brandUrl' => Yii::$app->homeUrl,
	'options' => [
		'class' => 'navbar navbar-fixed-top navbar-default',
	],
	'innerContainerOptions' => ['class' => 'container-fluid'],
	#'ContainerEnds' => '<div class="b-yellow-strip"></div><div class="b-green-strip"></div>',
]);

$items = myMenuHelper::getAssignedMenuByName(1, 'TopMenu');
#var_dump($items);

echo Nav::widget([
	'options' => ['class' => 'navbar-nav navbar-right'],
	'items' => $items
]);

NavBar::end();
