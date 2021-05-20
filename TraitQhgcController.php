<?php
/**
 * File Name: TraitQhgcController.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/18 11:30 上午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\qhgc;


use qh4module\qhgc\external\ExtQhgc;
use qh4module\qhgc\models\normal\ColumnList;
use qh4module\qhgc\models\normal\DownloadZip;
use qh4module\qhgc\models\normal\GenerateCurdFile;
use qh4module\qhgc\models\normal\LoadFile;
use qh4module\qhgc\models\normal\SearchFile;
use qh4module\qhgc\models\normal\TableList;
use qh4module\qhgc\models\server\GenerateActiveRecordFile;
use qh4module\qhgc\models\server\GenerateModelFile;
use QTTX;

trait TraitQhgcController
{
    /**
     * @return ExtQhgc 控制类
     */
    protected function ext_qhgc()
    {
        return new ExtQhgc();
    }

    /**
     * 获取所有表名
     * @return array|false
     */
    public function actionTableList()
    {
        if (!ENV_DEV) {
            QTTX::$response->setStatusCode(404);
            return false;
        }

        $model = new TableList([
            'external' => $this->ext_qhgc(),
        ]);
        return $this->runModel($model);
    }

    /**
     * 获取表的字段信息
     * @return array|false
     */
    public function actionColumnList()
    {
        if (!ENV_DEV) {
            QTTX::$response->setStatusCode(404);
            return false;
        }

        $model = new ColumnList([
            'external' => $this->ext_qhgc(),
        ]);
        return $this->runModel($model);
    }

    /**
     * 生成 activerecord 文件
     * @return array|false
     */
    public function actionGenerateActiveRecord()
    {
        if (!ENV_DEV) {
            QTTX::$response->setStatusCode(404);
            return false;
        }

        $model = new GenerateActiveRecordFile([
            'external' => $this->ext_qhgc(),
        ]);
        return $this->runModel($model);
    }


    /**
     * 生成model文件
     * @return array|false
     */
    public function actionGenerateModel()
    {
        if (!ENV_DEV) {
            QTTX::$response->setStatusCode(404);
            return false;
        }

        $model = new GenerateModelFile([
            'external' => $this->ext_qhgc(),
        ]);
        return $this->runModel($model);
    }

    /**
     * 根据命名空间遍历目录下的文件
     * @return array|false
     */
    public function actionSearchFile()
    {
        if (!ENV_DEV) {
            QTTX::$response->setStatusCode(404);
            return false;
        }

        $model = new SearchFile([
            'external' => $this->ext_qhgc(),
        ]);

        return $this->runModel($model);
    }

    /**
     * curd页面通过 ar和model类加载
     * @return array|false
     */
    public function actionLoadFile()
    {
        if (!ENV_DEV) {
            QTTX::$response->setStatusCode(404);
            return false;
        }

        $model = new LoadFile([
            'external' => $this->ext_qhgc(),
        ]);
        return $this->runModel($model);
    }

    /**
     * 生成curd文件
     * @return array|false
     */
    public function actionGenerateCurd()
    {
        if (!ENV_DEV) {
            QTTX::$response->setStatusCode(404);
            return false;
        }

        $model = new GenerateCurdFile([
            'external' => $this->ext_qhgc(),
        ]);
        return $this->runModel($model);
    }

    /**
     * 下载生成压缩文件
     * @return false
     */
    public function actionDownloadZip()
    {
        if (!ENV_DEV) {
            QTTX::$response->setStatusCode(404);
            return false;
        }

        $model = new DownloadZip();
        if ($model->run() === false) {
            QTTX::$response->setStatusCode(404);
        }

        exit();
    }
}