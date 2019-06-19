<?php
//Класс расписания

class Schedule{
	
    protected $connection = null;

    //Конструктор класса. В нем происходит подключение к базе данных

    public function __construct() 
    {
        require_once 'connection.php';
        if (!empty($host) && !empty($user) && !empty($database) && !empty($charset)) 
        {
            $this->connection = pg_connect("host=".$host." dbname=".$database." user=".$user." password=".$password);
        }
    }

    //Функция, возвращающая список медицинских учреждений
    //В список учреждений включается название учреждение, название филиалов, адрес и телефон филиалов
    //$city принимает значение 'Y', если должен быть закружен список учреждений города, или 'N' для областных учреждений

    public function getLpuList($city)
    {
        $isCity = pg_escape_string($this->connection, $city);
        $query = "SELECT * FROM lpu WHERE city='".$isCity."' ORDER BY title ASC";
        $queryLpu = pg_query($this->connection, $query);
        
        $lpuList = array();
        $i = 0;
        while($rowLpu = pg_fetch_array($queryLpu))
        {
            $lpuList[$i] = array(
                "lpu_id" => $rowLpu["lpu_id"],
                "title" => $rowLpu["title"]
            );

            $lpuList[$i]["departmentList"] = $this->getDepartmentList($rowLpu["lpu_id"]);

            $i++;
        }
        return($lpuList);
    }

    //Функция, возвращающая список с названием медицинских учреждений

    public function getLpuNameList()
    {
        $query = "SELECT * FROM lpu ORDER BY title ASC";
        $queryLpu = pg_query($this->connection, $query);

        $lpuNameList = array();
        $i = 0;
        while($rowLpu = pg_fetch_array($queryLpu))
        {
            $lpuNameList[$i] = array(
                "lpu_id" => $rowLpu["lpu_id"],
                "title" => $rowLpu["title"]
            );			
            $i++;
        }
        return($lpuNameList);
    }

    //Функция, возвращающая список c информацией о филиалах медицинского учреждения
    //$lpuID идентификатор медицинского учреждения

    public function getDepartmentList($lpuID)
    {
        $query = "SELECT * FROM lpu_department WHERE lpu_id=".$lpuID." ORDER BY title ASC";
        $queryDepartment = pg_query($this->connection, $query);

        $departmentList = array();
        $i = 0;
        while($rowDepartment = pg_fetch_array($queryDepartment))
        {
            $departmentList[$i] = array(
                "department_id" => $rowDepartment["department_id"],
                "lpu_id" => $rowDepartment["lpu_id"],
                "title" => $rowDepartment["title"],
                "address" => $rowDepartment["address"],
                "phone" => $rowDepartment["phone"]
            );
            $i++;
        }
        return($departmentList);
    }

    //Функция, возвращающая список специальностей врачей

    public function getSpecialityList()
    {
        $query = "SELECT * FROM doctor_speciality ORDER BY speciality ASC";
        $querySpeciality = pg_query($this->connection, $query);

        $specialityList = array();
        $i = 0;
        while($row = pg_fetch_array($querySpeciality))
        {
            $specialityList[$i] = array(
                    "speciality_id" => $row["speciality_id"],
                    "speciality" => $row["speciality"]
            );
            $i++;
        }
        return($specialityList);
    }

    //Функция, возвращающая список докторов с их расписанием, выбранного учреждения или филиала
    //$lpuID идентификатор медицинского учреждения
    //$departmentID идентификатор филиала медицинского учреждения
    //$specialityID идентификатор специальности
    //$week номер недели

    public function getDoctorList($lpuID, $departmentID = 0, $specialityID = 0, $week = 0)
    {
        $where = array();
        $where[] = "d.lpu_id=".$lpuID;
        if($departmentID > 0)
        {
            $where[] = "d.department_id=".$departmentID;
        }
        if($specialityID > 0)
        {
            $where[] = "s.speciality_id=".$specialityID;
        }
        $where[] = "t.week=".$week;

        $query = "SELECT d.doctor_id, d.name, d.office, t.monday, t.tuesday, 
            t.wednesday, t.thursday, t.friday, t.saturday, t.sunday, t.week, 
            s.speciality 
            FROM doctor as d
            INNER JOIN doctor_time as t ON d.doctor_id=t.doctor_id 
            JOIN doctor_speciality as s ON d.speciality_id=s.speciality_id
            ".(count($where) > 0 ? "WHERE ".implode(" AND ", $where) : "")." 
            ORDER BY name ASC";
        $queryDoctor = pg_query($this->connection, $query);

        $doctorList = array();
        $i = 0;
        while($rowDoctor = pg_fetch_array($queryDoctor))
        {
            $doctorList[$i] = array(
                "doctor_id" => $rowDoctor["doctor_id"],
                "name" => $rowDoctor["name"],
                "office" => $rowDoctor["office"],
                "monday" => $rowDoctor["monday"],
                "tuesday" => $rowDoctor["tuesday"],
                "wednesday" => $rowDoctor["wednesday"],
                "thursday" => $rowDoctor["thursday"],
                "friday" => $rowDoctor["friday"],
                "saturday" => $rowDoctor["saturday"],
                "sunday" => $rowDoctor["sunday"],
                "speciality" => $rowDoctor["speciality"]
            );
            $i++;
        }
        return($doctorList);
    }

