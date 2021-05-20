<?php
/**
 * File Name: LoadFile.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/2 2:52 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\qhgc\models\normal;


use qh4module\qhgc\external\ExtQhgc;
use qh4module\qhgc\models\GenerateTool;
use QTTX;
use qttx\helper\StringHelper;
use qttx\web\ActiveRecord;
use qttx\web\Model;
use qttx\web\ServiceModel;

/**
 * Class LoadFile
 * @package qh4module\qhgc\models\normal
 * @property ExtQhgc $external
 */
class LoadFile extends ServiceModel
{
    /**
     * @var string 接收参数
     */
    public $active_class;

    /**
     * @var string 接收参数
     */
    public $model_class;


    public function rules()
    {
        return [
            [['active_class','model_class'],'required'],
            [['active_class','model_class'],'string'],
        ];
    }

    public function run()
    {
        // 校验并格式化输入的类
        if(!self::checkARandModel($this)) return false;

        // 获取表名
        $table_name = call_user_func([$this->active_class, 'originalTableName']);

        // 获取注释
        $model = QTTX::createObject($this->model_class);
        $langs = $model->attributeLangs();
        $language = QTTX::getConfig('language');
        if ($language && isset($langs[$language])) {
            $langs = $langs[$language];
        }

        $columns = GenerateTool::getColumns($table_name, $this->external);

        // 循环替换注释
        foreach ($columns as &$item) {
            $name = $item['COLUMN_NAME'];
            if (isset($langs[$name])) {
                $item['title'] = $langs[$name];
            }else{
                $item['title'] = lcfirst(StringHelper::underline2capital($item['COLUMN_NAME']));
            }
        }

        return $columns;
    }

    /**
     * @param $model LoadFile|GenerateCurdFile
     * @return bool
     */
    public static function checkARandModel($model)
    {
        $model->active_class = GenerateTool::formatNamespace($model->active_class);
        $model->model_class = GenerateTool::formatNamespace($model->model_class);
        if (!class_exists($model->active_class)) {
            $model->addError('active_class', 'ActiveRecord类无效');
            return false;
        }
        if (!class_exists($model->model_class)) {
            $model->addError('model_class', 'Model类无效');
            return false;
        }
        $ac = QTTX::createObject($model->active_class);
        if (!$ac instanceof ActiveRecord) {
            $model->addError('active_class', 'ActiveRecord类无效');
            return false;
        }
        $mc = QTTX::createObject($model->model_class);
        if (!$mc instanceof Model) {
            $model->addError('model_class', 'Model类无效');
            return false;
        }

        return true;
    }
}