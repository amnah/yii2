<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * This command builds a revision compiled folder for assets
 */
class BuildController extends Controller
{
    public $hashLength = 15;
    
    public function actionIndex()
    {
        // determine webpath
        $webPath = Yii::getAlias('@app/web');

        // get files in directory
        $newFiles = [];
        $deleteFiles = [];
        $keepFiles = [];
        $files = glob("$webPath/compiled/*.{css,js}", GLOB_BRACE);
        foreach ($files as $file) {
            // mark file to be deleted
            if (preg_match('/\..{' . $this->hashLength . '}\./', $file)) {
                $deleteFiles[] = $file;
                continue;
            }

            // compute hash and add to filename
            $hash = hash_file('sha1', $file);
            $hash = substr($hash, 0, $this->hashLength);
            $newFile = preg_replace('/(.*)(\.min)?(\.css|\.js)/U', "$1.$hash$2$3", $file);

            // determine if we need to copy the file or keep it as-is
            if (!in_array($newFile, $files)) {
                $newFiles[$file] = $newFile;
            } else {
                $keepFiles[$file] = $newFile;
            }
        }

        // copy new files
        foreach ($newFiles as $oldFile => $newFile) {
            $cmd = "cp '$oldFile' '$newFile'";
            $this->stdout("Copying file [ $newFile ]\n", Console::FG_YELLOW);
            shell_exec($cmd);
        }

        // write manifest file
        $manifestData = [];
        $assetFiles = array_merge($newFiles, $keepFiles);
        foreach ($assetFiles as $original => $compiled) {
            $original = str_replace($webPath, "", $original);
            $compiled = str_replace($webPath, "", $compiled);
            $manifestData[$original] = $compiled;
        }
        $manifestFile = "$webPath/compiled/manifest.json";
        $this->stdout("Writing manifest file\n", Console::FG_YELLOW);
        file_put_contents($manifestFile, json_encode($manifestData, JSON_PRETTY_PRINT));

        // delete files that aren't needed anymore
        $deleteFiles = array_diff($deleteFiles, $keepFiles);
        if ($deleteFiles) {
            foreach ($deleteFiles as $k => $file) {
                $deleteFiles[$k] = "'{$file}'";
            }
            $deleteFiles = implode(" ", $deleteFiles);
            $cmd = "rm -f $deleteFiles";
            $this->stdout("Removing files [ $cmd ]\n", Console::FG_YELLOW);
            shell_exec($cmd);
        }
    }

    public function actionClear()
    {
        $webPath = Yii::getAlias('@app/web');
        $questionMarks = str_repeat('?', $this->hashLength);
        $cmd = "rm -f $webPath/compiled/*.$questionMarks.*";
        $this->stdout("Removing files [ $cmd ]\n", Console::FG_YELLOW);
        shell_exec($cmd);
        $cmd = "rm -f $webPath/compiled/manifest.json";
        $this->stdout("Removing manifest [ $cmd ]\n", Console::FG_YELLOW);
        shell_exec($cmd);
    }
}
