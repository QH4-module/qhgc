<?php
/**
 * File Name: GenerateLayoutFile.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/19 11:03 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\qhgc\models\antd;


use qh4module\qhgc\models\GenerateTool;
use qttx\helper\StringHelper;

class GenerateLayoutFile
{

    public $dir;

    protected $import = [];

    public function run()
    {
        $content = GenerateTool::loadTemplate($this->template(), [
            'time' => date('Y-m-d H:i:s'),
            'import' => GenerateTool::formatImport($this->import),
        ]);

        $filename = StringHelper::combPath($this->dir, '_layout.jsx');
        file_put_contents($filename, $content);
    }

    protected function template()
    {
        return '/**
 * Automatically generated by QHGC tool
 * @date: {{time}}
 */
import React from "react";
import BasicLayout from "@/layouts/BasicLayout";

export default function Index(props) {
  return (
    <BasicLayout>
      {props.children}
    </BasicLayout>
  )
};';
    }
}