<?php

class menuModel extends baseModel {

    function __construct() {

        $this->table = ' t_menu ';        
        $this->db = $this->connDB( DEFAULT_DB );

    }

    function __destruct() {

        # db close
        $this->db->dbClose();

    }

    /**
     * 데이터 입력
     */
    public function insertMenu( $arg_data ){
        return $this->db->insert( $this->table, $arg_data );
    }

    /**
     * 데이터 수정.
     */
    public function updateMenu( $arg_data, $arg_where ) {
        return $this->db->update( $this->table, $arg_data, $arg_where );
    }
    

}

?>