<?php 
include ('_header.php');
require_once ('schedule.php');
$schedule = new Schedule();
$url_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
$dateFrom = new \DateTime();
$dateTo = new \DateTime();
?>
<div class="container content">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div>
            <a class="btn btn-primary" href="index.php">Назад</a>
            <h4 class="text-center">Расписание учреждения
                <?php
                //Вывод названия выбранного учреждения
                $lpuNameList = $schedule->getLpuNameList();
                foreach($lpuNameList as $lpu)
                {
                    if($_GET['lpu_id'] == $lpu['lpu_id'])
                    {
                        echo $lpu['title'];
                    }
                }
                ?>
            </h4>
            <div class="row">
                <div class="form-group has-focus col-md-12 col-sm-12 col-xs-12">
                    <select class="selectpicker form-control doctor-select" data-live-search="true">
                        <?php
                        //Вывод всех медицинских учреждения в селект
                        foreach($lpuNameList as $lpu)
                        {
                            echo '<option value="'.$url_parts[0].'?lpu_id='.$lpu["lpu_id"];
                            if(isset($_GET['speciality_id']))
                            {
                                echo '&speciality_id='.$_GET['speciality_id'];
                            }
                            echo '&week='.$_GET['week'].'"';
                            if($_GET['lpu_id'] == $lpu['lpu_id'])
                            {
                                echo 'selected';
                            }
                            echo '>'.$lpu['title'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group has-focus col-md-6 col-sm-12 col-xs-12">
                    <select class=" selectpicker form-control doctor-select" data-live-search="true">
                        <?php
                        echo '<option value="'.$url_parts[0].'?lpu_id='.$_GET['lpu_id'];
                        if(isset($_GET['speciality_id']))
                        {
                            echo '&speciality_id='.$_GET['speciality_id'];
                        }
                        echo '&week='.$_GET['week'].'"';
                        echo '>Все филиалы</option>';
                        //Вывод всех филиалов медицинских учреждения в селект
                        $departmentList = $schedule->getDepartmentList($_GET['lpu_id']);
                        foreach($departmentList as $department)
                        {
                            echo '<option value="'.$url_parts[0].'?lpu_id='.$_GET['lpu_id'];
                            echo '&department_id='.$department['department_id'];
                            if(isset($_GET['speciality_id']))
                            {
                                echo '&speciality_id='.$_GET['speciality_id'];
                            }
                            echo '&week='.$_GET['week'].'"';
                            if(isset($_GET['department_id']))
                            {
                                if ($_GET['department_id'] == $department['department_id'] || count($departmentList) == 1)
                                {
                                    echo 'selected';
                                }
                            }
                            echo '>'.$department['title'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group has-focus col-md-6 col-sm-12 col-xs-12">
                    <select class="selectpicker form-control doctor-select" data-live-search="true">
                        <?php
                        echo '<option value="'.$url_parts[0].'?lpu_id='.$_GET['lpu_id'];
                        if(isset($_GET['department_id']))
                        {
                            echo '&department_id=' . $_GET['department_id'];
                        }
                        echo '&week='.$_GET['week'].'"';
                        echo '>Все специальности</option>';
                        //Вывод всех специальностей врачей в селект
                        $specialityList = $schedule->getSpecialityList();
                        foreach($specialityList as $speciality)
                        {
                            echo '<option value="'.$url_parts[0].'?lpu_id='.$_GET['lpu_id'];
                            if(isset($_GET['department_id']))
                            {
                                echo '&department_id=' . $_GET['department_id'];
                            }
                            echo '&speciality_id='.$speciality['speciality_id'];
                            echo '&week='.$_GET['week'].'"';
                            if(isset($_GET['speciality_id']))
                            {
                                if ($_GET['speciality_id'] == $speciality['speciality_id'])
                                {
                                    echo 'selected';
                                }
                            }
                            echo '>'.$speciality['speciality'].'</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php
                //Вывод трех кнопок с промежутками времени, за которое должно быть показано расписание
                $dateFrom->setDate($dateFrom->format('Y'), $dateFrom->format('m'), 1);
                $dateTo->setDate($dateTo->format('Y'), $dateTo->format('m'), 1);
                if($dateFrom->format('t') == 30 && $dateFrom->format('N') == 7)
                {
                    $n = 6;
                }
                elseif($dateFrom->format('t') == 31 && ($dateFrom->format('N') == 6 || $dateFrom->format('N') == 7))
                {
                    $n = 6;
                }
                elseif($dateFrom->format('t') == 28 && $dateFrom->format('N') == 1)
                {
                    $n = 4;
                }
                else
                {
                    $n = 5;
                }

                for($i = 0; $i < $n; $i++)
                {
                    if($i != 0)
                    {
                        $dateFrom->modify('next Monday');
                    }
                    elseif($dateFrom->format('N') != 1)
                    {
                        $dateFrom->modify('last Monday');
                    }
                    $dateTo->modify('next Sunday');
                    if($n == 6)
                    {
                        echo "<div class=\"form-group col-lg-3 col-md-3 col-sm-4 col-xs-12\">";
                    }
                    else
                    {
                        echo "<div class=\"form-group col-lg-2 col-md-3 col-sm-4 col-xs-12\">";
                    }
                    echo "<a href='";
                    echo $url_parts[0].'?lpu_id='.$_GET['lpu_id'];
                    if(isset($_GET['department_id']))
                    {
                        echo '&department_id=' . $_GET['department_id'];
                    }
                    if(isset($_GET['speciality_id']))
                    {
                        echo '&speciality_id='.$_GET['speciality_id'];
                    }
                    echo '&week='.$dateFrom->format('W');
                    echo "' class='btn btn-default'";

                    if($_GET['week'] == $dateFrom->format('W'))
                    {
                        echo "disabled";
                        $dateTable = new \DateTime($dateFrom->format('Y-m-d'));
                    }

                    echo ">";
                    echo $dateFrom->format('d.m.Y')." - ".$dateTo->format('d.m.Y');
                    echo "</a>";
                    echo "</div>";
                }
                ?>
                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                        <div class="input-group transparent has-focus">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input id="search-list" type="text" class="form-control clearable" required placeholder="Введите фамилию или специализацию врача" onkeyup="tableDoctorSearch()">
                        </div>
                </div>
            </div>
            <div class="table-responsive">
                <?php
                if(isset($_GET['department_id']))
                {
                    $tempDepartmet = $_GET['department_id'];
                }
                else
                {
                    $tempDepartmet = 0;
                }
                if(isset($_GET['speciality_id']))
                {
                    $tempSpeciality = $_GET['speciality_id'];
                }
                else
                {
                    $tempSpeciality = 0;
                }
                $doctorList = $schedule->getDoctorList($_GET['lpu_id'], $tempDepartmet, $tempSpeciality, $_GET['week']);
                ?>
                <table class="table table-hover table-striped" id="doctor-list">
                    <thead >
                        <tr>
                            <td colspan="8">
                                <?php
                                //Вывод названия выбранного учреждения с адресом и телефоном регистратуры
                                foreach($lpuNameList as $lpu)
                                {
                                    if($_GET['lpu_id'] == $lpu['lpu_id'])
                                    {
                                        echo "<b>".$lpu['title']."</b> ";
                                    }
                                }
                                if(!isset($_GET['department_id']))
                                {
                                    echo $departmentList[0]['address']." ";
                                    echo "<b>".$departmentList[0]['phone']."</b>";
                                }
                                else 
                                {
                                    foreach($departmentList as $department)
                                    {	
                                        if($_GET['department_id'] == $department['department_id'])
                                        {
                                            echo $department['address']." ";
                                            echo "<b>".$department['phone']."</b>";
                                        }
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Врач</th>
                            <?php
                            //Вывод названия колонок, включающих в себя название дня недели и дату
                            $days = array('Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье');
                            for($i = 0; $i < 7; $i++)
                            {
                                echo "<th>";
                                echo date($days[$i]." ".$dateTable->format('d.m.Y'));
                                $dateTable->modify('+1 day');
                                echo "</th>";
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        //Вывод расписания врачей
                        for($i = 0; $i < count($doctorList); $i++)
                        {
                            echo "<tr>";
                            echo "<td><b>".$doctorList[$i]["name"]."</b><br>";
                            echo $doctorList[$i]["speciality"]."<br>";
                            echo "Каб.: ".$doctorList[$i]["office"];
                            if($doctorList[$i]["monday"])
                            {
                                echo "<td>".$doctorList[$i]["monday"]."</td>";
                            }
                            else
                            {
                                echo "<td class='no-time'>Приема нет</td>";
                            }
                             if($doctorList[$i]["tuesday"])
                            {
                                echo "<td>".$doctorList[$i]["tuesday"]."</td>";
                            }
                            else
                            {
                                echo "<td class='no-time'>Приема нет</td>";
                            }
                             if($doctorList[$i]["wednesday"])
                            {
                                echo "<td>".$doctorList[$i]["wednesday"]."</td>";
                            }
                            else
                            {
                                echo "<td class='no-time'>Приема нет</td>";
                            }
                             if($doctorList[$i]["thursday"])
                            {
                                echo "<td>".$doctorList[$i]["thursday"]."</td>";
                            }
                            else
                            {
                                echo "<td class='no-time'>Приема нет</td>";
                            }
                             if($doctorList[$i]["friday"])
                            {
                                echo "<td>".$doctorList[$i]["friday"]."</td>";
                            }
                            else
                            {
                                echo "<td class='no-time'>Приема нет</td>";
                            }
                             if($doctorList[$i]["saturday"])
                            {
                                echo "<td>".$doctorList[$i]["saturday"]."</td>";
                            }
                            else
                            {
                                echo "<td class='no-time'>Приема нет</td>";
                            }
                             if($doctorList[$i]["sunday"])
                            {
                                echo "<td>".$doctorList[$i]["sunday"]."</td>";
                            }
                            else
                            {
                                echo "<td class='no-time'>Приема нет</td>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                         ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include ('_footer.php');?>