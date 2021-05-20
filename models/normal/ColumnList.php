<?php
/**
 * File Name: ColumnList.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/2 10:59 上午
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
use qttx\web\ServiceModel;

/**
 * Class ColumnList
 * @package qh4module\qhgc\models\normal
 * @property ExtQhgc $external
 */
class ColumnList extends ServiceModel
{
    /**
     * @var string 接收参数
     */
    public $table_name;


    public function rules()
    {
        return [
            [['table_name'],'required']
        ];
    }

    public function run()
    {
        return GenerateTool::getColumns($this->table_name, $this->external);
    }


}