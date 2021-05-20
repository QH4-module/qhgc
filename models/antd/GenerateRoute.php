<?php
/**
 * File Name: GenerateRoute.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/19 11:18 下午
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

class GenerateRoute
{
    public $ui_dictionary;
    public $ui_list_file;
    public $ui_create_file;
    public $ui_update_file;
    public $ui_detail_file;


    public function run()
    {

        $route = [
            'path' => '/' . strtolower($this->ui_dictionary),
            'name' => '新增的菜单',
            'component' => '@/layouts/BasicLayout',
            'routes' => []
        ];
        $route['routes'][] = $this->getItem($this->ui_list_file);
        $route['routes'][] = $this->getItem($this->ui_create_file);
        $route['routes'][] = $this->getItem($this->ui_update_file);
        $route['routes'][] = $this->getItem($this->ui_detail_file);

        return $this->format($route);

//        $temp = "{
//  path:'{{root_path}}',
//  name:'新增的菜单',
//  component: '@/layouts/BasicLayout',
//  routes:[
//{{routes_item}}
//  ]
//},";
//
//        $str_item = '';
//
//        $str_item .= $this->getItem($this->ui_list_file);
//        $str_item .= $this->getItem($this->ui_create_file);
//        $str_item .= $this->getItem($this->ui_update_file);
//        $str_item .= $this->getItem($this->ui_detail_file);
//
//        return GenerateTool::loadTemplate($temp, [
//            'root_path'=>strtolower($this->ui_dictionary),
//            'routes_item'=>$str_item,
//        ]);
    }


    protected function format($ary,$level=0)
    {
        $space = str_repeat("\t", $level*2);
        $space1 = str_repeat("\t", $level * 2 + 1);
        $str = "{$space}{";
        foreach ($ary as $key => $value) {
            $str .= "\n";
            if ($value === true) {
                $str .= "{$space1}{$key}: true,";
            }else{
                if (is_array($value)) {
                    $str .=  "{$space1}{$key}: [\n";
                    foreach ($value as $k2 => $v2) {
                        $str .= $this->format($v2,$level+1);
                        $str .= "\n";
                    }
                    $str .= "{$space1}],";
                }else{
                    $str .= "{$space1}{$key}: '{$value}',";
                }
            }
        }
        $str .= "\n{$space}},";
        return $str;
    }



    protected function getItem($file)
    {

        $ary = [
            'path'=>(isset($file['path']) && $file['path']) ? $file['path'] : '',
            'component'=>(isset($file['component']) && $file['component']) ? $file['component'] : '',
        ];
        if (isset($file['name']) && $file['name']) {
            $ary['name'] = $file['name'];
        }
        if (isset($file['icon']) && $file['icon']) {
            $ary['icon'] = $file['icon'];
        }
        if (isset($file['hideInMenu']) && $file['hideInMenu']) {
            $ary['hideInMenu'] = true;
        }

        return $ary;

//
//        $temp_item = "    {
//      path: '{{path}}',
//      component: '{{component}}',
//      name: '{{name}}',
//      icon: '{{icon}}',
//      hideInMenu: {{hide_menu}}
//    },
//";
//
//        return GenerateTool::loadTemplate($temp_item, [
//            'path' => (isset($file['path']) && $file['path']) ? $file['path'] : '',
//            'component' => (isset($file['component']) && $file['component']) ? $file['component'] : '',
//            'name' => (isset($file['name']) && $file['name']) ? $file['name'] : '',
//            'icon' => (isset($file['icon']) && $file['icon']) ? $file['icon'] : '',
//            'hide_menu' => (isset($file['hideInMenu']) && $file['hideInMenu']) ? 'true' : 'false',
//        ]);

    }
}