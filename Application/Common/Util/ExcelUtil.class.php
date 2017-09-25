<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/21 0021
 * Time: 下午 3:32
 */

namespace Common\Util;


class ExcelUtil
{
    public function export($column_num,$modellist,$start,$end,$cellvalue){
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $head=[0=>"A",1=>"B",2=>"C",3=>"D",4=>"E",5=>"F",6=>"G",7=>"H",8=>"I",9=>"J",10=>"K",11=>"L",12=>"M",
            13=>"N",14=>"O",15=>"P",16=>"Q",17=>"R",18=>"S",19=>"T",20=>"U",21=>"V",22=>"W",23=>"X",24=>"Y",25=>"Z"];
        //设置宽度
        foreach($cellvalue as $k=>$v){
            $objPHPExcel->getActiveSheet()->getColumnDimension($head[$k])->setWidth($v['width']);
        }

        //设置行高
        $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);

        //设置字体样式
        $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10); //默认字体大小
//        $objPHPExcel->getActiveSheet()->getStyle($start.'1')->getFont()->setSize(16)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle($start."1:".$end."1")->getFont()->setBold(true); //粗体

        //合并excel
//        $objPHPExcel->getActiveSheet()->mergeCells($start."1:".$end."1");

        //设置垂直、水平居中
//        $objPHPExcel->getActiveSheet()->getStyle($start.'1')->getAlignment()
//            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
//            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($start."1:".$end."1")->getAlignment()
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //设置边框
        $objPHPExcel->getActiveSheet()->getStyle($start."1:".$end."1")->getBorders()->getAllBorders()
            ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        $cell=$objPHPExcel->setActiveSheetIndex(0);

        for($i=0;$i<$column_num;$i++){
            $cell->setCellValue($head[$i].'1',$cellvalue[$i]['value']);
        }
        //数据行设置
        for($i = 0;$i < count($modellist);$i++)
        {
            for($j=0;$j<$column_num;$j++){
                $name=$cellvalue[$j]['name'];
                $objPHPExcel->getActiveSheet()->setCellValue($head[$j] . ($i+2), $modellist[$i][$name]);
            }
            //设置垂直、水平居中
            $objPHPExcel->getActiveSheet()->getStyle($start . ($i+2).':'.$end.($i+2))->getAlignment()
                ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getRowDimension($i+2)->setRowHeight(16);//行高

            //设置边框
            $objPHPExcel->getActiveSheet()->getStyle($start . ($i+2).':'.$end.($i+2))->getBorders()->getAllBorders()
                ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        }
        return $objPHPExcel;
    }
}