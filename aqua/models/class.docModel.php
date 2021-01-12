<?php

class docModel extends baseModel {

    
    private $table_doc_lib;    
    private $table_doc_usage;    
    private $table_service;    
    private $table_company;    

    function __construct() {

        $this->table_doc_lib = ' t_document_library ';
        $this->table_items = ' t_service_items ';
        $this->table_doc_usage = ' t_document_usage ';
        $this->table_company = ' t_company_info ';
        $this->db = $this->connDB( DEFAULT_DB );

    }

    /**
     * 문서 양식을 가져온다.
     */
    public function getDocForms( $arg_data ){

        $result = [];
        $join_table = "
            (
                SELECT  
                        as_lib.*
                        , as_servic_item.title AS item_title

                FROM
                        ". $this->table_doc_lib ." AS as_lib LEFT OUTER JOIN ". $this->table_items ." AS as_servic_item
                        ON as_lib.item_code = as_servic_item.item_code

            ) AS t_new
            
        ";

        $query = " SELECT COUNT(*) AS cnt FROM ". $join_table ." WHERE 1=1 " . $arg_data['query_where'];

        $query_result = $this->db->execute( $query );

        $result['total_rs'] = $query_result['return_data']['row']['cnt'];

        $query = " SELECT * FROM ". $join_table ." WHERE 1=1 " . $arg_data['query_where']. $arg_data['query_sort'] . $arg_data['limit'];
        
        $query_result = $this->db->execute( $query );

        $result['rows'] = $query_result['return_data']['rows'];

        return $result;

    }

    /**
     * 문서양식 정보를 반환한다.
     */
    public function getDocForm( $arg_where ) {

        $query = " SELECT * FROM ". $this->table_doc_lib ." WHERE " . $arg_where;
        $query_result = $this->db->execute( $query );

        return $query_result['return_data'];

    }


    /**
     * 문서를 가져온다.
     */
    public function getDocs( $arg_data ){

        $result = [];
        $join_table = "
            (
                SELECT  
                        as_company_doc.*
                        , as_servic_item.title AS item_title
                        ,as_company.company_name

                FROM
                        ". $this->table_doc_usage ." AS as_company_doc LEFT OUTER JOIN ". $this->table_items ." AS as_servic_item
                        ON as_company_doc.item_code = as_servic_item.item_code
                        LEFT OUTER JOIN ". $this->table_company ." AS as_company
                        ON as_company_doc.company_idx = as_company.company_idx


            ) AS t_new
            
        ";

        $query = " SELECT COUNT(*) AS cnt FROM ". $join_table ." WHERE 1=1 " . $arg_data['query_where'];

        $query_result = $this->db->execute( $query );

        $result['total_rs'] = $query_result['return_data']['row']['cnt'];

        $query = " SELECT * FROM ". $join_table ." WHERE 1=1 " . $arg_data['query_where']. $arg_data['query_sort'] . $arg_data['limit'];
        
        $query_result = $this->db->execute( $query );

        $result['rows'] = $query_result['return_data']['rows'];

        return $result;

    }

    /**
     * 문서 정보를 반환한다.
     */
    public function getDoc( $arg_where ) {

        $join_table = "
            (
                SELECT  
                        as_company_doc.*
                        , as_servic_item.title AS item_title
                        ,as_company.company_name

                FROM
                        ". $this->table_doc_usage ." AS as_company_doc LEFT OUTER JOIN ". $this->table_items ." AS as_servic_item
                        ON as_company_doc.item_code = as_servic_item.item_code
                        LEFT OUTER JOIN ". $this->table_company ." AS as_company
                        ON as_company_doc.company_idx = as_company.company_idx


            ) AS t_new
            
        ";

        $query = " SELECT * FROM ". $join_table ." WHERE " . $arg_where;
        $query_result = $this->db->execute( $query );

        return $query_result['return_data'];

    }

    /**
     * 문서양식 정보를 삽입한다.
     */
    public function insertDocForm( $arg_data ){
        return $this->db->insert( $this->table_doc_lib, $arg_data );
    }

    /**
     * 문서양식 정보를 갱신한다.
     */
    public function updateDocForm( $arg_data, $arg_where ) {
        return $this->db->update( $this->table_doc_lib, $arg_data, $arg_where );
    }

    /**
     * 문서양식 정보를 삽입한다.
     */
    public function insertDoc( $arg_data ){
        return $this->db->insert( $this->table_doc_usage, $arg_data );
    }

    /**
     * 문서양식 정보를 갱신한다.
     */
    public function updateDoc( $arg_data, $arg_where ) {
        return $this->db->update( $this->table_doc_usage, $arg_data, $arg_where );
    }


    function __destruct() {

        # db close
        $this->db->dbClose();

    }

    

}

?>