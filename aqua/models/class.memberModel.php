<?php

class memberModel extends baseModel {

    function __construct() {

        $this->table = ' t_masicgong_member ';        
        $this->db = $this->connDB( DEFAULT_DB );

    }

    function __destruct() {

        # db close
        $this->db->dbClose();

    }
	
	  /**
     * 유형 코드를 생성해 반환한다.
     */
    public function getMaxCode( $arg_type, $arg_year ){

        $query = " SELECT LEFT( IFNULL( MAX( member_code ), ''), 5) AS max_code FROM ". $this->table ." WHERE member_code LIKE '". $arg_type ."%%".$arg_year ."' ";

        $query_result = $this->db->execute( $query );

        return $query_result['return_data']['row']['max_code'];

    }


    /**
     * 사원 목록을 반환한다.
     */
    public function getMembers( $arg_data ){

        $result = [];
        

        $query = " SELECT COUNT(*) AS cnt FROM ". $this->table ." WHERE 1=1 " . $arg_data['query_where'];

        $query_result = $this->db->execute( $query );

        $result['total_rs'] = $query_result['return_data']['row']['cnt'];

        $query = " SELECT * FROM ". $this->table ." WHERE 1=1 " . $arg_data['query_where']. $arg_data['query_sort'] . $arg_data['limit'];
        
        $query_result = $this->db->execute( $query );

        $result['rows'] = $query_result['return_data']['rows'];

        return $result;

    }

	/**
     * 사원 정보를 반환한다.
     */
    public function getMember( $arg_where ) {

        $query = " SELECT * FROM ". $this->table ." WHERE " . $arg_where;
        $query_result = $this->db->execute( $query );

        return $query_result['return_data'];

    }

    /**
     * 기업정보를 insert 한다.
     */
    public function insertMember( $arg_data ){
        return $this->db->insert( $this->table, $arg_data );
    }

    /**
     * 기업정보를 수정한다.
     */
    public function updateMember( $arg_data, $arg_where ) {
        return $this->db->update( $this->table, $arg_data, $arg_where );
    }
    
}

?>