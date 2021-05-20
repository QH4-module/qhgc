<?php
/**
 * File Name: ExtQgc.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/4/1 5:42 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\qhgc\external;


use QTTX;
use qttx\web\External;

class ExtQhgc extends External
{
    /**
     * 数据库名称
     * @return array|string|null
     */
    public function databaseName()
    {
        $master = null;
        $config = QTTX::getConfig('components.db.config.masters', null);
        if ($config && is_array($config)) {
            $master = current($config);
        } else {
            $config = QTTX::getConfig('db.masters');
            if ($config && is_array($config)) {
                $master = current($config);
            }
        }

        return $master['db_name'];
    }


    /**
     * 默认值为用户id的字段
     * 这些字段如果没有传入值,则默认使用用户的id
     * @return string[]
     */
    public function defaultUserIdField()
    {
        return ['create_by', 'update_by'];
    }

    /**
     * 默认为时间戳的字段
     * @return string[]
     */
    public function defaultTimestampField()
    {
        return ['create_time', 'update_time', 'create_at', 'update_at'];
    }

    /**
     * 作为删除标记的字段
     * @return array[] 键是字段名,值是 array(未删除时候的值,删除时候设置的值)
     *                 字符串需要使用双层引号
     */
    public function deleteTagField()
    {
        return [
            'del_time' => [0, 'time()'],
            'del_at' => [0, 'time()'],
            'is_del' => [0, 1],
            'is_delete' => [0, 1],

//            'del_time' => [0, "date('Y-m-d H:i:s')"],
//            'del_time' => ["'n'","'y'"],
        ];
    }

    /**
     * @return string 自动生成的ActiveRecord文件后缀
     */
    public function arFileNameSuffix()
    {
        return 'Ad';
    }

    /**
     * @return string 自动生成的Model文件后缀
     */
    public function modelFileNameSuffix()
    {
        return 'Md';
    }

}