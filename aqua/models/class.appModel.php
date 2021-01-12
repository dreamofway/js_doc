<?php

class appModel extends baseModel {

    
    private $table_app;    
    private $table_ver_history;    
    private $table_push_log;    
    private $table_masicgong_member;    

    function __construct() {

        $this->table_app = ' t_app_Info ';
        $this->table_push_log = ' t_push_log ';
        $this->table_ver_history = ' t_app_version_history ';
        $this->table_masicgong_member = ' t_masicgong_member ';
        
        $this->db = $this->connDB( DEFAULT_DB );

    }

    /**
     * 앱 목록을 가져온다.
     */
    public function getApps( $arg_data ){

        $result = [];
        
        $query = " SELECT COUNT(*) AS cnt FROM ". $this->table_app ." WHERE 1=1 " . $arg_data['query_where'];

        $query_result = $this->db->execute( $query );

        $result['total_rs'] = $query_result['return_data']['row']['cnt'];

        $query = " SELECT * FROM ". $this->table_app ." WHERE 1=1 " . $arg_data['query_where']. $arg_data['query_sort'] . $arg_data['limit'];
        
        $query_result = $this->db->execute( $query );

        $result['rows'] = $query_result['return_data']['rows'];

        return $result;

    }

    /**
     * 앱 정보를 반환한다.
     */
    public function getApp( $arg_where ) {

        $query = " SELECT * FROM ". $this->table_app ." WHERE " . $arg_where;
        $query_result = $this->db->execute( $query );

        return $query_result['return_data'];

    }


    /**
     * 버전 정보 목록을 가져온다.
     */
    public function getVerHistorys( $arg_data ){

        $result = [];
        
        $query = " SELECT COUNT(*) AS cnt FROM ". $this->table_ver_history ." WHERE 1=1 " . $arg_data['query_where'];

        $query_result = $this->db->execute( $query );

        $result['total_rs'] = $query_result['return_data']['row']['cnt'];

        $query = "
                     SELECT * 
                            ,( SELECT name FROM ". $this->table_masicgong_member ." WHERE ". $this->table_masicgong_member .".member_idx = reg_idx) AS reg_member_name
                    FROM ". $this->table_ver_history ." WHERE 1=1 " . $arg_data['query_where']. $arg_data['query_sort'] . $arg_data['limit'];
        
        $query_result = $this->db->execute( $query );

        $result['rows'] = $query_result['return_data']['rows'];

        return $result;

    }

    /**
     * 버전 정보를 반환한다.
     */
    public function getVerHistory( $arg_where ) {

        $query = " SELECT * FROM ". $this->table_ver_history ." WHERE " . $arg_where;
        $query_result = $this->db->execute( $query );

        return $query_result['return_data'];

    }

    /**
     * 가장 큰 버전 정보를 반환한다.
     */
    public function getMaxVer( $arg_where ) {

        $query = " SELECT IFNULL( MAX( version ), '') AS max_ver FROM ". $this->table_ver_history ." WHERE " . $arg_where;
        $query_result = $this->db->execute( $query );

        return $query_result['return_data'];

    }
	
	/**
     * 앱 목록을 가져온다.
     */
    public function getPushLog( $arg_data ){

        $result = [];
        
        $query = " SELECT COUNT(*) AS cnt FROM ". $this->table_push_log ." WHERE 1=1 " . $arg_data['query_where'];

        $query_result = $this->db->execute( $query );

        $result['total_rs'] = $query_result['return_data']['row']['cnt'];

        $query = " SELECT *, ( SELECT  app_name FROM ". $this->table_app ." WHERE aos_package = t_push_log.app_id ) AS app_name
						FROM ". $this->table_push_log ." WHERE 1=1 " . $arg_data['query_where']. $arg_data['query_sort'] . $arg_data['limit'];

        $query_result = $this->db->execute( $query );

        $result['rows'] = $query_result['return_data']['rows'];

        return $result;

    }

    /**
     * 앱 정보를 삽입한다.
     */
    public function insertApp( $arg_data ){
        return $this->db->insert( $this->table_app, $arg_data );
    }

    /**
     * 앱 정보를 갱신한다.
     */
    public function updateApp( $arg_data, $arg_where ) {
        return $this->db->update( $this->table_app, $arg_data, $arg_where );
    }

    /**
     * 버전 정보를 삽입한다.
     */
    public function insertVerHistory( $arg_data ){
        return $this->db->insert( $this->table_ver_history, $arg_data );
    }

    /**
     * 버전 정보를 갱신한다.
     */
    public function updateVerHistory( $arg_data, $arg_where ) {
        return $this->db->update( $this->table_ver_history, $arg_data, $arg_where );
    }


    function __destruct() {

        # db close
        $this->db->dbClose();

    }

    

}

?>