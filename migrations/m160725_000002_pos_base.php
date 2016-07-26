<?php

use yii\db\Migration;

/**
 * Handles the creation for table `key_table`.
 */
class m160725_000002_pos_base extends Migration {

	public function init() {
		$this->db = 'dbPos';
		parent::init();
	}

	/**
	 * @inheritdoc
	 */
	public function up() {
		$this->createTable('RegPos', [
			'ID' => $this->primaryKey(),
			'ClientN' => $this->text()->notNull(),
			'Name' => $this->text()->notNull(),
			'ContractN' => $this->text()->notNull(),
			'TerminalID' => $this->text()->notNull(),
			'City' => $this->text()->notNull(),
			'Address' => $this->text()->notNull(),
			'MerchantID' => $this->text()->notNull(),
			'KeyNum' => $this->text()->notNull(),
			'TMK_CHECK' => $this->text()->notNull(),
			'TPK_KEY' => $this->text()->notNull(),
			'TPK_CHECK' => $this->text()->notNull(),
			'TAK_KEY' => $this->text()->notNull(),
			'TAK_CHECK' => $this->text()->notNull(),
			'TDK_KEY' => $this->text()->notNull(),
			'TDK_CHECK' => $this->text()->notNull(),
		]);
		$this->createIndex('IDX_REG_TERMINALID', 'RegPos', 'TerminalID', false);
		$this->createIndex('IDX_REG_KEYNUM', 'RegPos', ['TerminalID', 'KeyNum'], true);
		$this->createIndex('IDX_REG_TMK', 'RegPos', 'TMK_CHECK', true);
		$this->createIndex('IDX_REG_ADDRESS', 'RegPos', 'Address', false);
		$this->createIndex('IDX_REG_NAME', 'RegPos', 'Name', false);
		$this->createIndex('IDX_REG_MERCH', 'RegPos', 'MerchantID', false);
	}

	/**
	 * @inheritdoc
	 */
	public function down() {
		$this->dropTable('RegPos');
		#return false;
	}

}
