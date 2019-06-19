<?php 
//Подключение библиотеки для работы с excel
require_once "PHPExcel/PHPExcel.php";

//Подключение класса расписания
require_once ('schedule.php');
$schedule = new Schedule();

//Загрузка файла
$uploaddir = $_SERVER["DOCUMENT_ROOT"].'schedule/var/files/';
$uploadfile = $uploaddir.basename($_FILES['file']['name']);
$date = new \DateTime();
$date->setDate($date->format('Y'), $date->format('m'), 1);

if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) 
{
    $PHPExcel_file = PHPExcel_IOFactory::load("$uploadfile");
    foreach ($PHPExcel_file->getWorksheetIterator() as $worksheetID => $worksheet) {
        $rowsCount = $worksheet->getHighestRow();
        $doctorList = array();
        if($worksheetID == 0)
        {
            $lpuID = $schedule->getLpuID($worksheet->getCellByColumnAndRow(2, 3)->getValue());
        }
        else {
            for ($row = 2; $row <= $rowsCount; $row++)
            {
                $departmentID = $schedule->getDepartmentID($worksheet->getCellByColumnAndRow(1, $row)->getValue(), $lpuID);
                $specialityID = $schedule->getSpecialityID($worksheet->getCellByColumnAndRow(2, $row)->getValue());
                $doctorID = $schedule->getDoctorID($worksheet->getCellByColumnAndRow(0, $row)->getValue());
                if($doctorID)
                {
                    $time = array(
                        "monday" => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                        "tuesday" => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                        "wednesday" => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                        "thursday" => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
                        "friday" => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
                        "saturday" => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
                        "sunday" => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
                        "week" => $date->format('W') + $worksheetID - 1
                    );
                    $schedule->saveDoctorTime($doctorID, $time);
                }
                else
                {
                    $doctorList[$row] = array(
                        "lpu_id" => $lpuID,
                        "department_id" => $departmentID,
                        "speciality_id" => $specialityID,
                        "name" => $worksheet->getCellByColumnAndRow(0, $row)->getValue(),
                        "office" => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
                        "time" => array(
                            "monday" => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
                            "tuesday" => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
                            "wednesday" => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
                            "thursday" => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
                            "friday" => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
                            "saturday" => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
                            "sunday" => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
                            "week" => $date->format('W') + $worksheetID - 1
                        )
                    );
                }
            }
            if(!empty($doctorList))
            {
                $schedule->saveDoctorList($doctorList);
            }
        }
    }
}
?>