    //Функция, возвращающая id медицинского учреждения по его названию
    //$lpu название медицинского учреждения

    public function getLpuID($lpu)
    {
        $query = preg_replace( "/«|»/", '"', "SELECT lpu_id FROM lpu WHERE title='".$lpu."'" );
        $result = pg_query($this->connection, $query);
        $row = pg_fetch_array($result);
        if(is_array($row))
        {
            $lpuID = $row["lpu_id"];
            return($lpuID);
        }	    
    }

    //Функция, возвращающая id филиала медицинского учреждения по его названию
    //$department название филиала медицинского учреждения

    public function getDepartmentID($department, $lpuID)
    {
        $result = pg_query($this->connection, "SELECT department_id FROM lpu_department WHERE title='".$department."' AND lpu_id=".$lpuID);
        print_r("SELECT department_id FROM lpu_department WHERE title='".$department."' AND lpu_id=".$lpuID);
        $row = pg_fetch_array($result);
        $departmentID = $row["department_id"];
        return($departmentID);
    }

    //Функция, возвращающая id специальности врача
    //$speciality название специальности

    public function getSpecialityID($speciality)
    {
        $result = pg_query($this->connection, "SELECT speciality_id FROM doctor_speciality WHERE speciality='".$speciality."'");
        $row = pg_fetch_array($result);
        $specialityID = $row["speciality_id"];
        return($specialityID);
    }

    //Функция, возвращающая id врача
    //$name ФИО врача

    public function getDoctorID($name)
    {
        $result = pg_query($this->connection, "SELECT doctor_id FROM doctor WHERE name='".$name."'");
        $row = pg_fetch_array($result);
        $doctorID = $row["doctor_id"];
        return($doctorID);
    }

    //Функция, сохраняющая врачей
    //В таблицу базы данных вносятся все врачи, переданные в массиве
    //$doctorList список врачей с их расписанием

    public function saveDoctorList($doctorList)
    {
        $doctors = array_values($doctorList);
        for($i = 0; $i < count($doctors); $i++)
        {
            $query = "INSERT INTO doctor(lpu_id, department_id, speciality_id, name, office)
                VALUES(
                ".$doctors[$i]["lpu_id"].", 
                ".$doctors[$i]["department_id"].",
                ".$doctors[$i]["speciality_id"].",
                '".$doctors[$i]["name"]."',
                '".$doctors[$i]["office"]."')";
            pg_query($this->connection, $query);

            $result = pg_query($this->connection, "SELECT doctor_id FROM doctor ORDER BY doctor_id DESC LIMIT 1");
            $row = pg_fetch_array($result);
            $doctorID = $row["doctor_id"];
            $this->saveDoctorTime($doctorID, $doctors[$i]["time"]);
        }
    }

    //Функция, сохраняющая расписание врачей
    //В таблицу базы данных вносится расписание врача с указанным doctor_id
    //$doctorID идентификатор врача, расписание которого необходимо добавить
    //$time расписание

    public function saveDoctorTime($doctorID, $time)
    {
        $result = pg_query($this->connection, "SELECT time_id FROM doctor_time WHERE week='".$time["week"]."' AND doctor_id=".$doctorID);
        $row = pg_fetch_array($result);
        $timeID = $row["time_id"];

        if($timeID)
        {
            $query = "UPDATE doctor_time SET
                monday='".$time["monday"]."',
                tuesday='".$time["tuesday"]."',
                wednesday='".$time["wednesday"]."',
                thursday='".$time["thursday"]."',
                friday='".$time["friday"]."',
                saturday='".$time["saturday"]."',
                sunday='".$time["sunday"]."'
                WHERE week='".$time["week"]."' AND doctor_id=".$doctorID;
        }
        else
        {
            $query = "INSERT INTO doctor_time(doctor_id, monday, tuesday, wednesday, thursday, friday, saturday, sunday, week)
                VALUES(
                ".$doctorID.", 
                '".$time["monday"]."',
                '".$time["tuesday"]."',
                '".$time["wednesday"]."',
                '".$time["thursday"]."',
                '".$time["friday"]."',
                '".$time["saturday"]."',
                '".$time["sunday"]."',
                ".$time["week"].")";
        }
        pg_query($this->connection, $query);
    }
}

?>