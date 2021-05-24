<?php
/**
 * File Name: GenerateIndexFile.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/19 4:22 下午
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

class GenerateListFile
{

    public $dir;
    public $columns;
    public $active_record_classname;

    protected $import = [];

    public function run()
    {
        $primary = call_user_func([$this->active_record_classname, 'primaryKey']);

        $content = GenerateTool::loadTemplate($this->template(), [
            'time' => date('Y-m-d H:i:s'),
            'primary' => $primary,
            'default_columns' => $this->getDefaultColumns(),
            'rand' => StringHelper::random(6),
//            'search_form_item' => $this->getSearchFormItem(),
            'import' => GenerateTool::formatImport($this->import),
        ]);

        $filename = StringHelper::combPath($this->dir, 'list.jsx');
        file_put_contents($filename, $content);
    }


    public function template()
    {
        return file_get_contents(__DIR__ . '/index.txt');
    }


    public function getSearchFormItem()
    {
        $str = '';

        for ($i = 0; $i < 3; $i++) {
            if (!isset($this->columns[$i])) {
                continue;
            }
            $col = $this->columns[$i];
            if (!isset($col['used_filter']) || !$col['used_filter']) {
                continue;
            }
            $str .= $this->_get_search_form_item($col);
        }
        if (sizeof($this->columns) > 3) {
            $temp = "            {
              expand &&
              <>
{{item}}
              </>
            }
";
            $str_exp = '';
            for ($i = 3; $i < sizeof($this->columns); $i++) {
                $col = $this->columns[$i];
                if (!isset($col['used_filter']) || !$col['used_filter']) {
                    continue;
                }
                $str_exp .= $this->_get_search_form_item($col);
            }
            $str .= GenerateTool::loadTemplate($temp, [
                'item' => $str_exp,
            ]);
        }

        return $str;
    }

    protected function _get_search_form_item($col)
    {
        $temp = '            <Col span={8} xxl={6}>
              <Form.Item name={\'{{name}}\'} label="{{title}}">
                <Input placeholder="{{place}}"/>
              </Form.Item>
            </Col>
';
        $ary = [
            'name' => $col['COLUMN_NAME'],
            'title' => AntdTool::getTitle($col),
        ];
        if ($col['used_filter'] == '==') {
            $ary['place'] = "输入完整的{$ary['title']}";
        } else {
            $ary['place'] = '';
        }

        return GenerateTool::loadTemplate($temp, $ary);
    }

    public function getDefaultColumns()
    {
        $temp = "    {
      title: '{{title}}',
      dataIndex: '{{name}}',
      key: '{{name}}',{{sortable}}
      width: 200,
      // hideInSearch: true,
    },
";

        $temp_sort = "
      sorter: {multiple: 1},";

        $str = '';

        foreach ($this->columns as $index => $col) {
            if (!$col['show_list']) continue;
            $ary = [
                'title' => AntdTool::getTitle($col),
                'name' => $col['COLUMN_NAME'],
            ];
            $ary['sortable'] = $col['sortable'] ? $temp_sort : '';
            $str .= GenerateTool::loadTemplate($temp, $ary);
        }

        return $str;
    }
}