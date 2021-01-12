<?php

class authModel extends baseModel {

    function __construct() {

        $this->db = $this->connDB( DEFAULT_DB );

    }

    public function loginProc( $arg_data ) {

        $query = '  SELECT * 
                    FROM t_masicgong_member 
                    WHERE   ( ( ( member_code = "'. $arg_data['id'] .'" ) OR ( phone_no = "'. $arg_data['id'] .'" ) 
                                OR ( email = "'. $arg_data['id'] .'" ) ) AND ( password = "'. $arg_data['pw'] .'" ) 
                            ) AND ( use_flag="Y" )
                            
                            
        ';
        
        $query_result = $this->db->execute( $query );

        return $query_result;

    }

    function __destruct() {

        # db close
        $this->db->dbClose();

    }

}

?>