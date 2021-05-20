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
     * @var int 页数,从1开始
     */
    public $page = 1;

    /**
     * @var int 每页显示数量
     */
    public $limit = 10;

    /**
     * @inheritDoc
     */
    public function run()
    {
        $name = $this->external->databaseName();
        $start = ($this->page - 1) * $this->limit;
        $db = $this->external->getDb();

        $db->calcFoundRows();
        $sql = $db->select('*')
            ->from('information_schema.TABLES')
            ->where('table_schema= :ts')
            ->bindValue('ts', $name);

        $result = $sql->offset($start)
            ->limit($this->limit)
            ->query();

        foreach ($result as &$item) {
            $tn = GenerateTool::getTableName($item['TABLE_NAME']);
            $item['ad_class_name'] = StringHelper::underline2capital(ucfirst($tn['table_base_name'])) . $this->external->arFileNameSuffix();
            $item['md_class_name'] = StringHelper::underline2capital(ucfirst($tn['table_base_name'])) . $this->external->modelFileNameSuffix();
        }

        return $result;
    }
}