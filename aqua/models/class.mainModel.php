<?php

class mainModel extends baseModel {

    function __construct() {

        $this->db = $this->connDB( DEFAULT_DB );

    }

    function __destruct() {

        # db close
        $this->db->dbClose();

    }

}

?>