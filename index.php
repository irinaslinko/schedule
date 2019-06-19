<?php 
include ('_header.php');
require_once ('schedule.php');
$schedule = new Schedule();
$date = new \DateTime();
$week = $date->format('W');
?>
<div class="container content">
    <div class="col-md-12 col-sm-12">
        <ul class="nav nav-tabs primary">
            <li class="active">
                <a href="#city" data-toggle="tab">Городские учреждения</a>
            </li>
            <li>
                <a href="#area" data-toggle="tab">Областные учреждения</a>
            </li>
        </ul>
            <div class="tab-content primary">
                <div class="tab-pane fade in active" id="city">
                    <div>
                        <br><h4 class="text-center">Учреждения здравоохранения города Новосибирска</h4>
                        <div class="row">
                            <div class="form-group has-focus col-md-12 col-sm-12 col-xs-12">
                                <select class="selectpicker form-control" id="lpu-city-select" data-live-search="true">
                                    <option value="0">Поиск по медицинским учреждениям</option>
                                    <?php
                                    //Вывод всех медицинских учреждения в селект
                                    $lpuCityList = $schedule->getLpuList('Y');
                                    foreach($lpuCityList as $lpu)
                                    {
                                        echo '<option value='.$lpu["lpu_id"].'>'.$lpu['title'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <form class="form-group">
                            <div class="input-group transparent has-focus">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input id="city-search-list" type="text" class="form-control clearable" placeholder="Поиск по ключевым словам или телефону" />
                            </div>
                        </form>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="city-lpu">
                                    <thead >
                                        <tr>
                                            <th class="organization">Наименование организации</th>
                                            <th>
                                                <div class="header">
                                                    <div class="name">Название</div>
                                                    <div class="address">Адрес</div>
                                                    <div class="contacts">Телефон регистратуры</div>
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        //Вывод таблицы со медицинскими учреждениями города
                                        for($i = 0; $i < count($lpuCityList); $i++)
                                        {
                                            echo "<tr>";
                                            echo "<th><a href='doctor.php?lpu_id=".$lpuCityList[$i]["lpu_id"];
                                            echo "&week=".$week."'>".$lpuCityList[$i]["title"]."</a></th>";
                                            echo "<td colspan='3'><table class='table'>";
                                            for($j = 0; $j < count($lpuCityList[$i]["departmentList"]); $j++)
                                            {
                                                echo "<tr>";
                                                echo "<td><a href='doctor.php?lpu_id=";
                                                echo $lpuCityList[$i]["lpu_id"]."&department_id=".$lpuCityList[$i]["departmentList"][$j]["department_id"];
                                                echo "&week=".$week."'>";
                                                echo $lpuCityList[$i]["departmentList"][$j]["title"];
                                                echo "</a></td>";
                                                echo "<td>".$lpuCityList[$i]["departmentList"][$j]["address"]."</td>";
                                                echo "<td>".$lpuCityList[$i]["departmentList"][$j]["phone"]."</td>";
                                                echo "</tr>";
                                            }
                                            echo "</td></table>";
                                            echo "</tr>";
                                        }
                                         ?>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="area">
                    <div>
                        <br><h4 class="text-center">Учреждения здравоохранения Новосибирской области</h4>
                        <div class="row">
                            <div class="form-group has-focus col-md-12 col-sm-12 col-xs-12">
                                <select class="selectpicker form-control" id="lpu-area-select" data-live-search="true">
                                    <option>Поиск по медицинским учреждениям</option>
                                    <?php
                                    //Вывод всех медицинских учреждения в селект
                                    $lpuAreaList = $schedule->getLpuList('N');
                                    foreach($lpuAreaList as $lpu)
                                    {
                                        echo '<option value='.$lpu["lpu_id"].'>'.$lpu['title'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group transparent has-focus">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input id="area-search-list" type="text" class="form-control clearable" placeholder="Поиск по ключевым словам или телефону" onkeyup="tableAreaSearch()">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="area-lpu">
                                <thead >
                                    <tr>
                                        <th class="organization">Наименование организации</th>
                                        <th>
                                            <div class="header">
                                                <div class="name">Название</div>
                                                <div class="address">Адрес</div>
                                                <div class="contacts">Телефон регистратуры</div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    //Вывод таблицы со медицинскими учреждениями области
                                    for($i = 0; $i < count($lpuAreaList); $i++)
                                    {
                                        echo "<tr>";
                                        echo "<th><a href='doctor.php?lpu_id=".$lpuAreaList[$i]["lpu_id"];
                                        echo "&week=".$week."'>".$lpuAreaList[$i]["title"]."</a></th>";
                                        echo "<td colspan='3'><table class='table'>";
                                        for($j = 0; $j < count($lpuAreaList[$i]["departmentList"]); $j++)
                                        {
                                            echo "<tr>";
                                            echo "<td><a href='doctor.php?lpu_id=";
                                            echo $lpuAreaList[$i]["lpu_id"]."&department_id=".$lpuAreaList[$i]["departmentList"][$j]["department_id"];
                                            echo "&week=".$week."'>";
                                            echo $lpuAreaList[$i]["departmentList"][$j]["title"];
                                            echo "</a></td>";
                                            echo "<td>".$lpuAreaList[$i]["departmentList"][$j]["address"]."</td>";
                                            echo "<td>".$lpuAreaList[$i]["departmentList"][$j]["phone"]."</td>";
                                            echo "</tr>";
                                        }
                                        echo "</td></table>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
<?php include ('_footer.php');?>