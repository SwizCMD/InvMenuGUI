<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$release = $root . "/build/release";
if(!extension_loaded("phar")){
    throw new RuntimeException("PHP Phar extension is required");
}
if(ini_get("phar.readonly") !== "0"){
    throw new RuntimeException("Run with -d phar.readonly=0");
}
@mkdir($release, 0777, true);

function addTree(Phar $phar, string $directory, string $prefix = "") : void{
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
    foreach($it as $file){
        if($file->isFile()){
            $relative = substr($file->getPathname(), strlen($directory) + 1);
            $phar->addFile($file->getPathname(), $prefix . str_replace(DIRECTORY_SEPARATOR, "/", $relative));
        }
    }
}
function buildPharFile(string $target, string $alias, callable $populate) : void{
    if(file_exists($target)){ unlink($target); }
    $phar = new Phar($target);
    $phar->startBuffering();
    $populate($phar);
    $phar->setStub("<?php __HALT_COMPILER();");
    $phar->setAlias($alias);
    $phar->stopBuffering();
}
function addDirectoryToZip(PharData $zip, string $directory, string $prefix) : void{
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
    foreach($it as $file){
        if($file->isFile()){
            $relative = substr($file->getPathname(), strlen($directory) + 1);
            $zip->addFile($file->getPathname(), $prefix . str_replace(DIRECTORY_SEPARATOR, "/", $relative));
        }
    }
}

buildPharFile($release . "/InvMenuGUI.phar", "InvMenuGUI", static function(Phar $phar) use($root) : void{
    $phar->addFile($root . "/plugin.yml", "plugin.yml");
    addTree($phar, $root . "/src", "src/");
});
buildPharFile($release . "/InvMenuGUI-Virion.phar", "InvMenuGUI-Virion", static function(Phar $phar) use($root) : void{
    foreach(["virion.yml","composer.json","README.md"] as $f){ $phar->addFile($root . "/" . $f, $f); }
    addTree($phar, $root . "/src", "src/");
});
$zipPath = $release . "/InvMenuGUI-Virion-Source.zip";
if(file_exists($zipPath)){ unlink($zipPath); }
$zip = new PharData($zipPath);
foreach(["virion.yml","composer.json","README.md","LICENSE"] as $f){ $zip->addFile($root . "/" . $f, $f); }
addDirectoryToZip($zip, $root . "/src", "src/");
unset($zip);

buildPharFile($release . "/InvMenuGUITest.phar", "InvMenuGUITest", static function(Phar $phar) use($root) : void{
    $phar->addFile($root . "/examples/InvMenuGUITest/plugin.yml", "plugin.yml");
    addTree($phar, $root . "/src", "src/");
    $phar->addFile($root . "/examples/InvMenuGUITest/src/Main.php", "src/srshull/invmenuguitest/Main.php");
});

echo "Built standalone Phar, Virion Phar, Virion source ZIP and InvMenuGUITest.phar.\\n";
