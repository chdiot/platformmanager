<?php

require_once 'Framework/Model.php';

class ClCompany extends Model {

    public function __construct() {
        $this->tableName = "cl_company";
        $this->setColumnsInfo("id", "int(11)", 0);
        $this->setColumnsInfo("id_space", "int(11)", 0);
        $this->setColumnsInfo("name", "varchar(255)", 0);
        $this->setColumnsInfo("address", "text", ""); 
        $this->setColumnsInfo("zipcode", "varchar(255)", 0);
        $this->setColumnsInfo("city", "varchar(255)", 0);
        $this->setColumnsInfo("county", "varchar(255)", 0);
        $this->setColumnsInfo("country", "varchar(255)", 0);
        $this->setColumnsInfo("tel", "varchar(255)", 0);
        $this->setColumnsInfo("fax", "varchar(255)", 0);
        $this->setColumnsInfo("email", "varchar(255)", 0);
        $this->setColumnsInfo("approval_number", "varchar(255)", 0);
        
        $this->primaryKey = "id";
    }

    public function getForSpace($id_space) {
        $sql = "SELECT * FROM cl_company WHERE id_space=?";
        return $this->runRequest($sql, array($id_space))->fetch();
    }

    public function get($id) {
        $sql = "SELECT * FROM cl_company WHERE id=?";
        return $this->runRequest($sql, array($id))->fetch();
    }
    
    public function getName($id){
        $sql = "SELECT reference FROM cl_company WHERE id=?";
        $data = $this->runRequest($sql, array($id))->fetch();
        return $data[0];
    }

    public function set($id_space, $name, $address, $zipcode, $city, 
            $county, $country, $tel, $fax, $email, $approval_number) {
        
        $id = $this->exists($id_space);
        if ( $id == 0 ) {
            $sql = 'INSERT INTO cl_company (id_space, name, address, zipcode, city, 
            county, country, tel, fax, email, approval_number) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
            $this->runRequest($sql, array( $id_space, $name, $address, $zipcode, $city, 
            $county, $country, $tel, $fax, $email, $approval_number ));
            return $this->getDatabase()->lastInsertId();
        } else {
            $sql = 'UPDATE cl_company SET id_space=?, name=?, address=?, zipcode=?, 
                city=?, county=?, country=?, tel=?, fax=?, email=?, approval_number=? WHERE id=?';
            $this->runRequest($sql, array($id_space, $name, $address, $zipcode, $city, 
            $county, $country, $tel, $fax, $email, $approval_number, $id));
            return $id;
        }
    }
    
    protected function exists($id_space){
        $sql = "SELECT id FROM cl_company WHERE id_space=?";
        $req = $this->runRequest($sql, array($id_space));
        if($req->rowCount() > 0){
            $tmp = $req->fetch();
            return $tmp[0];
        }
        return 0;
    }

    public function delete($id) {
        $sql = "DELETE FROM cl_company WHERE id=?";
        $this->runRequest($sql, array($id));
    }

}
