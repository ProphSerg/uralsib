<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models\atm;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\atm\arATMOrder;

/**
 * Description of RequestSearch
 *
 * @author proph
 */
class ATMOrderSearch extends arATMOrder {

	public function rules() {
		return[
			#[['Number', 'Serial', 'sprATM.TerminalID'], 'string'],
			[['Number', 'Serial', 'sprATM.TerminalID', 'statusNameLast.StatusName', 'techNameLast.Name'], 'string'],
			[['EnterDate'], 'safe'],
		];
	}

	public function attributes() {
		// делаем поле зависимости доступным для поиска
		#return array_merge(parent::attributes(), ['sprATM.TerminalID']);
		return array_merge(parent::attributes(), ['sprATM.TerminalID', 'statusNameLast.StatusName', 'techNameLast.Name']);
	}

	public function scenarios() {
		return Model::scenarios();
	}

	public function search($param) {
		$query = arATMOrder::find();
		$query->joinWith('statusNameLast')
			->joinWith('techNameLast')
			->joinWith('sprATM');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'EnterDate' => SORT_DESC,
				],
			],
		]);

		$dataProvider->sort->attributes['sprATM.TerminalID'] = [
			'asc' => ['sprATM.TerminalID' => SORT_ASC],
			'desc' => ['sprATM.TerminalID' => SORT_DESC]
		];

		$dataProvider->sort->attributes['statusNameLast.StatusName'] = [
			'asc' => ['vATMOrderStatus.Status' => SORT_ASC],
			'desc' => ['vATMOrderStatus.Status' => SORT_DESC]
		];

		$dataProvider->sort->attributes['techNameLast.Name'] = [
			'asc' => ['sprATMOrderTech.Name' => SORT_ASC],
			'desc' => ['sprATMOrderTech.Name' => SORT_DESC]
		];

		if (!($this->load($param) && $this->validate())) {
			return $dataProvider;
		}

		$query->andFilterWhere([
			'and',
			['like', 'Number', $this->Number],
			#['like', 'Desc', $this->Desc],
			#['like', 'Name', $this->Name],
			['like', 'sprATM.TerminalID', $this->getAttribute('sprATM.TerminalID')],
			['strftime("%d/%m/%Y", EnterDate, "localtime")' => $this->EnterDate],
			['vATMOrderStatus.Status' => $this->getAttribute('statusNameLast.StatusName')],
			['vATMOrderTech.Code' => $this->getAttribute('techNameLast.Name')],
		]);
		#var_dump($query->where);

		return $dataProvider;
	}

}
