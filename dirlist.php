<?php
/**
* directory_list
* return an array containing optionally all files, only directiories or only files at a file system path
* @author     cgray The Metamedia Corporation www.metamedia.us
*
* @param    $base_path         string    either absolute or relative path
* @param    $filter_dir        boolean    Filter directories from result (ignored except in last directory if $recursive is true)
* @param    $filter_files    boolean    Filter files from result
* @param    $exclude        string    Pipe delimited string of files to always ignore
* @param    $recursive        boolean    Descend directory to the bottom?
* @return    $result_list    array    Nested array or false
* @access public
* @license    GPL v3
*/
function directory_list($directory_base_path, $filter_dir = false, $filter_files = false, $exclude = ".|..|.DS_Store|.svn", $recursive = true){
    $directory_base_path = rtrim($directory_base_path, "/") . "/";

    if (!is_dir($directory_base_path)){
        error_log(__FUNCTION__ . "File at: $directory_base_path is not a directory.");
        return false;
    }

    $result_list = array();
    $exclude_array = explode("|", $exclude);

    if (!$folder_handle = opendir($directory_base_path)) {
        error_log(__FUNCTION__ . "Could not open directory at: $directory_base_path");
        return false;
    }else{
        while(false !== ($filename = readdir($folder_handle))) {
            if(!in_array($filename, $exclude_array)) {
                if(is_dir($directory_base_path . $filename . "/")) {
                    if($recursive && strcmp($filename, ".")!=0 && strcmp($filename, "..")!=0 ){ // prevent infinite recursion
                        error_log($directory_base_path . $filename . "/");
                        $result_list[$filename] = directory_list("$directory_base_path$filename/", $filter_dir, $filter_files, $exclude, $recursive);
                    }elseif(!$filter_dir){
                        $result_list[] = $filename;
                    }
                }elseif(!$filter_files){
                    $result_list[] = $filename;
                }
            }
        }
        closedir($folder_handle);
        return $result_list;
    }
}
?>