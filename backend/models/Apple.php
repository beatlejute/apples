<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

/**
 * Модель Apple
 * @package backend\models
 *
 * @property int id
 * @property string color
 * @property int dateCreate
 * @property int dateFall
 * @property bool fallen
 * @property bool rotten
 * @property int remainderPercent
 *
 */
class Apple extends ActiveRecord {
    /**
     * Название таблицы
     */
    const TABLE_NAME = "apple";
    /**
     * Идентификатор яблока
     */
    const FIELD_ID = "id";
    /**
     * Цвет яблока
     */
    const FIELD_COLOR = "color";
    /**
     * Дата создания
     */
    const FIELD_DATE_CREATE = "dateCreate";
    /**
     * Дата подения
     */
    const FIELD_DATE_FALL = "dateFall";
    /**
     * Яблоко упало
     */
    const FIELD_FALLEN = "fallen";
    /**
     * Яблоко сгнило
     */
    const FIELD_ROTTEN = "rotten";
    /**
     * Остаток яблока в процентах
     */
    const FIELD_REMAINDER_PERCENT = "remainderPercent";

    /**
     * Время между падением и сгневанием яблока
     */
    const FRESH_TIME = 5 * 60 * 60;

    /**
     * Цвет гнили
     */
    const ROTTEN_COLOR = "693206";

    /**
     * @return string
     */
    public static function tableName() {
        return self::TABLE_NAME;
    }

    /**
     * Apple constructor.
     * @param $student_name
     */
    public function __construct($color = null){

        parent::__construct();

        switch ($color) {
            case "green":
                $r = dechex(rand(50,128));
                $g = dechex(rand(50,255));
                $b = dechex(rand(0,128));
                break;
            case "red":
                $r = dechex(rand(50,255));
                $g = dechex(rand(50,128));
                $b = dechex(rand(0,128));
                break;
            case "yellow":
                $r = dechex(rand(128,255));
                $g = dechex(rand(128,255));
                $b = dechex(rand(0,128));
                break;
            default:
                $r = dechex(rand(50,255));
                $g = dechex(rand(50,255));
                $b = dechex(rand(0,128));
        }

        if(strlen($r) == 1) {
            $r = "0" . $r;
        }
        if(strlen($g) == 1) {
            $g = "0" . $g;
        }
        if(strlen($b) == 1) {
            $b = "0" . $b;
        }

        $this->{$this::FIELD_COLOR} = $r . $g . $b;
        $this->{$this::FIELD_DATE_CREATE} = time();
    }

    /**
     * Упало/лежит на земле
     *
     * @return bool
     */
    public function isFallen() {
        return !empty($this->{$this::FIELD_FALLEN});
    }

    /**
     * Висит на дереве
     *
     * @return bool
     */
    public function isOnTree() {
        return !$this->isFallen();
    }

    /**
     * Гнилое
     *
     * @return bool
     */
    public function isRotten() {
        return !empty($this->{$this::FIELD_ROTTEN});
    }

    /**
     * Получить яблоко по ИД
     *
     * @param int $id ИД яблока
     * @return array|ActiveRecord|null
     */
    public static function get($id) {

        $apple = self::find()->where(["id" => (int)$id])->one();

        if ($apple && $apple->{self::FIELD_DATE_FALL} + self::FRESH_TIME <= time() && $apple->isFallen()) {
            $apple->rotten();
        }

        return $apple;
    }

    /**
     * Получить все яблоки
     *
     * @return array|ActiveRecord[]
     */
    public static function getAll() {

        $apples = self::find()->all();

        foreach ($apples as $apple) {
            if ($apple->{self::FIELD_DATE_FALL} + self::FRESH_TIME <= time() && $apple->isFallen()) {
                $apple->rotten();
            }
        }

        return $apples;
    }

    /**
     * Сбросить яблоко
     *
     * @return bool
     * @throws \Exception
     */
    public function fall() {
        if ($this->isOnTree()) {
            $this->{Apple::FIELD_FALLEN} = true;
            $this->{Apple::FIELD_DATE_FALL} = time();
            $this->save();

            return true;
        } else {
            throw new \Exception("Яблоко уже упало!");
        }
    }

    /**
     * Откусить от яблока
     *
     * @param int|null $percent Процент укуса
     * @return int Остаток
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function bite(int $percent = null) {
        if ($this->isOnTree()) {
            throw new \Exception("Яблоко еще не упало!");
        }
        if ($this->isRotten()) {
            throw new \Exception("Яблоко уже сгнило!");
        }

        if(!$percent) {
            $percent = rand(0,50);
        }

        if($this->{$this::FIELD_REMAINDER_PERCENT} <= $percent) {
            $this->delete();
            return 0;
        }

        $this->{$this::FIELD_REMAINDER_PERCENT} -= $percent;
        $this->save();

        return $this->{$this::FIELD_REMAINDER_PERCENT};
    }

    /**
     * Сгноить яблоко
     *
     * @throws \Exception
     */
    public function rotten() {
        if ($this->isOnTree()) {
            throw new \Exception("Яблоко еще не упало!");
        }
        if ($this->{self::FIELD_DATE_FALL} + self::FRESH_TIME > time()) {
            throw new \Exception("Яблоко недавно упало!");
        }


        $this->{self::FIELD_ROTTEN} = true;
        $this->save();
    }
}