<?php
/**
 * File Name: TableList.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/1 5:45 下午
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
use qttx\helper\StringHelper;
use qttx\web\ServiceModel;

/**
 * Class TableList
 * @package qh4module\qhgc\models\normal
 * @property ExtQhgc $external
 */
class TableList extends ServiceModel
{
    /**
     * @inheritDoc
     */
    public function run()
    {
        $name = $this->external->databaseName();
        $db = $this->external->getDb();

        $db->calcFoundRows();
        $result = $db->select('*')
            ->from('information_schema.TABLES')
            ->where('table_schema= :ts')
            ->bindValue('ts', $name)
            ->query();

        foreach ($result as &$item) {
            $tn = GenerateTool::getTableName($item['TABLE_NAME']);
            $item['ad_class_name'] = StringHelper::underline2capital(ucfirst($tn['table_base_name'])) . $this->external->arFileNameSuffix();
            $item['md_class_name'] = StringHelper::underline2capital(ucfirst($tn['table_base_name'])) . $this->external->modelFileNameSuffix();
        }

        return $result;
    }
}