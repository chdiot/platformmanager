<?php

require_once 'Framework/Model.php';
require_once 'Modules/core/Model/CoreConfig.php';
require_once 'Modules/ecosystem/Model/EcResponsible.php';
require_once 'Modules/ecosystem/Model/EcUnit.php';

/**
 * Class defining the User model
 *
 * @author Sylvain Prigent
 */
class EcUser extends Model {

    /**
     * Create the user table
     *
     * @return PDOStatement
     */
    public function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `ec_users` (
		`id` int(11) NOT NULL,
		`phone` varchar(30) NOT NULL DEFAULT '',
		`id_unit` int(11) NOT NULL DEFAULT 1,	
		`date_convention` DATE,
                `is_responsible` INT(1) NOT NULL DEFAULT 0,
                `convention_url` TEXT(255),
		PRIMARY KEY (`id`)
		);";

        $this->runRequest($sql);
    }

    public function mergeUsers($users) {
        for ($i = 1; $i < count($users); $i++) {
            $sql = "DELETE FROM ec_users WHERE id=?";
            $this->runRequest($sql, array($users[$i]));
        }
    }

    public function mergeUnits($units) {
        for ($i = 1; $i < count($units); $i++) {
            $sql = "UPDATE ec_users SET id_unit=? WHERE id_unit=?";
            $this->runRequest($sql, array($units[0], $units[$i]));
        }
    }

    public function exists($id) {
        $sql = "SELECT * FROM ec_users WHERE id=?";
        $req = $this->runRequest($sql, array($id));
        if ($req->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getResponsibles() {
        $sql = "SELECT ec_users.id as id, core_users.name as name, core_users.firstname as firstname "
                . "FROM ec_users "
                . "INNER JOIN core_users ON ec_users.id = core_users.id "
                . "WHERE ec_users.is_responsible=1 "
                . "ORDER BY core_users.name ASC";
        return $this->runRequest($sql)->fetchAll();
    }

    public function getUnit($id_user) {
        $sql = "SELECT id_unit FROM ec_users WHERE id=?";
        $req = $this->runRequest($sql, array($id_user));
        if ($req->rowCount() == 1) {
            $tmp = $req->fetch();
            return $tmp[0];
        }
        return 0;
    }

    public function getStatus($id_user) {
        $sql = "SELECT id_status FROM core_users WHERE id=?";
        $req = $this->runRequest($sql, array($id_user))->fetch();
        return $req[0];
    }

    public function importCoreUsers() {
        $sql1 = "SELECT * FROM core_users WHERE id NOT IN (SELECT id FROM ec_users)";
        $users = $this->runRequest($sql1)->fetchAll();
        foreach ($users as $user) {
            $sql = "INSERT INTO ec_users (id) VALUES(?)";
            $this->runRequest($sql, array($user["id"]));
        }
    }

    public function getActiveManagersEmails($id_space) {
        $sql = "SELECT email FROM core_users WHERE id IN (SELECT id_user FROM core_j_spaces_user WHERE status>2 AND id_space=?)";
        return $this->runRequest($sql, array($id_space))->fetchAll();
    }

    public function getActiveUsers($sortentry) {
        $modelUser = new CoreUser();
        return $modelUser->getActiveUsers($sortentry);
    }

    public function getAcivesForSelect($sortentry) {
        $modelUser = new CoreUser();
        $users = $modelUser->getActiveUsers($sortentry);
        $names = array();
        $ids = array();
        $names[] = "";
        $ids[] = "";
        foreach ($users as $res) {
            $names[] = $res["name"] . " " . $res["firstname"];
            $ids[] = $res["id"];
        }
        return array("names" => $names, "ids" => $ids);
    }

    public function getAcivesRespsForSelect($sortentry = "name") {
        $sql = "SELECT ec_users.id, core_users.name, core_users.firstname "
                . "FROM ec_users "
                . "INNER JOIN core_users ON ec_users.id = core_users.id "
                . "WHERE ec_users.is_responsible=1 "
                . "ORDER BY core_users." . $sortentry . " ASC";
        $data = $this->runRequest($sql)->fetchAll();
        $names = array();
        $ids = array();
        $names[] = "";
        $ids[] = "";
        foreach ($data as $res) {
            $names[] = $res["name"] . " " . $res["firstname"];
            $ids[] = $res["id"];
        }
        return array("names" => $names, "ids" => $ids);
    }

    /**
     * get the informations of a user from it's id
     *
     * @param int $id
     *        	Id of the user to query
     * @throws Exception if the user connot be found
     */
    public function userAllInfo($id) {
        $sql = "SELECT core_users.*, ec_users.* "
                . "FROM core_users "
                . "INNER JOIN ec_users ON core_users.id = ec_users.id "
                . "WHERE core_users.id=?";
        $req = $this->runRequest($sql, array($id));


        if ($req->rowCount() == 1) {
            $userInfo = $req->fetch();
            $userInfo["id_resps"] = $this->getUserResponsibles($id);

            return $userInfo;
        } else {
            return array("id" => 0,
                "login" => 'unknown',
                "firstname" => 'unknown',
                "name" => 'unknown',
                "email" => '',
                "tel" => '',
                "pwd" => '',
                "id_unit" => 1,
                "id_status" => 1,
                "convention" => 0,
                "date_convention" => '0000-00-00',
                "date_created" => '0000-00-00',
                "date_last_login" => '0000-00-00',
                "date_end_contract" => '0000-00-00',
                "is_active" => 1,
                "source" => 'local');
        }
    }

    public function add($name, $firstname, $login, $pwd, $email, $phone, $unit, $is_responsible, $status_id, $date_convention, $date_end_contract, $encrypte = true) {
        $model = new CoreUser();
        $id = $model->add($login, $pwd, $name, $firstname, $email, $status_id, $date_end_contract, 1, $encrypte);

        $sql = "INSERT INTO ec_users (id, phone, id_unit, is_responsible, date_convention) VALUES (?,?,?,?,?)";
        $this->runRequest($sql, array($id, $phone, $unit, $is_responsible, $date_convention));
        return $id;
    }

    public function initialize($id) {
        $sql = "INSERT INTO ec_users (id, phone, id_unit, is_responsible, date_convention) VALUES (?,?,?,?,?)";
        $this->runRequest($sql, array($id, "", 1, 0, "0000-00-00"));
    }

    public function edit($id, $name, $firstname, $login, $email, $phone, $unit, $is_responsible, $id_status, $date_convention, $date_end_contract, $is_active) {

        $modelUser = new CoreUser();
        $modelUser->edit($id, $login, $name, $firstname, $email, $id_status, $date_end_contract, $is_active);

        $sql = "UPDATE ec_users SET phone=?, id_unit=?, is_responsible=?, date_convention=? WHERE id=?";
        $this->runRequest($sql, array($phone, $unit, $is_responsible, $date_convention, $id));
    }

    public function setResponsible($id_user, $id_resp) {
        $sql = "SELECT * FROM ec_users WHERE id=?";
        $req = $this->runRequest($sql, array($id_user));
        if ($req->rowCount() > 0) {
            $sql = "UPDATE ec_users SET is_responsible=? WHERE id=?";
            $this->runRequest($sql, array($id_resp, $id_user));
        } else {
            $sql = "INSERT INTO ec_users (id, is_responsible) VALUES (?,?)";
            $this->runRequest($sql, array($id_user, $id_resp));
        }
    }

    public function import2($id, $phone, $id_unit, $date_convention, $is_responsible, $convention_url) {

        $sql = "SELECT id FROM ec_users WHERE id=?";
        $req = $this->runRequest($sql, array($id));
        if ($req->rowCount() > 0) {
            $sql = "UPDATE ec_users SET phone=?, id_unit=?, date_convention=?, is_responsible=?, convention_url=? WHERE id=?";
            $this->runRequest($sql, array($phone, $id_unit, $date_convention, $is_responsible, $convention_url, $id));
        } else {
            $sql = "INSERT INTO ec_users (id, phone, id_unit, date_convention, is_responsible, convention_url) VALUES (?,?,?,?,?,?)";
            $this->runRequest($sql, array($id, $phone, $id_unit, $date_convention, $is_responsible, $convention_url));
        }
    }

    public function getDefault() {
        return array("id" => 0,
            "login" => "",
            "name" => "",
            "firstname" => "",
            "email" => "",
            "status_id" => 0,
            "source" => "local",
            "is_active" => 1,
            "date_created" => "",
            "date_end_contract" => "",
            "phone" => '',
            "id_unit" => 1,
            "date_convention" => "",
            "is_responsible" => 0,
            "convention_url" => "",
            "id_resps" => array());
    }

    public function getInfo($id) {
        $sql = "SELECT core_users.*, ec_users.* "
                . "FROM core_users "
                . "INNER JOIN ec_users ON core_users.id = ec_users.id "
                . "WHERE core_users.id=?";
        $userInfo = $this->runRequest($sql, array($id))->fetch();

        $userInfo["id_resps"] = $this->getUserResponsibles($id);

        return $userInfo;
    }

    public function setConventionUrl($id_user, $url) {
        $sql = "UPDATE ec_users SET convention_url=? WHERE id=?";
        $this->runRequest($sql, array($url, $id_user));
    }

    public function getActiveUsersInfoLetter($letter, $active) {

        $sql = "SELECT * FROM core_users WHERE is_active=? AND name LIKE '" . $letter . "%' ORDER BY name ASC;";
        $users = $this->runRequest($sql, array($active))->fetchAll();
        $modelUnit = new EcUnit();
        $modelStatus = new CoreStatus();
        for ($i = 0; $i < count($users); $i++) {

            $sql = "SELECT * FROM ec_users WHERE id=?";
            $req = $this->runRequest($sql, array($users[$i]["id"]));
            if ($req->rowCount() == 0) {
                $sql = "INSERT into ec_users (id, phone, id_unit, date_convention, is_responsible, convention_url) VALUES (?,?,?,?,?,?)";
                $this->runRequest($sql, array($users[$i]["id"], "", 0, "0000-00-00", 0, ""));
            }

            $sql2 = "SELECT * FROM ec_users WHERE id=?";
            $userInfo = $this->runRequest($sql2, array($users[$i]["id"]))->fetch();

            $users[$i]["status"] = $modelStatus->getStatusName($users[$i]["status_id"]);

            $users[$i]["phone"] = $userInfo["phone"];
            $users[$i]["unit"] = $modelUnit->getUnitName($userInfo["id_unit"]);
            $users[$i]["date_convention"] = $userInfo["date_convention"];
            $users[$i]["is_responsible"] = $userInfo["is_responsible"];
            $users[$i]["convention_url"] = $userInfo["convention_url"];
        }
        return $users;
    }

    public function getActiveUsersInfo($active) {
        $sql = "SELECT * FROM core_users WHERE is_active=? ORDER BY name ASC;";
        $users = $this->runRequest($sql, array($active))->fetchAll();
        $modelUnit = new EcUnit();
        $modelStatus = new CoreStatus();
        for ($i = 0; $i < count($users); $i++) {

            $sql = "SELECT * FROM ec_users WHERE id=?";
            $req = $this->runRequest($sql, array($users[$i]["id"]));
            if ($req->rowCount() == 0) {
                $sql = "INSERT into ec_users (id, phone, id_unit, date_convention, is_responsible, convention_url) VALUES (?,?,?,?,?,?)";
                $this->runRequest($sql, array($users[$i]["id"], "", 0, "0000-00-00", 0, ""));
            }

            $sql2 = "SELECT * FROM ec_users WHERE id=?";
            $userInfo = $this->runRequest($sql2, array($users[$i]["id"]))->fetch();

            $users[$i]["status"] = $modelStatus->getStatusName($users[$i]["status_id"]);

            $users[$i]["phone"] = $userInfo["phone"];
            $users[$i]["unit"] = $modelUnit->getUnitName($userInfo["id_unit"]);
            $users[$i]["date_convention"] = $userInfo["date_convention"];
            $users[$i]["is_responsible"] = $userInfo["is_responsible"];
            $users[$i]["convention_url"] = $userInfo["convention_url"];
        }
        return $users;
    }

    /**
     * GEt the responsible of a given user
     * @param number $id User id
     * @return number Responsible ID
     */
    public function getUserResponsibles($id) {

        $sql = "SELECT id_resp FROM ec_j_user_responsible WHERE id_user = ?";
        $req = $this->runRequest($sql, array($id));
        $userr = $req->fetchAll();

        for ($i = 0; $i < count($userr); $i++) {
            $userr[$i]["id"] = $userr[$i]["id_resp"];
            $userr[$i]["fullname"] = $this->getUserFUllName($userr[$i]["id_resp"]);
        }
        return $userr;
    }

    public function getIdFromLogin($login) {
        $sql = "SELECT id FROM core_users WHERE login=?";
        $req = $this->runRequest($sql, array($login));
        $userr = $req->fetch();
        return $userr[0];
    }

    /**
     * get the firstname and name of a user from it's id
     *
     * @param int $id
     *        	Id of the user to get
     * @throws Exception
     * @return string "firstname name"
     */
    public function getUserFUllName($id) {
        $sql = "select firstname, name from core_users where id=?";
        $user = $this->runRequest($sql, array(
            $id
        ));

        if ($user->rowCount() == 1) {
            $userf = $user->fetch();
            return $userf ['name'] . " " . $userf ['firstname'];
        } else {
            return "";
        }
    }

    public function getResponsibleOfUnit($id_unit) {
        $sql = "SELECT core_users.id AS id, core_users.name AS name, core_users.firstname AS firstname "
                . "FROM ec_users "
                . "INNER JOIN core_users ON core_users.id = ec_users.id "
                . "WHERE ec_users.is_responsible=1 AND ec_users.id_unit=?";
        $users = $this->runRequest($sql, array($id_unit))->fetchAll();

        $names = array();
        $ids = array();
        $names[] = "--";
        $ids[] = 0;
        foreach ($users as $u) {
            $names[] = $u["name"] . " " . $u["firstname"];
            $ids[] = $u["id"];
        }
        return array("names" => $names, "ids" => $ids);
    }

    public function delete($id) {
        $sql = "DELETE FROM ec_users WHERE id=?";
        $this->runRequest($sql, array($id));

        $sql1 = "DELETE FROM core_users WHERE id=?";
        $this->runRequest($sql1, array($id));
    }

    public function getAllActifEmails() {
        $sql = "select email from core_users where is_active=1 order by name ASC;";
        $user = $this->runRequest($sql);
        return $user->fetchAll();
    }

    public function getAllSpaceActifEmails($id_space) {
        $sql = "SELECT email FROM core_users WHERE id IN "
                . "(SELECT id_user FROM core_j_spaces_user WHERE status>0 AND id_space=?)";
        $req = $this->runRequest($sql, array($id_space));
        return $req->fetchAll();
    }

    /**
     * Export Responsible lists to file
     * @param number $idType Active/Unative/all
     */
    public function exportResponsible($idType) {

        // get the responsibles
        $resps = array();
        $typeResp = "";

        if ($idType == 0) { // all
            //$sql = "SELECT * FROM core_users WHERE id IN (SELECT id FROM ec_users WHERE is_responsible=1) ORDER BY name ASC";
            $sql = "SELECT core.*, ec_users.* "
                    . " FROM ec_users"
                    . " INNER JOIN core_users as core ON ec_users.id = core.id "
                    . " WHERE ec_users.is_responsible=1 "
                    . "ORDER BY core.name ASC";

            $req = $this->runRequest($sql);
            $resps = $req->fetchAll();
        } else if ($idType == 1) { // active
            //$sql = "SELECT * FROM ec_users WHERE id IN (SELECT id_users FROM ec_responsibles) AND is_active=? ORDER BY name ASC";
            $sql = "SELECT core.*, ec_users.* "
                    . " FROM ec_users"
                    . " INNER JOIN core_users as core ON ec_users.id = core.id "
                    . " WHERE ec_users.is_responsible=1 AND is_active=?"
                    . "ORDER BY core.name ASC";

            $req = $this->runRequest($sql, array(1));
            $resps = $req->fetchAll();
            $typeResp = "actifs";
        } else if ($idType == 2) { // inactive
            //$sql = "SELECT * FROM ec_users WHERE id IN (SELECT id_users FROM ec_responsibles) AND is_active=0 ORDER BY name ASC";
            $sql = "SELECT core.*, ec_users.* "
                    . " FROM ec_users"
                    . " INNER JOIN core_users as core ON ec_users.id = core.id "
                    . " WHERE ec_users.is_responsible=1 AND is_active=?"
                    . "ORDER BY core.name ASC";

            $req = $this->runRequest($sql, array(0));
            $resps = $req->fetchAll();
            $typeResp = "inactifs";
        }

        // export to xls
        include_once ("externals/PHPExcel/Classes/PHPExcel.php");
        include_once ("externals/PHPExcel/Classes/PHPExcel/Writer/Excel5.php");
        include_once ("externals/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

        // get resource category
        // header
        $today = date('d/m/Y');
        $header = "Date d'édition de ce document : \n" . $today;
        $titre = "Liste des responsables " . $typeResp;

        // file name
        $nom = date('Y-m-d-H-i') . "_" . "responsables" . ".xlsx";
        $teamName = Configuration::get("name");
        $footer = "platformmanager/" . $teamName . "/exportFiles/" . $nom;

        // Création de l'objet
        $objPHPExcel = new PHPExcel ();

        // Définition de quelques propriétés
        $objPHPExcel->getProperties()->setCreator($teamName);
        $objPHPExcel->getProperties()->setLastModifiedBy($teamName);
        $objPHPExcel->getProperties()->setTitle("Liste des responsables " . $typeResp);
        $objPHPExcel->getProperties()->setSubject("");
        $objPHPExcel->getProperties()->setDescription("Fichier genere avec PHPExel depuis la base de donnees");

        $center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $gras = array(
            'font' => array(
                'bold' => true
            )
        );
        $border = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $borderLR = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            )
        );

        $borderG = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                )
            )
        );

        $borderLRB = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $style = array(
            'font' => array(
                'bold' => false,
                'color' => array(
                    'rgb' => '000000'
                ),
                'size' => 15,
                'name' => 'Calibri'
            )
        );

        $style2 = array(
            'font' => array(
                'bold' => false,
                'color' => array(
                    'rgb' => '000000'
                ),
                'size' => 10,
                'name' => 'Calibri'
            )
        );

        // Nommage de la feuille
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Liste responsables ' . $typeResp);

        // Mise en page de la feuille
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $sheet->setBreak('A55', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E55', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A110', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E110', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A165', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E165', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A220', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E220', PHPExcel_Worksheet::BREAK_COLUMN);
        // $sheet->getPageSetup()->setFitToWidth(1);
        // $sheet->getPageSetup()->setFitToHeight(10);
        $sheet->getPageMargins()->SetTop(0.9);
        $sheet->getPageMargins()->SetBottom(0.5);
        $sheet->getPageMargins()->SetLeft(0.2);
        $sheet->getPageMargins()->SetRight(0.2);
        $sheet->getPageMargins()->SetHeader(0.2);
        $sheet->getPageMargins()->SetFooter(0.2);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        // $sheet->getPageSetup()->setVerticalCentered(false);

        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(8);

        // Header
        /*
          $objDrawing = new PHPExcel_Worksheet_HeaderFooterDrawing ();
          $objDrawing->setName('PHPExcel logo');
          $objDrawing->setPath('./Themes/logo.jpg');
          $objDrawing->setHeight(60);
          $objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);
          $sheet->getHeaderFooter()->setOddHeader('&L&G&R' . $header);

         */

        // Titre
        $ligne = 1;
        $colonne = 'A';
        $sheet->getRowDimension($ligne)->setRowHeight(30);
        $sheet->SetCellValue($colonne . $ligne, $titre);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($style);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($gras);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
        $sheet->getStyle($colonne . $ligne)->getAlignment()->setWrapText(true);
        $sheet->mergeCells($colonne . $ligne . ':D' . $ligne);

        /*
          // Avertissement
          $ligne = 2;
          $sheet->mergeCells ( 'A' . $ligne . ':D' . $ligne );
          $sheet->SetCellValue ( 'A' . $ligne, "" );
          $sheet->getStyle ( 'A' . $ligne )->applyFromArray ( $gras );
          $sheet->getStyle ( 'A' . $ligne )->applyFromArray ( $center );
          $sheet->getStyle ( 'A' . $ligne )->getAlignment ()->setWrapText ( true );

          // Réservation
          $ligne = 3;
          $sheet->mergeCells ( 'A' . $ligne . ':D' . $ligne );
          $sheet->SetCellValue ( 'A' . $ligne, "" );
          $sheet->getStyle ( 'A' . $ligne )->applyFromArray ( $center );
         */
        $ligne = 2;
        $sheet->SetCellValue('A' . $ligne, "Laboratoire");
        $sheet->getStyle('A' . $ligne)->applyFromArray($border);
        $sheet->getStyle('A' . $ligne)->applyFromArray($center);
        $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('B' . $ligne, "Nom Prénom");
        $sheet->getStyle('B' . $ligne)->applyFromArray($border);
        $sheet->getStyle('B' . $ligne)->applyFromArray($center);
        $sheet->getStyle('B' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('C' . $ligne, "Charte");
        $sheet->getStyle('C' . $ligne)->applyFromArray($border);
        $sheet->getStyle('C' . $ligne)->applyFromArray($center);
        $sheet->getStyle('C' . $ligne)->applyFromArray($gras);

        $ligne = 3;
        foreach ($resps as $r) {

            if ($r["id"] > 1) {

                $colonne = 'A';
                $sheet->getRowDimension($ligne)->setRowHeight(13);


                $sql = "select name from ec_units where id=?";
                $unitReq = $this->runRequest($sql, array($r ["id_unit"]));
                $unitName = $unitReq->fetch();

                $sheet->SetCellValue($colonne . $ligne, $unitName[0]); // unit name
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
                $colonne ++;
                $sheet->SetCellValue($colonne . $ligne, $r ["name"] . " " . $r ["firstname"]); // user name
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
                $colonne ++;
                // $date=date('d/m/Y', $r[2]); // date
                $sheet->SetCellValue($colonne . $ligne, CoreTranslator::dateFromEn($r ["date_convention"], "fr"));
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
                $colonne ++;

                if (!($ligne % 55)) {
                    $sheet->getStyle('A' . $ligne)->applyFromArray($borderLRB);
                    $sheet->getStyle('B' . $ligne)->applyFromArray($borderLRB);
                    $sheet->getStyle('C' . $ligne)->applyFromArray($borderLRB);
                    $sheet->getStyle('D' . $ligne)->applyFromArray($borderLRB);
                    $ligne ++;
                    // Titre
                    $colonne = 'A';
                    $sheet->getRowDimension($ligne)->setRowHeight(30);
                    $sheet->SetCellValue($colonne . $ligne, $titre);
                    $sheet->getStyle($colonne . $ligne)->applyFromArray($style);
                    $sheet->getStyle($colonne . $ligne)->applyFromArray($gras);
                    $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                    $sheet->getStyle($colonne . $ligne)->getAlignment()->setWrapText(true);
                    $sheet->mergeCells($colonne . $ligne . ':D' . $ligne);

                    /*
                      // Avertissement
                      $ligne ++;
                      $sheet->mergeCells ( 'A' . $ligne . ':D' . $ligne );
                      $sheet->SetCellValue ( 'A' . $ligne, "" );
                      $sheet->getStyle ( 'A' . $ligne )->applyFromArray ( $gras );
                      $sheet->getStyle ( 'A' . $ligne )->applyFromArray ( $center );
                      $sheet->getStyle ( 'A' . $ligne )->getAlignment ()->setWrapText ( true );

                      // Réservation
                      $ligne ++;
                      $sheet->mergeCells ( 'A' . $ligne . ':D' . $ligne );
                      $sheet->SetCellValue ( 'A' . $ligne, "" );
                      $sheet->getStyle ( 'A' . $ligne )->applyFromArray ( $center );
                     */
                    // Noms des colonnes
                    $ligne += 2;
                    $sheet->SetCellValue('A' . $ligne, "Laboratoire");
                    $sheet->getStyle('A' . $ligne)->applyFromArray($border);
                    $sheet->getStyle('A' . $ligne)->applyFromArray($center);
                    $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
                    $sheet->SetCellValue('B' . $ligne, "NOM Prénom");
                    $sheet->getStyle('B' . $ligne)->applyFromArray($border);
                    $sheet->getStyle('B' . $ligne)->applyFromArray($center);
                    $sheet->getStyle('B' . $ligne)->applyFromArray($gras);
                    $sheet->SetCellValue('C' . $ligne, "Date");
                    $sheet->getStyle('C' . $ligne)->applyFromArray($border);
                    $sheet->getStyle('C' . $ligne)->applyFromArray($center);
                    $sheet->getStyle('C' . $ligne)->applyFromArray($gras);
                    $sheet->SetCellValue('D' . $ligne, "Charte");
                    $sheet->getStyle('D' . $ligne)->applyFromArray($border);
                    $sheet->getStyle('D' . $ligne)->applyFromArray($center);
                    $sheet->getStyle('D' . $ligne)->applyFromArray($gras);
                }
                $ligne ++;
            }
        }
        $ligne --;
        $sheet->getStyle('A' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('B' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('C' . $ligne)->applyFromArray($borderLRB);
        //$sheet->getStyle('D' . $ligne)->applyFromArray($borderLRB);
        // Footer
        $sheet->getHeaderFooter()->setOddFooter('&L ' . $footer . '&R Page &P / &N');
        $sheet->getHeaderFooter()->setEvenFooter('&L ' . $footer . '&R Page &P / &N');

        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        //$writer->save ( './data/' . $nom );
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $nom . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    /**
     * Export Responsible lists to file
     * @param number $id_space space id
     */
    public function exportAll($id_space) {

        // get the users
        $sql = "SELECT core.*, ec_users.* "
                . " FROM ec_users"
                . " INNER JOIN core_users as core ON ec_users.id = core.id "
                . " WHERE ec_users.id IN ( SELECT id_user FROM core_j_spaces_user WHERE id_space=? AND status>0 ) "
                . " AND core.is_active=1 "
                . " ORDER BY core.name ASC";

        $req = $this->runRequest($sql, array($id_space));
        $resps = $req->fetchAll();


        // export to xls
        include_once ("externals/PHPExcel/Classes/PHPExcel.php");
        include_once ("externals/PHPExcel/Classes/PHPExcel/Writer/Excel5.php");
        include_once ("externals/PHPExcel/Classes/PHPExcel/Writer/Excel2007.php");

        // get resource category
        // header
        $today = date('d/m/Y');
        $header = "Date d'édition de ce document : \n" . $today;
        $titre = "Liste des utilisateurs";

        // file name
        $nom = date('Y-m-d-H-i') . "_" . "utilisateurs" . ".xlsx";
        $teamName = Configuration::get("name");
        $footer = "platformmanager/" . $teamName . "/exportFiles/" . $nom;

        // Création de l'objet
        $objPHPExcel = new PHPExcel ();

        // Définition de quelques propriétés
        $objPHPExcel->getProperties()->setCreator($teamName);
        $objPHPExcel->getProperties()->setLastModifiedBy($teamName);
        $objPHPExcel->getProperties()->setTitle("Liste des utilisateurs ");
        $objPHPExcel->getProperties()->setSubject("");
        $objPHPExcel->getProperties()->setDescription("Fichier genere avec PHPExel depuis la base de donnees");

        $center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );
        $gras = array(
            'font' => array(
                'bold' => true
            )
        );
        $border = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $borderLR = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                )
            )
        );

        $borderG = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM
                )
            )
        );

        $borderLRB = array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_NONE
                ),
                'left' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'right' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'bottom' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $style = array(
            'font' => array(
                'bold' => false,
                'color' => array(
                    'rgb' => '000000'
                ),
                'size' => 15,
                'name' => 'Calibri'
            )
        );

        $style2 = array(
            'font' => array(
                'bold' => false,
                'color' => array(
                    'rgb' => '000000'
                ),
                'size' => 10,
                'name' => 'Calibri'
            )
        );

        // models
        $modelResp = new EcResponsible();

        // Nommage de la feuille
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle('Liste des utilisateurs ');

        // Mise en page de la feuille
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $sheet->setBreak('A55', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E55', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A110', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E110', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A165', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E165', PHPExcel_Worksheet::BREAK_COLUMN);
        $sheet->setBreak('A220', PHPExcel_Worksheet::BREAK_ROW);
        $sheet->setBreak('E220', PHPExcel_Worksheet::BREAK_COLUMN);
        // $sheet->getPageSetup()->setFitToWidth(1);
        // $sheet->getPageSetup()->setFitToHeight(10);
        $sheet->getPageMargins()->SetTop(0.9);
        $sheet->getPageMargins()->SetBottom(0.5);
        $sheet->getPageMargins()->SetLeft(0.2);
        $sheet->getPageMargins()->SetRight(0.2);
        $sheet->getPageMargins()->SetHeader(0.2);
        $sheet->getPageMargins()->SetFooter(0.2);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        // $sheet->getPageSetup()->setVerticalCentered(false);

        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(8);

        // Header
        /*
          $objDrawing = new PHPExcel_Worksheet_HeaderFooterDrawing ();
          $objDrawing->setName('PHPExcel logo');
          $objDrawing->setPath('./Themes/logo.jpg');
          $objDrawing->setHeight(60);
          $objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);
          $sheet->getHeaderFooter()->setOddHeader('&L&G&R' . $header);

         */

        // Titre
        $ligne = 1;
        $colonne = 'A';
        $sheet->getRowDimension($ligne)->setRowHeight(30);
        $sheet->SetCellValue($colonne . $ligne, $titre);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($style);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($gras);
        $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
        $sheet->getStyle($colonne . $ligne)->getAlignment()->setWrapText(true);
        $sheet->mergeCells($colonne . $ligne . ':D' . $ligne);

        /*
          // Avertissement
          $ligne = 2;
          $sheet->mergeCells ( 'A' . $ligne . ':D' . $ligne );
          $sheet->SetCellValue ( 'A' . $ligne, "" );
          $sheet->getStyle ( 'A' . $ligne )->applyFromArray ( $gras );
          $sheet->getStyle ( 'A' . $ligne )->applyFromArray ( $center );
          $sheet->getStyle ( 'A' . $ligne )->getAlignment ()->setWrapText ( true );

          // Réservation
          $ligne = 3;
          $sheet->mergeCells ( 'A' . $ligne . ':D' . $ligne );
          $sheet->SetCellValue ( 'A' . $ligne, "" );
          $sheet->getStyle ( 'A' . $ligne )->applyFromArray ( $center );
         */
        $ligne = 2;
        $sheet->SetCellValue('A' . $ligne, "Nom Prénom");
        $sheet->getStyle('A' . $ligne)->applyFromArray($border);
        $sheet->getStyle('A' . $ligne)->applyFromArray($center);
        $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('B' . $ligne, "Couriel");
        $sheet->getStyle('B' . $ligne)->applyFromArray($border);
        $sheet->getStyle('B' . $ligne)->applyFromArray($center);
        $sheet->getStyle('B' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('C' . $ligne, "Téléphone");
        $sheet->getStyle('C' . $ligne)->applyFromArray($border);
        $sheet->getStyle('C' . $ligne)->applyFromArray($center);
        $sheet->getStyle('C' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('D' . $ligne, "Unité");
        $sheet->getStyle('D' . $ligne)->applyFromArray($border);
        $sheet->getStyle('D' . $ligne)->applyFromArray($center);
        $sheet->getStyle('D' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('E' . $ligne, "Responsables");
        $sheet->getStyle('E' . $ligne)->applyFromArray($border);
        $sheet->getStyle('E' . $ligne)->applyFromArray($center);
        $sheet->getStyle('E' . $ligne)->applyFromArray($gras);
        $sheet->SetCellValue('F' . $ligne, "Est responsable");
        $sheet->getStyle('F' . $ligne)->applyFromArray($border);
        $sheet->getStyle('F' . $ligne)->applyFromArray($center);
        $sheet->getStyle('F' . $ligne)->applyFromArray($gras);


        if (file_exists('Modules/resources/Model/ReCategory.php')) {

            require_once 'Modules/resources/Model/ReCategory.php';
            require_once 'Modules/booking/Model/BkAuthorization.php';

            $resourcesCatModel = new ReCategory();
            $modelAuth = new BkAuthorization();
            $resourcesCat = $resourcesCatModel->getBySpace($id_space);
            $colonne = 'F';
            foreach ($resourcesCat as $resCat) {

                //print_r($resCat);

                $colonne++;
                $sheet->SetCellValue($colonne . $ligne, $resCat["name"]);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($border);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($gras);
            }
        }
        $ligne = 3;

        //print_r($resps);
        foreach ($resps as $r) {

            if ($r["id"] > 1) {

                //echo "id = " . $r["id"] . "<br/>";
                $colonne = 'A';
                $sheet->getRowDimension($ligne)->setRowHeight(13);


                $sql = "select name from ec_units where id=?";
                $unitReq = $this->runRequest($sql, array($r ["id_unit"]));
                $unitName = $unitReq->fetch();


                $sheet->SetCellValue($colonne . $ligne, $r ["name"] . " " . $r ["firstname"]); // user name
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
                $colonne ++;
                // $date=date('d/m/Y', $r[2]); // date
                $sheet->SetCellValue($colonne . $ligne, $r ["email"]);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
                $colonne ++;
                $sheet->SetCellValue($colonne . $ligne, $r ["phone"]); // phone
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
                $colonne ++;
                $sheet->SetCellValue($colonne . $ligne, $unitName[0]); // unit name
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
                $colonne ++;

                // responsibles
                $resps = $modelResp->getUserResponsibles($r["id"]);
                $respNames = "";
                foreach ($resps as $re) {
                    $respNames .= $modelResp->getUserFUllName($re["id"]);
                }
                $sheet->SetCellValue($colonne . $ligne, $respNames); // Resps
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
                $colonne ++;

                // is responsible
                $id_resp = "Non";
                if ($r["is_responsible"]) {
                    $id_resp = "Oui";
                }

                $sheet->SetCellValue($colonne . $ligne, $id_resp); // Resps
                $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
                $colonne ++;

                if (file_exists('Modules/resources/Model/ReCategory.php')) {

                    require_once 'Modules/resources/Model/ReCategory.php';
                    require_once 'Modules/resources/Model/ReVisa.php';

                    foreach ($resourcesCat as $resCat) {

                        $modelVisa = new ReVisa();
                        $authInfo = $modelAuth->getAuthorisationInfo($resCat["id"], $r ["id"]);

                        if ($authInfo != 0) {
                            $txt = CoreTranslator::dateFromEn($authInfo["date"], "fr") . " " . $modelVisa->getVisaShortDescription($authInfo["visa_id"], "fr");

                            $sheet->SetCellValue($colonne . $ligne, $txt);
                            $sheet->getStyle($colonne . $ligne)->applyFromArray($style2);
                            $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                            $sheet->getStyle($colonne . $ligne)->applyFromArray($borderLR);
                        }
                        $colonne++;
                    }
                }

                /*
                  if (!($ligne % 55)) {
                  $sheet->getStyle('A' . $ligne)->applyFromArray($borderLRB);
                  $sheet->getStyle('B' . $ligne)->applyFromArray($borderLRB);
                  $sheet->getStyle('C' . $ligne)->applyFromArray($borderLRB);
                  //$sheet->getStyle('D' . $ligne)->applyFromArray($borderLRB);
                  $ligne ++;
                  // Titre
                  $colonne = 'A';
                  $sheet->getRowDimension($ligne)->setRowHeight(30);
                  $sheet->SetCellValue($colonne . $ligne, $titre);
                  $sheet->getStyle($colonne . $ligne)->applyFromArray($style);
                  $sheet->getStyle($colonne . $ligne)->applyFromArray($gras);
                  $sheet->getStyle($colonne . $ligne)->applyFromArray($center);
                  $sheet->getStyle($colonne . $ligne)->getAlignment()->setWrapText(true);
                  $sheet->mergeCells($colonne . $ligne . ':D' . $ligne);


                  // Noms des colonnes
                  $ligne += 2;
                  $sheet->SetCellValue('A' . $ligne, "Laboratoire");
                  $sheet->getStyle('A' . $ligne)->applyFromArray($border);
                  $sheet->getStyle('A' . $ligne)->applyFromArray($center);
                  $sheet->getStyle('A' . $ligne)->applyFromArray($gras);
                  $sheet->SetCellValue('B' . $ligne, "NOM Prénom");
                  $sheet->getStyle('B' . $ligne)->applyFromArray($border);
                  $sheet->getStyle('B' . $ligne)->applyFromArray($center);
                  $sheet->getStyle('B' . $ligne)->applyFromArray($gras);
                  $sheet->SetCellValue('C' . $ligne, "Email");
                  $sheet->getStyle('C' . $ligne)->applyFromArray($border);
                  $sheet->getStyle('C' . $ligne)->applyFromArray($center);
                  $sheet->getStyle('C' . $ligne)->applyFromArray($gras);
                  }
                  $ligne ++;
                 */
                $ligne++;
            }
        }
        $ligne --;
        $sheet->getStyle('A' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('B' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('C' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('D' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('E' . $ligne)->applyFromArray($borderLRB);
        $sheet->getStyle('F' . $ligne)->applyFromArray($borderLRB);
        // Footer
        $sheet->getHeaderFooter()->setOddFooter('&L ' . $footer . '&R Page &P / &N');
        $sheet->getHeaderFooter()->setEvenFooter('&L ' . $footer . '&R Page &P / &N');

        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        //$writer->save ( './data/' . $nom );
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $nom . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

}
