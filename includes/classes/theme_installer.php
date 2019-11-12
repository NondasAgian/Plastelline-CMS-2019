<?php

class Theme_Installer
{
    public static function GetActiveThemes()
    {
        $path='themes'.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'*.php';
        $files_path_array = glob($path);
        foreach ($files_path_array as $path) 
        {
            $th_name = explode(DIRECTORY_SEPARATOR, $path);
            if($th_name[1] != 'admin_topnav')
            {
                $theme_name = array($th_name[1], $th_name[1]);                
            }

            // var_dump($theme_name);
            
            $result = $theme_name[0].",".Router::ThemeNameExists($theme_name[1]);
            // var_dump($result);
            $result_array = explode(",",$result);
            
            if($result_array[1] == "")
            {
                // var_dump($result_array[0]);
                Theme_Installer::Install_Theme($result_array[0]);
                // Theme_Installer::Permit_Theme($result_array[0]);
            }
            // var_dump($theme_name);
        }//at this point we can see the installed modules, and the ones that are nor defined in the database, will show up as false.
        //the function will run for all the returned arrays that have length = 1
    }

    public static function Install_Theme($theme)
    {
        $query = new Query(new DB());
        $query->SetTableName("theme_registry");
        $query->Insert([null, "{$theme}", "{$theme}"."_Theme", "A custom "."{$theme}"." theme", 0]);
        $query->Run();
        // $result = $query->getSQL();
        $query = null;
        var_dump("theme_name is: ".$theme);
        // var_dump("result is: ".$result);
    }

    // public static function Permit_Module($module)
    // {
    //     //get the module id
    //     $query = new Query(new DB());
    //     $query-> SetTableName('routes');
    //     $query->Select(['routeid']);
    //     $query->Where(['mod_name','=',$module]);
    //     $query->Run();
    //     $modId = $query->GetReturnedRows();
    //     $query = null;

    //     // var_dump($modId);

    //     //insert queries for each role
    //     $query = new Query(new DB());
    //     $query-> SetTableName('permissions');
    //     $query-> Insert([null, 1, "{$modId[0][0]}", 0]);
    //     $query->Run();
    //     $query = null;

    //     $query = new Query(new DB());
    //     $query-> SetTableName('permissions');
    //     $query-> Insert([null, 2, "{$modId[0][0]}", 1]);
    //     $query->Run();
    //     $query = null;

    //     $query = new Query(new DB());
    //     $query-> SetTableName('permissions');
    //     $query-> Insert([null, 3, "{$modId[0][0]}", 0]);
    //     $query->Run();
    //     $query = null;
    // }
    
}

?>