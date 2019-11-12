<?php

class Module_Installer
{
    public static function GetActiveModules()
    {
        $path='modules'.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'*.php';
        $files_path_array = glob($path);
        foreach ($files_path_array as $path) 
        {
            $mod_name = explode(DIRECTORY_SEPARATOR, $path);
            if($mod_name[1] != 'admin_topnav')
            {
                $module_name = array($mod_name[1], $mod_name[1]);                
            }

            // var_dump($module_name);
            
            $result = $module_name[0].",".Router::ModuleNameExists($module_name[1]);
            // var_dump($result);
            $result_array = explode(",",$result);
            
            if($result_array[1] == "")
            {
                // var_dump($result_array[0]);
                Module_Installer::Install_Module($result_array[0]);
                Module_Installer::Permit_Module($result_array[0]);
            }
            // var_dump($module_name);
        }//at this point we can see the installed modules, and the ones that are nor defined in the database, will show up as false.
        //the function will run for all the returned arrays that have length = 1
    }

    public static function Install_Module($module)
    {
        $query = new Query(new DB());
        $query->SetTableName("routes");
        $query->Insert([null, "{$module}", null, null, "{$module}", "{$module}"." display", "{$module}"." desc",1, 1, 1]);
        $query->Run();
        // $result = $query->getSQL();
        $query = null;
        // var_dump("module_name is: ".$module);
        // var_dump("result is: ".$result);
    }

    public static function Permit_Module($module)
    {
        //get the module id
        $query = new Query(new DB());
        $query-> SetTableName('routes');
        $query->Select(['routeid']);
        $query->Where(['mod_name','=',$module]);
        $query->Run();
        $modId = $query->GetReturnedRows();
        $query = null;

        // var_dump($modId);

        //insert queries for each role
        $query = new Query(new DB());
        $query-> SetTableName('permissions');
        $query-> Insert([null, 1, "{$modId[0][0]}", 0]);
        $query->Run();
        $query = null;

        $query = new Query(new DB());
        $query-> SetTableName('permissions');
        $query-> Insert([null, 2, "{$modId[0][0]}", 1]);
        $query->Run();
        $query = null;

        $query = new Query(new DB());
        $query-> SetTableName('permissions');
        $query-> Insert([null, 3, "{$modId[0][0]}", 0]);
        $query->Run();
        $query = null;
    }
    
}

?>