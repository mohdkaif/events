<?php

function upload_file($request, $file_name, $folder_name)
{
    $file       = $request->file($file_name);
    $extension  = $file->getClientOriginalExtension();
    $file_name  = $file->getClientOriginalName();
    $backupLoc =  'assets/uploads/' . $folder_name;
    if (!is_dir($backupLoc)) {
        mkdir($backupLoc, 0755, true);
    }
    $file->move($backupLoc, $file_name);
    return ($backupLoc . '/' . $file_name);
}
