<?php

namespace app\models\api;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use app\models\api\arBodyPatt;

/**
 * Description of PostMail
 *
 * @author proph
 */
class MailAction extends \yii\base\Action {

	const LOG_CATEGORY = 'api';

	public $checkAccess;

	private function pairAttr(\SimpleXMLElement $kids) {
		$r = [];
		foreach ($kids->attributes() as $a => $b) {
			$r[] = (string) $a . "=" . (string) $b;
		}
		return implode(', ', $r);
	}

	private function logDebug(\SimpleXMLElement $items) {
		$fd = fopen("/Users/proph/Sites/debug.log", "a");

		fwrite($fd, ">>>" . (string) $items->noteinfo[0]['unid'] . "\n");
		foreach ($items->item as $item) {
			fwrite($fd, (string) $item['name'] .
				"\n===\n" . mb_convert_encoding(base64_decode(trim((string) $item)), 'UTF-8', 'UTF-16LE') .
				"\n---------------------------------------------\n");
			fwrite($fd, "====================================================================================\n\n");
		}
		fclose($fd);
	}

	private function logDebugMail(Mail $mail, $all = true) {
		$fd = fopen("/Users/proph/Sites/debugMail.log", "a");

		fwrite($fd, ">>>" . (string) $mail->UniversalID . "\n");
		if ($all == true) {
			fwrite($fd, $mail->Body . "\n====================================================================================\n\n");
		}
		fclose($fd);
	}

	private function parse(\SimpleXMLElement $item) {
		return mb_convert_encoding(base64_decode(trim((string) $item)), 'UTF-8', 'UTF-16LE');
	}

	public function run() {
		if ($this->checkAccess) {
			call_user_func($this->checkAccess, $this->id);
		}

		#echo Yii::$app->getRequest()->getRawBody();
		libxml_use_internal_errors(true);
		$XML = new \SimpleXMLElement(Yii::$app->getRequest()->getRawBody());
		if (!$XML) {
			throw new ServerErrorHttpException('Failed to parse data. ' . implode('. ', libxml_get_errors()));
		}
		#$this->logDebug($XML);

		$mail = new Mail;
		$mail->UniversalID = (string) ($XML->noteinfo[0]['unid']);
		Yii::info('Обработка входящей почты (UNID:' . $mail->UniversalID . ')', self::LOG_CATEGORY);

		foreach ($XML->item as $item) {
			#echo "|" . $item['name'] . "|\n";
			#var_dump($item['name']);
			$mail->{$item['name']} = $this->parse($item);
		}

		foreach (arMailPatt::find()->orderBy('Priority')->all() as $mp) {
			#echo 'Pattern: ' . $mp->Pattern . "\n";
			if (preg_match_all("/(\w+)\|(.+)\|/", $mp->Pattern, $matches, PREG_SET_ORDER) > 0) {
				foreach ($matches as $out) {
					if (!isset($mail->{$out[1]})) {
						Yii::error('Ошибка применения шаблона (ID:' . $mp->ID . ')! Поле ' . $out[1] . ' не найдено!', self::LOG_CATEGORY);
						throw new ServerErrorHttpException('Ошибка примения шаблона. Поле ' . $out[1] . " не найдено!");
					}
					#echo "Patt: " . $out[2] . "| " . $mail->{$out[1]} . "\n";
					if (preg_match($out[2], $mail->{$out[1]}) > 0) {
						Yii::info('Применился первичный шаблон (ID:' . $mp->ID . ', <' . $out[0] . '>)', self::LOG_CATEGORY);
						if ($mp->BodyPattern == 'IGNORE') {
							Yii::info('Письмо игнорируется!', self::LOG_CATEGORY);
							return true;
						}
						$result = $this->BodyParse($mp->bodyPatt, trim(str_replace("\r", "", $mail->Body)));
						if (count($result) == 0) {
							Yii::warning('Ошибка обработки!Body-шаблон (ID:' . $bp->ID . ')', self::LOG_CATEGORY);
							Yii::error('Ошибка обработки входящей почты (UNID:' . $mail->UniversalID . ')', self::LOG_CATEGORY);
							Yii::error($bp, self::LOG_CATEGORY);
							Yii::error($mail, self::LOG_CATEGORY);
							#$this->logDebugMail($mail);
							throw new ServerErrorHttpException('Ошибка обработки входящей почты!');
						}

						#Yii::trace($result, self::LOG_CATEGORY);

						$cln = "app\\models\\api\\" . $mp->Model;
						if (!class_exists($cln)) {
							Yii::warning('Ошибка обработки!Не найден класс:' . $bp->Model, self::LOG_CATEGORY);
							Yii::error('Ошибка обработки!Не найден класс:' . $bp->Model, self::LOG_CATEGORY);
							throw new ServerErrorHttpException('Ошибка обработки!Не найден класс:' . $bp->Model);
						}

						$cl = new $cln();
						$cl->save($result, $mail);

						return true;
					}
				}
			}
		}
		Yii::warning('Не найден шаблон для обработки почты (UNID:' . $mail->UniversalID . ', Subject:' . $mail->Subject . ')', self::LOG_CATEGORY);
		#return $mail;
		return false;
	}

	private function BodyParse($BPs, $Body) {
		$result = [];
		foreach ($BPs as $bp) {
			#Yii::trace('Шаблон (' . $bp->Pattern . ')', self::LOG_CATEGORY);
			#echo "Patt:\n" . trim(str_replace("\r", "", $bp->Pattern)) . "\n";
			#echo "Body:\n" . trim(str_replace("\r", "", $mail->Body)) . "\n";

			preg_match_all(trim(str_replace("\r", "", $bp->Pattern)), $Body, $bmatch, PREG_SET_ORDER);
			foreach ($bmatch as $val) {
				#Yii::trace($val, self::LOG_CATEGORY);
				foreach ($val as $bk => $bv) {
					if (preg_match('/^\d+$/', $bk) == 0) {
						if (preg_match('/^repit\.(.+)$/i', $bk, $rmach) > 0) {
							$result[$rmach[1]][] = $this->BodyParse(arBodyPatt::find()->BP($rmach[1]), $bv);
						} else {
							$result[$bk] = $bv;
						}
					}
				}
			}
		}
		return $result;
	}

}
