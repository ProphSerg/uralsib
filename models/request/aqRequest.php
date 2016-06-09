<?php

namespace app\models\request;

/**
 * This is the ActiveQuery class for [[arRequest]].
 *
 * @see arRequest
 */
class aqRequest extends \yii\db\ActiveQuery {

	public function getRequest($num, $type = 0) {
		return $this->andWhere(['Number' => $num, 'Type' => $type])->one();
	}

	/**
	 * @inheritdoc
	 * @return arRequest[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return arRequest|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

}
